@extends('layouts.app')

@section('title', 'Riwayat Pesanan - E-Canteen')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Pesanan Aktif</a>
        <h1 class="font-black text-2xl mt-1">Riwayat Pesanan</h1>
    </div>

    @if($orders->count() > 0)
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-3xl border border-neutral-100 p-5">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-black text-base">Order #{{ $order->id }}</h3>
                    <p class="text-xs text-neutral-400">{{ $order->created_at->format('d M Y H:i') }}</p>
                    <p class="text-xs text-neutral-500 mt-0.5">Pengambilan: <span class="font-bold text-neutral-700">{{ $order->waktu_pengambilan }}</span></p>
                    <p class="text-xs text-neutral-500">Bayar:
                        @if($order->payment_method === 'saldo')
                            <span class="font-bold text-emerald-600">Saldo E-Canteen</span>
                        @else
                            <span class="font-bold text-amber-600">Cash</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700">Selesai</span>
                    <p class="font-black text-lg text-yellow-600 mt-1">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="border-t border-neutral-100 pt-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Detail Pesanan</p>
                <ul class="space-y-1">
                    @foreach($order->orderDetails as $detail)
                    <li class="flex justify-between text-xs">
                        <span class="text-neutral-600">{{ $detail->menu->nama_menu ?? '-' }} <span class="text-neutral-400">({{ $detail->menu->toko->nama_toko ?? '-' }})</span> ×{{ $detail->quantity }}</span>
                        <span class="font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-neutral-100 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-neutral-400 text-sm mb-4">Belum ada riwayat pesanan.</p>
        <a href="{{ route('menus.index') }}" class="inline-block bg-black hover:bg-neutral-800 text-white font-bold py-2.5 px-6 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
            Pesan Sekarang
        </a>
    </div>
    @endif
</div>
@endsection
