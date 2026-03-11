@extends('layouts.app')

@section('title', 'Dashboard Penjual - ' . $toko->nama_toko)

@section('content')
<div class="max-w-6xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black tracking-tight">{{ $toko->nama_toko }}</h1>
            <p class="text-neutral-500 text-sm font-medium mt-1">Dashboard Penjual &mdash; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('penjual.menus') }}"
            class="bg-yellow-400 text-black px-5 py-2.5 rounded-2xl text-sm font-bold hover:bg-yellow-300 transition-all active:scale-[0.97] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Kelola Menu
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2">Pesanan Masuk</p>
            <p class="text-3xl font-black">{{ $pendingOrders->count() }}</p>
            <p class="text-xs text-neutral-400 mt-1">Menunggu diproses</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2">Sedang Diproses</p>
            <p class="text-3xl font-black text-blue-600">{{ $diprosesOrders->count() }}</p>
            <p class="text-xs text-neutral-400 mt-1">Sedang disiapkan</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2">Siap Diambil</p>
            <p class="text-3xl font-black text-emerald-600">{{ $siapOrders->count() }}</p>
            <p class="text-xs text-neutral-400 mt-1">Menunggu pelanggan</p>
        </div>
        <div class="bg-yellow-400 rounded-3xl p-5">
            <p class="text-xs font-black text-yellow-800/70 uppercase tracking-widest mb-2">Order Hari Ini</p>
            <p class="text-3xl font-black">{{ $totalOrdersToday }}</p>
            <p class="text-xs text-yellow-800/60 mt-1">Total pesanan masuk</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Order Queue --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Pending Orders --}}
            <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-black text-base">Pesanan Masuk</h2>
                        <p class="text-xs text-neutral-400 mt-0.5">Perlu segera diproses</p>
                    </div>
                    @if($pendingOrders->count() > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-black px-3 py-1 rounded-full">{{ $pendingOrders->count() }} baru</span>
                    @endif
                </div>
                <div class="divide-y divide-neutral-50">
                    @forelse($pendingOrders as $order)
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-bold text-sm">Pesanan #{{ $order->id }}</p>
                            <p class="text-xs text-neutral-500 mt-0.5">{{ $order->user->name ?? 'Siswa' }} &mdash; {{ $order->waktu_pengambilan }}</p>
                            <p class="text-xs text-neutral-400 mt-0.5">
                                @foreach($order->orderDetails->whereIn('menu_id', $toko->menus->pluck('id')) as $det)
                                    {{ $det->menu->nama_menu ?? '-' }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        </div>
                        <form action="{{ route('penjual.orders.update-status', $order) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="status" value="diproses">
                            <button type="submit" class="bg-black text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-neutral-800 transition-all whitespace-nowrap">
                                Proses &rarr;
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-neutral-300 text-4xl mb-2">&#9749;</p>
                        <p class="text-sm text-neutral-400 font-medium">Tidak ada pesanan baru</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Diproses Orders --}}
            @if($diprosesOrders->count() > 0)
            <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="font-black text-base">Sedang Diproses</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Tandai siap jika sudah selesai</p>
                </div>
                <div class="divide-y divide-neutral-50">
                    @foreach($diprosesOrders as $order)
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-bold text-sm">Pesanan #{{ $order->id }}</p>
                            <p class="text-xs text-neutral-500 mt-0.5">{{ $order->user->name ?? 'Siswa' }} &mdash; {{ $order->waktu_pengambilan }}</p>
                        </div>
                        <form action="{{ route('penjual.orders.update-status', $order) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="status" value="siap">
                            <button type="submit" class="bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-emerald-600 transition-all whitespace-nowrap">
                                Siap &#10003;
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Siap Orders --}}
            @if($siapOrders->count() > 0)
            <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="font-black text-base">Siap Diambil</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Menunggu pelanggan mengambil</p>
                </div>
                <div class="divide-y divide-neutral-50">
                    @foreach($siapOrders as $order)
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-bold text-sm">Pesanan #{{ $order->id }}</p>
                            <p class="text-xs text-neutral-500 mt-0.5">{{ $order->user->name ?? 'Siswa' }} &mdash; {{ $order->waktu_pengambilan }}</p>
                        </div>
                        <form action="{{ route('penjual.orders.update-status', $order) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="bg-neutral-100 text-neutral-600 text-xs font-bold px-4 py-2 rounded-xl hover:bg-neutral-200 transition-all whitespace-nowrap">
                                Selesai
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right Sidebar: Menu Summary + Recent Orders --}}
        <div class="space-y-6">

            {{-- Menu Summary --}}
            <div class="bg-white rounded-3xl border border-neutral-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-sm">Menu Toko</h3>
                    <a href="{{ route('penjual.menus.create') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-500">+ Tambah</a>
                </div>
                <div class="space-y-1.5 mb-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Total Menu</span>
                        <span class="font-black">{{ $totalMenus }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Tersedia</span>
                        <span class="font-black text-emerald-600">{{ $tersediaMenus }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Habis</span>
                        <span class="font-black text-red-500">{{ $totalMenus - $tersediaMenus }}</span>
                    </div>
                </div>
                <a href="{{ route('penjual.menus') }}"
                    class="w-full block text-center bg-neutral-50 hover:bg-neutral-100 text-neutral-700 py-2.5 rounded-xl text-xs font-bold transition-all">
                    Lihat Semua Menu
                </a>
            </div>

            {{-- Recent Completed Orders --}}
            @if($recentOrders->count() > 0)
            <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h3 class="font-black text-sm">Order Selesai Terbaru</h3>
                </div>
                <div class="divide-y divide-neutral-50">
                    @foreach($recentOrders->take(5) as $order)
                    <div class="px-6 py-3">
                        <div class="flex justify-between items-center">
                            <p class="text-sm font-bold">#{{ $order->id }}</p>
                            <p class="text-xs text-neutral-400">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $order->user->name ?? 'Siswa' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
