<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SaldoHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * JSON endpoint: returns the count of 'siap' orders for the authenticated user.
     * Used by the client-side notification polling.
     */
    public function siapCount()
    {
        $count = Auth::user()->orders()->where('status', 'siap')->count();
        return response()->json(['count' => $count]);
    }

    public function index()
    {
        $orders = Auth::user()->orders()
            ->with('orderDetails.menu.toko')
            ->where('status', '!=', 'selesai')
            ->latest()
            ->get();
        return view('orders.index', compact('orders'));
    }

    public function riwayat()
    {
        $orders = Auth::user()->orders()
            ->with('orderDetails.menu.toko')
            ->where('status', 'selesai')
            ->latest()
            ->paginate(10);
        return view('orders.riwayat', compact('orders'));
    }

    /**
     * Show checkout page (GET)
     * - ?menu_id=X  → direct buy single item
     * - session checkout_items → from cart
     */
    public function showCheckout(Request $request)
    {
        $user = Auth::user();

        if ($request->has('menu_id')) {
            // Direct buy single item
            $menu = Menu::with('toko')->findOrFail($request->menu_id);
            $items = [[
                'menu_id'   => $menu->id,
                'name'      => $menu->nama_menu,
                'harga'     => $menu->harga,
                'qty'       => 1,
                'toko_name' => $menu->toko->nama_toko,
                'subtotal'  => $menu->harga,
            ]];
            session(['checkout_items' => $items]);
        } else {
            $items = session('checkout_items', []);
        }

        if (empty($items)) {
            return redirect()->route('menus.index')->with('error', 'Tidak ada item untuk dicheckout.');
        }

        $total = array_sum(array_column($items, 'subtotal'));

        return view('checkout.index', compact('items', 'total', 'user'));
    }

    /**
     * Prepare checkout from cart (POST)
     * Receives items[] array from JavaScript, stores in session
     */
    public function prepareCheckout(Request $request)
    {
        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.menu_id'     => 'required|exists:menus,id',
            'items.*.quantity'    => 'required|integer|min:1',
        ]);

        $items = [];
        $tokoId = null;
        foreach ($request->items as $item) {
            $menu = Menu::with('toko')->findOrFail($item['menu_id']);
            $qty  = (int) $item['quantity'];

            // Enforce single-toko rule
            if ($tokoId === null) {
                $tokoId = $menu->toko_id;
            } elseif ($menu->toko_id !== $tokoId) {
                return redirect()->route('cart.index')
                    ->withErrors(['error' => 'Semua item harus berasal dari satu toko yang sama.']);
            }

            $items[] = [
                'menu_id'   => $menu->id,
                'name'      => $menu->nama_menu,
                'harga'     => $menu->harga,
                'qty'       => $qty,
                'toko_name' => $menu->toko->nama_toko,
                'subtotal'  => $menu->harga * $qty,
            ];
        }

        session(['checkout_items' => $items]);
        return redirect()->route('checkout.show');
    }

    /**
     * Place order — called from checkout form
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'waktu_pengambilan' => 'required|in:Istirahat 1,Istirahat 2',
            'payment_method'    => 'required|in:saldo,cash',
            'catatan'           => 'nullable|string|max:300',
        ]);

        $user  = Auth::user();
        $items = session('checkout_items', []);

        if (empty($items)) {
            return redirect()->route('menus.index')->with('error', 'Sesi checkout habis. Silakan mulai lagi.');
        }

        // Re-validate menus exist and get fresh prices
        $total = 0;
        $menuItems = [];
        $tokoId = null;
        foreach ($items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            if (!$menu->isAvailable()) {
                return back()->withErrors(['error' => "Menu {$menu->nama_menu} sedang tidak tersedia."]);
            }
            // Enforce single-toko rule
            if ($tokoId === null) {
                $tokoId = $menu->toko_id;
            } elseif ($menu->toko_id !== $tokoId) {
                session()->forget('checkout_items');
                return redirect()->route('cart.index')
                    ->withErrors(['error' => 'Semua item harus berasal dari satu toko yang sama.']);
            }
            $subtotal    = $menu->harga * $item['qty'];
            $total      += $subtotal;
            $menuItems[] = ['menu' => $menu, 'qty' => $item['qty'], 'subtotal' => $subtotal];
        }

        if ($request->payment_method === 'saldo') {
            if ($user->saldo < $total) {
                return back()->withErrors(['error' => 'Saldo tidak cukup. Silakan top-up terlebih dahulu.']);
            }

            $order = DB::transaction(function () use ($user, $total, $menuItems, $request, $tokoId) {
                $order = Order::create([
                    'user_id'           => $user->id,
                    'total_harga'       => $total,
                    'waktu_pengambilan' => $request->waktu_pengambilan,
                    'catatan'           => $request->catatan ?: null,
                    'status'            => 'pending',
                    'payment_method'    => 'saldo',
                ]);

                foreach ($menuItems as $item) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'menu_id'  => $item['menu']->id,
                        'quantity' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                // Deduct from buyer
                $user->saldo -= $total;
                $user->save();

                SaldoHistory::create([
                    'user_id'    => $user->id,
                    'jenis'      => 'keluar',
                    'nominal'    => $total,
                    'keterangan' => "Pembelian Order #{$order->id}",
                    'saldo_akhir' => $user->saldo,
                ]);

                // Credit penjual immediately (saldo payment = instant transfer)
                $toko    = \App\Models\Toko::find($tokoId);
                $penjual = $toko?->owner;
                if ($penjual) {
                    $penjual->increment('saldo', $total);
                    SaldoHistory::create([
                        'user_id'    => $penjual->id,
                        'jenis'      => 'masuk',
                        'nominal'    => $total,
                        'keterangan' => "Pendapatan Order #{$order->id}",
                        'saldo_akhir' => $penjual->fresh()->saldo,
                    ]);
                }

                return $order;
            });

            session()->forget('checkout_items');
            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat! Saldo dipotong Rp ' . number_format($total, 0, ',', '.'));
        }

        // Cash / GoPay — create pending order
        $paymentCode = strtoupper(Str::random(12));

        $order = DB::transaction(function () use ($user, $total, $menuItems, $request, $paymentCode) {
            $order = Order::create([
                'user_id'           => $user->id,
                'total_harga'       => $total,
                'waktu_pengambilan' => $request->waktu_pengambilan,
                'catatan'           => $request->catatan ?: null,
                'status'            => 'menunggu_pembayaran',
                'payment_method'    => $request->payment_method,
                'payment_code'      => $paymentCode,
            ]);

            foreach ($menuItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['menu']->id,
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        session()->forget('checkout_items');
        return redirect()->route('orders.payment', $order);
    }

    /**
     * Show payment pending page (QR / GoPay)
     */
    public function showPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if (!in_array($order->status, ['menunggu_pembayaran'])) {
            return redirect()->route('orders.show', $order);
        }
        $order->load('orderDetails.menu');
        return view('orders.payment', compact('order'));
    }

    /**
     * Simulate confirming payment (for demo purposes)
     */
    public function confirmPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status !== 'menunggu_pembayaran') {
            return redirect()->route('orders.show', $order);
        }

        $user = Auth::user();

        DB::transaction(function () use ($order) {
            $order->status = 'pending';
            $order->save();
        });

        return redirect()->route('orders.index')->with('success', 'Pembayaran dikonfirmasi! Pesanan sedang diproses.');
    }

    /**
     * Legacy checkout (from localStorage form submit — kept for backwards compatibility)
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.menu_id'        => 'required|exists:menus,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'waktu_pengambilan'      => 'required|in:Istirahat 1,Istirahat 2',
        ]);

        $user = Auth::user();
        $totalHarga = 0;
        $menuItems  = [];

        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            if (!$menu->isAvailable()) {
                return back()->withErrors(['error' => "Menu {$menu->nama_menu} sedang tidak tersedia."]);
            }
            $subtotal    = $menu->harga * $item['quantity'];
            $totalHarga += $subtotal;
            $menuItems[] = ['menu' => $menu, 'quantity' => $item['quantity'], 'subtotal' => $subtotal];
        }

        if ($user->saldo < $totalHarga) {
            return back()->withErrors(['error' => 'Saldo tidak cukup. Silakan top-up terlebih dahulu.']);
        }

        DB::transaction(function () use ($user, $totalHarga, $menuItems, $request) {
            $order = Order::create([
                'user_id'           => $user->id,
                'total_harga'       => $totalHarga,
                'waktu_pengambilan' => $request->waktu_pengambilan,
                'status'            => 'pending',
                'payment_method'    => 'saldo',
            ]);

            foreach ($menuItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['menu']->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $user->saldo -= $totalHarga;
            $user->save();

            SaldoHistory::create([
                'user_id'    => $user->id,
                'jenis'      => 'keluar',
                'nominal'    => $totalHarga,
                'keterangan' => "Pembelian Order #{$order->id}",
                'saldo_akhir' => $user->saldo,
            ]);
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load('orderDetails.menu.toko');
        return view('orders.show', compact('order'));
    }
}
