@extends('layouts.app')

@section('title', 'Dashboard Admin - E-Canteen')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-black">Dashboard Admin</h1>
    </div>

    {{-- Order Queue Summary --}}
    @php $totalAntri = $orderStats['pending'] + $orderStats['diproses'] + $orderStats['siap']; @endphp
    <a href="{{ route('admin-toko.antri') }}"
        class="block bg-black text-white rounded-3xl p-6 mb-6 hover:bg-neutral-900 transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-black text-neutral-400 uppercase tracking-widest mb-1">Antri Pesanan</p>
                <p class="text-3xl font-black">{{ $totalAntri }} <span class="text-base font-semibold text-neutral-400">pesanan aktif</span></p>
            </div>
            <div class="w-12 h-12 bg-yellow-400 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex-1 bg-white/10 rounded-2xl px-4 py-3 text-center">
                <p class="text-xl font-black text-amber-400">{{ $orderStats['pending'] }}</p>
                <p class="text-xs text-neutral-400 mt-0.5">Pending</p>
            </div>
            <div class="flex-1 bg-white/10 rounded-2xl px-4 py-3 text-center">
                <p class="text-xl font-black text-blue-400">{{ $orderStats['diproses'] }}</p>
                <p class="text-xs text-neutral-400 mt-0.5">Diproses</p>
            </div>
            <div class="flex-1 bg-white/10 rounded-2xl px-4 py-3 text-center">
                <p class="text-xl font-black text-emerald-400">{{ $orderStats['siap'] }}</p>
                <p class="text-xs text-neutral-400 mt-0.5">Siap</p>
            </div>
        </div>
    </a>

    {{-- Toko Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        @forelse($tokos as $toko)
        <a href="{{ route('admin-toko.tokos.menus', $toko) }}" class="bg-white rounded-2xl border border-neutral-100 p-4 text-center hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <p class="font-bold text-sm truncate">{{ $toko->nama_toko }}</p>
            <p class="text-xs text-neutral-400 mt-1">{{ $toko->menus_count }} menu</p>
        </a>
        @empty
        <div class="col-span-4 bg-white rounded-2xl border border-neutral-100 p-6 text-center text-neutral-400 text-sm">
            Belum ada toko. <a href="{{ route('admin-toko.tokos.create') }}" class="text-yellow-600 font-bold hover:underline">Tambah toko</a>
        </div>
        @endforelse
        <a href="{{ route('admin-toko.tokos') }}" class="bg-yellow-50 border-2 border-dashed border-yellow-300 rounded-2xl p-4 text-center hover:bg-yellow-100 transition-all">
            <p class="text-yellow-600 font-bold text-sm">+ Kelola Semua Toko</p>
        </a>
    </div>

    {{-- Menu Paling Laris --}}
    @if($menuLaris->count() > 0)
    <div class="bg-white rounded-3xl border border-neutral-100 mb-6">
        <div class="px-6 py-4 border-b border-neutral-100 flex justify-between items-center">
            <h2 class="font-bold flex items-center gap-2">🔥 Menu Paling Laris</h2>
            <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-widest">Berdasarkan pesanan selesai</span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($menuLaris as $i => $item)
            <div class="px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black
                        {{ $i === 0 ? 'bg-yellow-400 text-black' : ($i === 1 ? 'bg-neutral-200 text-neutral-600' : ($i === 2 ? 'bg-orange-200 text-orange-800' : 'bg-neutral-100 text-neutral-400')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div>
                        <p class="font-bold text-sm">{{ $item->menu->nama_menu ?? '-' }}</p>
                        <p class="text-xs text-neutral-400">{{ $item->menu->toko->nama_toko ?? '-' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-black text-yellow-600">{{ $item->total_terjual }}x terjual</p>
                    <p class="text-xs text-neutral-400">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Arsip Link --}}
    <div class="mt-6 text-right">
        <a href="{{ route('admin-toko.arsip') }}"
            class="inline-flex items-center gap-2 bg-black hover:bg-neutral-800 text-white text-sm font-bold px-5 py-2.5 rounded-2xl shadow-lg shadow-black/10 transition-all hover:scale-[1.02]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            Arsip Penjualan
        </a>
    </div>

</div>
@endsection