<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SaldoHistory;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PenjualController extends Controller
{
    /**
     * Helper: get the authenticated penjual's Toko (or redirect to setup).
     */
    private function getMyToko()
    {
        return Auth::user()->toko;
    }

    /**
     * Penjual dashboard — order queue + stats for their toko.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $toko = $this->getMyToko();

        // If penjual hasn't created a toko yet, redirect to setup
        if (!$toko) {
            return redirect()->route('penjual.setup')
                ->with('info', 'Silakan buat toko Anda terlebih dahulu.');
        }

        // Orders that have at least one item from this toko
        $ordersQuery = Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', $toko->id));

        $pendingOrders  = (clone $ordersQuery)->where('status', 'pending')->latest()->get();
        $diprosesOrders = (clone $ordersQuery)->where('status', 'diproses')->latest()->get();
        $siapOrders     = (clone $ordersQuery)->where('status', 'siap')->latest()->get();
        $recentOrders   = (clone $ordersQuery)->whereIn('status', ['selesai'])->latest()->take(10)->get();

        $totalMenus       = $toko->menus()->count();
        $tersediaMenus    = $toko->menus()->where('status', 'tersedia')->count();
        $totalOrdersToday = (clone $ordersQuery)
            ->whereDate('created_at', today())
            ->count();

        return view('penjual.dashboard', compact(
            'toko',
            'pendingOrders',
            'diprosesOrders',
            'siapOrders',
            'recentOrders',
            'totalMenus',
            'tersediaMenus',
            'totalOrdersToday'
        ));
    }

    /**
     * Dedicated antri (order queue) page for the penjual.
     */
    public function antri()
    {
        $toko = $this->getMyToko();
        if (!$toko) {
            return redirect()->route('penjual.setup');
        }

        $ordersQuery = Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', $toko->id));

        $pendingOrders  = (clone $ordersQuery)->where('status', 'pending')->latest()->get();
        $diprosesOrders = (clone $ordersQuery)->where('status', 'diproses')->latest()->get();
        $siapOrders     = (clone $ordersQuery)->where('status', 'siap')->latest()->get();
        $selesaiToday   = (clone $ordersQuery)->where('status', 'selesai')->whereDate('updated_at', today())->count();

        $counts = [
            'pending'  => $pendingOrders->count(),
            'diproses' => $diprosesOrders->count(),
            'siap'     => $siapOrders->count(),
            'selesai'  => $selesaiToday,
        ];

        return view('penjual.antri.index', compact('toko', 'pendingOrders', 'diprosesOrders', 'siapOrders', 'counts'));
    }

    /**
     * Rekap pendapatan & pesanan selesai untuk toko ini.
     */
    public function rekap()
    {
        $toko = $this->getMyToko();
        if (!$toko) {
            return redirect()->route('penjual.setup');
        }

        $menuIds = $toko->menus->pluck('id');

        // Base query: only orderDetails from this toko's menus, on selesai orders
        $base = OrderDetail::whereIn('menu_id', $menuIds)
            ->whereHas('order', fn($q) => $q->where('status', 'selesai'));

        // Summary totals
        $revenueToday = (clone $base)->whereDate('created_at', today())->sum('subtotal');
        $revenueMonth = (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('subtotal');
        $revenueYear  = (clone $base)->whereYear('created_at', now()->year)->sum('subtotal');
        $revenueTotal = (clone $base)->sum('subtotal');

        // Daily breakdown — last 7 days
        $dailyRevenue = collect(range(6, 0))->map(function ($daysAgo) use ($menuIds) {
            $date = now()->subDays($daysAgo);
            $rev  = OrderDetail::whereIn('menu_id', $menuIds)
                ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
                ->whereDate('created_at', $date)
                ->sum('subtotal');
            return ['date' => $date, 'revenue' => (float) $rev];
        });

        // Monthly breakdown — last 12 months
        $monthlyRevenue = collect(range(11, 0))->map(function ($monthsAgo) use ($menuIds) {
            $date = now()->subMonths($monthsAgo);
            $rev  = OrderDetail::whereIn('menu_id', $menuIds)
                ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('subtotal');
            return ['date' => $date, 'revenue' => (float) $rev];
        });

        // Yearly breakdown — last 3 years
        $yearlyRevenue = collect(range(2, 0))->map(function ($yearsAgo) use ($menuIds) {
            $date = now()->subYears($yearsAgo);
            $rev  = OrderDetail::whereIn('menu_id', $menuIds)
                ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
                ->whereYear('created_at', $date->year)
                ->sum('subtotal');
            return ['date' => $date, 'revenue' => (float) $rev];
        });

        // Top menus by revenue
        $topMenus = OrderDetail::whereIn('menu_id', $menuIds)
            ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
            ->selectRaw('menu_id, sum(quantity) as total_qty, sum(subtotal) as total_rev')
            ->groupBy('menu_id')
            ->orderByDesc('total_rev')
            ->with('menu')
            ->limit(5)
            ->get();

        // Paginated completed orders
        $selesaiOrders = Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', $toko->id))
            ->where('status', 'selesai')
            ->latest('updated_at')
            ->paginate(20);

        return view('penjual.rekap.index', compact(
            'toko',
            'revenueToday',
            'revenueMonth',
            'revenueYear',
            'revenueTotal',
            'dailyRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'topMenus',
            'selesaiOrders'
        ));
    }

    /**
     * Update order status (validate it belongs to penjual's toko).
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $toko = $this->getMyToko();

        if (!$toko) {
            return back()->withErrors(['error' => 'Anda belum memiliki toko.']);
        }

        // Verify order has items from this toko
        $belongs = $order->orderDetails()->whereHas('menu', fn($q) => $q->where('toko_id', $toko->id))->exists();
        if (!$belongs) {
            abort(403, 'Order ini bukan milik toko Anda.');
        }

        $request->validate([
            'status' => 'required|in:pending,diproses,siap,selesai',
        ]);

        // Only credit penjual saldo once when transitioning TO selesai (cash only — saldo already transferred at order placement)
        if ($request->status === 'selesai' && $order->status !== 'selesai') {
            DB::transaction(function () use ($order, $toko) {
                if ($order->payment_method !== 'saldo') {
                    $tokoSubtotal = $order->orderDetails()
                        ->whereHas('menu', fn($q) => $q->where('toko_id', $toko->id))
                        ->sum('subtotal');

                    if ($tokoSubtotal > 0) {
                        $penjual = $toko->owner;
                        $penjual->increment('saldo', $tokoSubtotal);

                        SaldoHistory::create([
                            'user_id'    => $penjual->id,
                            'jenis'      => 'masuk',
                            'nominal'    => $tokoSubtotal,
                            'keterangan' => "Pendapatan Order #{$order->id}",
                            'saldo_akhir' => $penjual->fresh()->saldo,
                        ]);
                    }
                }

                $order->update(['status' => 'selesai']);
            });
        } else {
            $order->update(['status' => $request->status]);
        }

        $statusLabel = [
            'pending'  => 'Menunggu',
            'diproses' => 'Diproses',
            'siap'     => 'Siap Diambil',
            'selesai'  => 'Selesai',
        ][$request->status] ?? $request->status;

        return back()->with('success', "Status order #{$order->id} diubah menjadi {$statusLabel}.");
    }

    // ─── Menu Management ─────────────────────────────────────────────────────

    /**
     * List menus for penjual's toko.
     */
    public function menus()
    {
        $toko  = $this->getMyToko();
        if (!$toko) return redirect()->route('penjual.setup');

        $menus = $toko->menus()->latest()->paginate(15);
        return view('penjual.menus.index', compact('toko', 'menus'));
    }

    /**
     * Show create menu form.
     */
    public function createMenu()
    {
        $toko = $this->getMyToko();
        if (!$toko) return redirect()->route('penjual.setup');

        return view('penjual.menus.create', compact('toko'));
    }

    /**
     * Store a new menu.
     */
    public function storeMenu(Request $request)
    {
        $toko = $this->getMyToko();
        if (!$toko) return redirect()->route('penjual.setup');

        $data = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'harga'     => 'required|numeric|min:0',
            'status'    => 'required|in:tersedia,habis',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $toko->menus()->create($data);

        return redirect()->route('penjual.menus')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Show edit menu form.
     */
    public function editMenu(Menu $menu)
    {
        $toko = $this->getMyToko();
        $this->authorizeMenu($menu, $toko);

        return view('penjual.menus.edit', compact('toko', 'menu'));
    }

    /**
     * Update a menu.
     */
    public function updateMenu(Request $request, Menu $menu)
    {
        $toko = $this->getMyToko();
        $this->authorizeMenu($menu, $toko);

        $data = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'harga'     => 'required|numeric|min:0',
            'status'    => 'required|in:tersedia,habis',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('penjual.menus')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Toggle menu status between tersedia / habis.
     */
    public function toggleMenuStatus(Menu $menu)
    {
        $toko = $this->getMyToko();
        $this->authorizeMenu($menu, $toko);

        $menu->update([
            'status' => $menu->status === 'tersedia' ? 'habis' : 'tersedia',
        ]);

        return back()->with('success', 'Status menu diperbarui.');
    }

    /**
     * Delete a menu.
     */
    public function destroyMenu(Menu $menu)
    {
        $toko = $this->getMyToko();
        $this->authorizeMenu($menu, $toko);

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('penjual.menus')->with('success', 'Menu berhasil dihapus!');
    }

    // ─── Setup ────────────────────────────────────────────────────────────────

    /**
     * Show toko setup form.
     */
    public function setup()
    {
        $user = Auth::user();

        if ($user->toko) {
            return redirect()->route('penjual.dashboard');
        }

        return view('penjual.setup');
    }

    /**
     * Store toko for first-time setup.
     */
    public function storeSetup(Request $request)
    {
        $user = Auth::user();

        if ($user->toko) {
            return redirect()->route('penjual.dashboard')->with('error', 'Anda sudah memiliki toko.');
        }

        $request->validate([
            'nama_toko' => 'required|string|max:255',
        ]);

        Toko::create([
            'nama_toko' => $request->nama_toko,
            'user_id'   => $user->id,
        ]);

        return redirect()->route('penjual.dashboard')->with('success', 'Toko berhasil dibuat! Selamat datang di E-Canteen.');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function authorizeMenu(Menu $menu, ?Toko $toko): void
    {
        if (!$toko || $menu->toko_id !== $toko->id) {
            abort(403, 'Menu ini bukan milik toko Anda.');
        }
    }
}
