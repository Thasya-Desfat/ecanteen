@extends('layouts.app')

@section('title', 'Checkout - E-Canteen')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Kembali</a>
        <h1 class="font-black text-2xl mt-1">Detail Pembayaran</h1>
    </div>

    {{-- Order Summary --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-5">
        <div class="bg-yellow-50 border-b border-yellow-100 px-5 py-3">
            <span class="text-[9px] font-black uppercase tracking-widest text-yellow-700">Ringkasan Pesanan</span>
        </div>
        <div class="divide-y divide-neutral-100">
            @php
                $byToko = [];
                foreach ($items as $item) {
                    $byToko[$item['toko_name']][] = $item;
                }
            @endphp
            @foreach($byToko as $tokoName => $tokoItems)
            <div class="px-5 pt-3 pb-2">
                <p class="text-[9px] font-black text-neutral-400 uppercase tracking-widest mb-2">{{ $tokoName }}</p>
                @foreach($tokoItems as $item)
                <div class="flex justify-between items-center py-1.5">
                    <div>
                        <p class="font-bold text-sm">{{ $item['name'] }}</p>
                        <p class="text-xs text-neutral-400">{{ $item['qty'] }} × Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                    </div>
                    <span class="font-bold text-sm">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        <div class="bg-neutral-50 border-t border-neutral-100 px-5 py-4 flex justify-between items-center">
            <span class="font-black text-sm">Total</span>
            <span class="text-xl font-black text-yellow-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Checkout Form --}}
    <form action="{{ route('orders.place') }}" method="POST" id="checkout-form">
        @csrf

        @foreach($items as $i => $item)
            <input type="hidden" name="items[{{ $i }}][menu_id]"  value="{{ $item['menu_id'] }}">
            <input type="hidden" name="items[{{ $i }}][quantity]" value="{{ $item['qty'] }}">
        @endforeach

        {{-- Waktu Pengambilan --}}
        <div class="bg-white rounded-3xl border border-neutral-100 p-5 mb-5">
            <label class="block font-black text-sm mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 text-yellow-500 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Waktu Pengambilan
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="waktu-option cursor-pointer">
                    <input type="radio" name="waktu_pengambilan" value="Istirahat 1" class="sr-only" required>
                    <div class="border-2 border-neutral-200 rounded-2xl p-4 text-center hover:border-yellow-400 transition-colors">
                        <p class="font-black text-sm">Istirahat 1</p>
                        <p class="text-xs text-neutral-400 mt-1">09:30 – 10:00</p>
                    </div>
                </label>
                <label class="waktu-option cursor-pointer">
                    <input type="radio" name="waktu_pengambilan" value="Istirahat 2" class="sr-only" required>
                    <div class="border-2 border-neutral-200 rounded-2xl p-4 text-center hover:border-yellow-400 transition-colors">
                        <p class="font-black text-sm">Istirahat 2</p>
                        <p class="text-xs text-neutral-400 mt-1">12:00 – 12:30</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="bg-white rounded-3xl border border-neutral-100 p-5 mb-6">
            <label class="block font-black text-sm mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 text-yellow-500 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Metode Pembayaran
            </label>

            <div class="space-y-3">

                {{-- Saldo --}}
                <label class="pay-option cursor-pointer block">
                    <input type="radio" name="payment_method" value="saldo" class="sr-only" required>
                    <div class="flex items-center justify-between border-2 border-neutral-200 rounded-2xl px-4 py-3.5 hover:border-yellow-400 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Saldo E-Canteen</p>
                                <p class="text-xs text-neutral-400">Saldo: <span class="{{ $user->saldo >= $total ? 'text-emerald-600' : 'text-red-500' }} font-bold">Rp {{ number_format($user->saldo, 0, ',', '.') }}</span></p>
                            </div>
                        </div>
                        @if($user->saldo < $total)
                        <span class="text-[9px] font-black uppercase tracking-widest text-red-500 bg-red-50 px-2 py-1 rounded-full">Tidak Cukup</span>
                        @endif
                    </div>
                </label>

                {{-- Cash --}}
                <label class="pay-option cursor-pointer block">
                    <input type="radio" name="payment_method" value="cash" class="sr-only" required>
                    <div class="flex items-center space-x-3 border-2 border-neutral-200 rounded-2xl px-4 py-3.5 hover:border-yellow-400 transition-colors">
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-sm">Cash</p>
                            <p class="text-xs text-neutral-400">Bayar tunai di kasir toko</p>
                        </div>
                    </div>
                </label>

            </div>
        </div>

        {{-- Catatan (opsional) --}}
        <div class="bg-white rounded-3xl border border-neutral-100 p-5 mb-6">
            <label for="catatan" class="block font-black text-sm mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 text-yellow-500 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Catatan
                <span class="text-neutral-400 font-medium text-xs ml-1">(opsional)</span>
            </label>
            <textarea id="catatan" name="catatan" rows="3" maxlength="300"
                placeholder="Contoh: tidak pakai pedas, tambah nasi, dll."
                class="w-full border border-neutral-200 rounded-2xl px-4 py-3 text-sm bg-neutral-50 focus:outline-none focus:border-yellow-400 resize-none font-medium placeholder:text-neutral-300 transition-colors">{{ old('catatan') }}</textarea>
            <p id="catatan-counter" class="text-[10px] text-neutral-300 text-right mt-1">0 / 300</p>
        </div>

        <button type="submit" id="pay-btn"
            class="w-full bg-black hover:bg-neutral-800 text-white font-black py-4 rounded-2xl text-base shadow-lg shadow-black/10 transition-all">
            Bayar Sekarang
        </button>
    </form>

</div>

<style>
.pay-option input:checked + div,
.waktu-option input:checked + div {
    border-color: #facc15;
    background-color: #fefce8;
}
</style>

<script>
document.querySelectorAll('.pay-option input, .waktu-option input').forEach(function(radio) {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.pay-option div').forEach(function(d) {
            d.classList.remove('border-yellow-400', 'bg-yellow-50');
        });
        document.querySelectorAll('.waktu-option div').forEach(function(d) {
            d.classList.remove('border-yellow-400', 'bg-yellow-50');
        });
    });
});

// Clear cart from localStorage after successful checkout form submit
document.getElementById('checkout-form').addEventListener('submit', function () {
    localStorage.removeItem('ecanteen_cart');
    const badge = document.getElementById('nav-cart-badge');
    if (badge) { badge.textContent = ''; badge.classList.add('hidden'); }
});

// Catatan character counter
const catatanEl  = document.getElementById('catatan');
const counterEl  = document.getElementById('catatan-counter');
function updateCounter() {
    const len = catatanEl.value.length;
    counterEl.textContent = len + ' / 300';
    counterEl.classList.toggle('text-yellow-500', len > 250);
    counterEl.classList.toggle('text-neutral-300', len <= 250);
}
catatanEl.addEventListener('input', updateCounter);
updateCounter();
</script>
@endsection
