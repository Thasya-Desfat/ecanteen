@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400 mb-1">Overview</p>
    <h1 class="font-black text-2xl mb-6">Super Admin Dashboard</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
        <div class="bg-white rounded-2xl border border-neutral-100 p-4 text-center">
            <p class="text-3xl font-black">{{ $stats['total_siswa'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Siswa</p>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-4 text-center">
            <p class="text-3xl font-black">{{ $stats['total_admin_toko'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Admin Toko</p>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-4 text-center">
            <p class="text-3xl font-black text-yellow-500">{{ $stats['total_toko'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Toko</p>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-4 text-center">
            <p class="text-3xl font-black">{{ $stats['total_orders'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Total Order</p>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-4 text-center">
            <p class="text-3xl font-black text-amber-500">{{ $stats['pending_topups'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Top-Up Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-4 text-center">
            <p class="text-sm font-black leading-tight">Rp {{ number_format($stats['total_transaksi'], 0, ',', '.') }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Total Transaksi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Daftar Toko --}}
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100">
                <h2 class="font-black">Daftar Toko</h2>
            </div>
            <div class="divide-y divide-neutral-100">
                @forelse($tokos as $toko)
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-sm">{{ $toko->nama_toko }}</p>
                        <p class="text-xs text-neutral-400">{{ $toko->owner->name ?? '-' }} &bull; {{ $toko->owner->email ?? '-' }}</p>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">Aktif</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-neutral-400 text-sm">Belum ada toko terdaftar.</div>
                @endforelse
            </div>
        </div>

        {{-- Order Terbaru --}}
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100">
                <h2 class="font-black">Order Terbaru</h2>
            </div>
            <div class="divide-y divide-neutral-100">
                @forelse($recentOrders as $order)
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-sm">{{ $order->user->name ?? '-' }}</p>
                        <p class="text-xs text-neutral-400">Rp {{ number_format($order->total_harga, 0, ',', '.') }} &bull; {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'confirmed' => 'bg-yellow-100 text-yellow-700',
                            'ready'     => 'bg-emerald-100 text-emerald-700',
                            'completed' => 'bg-neutral-100 text-neutral-600',
                            'cancelled' => 'bg-red-100 text-red-600',
                        ];
                        $color = $statusColors[$order->status] ?? 'bg-neutral-100 text-neutral-600';
                    @endphp
                    <span class="text-[9px] font-black uppercase tracking-widest {{ $color }} px-2.5 py-1 rounded-full">{{ $order->status }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-neutral-400 text-sm">Belum ada order.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 
