<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\OrderDetail;
use App\Models\SaldoHistory;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminTokoController extends Controller
{
    public function dashboard()
    {
        $tokos  = Toko::withCount('menus')->get();

        $orderStats = [
            'pending'  => Order::where('status', 'pending')->count(),
            'diproses' => Order::where('status', 'diproses')->count(),
            'siap'     => Order::where('status', 'siap')->count(),
        ];

        $menuLaris = OrderDetail::select(
            'menu_id',
            DB::raw('SUM(quantity) as total_terjual'),
            DB::raw('SUM(subtotal) as total_pendapatan')
        )
            ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
            ->with('menu.toko')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return view('admin-toko.dashboard', compact('tokos', 'orderStats', 'menuLaris'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,diproses,siap,selesai']);

        // Only credit penjual saldo once when transitioning TO selesai (cash only — saldo already transferred at order placement)
        if ($request->status === 'selesai' && $order->status !== 'selesai') {
            DB::transaction(function () use ($order) {
                if ($order->payment_method !== 'saldo') {
                    $perToko = $order->orderDetails()
                        ->with('menu.toko.user')
                        ->get()
                        ->groupBy(fn($d) => $d->menu->toko_id);

                    foreach ($perToko as $tokoId => $details) {
                        $subtotal = $details->sum('subtotal');
                        $penjual  = $details->first()->menu->toko->owner;

                        if ($penjual && $subtotal > 0) {
                            $penjual->increment('saldo', $subtotal);

                            SaldoHistory::create([
                                'user_id'    => $penjual->id,
                                'jenis'      => 'masuk',
                                'nominal'    => $subtotal,
                                'keterangan' => "Pendapatan Order #{$order->id}",
                                'saldo_akhir' => $penjual->fresh()->saldo,
                            ]);
                        }
                    }
                }

                $order->update(['status' => 'selesai']);
            });
        } else {
            $order->update(['status' => $request->status]);
        }

        return back()->with('success', 'Status pesanan berhasil diupdate!');
    }

    public function arsip()
    {
        $tokos = Toko::withCount('menus')->get()->map(function ($toko) {
            $toko->selesai_count     = Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', $toko->id))
                ->where('status', 'selesai')->count();
            $toko->total_pendapatan  = \App\Models\OrderDetail::whereHas('menu', fn($q) => $q->where('toko_id', $toko->id))
                ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
                ->sum('subtotal');
            return $toko;
        });

        return view('admin-toko.arsip.index', compact('tokos'));
    }

    public function arsipToko(Toko $toko)
    {
        $orders = Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', $toko->id))
            ->with(['orderDetails' => fn($q) => $q->whereHas('menu', fn($q2) => $q2->where('toko_id', $toko->id))->with('menu'), 'user'])
            ->where('status', 'selesai')
            ->latest()
            ->paginate(20);

        $totalPendapatan = \App\Models\OrderDetail::whereHas('menu', fn($q) => $q->where('toko_id', $toko->id))
            ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
            ->sum('subtotal');

        return view('admin-toko.arsip.toko', compact('toko', 'orders', 'totalPendapatan'));
    }

    // ==================== TOKO CRUD ====================

    public function tokos()
    {
        $tokos = Toko::with('owner')->withCount('menus')->latest()->get();
        return view('admin-toko.tokos.index', compact('tokos'));
    }

    public function createToko()
    {
        $penjuals = User::where('role', 'toko')->orderBy('name')->get();
        return view('admin-toko.tokos.create', compact('penjuals'));
    }

    public function storeToko(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'user_id'   => 'nullable|exists:users,id',
        ]);
        Toko::create([
            'nama_toko' => $request->nama_toko,
            'user_id'   => $request->user_id ?: null,
        ]);
        return redirect()->route('admin-toko.tokos')->with('success', 'Toko berhasil ditambahkan!');
    }

    public function editToko(Toko $toko)
    {
        $penjuals = User::where('role', 'toko')->orderBy('name')->get();
        return view('admin-toko.tokos.edit', compact('toko', 'penjuals'));
    }

    public function updateToko(Request $request, Toko $toko)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'user_id'   => 'nullable|exists:users,id',
        ]);
        $toko->update([
            'nama_toko' => $request->nama_toko,
            'user_id'   => $request->user_id ?: null,
        ]);
        return redirect()->route('admin-toko.tokos')->with('success', 'Toko berhasil diupdate!');
    }

    public function destroyToko(Toko $toko)
    {
        $toko->delete();
        return redirect()->route('admin-toko.tokos')->with('success', 'Toko berhasil dihapus!');
    }

    // ==================== ANTRI PESANAN ====================

    public function antri()
    {
        $with = ['orderDetails.menu.toko', 'user'];

        $pendingOrders  = Order::with($with)->where('status', 'pending')->latest()->get();
        $diprosesOrders = Order::with($with)->where('status', 'diproses')->latest()->get();
        $siapOrders     = Order::with($with)->where('status', 'siap')->latest()->get();

        $counts = [
            'pending'  => $pendingOrders->count(),
            'diproses' => $diprosesOrders->count(),
            'siap'     => $siapOrders->count(),
        ];

        return view('admin-toko.antri.index', compact('pendingOrders', 'diprosesOrders', 'siapOrders', 'counts'));
    }

    // ==================== KELOLA USER ====================

    public function users(Request $request)
    {
        $role  = $request->query('role', 'all');
        $query = User::query()->latest();
        if ($role !== 'all') {
            $query->where('role', $role);
        }
        $users = $query->paginate(20);
        return view('admin-toko.users.index', compact('users', 'role'));
    }

    public function createUser()
    {
        return view('admin-toko.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:user,admin,toko',
            'saldo'    => 'nullable|numeric|min:0',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'saldo'    => $request->saldo ?? 0,
        ]);

        return redirect()->route('admin-toko.users')->with('success', 'User berhasil ditambahkan!');
    }

    public function editUser(User $user)
    {
        return view('admin-toko.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:user,admin,toko',
            'saldo'    => 'nullable|numeric|min:0',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'saldo' => $request->saldo ?? $user->saldo,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin-toko.users')->with('success', 'User berhasil diupdate!');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }
        $user->delete();
        return redirect()->route('admin-toko.users')->with('success', 'User berhasil dihapus!');
    }

    public function menus(Toko $toko)
    {
        $menus = $toko->menus()->latest()->get();
        return view('admin-toko.menus.index', compact('menus', 'toko'));
    }

    public function createMenu(Toko $toko)
    {
        return view('admin-toko.menus.create', compact('toko'));
    }

    public function storeMenu(Request $request, Toko $toko)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'harga'     => 'required|integer|min:100',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'    => 'required|in:tersedia,habis',
        ]);

        $data = $request->only(['nama_menu', 'kategori', 'harga', 'status']);
        $data['toko_id'] = $toko->id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        Menu::create($data);
        return redirect()->route('admin-toko.tokos.menus', $toko)->with('success', 'Menu berhasil ditambahkan!');
    }

    public function editMenu(Toko $toko, Menu $menu)
    {
        return view('admin-toko.menus.edit', compact('toko', 'menu'));
    }

    public function updateMenu(Request $request, Toko $toko, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'harga'     => 'required|integer|min:100',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'    => 'required|in:tersedia,habis',
        ]);

        $data = $request->only(['nama_menu', 'kategori', 'harga', 'status']);

        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                \Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $menu->update($data);
        return redirect()->route('admin-toko.tokos.menus', $toko)->with('success', 'Menu berhasil diupdate!');
    }

    public function destroyMenu(Toko $toko, Menu $menu)
    {
        if ($menu->foto) {
            \Storage::disk('public')->delete($menu->foto);
        }
        $menu->delete();
        return redirect()->route('admin-toko.tokos.menus', $toko)->with('success', 'Menu berhasil dihapus!');
    }
}
