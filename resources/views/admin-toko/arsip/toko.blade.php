@extends('layouts.app')

@section('title', 'Arsip - {{ $toko->nama_toko }} - E-Canteen')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">

    <div class="mb-6">
        <a href="{{ route('admin-toko.arsip') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Kembali ke Arsip Penjualan</a>
        <h1 class="font-black text-2xl mt-1">{{ $toko->nama_toko }}</h1>
        <p class="text-xs text-neutral-400 mt-1">Riwayat pesanan selesai dari toko ini.</p>
    </div>

    {{-- Summary Card --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-neutral-100 px-6 py-5 text-center">
            <p class="text-3xl font-black text-yellow-500">{{ $orders->total() }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Total Order Selesai</p>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 px-6 py-5 text-center">
            <p class="text-xl font-black text-emerald-600">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-1">Total Pendapatan</p>
        </div>
    </div>

    {{-- Orders List --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <div class="border-b border-neutral-100 px-6 py-4 flex justify-between items-center">
            <h2 class="font-black text-base">Daftar Pesanan Selesai</h2>
            <span class="text-[9px] font-black uppercase tracking-widest text-neutral-400">{{ $orders->total() }} pesanan</span>
        </div>

        @if($orders->count() > 0)
        <div class="divide-y divide-neutral-100">
            @foreach($orders as $order)
            <div class="px-6 py-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-sm">
                            Order #{{ $order->id }}
                            <span class="text-neutral-400 font-normal">&middot; {{ $order->user->name }}</span>
                        </p>
                        <p class="text-xs text-neutral-400">
                            {{ $order->created_at->format('d M Y H:i') }}
                            &middot; Pengambilan: <strong class="text-neutral-600">{{ $order->waktu_pengambilan }}</strong>
                            &middot; Bayar: <span class="{{ $order->payment_method === 'saldo' ? 'text-emerald-600' : 'text-amber-600' }} font-bold">{{ $order->payment_method === 'saldo' ? 'Saldo' : 'Cash' }}</span>
                        </p>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-neutral-100 text-neutral-500 px-2.5 py-1 rounded-full">Selesai</span>
                </div>
                <ul class="text-xs text-neutral-500 ml-1 space-y-0.5 mb-2">
                    @foreach($order->orderDetails as $detail)
                    <li class="flex justify-between">
                        <span>{{ $detail->menu->nama_menu ?? '-' }} &times; {{ $detail->quantity }}</span>
                        <span class="font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="text-right">
                    <span class="text-xs font-black">Total: Rp {{ number_format($order->orderDetails->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $orders->links() }}
        </div>
        @else
        <div class="px-6 py-16 text-center text-neutral-400 text-sm">
            Belum ada pesanan selesai dari toko ini.
        </div>
        @endif
    </div>

</div>
@endsection
