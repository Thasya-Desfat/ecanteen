@extends('layouts.app')

@section('title', 'Pesanan Saya - E-Canteen')
@section('page-title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-black">Pesanan Saya</h1>
    </div>

    @if($orders->count() > 0)
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-3xl border border-neutral-100 p-6 hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold">Order #{{ $order->id }}</h3>
                    <p class="text-xs text-neutral-400 mt-0.5">{{ $order->created_at->format('d M Y H:i') }}</p>
                    <div class="flex flex-wrap gap-3 mt-2">
                        <span class="text-[10px] font-bold text-neutral-500">
                            ⏰ {{ $order->waktu_pengambilan }}
                        </span>
                        <span class="text-[10px] font-bold {{ $order->payment_method === 'saldo' ? 'text-emerald-600' : 'text-amber-600' }}">
                            💳 {{ $order->payment_method === 'saldo' ? 'Saldo E-Canteen' : 'Cash' }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                        @if($order->status == 'menunggu_pembayaran') bg-orange-50 text-orange-600
                        @elseif($order->status == 'pending') bg-amber-50 text-amber-600
                        @elseif($order->status == 'diproses') bg-blue-50 text-blue-600
                        @elseif($order->status == 'siap') bg-emerald-50 text-emerald-600
                        @else bg-neutral-100 text-neutral-500
                        @endif">
                        {{ $order->status === 'menunggu_pembayaran' ? 'Menunggu Bayar' : ucfirst($order->status) }}
                    </span>
                    <p class="text-lg font-black text-yellow-600 mt-2">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="border-t border-neutral-100 pt-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Detail Pesanan</p>
                <ul class="space-y-1.5">
                    @foreach($order->orderDetails as $detail)
                    <li class="flex justify-between text-xs text-neutral-600">
                        <span>{{ $detail->menu->nama_menu }} <span class="text-neutral-400">({{ $detail->menu->toko->nama_toko }})</span> &times;{{ $detail->quantity }}</span>
                        <span class="font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                </ul>
                @if($order->status === 'menunggu_pembayaran')
                <a href="{{ route('orders.payment', $order) }}" class="inline-flex items-center gap-2 mt-4 bg-black hover:bg-neutral-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-black/10">
                    Selesaikan Pembayaran &rarr;
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <p class="text-neutral-400 font-medium text-sm mb-4">Belum ada pesanan.</p>
        <a href="{{ route('menus.index') }}" class="inline-block bg-black hover:bg-neutral-800 text-white text-xs font-bold py-2.5 px-5 rounded-xl transition-all">
            Lihat Menu
        </a>
    </div>
    @endif
</div>
@endsection
