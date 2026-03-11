@extends('layouts.app')

@section('title', 'Menunggu Pembayaran - E-Canteen')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 pb-10">

    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-3
            {{ $order->payment_method === 'cash' ? 'bg-amber-100' : 'bg-yellow-100' }}">
            @if($order->payment_method === 'cash')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            @else
            <span class="text-yellow-600 font-black text-xl">E</span>
            @endif
        </div>
        <h1 class="font-black text-xl">Menunggu Pembayaran</h1>
        <p class="text-xs text-neutral-400 mt-1">Order #{{ $order->id }} &middot; {{ $order->waktu_pengambilan }}</p>
    </div>

    {{-- Total --}}
    <div class="bg-white rounded-2xl border border-neutral-100 px-6 py-4 mb-4 flex justify-between items-center">
        <span class="font-bold text-sm">Total Pembayaran</span>
        <span class="text-xl font-black text-yellow-500">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
    </div>

    @if($order->payment_method === 'cash')
    {{-- Cash Payment --}}
    <div class="bg-white rounded-3xl border border-neutral-100 p-6 mb-4 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <p class="font-black text-base">Bayar Tunai di Kasir</p>
        <p class="text-xs text-neutral-400 mt-1 mb-4">Tunjukkan nomor order ini ke kasir:</p>
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl py-4 px-6 mb-4">
            <p class="text-3xl font-black text-yellow-600">#{{ $order->id }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-3 text-left text-xs text-amber-700">
            <p class="font-black mb-1">Cara bayar Cash:</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Datang ke kasir toko saat waktu pengambilan</li>
                <li>Tunjukkan <strong>nomor order #{{ $order->id }}</strong> ke kasir</li>
                <li>Bayar tunai sebesar <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong></li>
                <li>Klik <strong>"Saya Sudah Bayar"</strong> setelah membayar</li>
            </ol>
        </div>
    </div>
    @endif

    {{-- Order Items --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-5">
        <div class="bg-neutral-50 border-b border-neutral-100 px-5 py-3">
            <span class="text-[9px] font-black uppercase tracking-widest text-neutral-400">Item Pesanan</span>
        </div>
        <div class="divide-y divide-neutral-100">
            @foreach($order->orderDetails as $detail)
            <div class="flex justify-between items-center px-5 py-3">
                <div>
                    <p class="font-bold text-sm">{{ $detail->menu->nama_menu }}</p>
                    <p class="text-xs text-neutral-400">{{ $detail->quantity }} × Rp {{ number_format($detail->menu->harga, 0, ',', '.') }}</p>
                </div>
                <span class="font-bold text-sm">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Confirm & Cancel --}}
    <form action="{{ route('orders.confirm-payment', $order) }}" method="POST" class="mb-3">
        @csrf
        <button type="submit"
            class="w-full bg-black hover:bg-neutral-800 text-white font-black py-4 rounded-2xl text-base shadow-lg shadow-black/10 transition-all"
            onclick="return confirm('Konfirmasi bahwa kamu sudah melakukan pembayaran?')">
            ✓ Saya Sudah Bayar
        </button>
    </form>

    <a href="{{ route('orders.index') }}"
        class="block w-full text-center border border-neutral-200 text-neutral-600 hover:bg-neutral-50 font-bold text-sm py-3 rounded-2xl transition">
        Bayar Nanti
    </a>

    <p class="text-center text-[10px] text-neutral-300 font-medium mt-4">
        Pesanan akan dibatalkan otomatis jika tidak dibayar dalam 30 menit.
    </p>
</div>
@endsection
