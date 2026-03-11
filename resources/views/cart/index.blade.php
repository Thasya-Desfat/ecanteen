@extends('layouts.app')

@section('title', 'Keranjang Belanja - E-Canteen')
@section('page-title', 'Keranjang Belanja')

@section('content')
<div class="max-w-5xl mx-auto px-6 pb-20">

    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('menus.index') }}" class="inline-flex items-center gap-1 text-xs text-neutral-400 hover:text-black font-bold mb-1.5 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Lanjut Belanja
            </a>
            <h1 class="text-2xl font-black tracking-tight">Keranjang Belanja</h1>
        </div>
        <button id="clear-all-btn" onclick="clearAllCart()"
            class="hidden text-xs text-red-500 hover:text-white hover:bg-red-500 border border-red-200 hover:border-red-500 px-4 py-2 rounded-2xl transition-all font-bold">
            Kosongkan Semua
        </button>
    </div>

    {{-- Two-column layout --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- LEFT: Cart Items --}}
        <div class="flex-1 min-w-0">

            {{-- Empty state --}}
            <div id="empty-state" class="hidden bg-white rounded-3xl border border-neutral-100 p-16 text-center">
                <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="font-black text-neutral-400 text-sm">Keranjang belanja kosong</p>
                <p class="text-neutral-300 text-xs mt-1">Yuk pilih menu dari toko-toko yang tersedia!</p>
                <a href="{{ route('menus.index') }}" class="mt-5 inline-block bg-black hover:bg-neutral-800 text-white text-xs font-bold px-6 py-2.5 rounded-2xl transition-all">
                    Mulai Belanja
                </a>
            </div>

            {{-- Cart items grouped by toko --}}
            <div id="cart-content" class="space-y-4"></div>

        </div>

        {{-- RIGHT: Summary sticky --}}
        <div class="lg:w-80 w-full lg:sticky lg:top-6">
            <div id="checkout-section" class="hidden bg-white rounded-3xl border border-neutral-100 overflow-hidden">

                {{-- Total block --}}
                <div class="p-6 border-b border-neutral-50">
                    <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-3">Ringkasan Pesanan</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-neutral-600">Total</span>
                        <span id="grand-total" class="text-2xl font-black text-yellow-500"></span>
                    </div>
                    <p class="text-[11px] text-neutral-400 mt-2 leading-relaxed">Pilih waktu pengambilan &amp; metode pembayaran di halaman berikutnya.</p>
                </div>

                {{-- Item summary count --}}
                <div class="px-6 py-3 border-b border-neutral-50">
                    <div class="flex justify-between text-xs text-neutral-500">
                        <span>Jumlah item</span>
                        <span id="item-count" class="font-bold text-neutral-700"></span>
                    </div>
                </div>

                {{-- Checkout button --}}
                <div class="p-6">
                    <button onclick="checkout()"
                        class="w-full bg-black hover:bg-neutral-800 text-white font-black py-3.5 rounded-2xl text-sm transition-all active:scale-[0.98]">
                        Lanjut ke Pembayaran →
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const CART_KEY      = 'ecanteen_cart';
const CHECKOUT_URL  = '{{ route("checkout.prepare") }}';
const CSRF          = '{{ csrf_token() }}';

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || '{}');
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

function render() {
    const cart = getCart();
    const keys = Object.keys(cart);
    const emptyState      = document.getElementById('empty-state');
    const cartContent     = document.getElementById('cart-content');
    const checkoutSection = document.getElementById('checkout-section');
    const clearBtn        = document.getElementById('clear-all-btn');

    if (keys.length === 0) {
        emptyState.classList.remove('hidden');
        cartContent.innerHTML = '';
        checkoutSection.classList.add('hidden');
        clearBtn.classList.add('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    checkoutSection.classList.remove('hidden');
    clearBtn.classList.remove('hidden');

    // Group by toko
    const byToko = {};
    let grandTotal = 0;
    let totalItems = 0;
    for (let id in cart) {
        const item = cart[id];
        if (!byToko[item.tokoId]) byToko[item.tokoId] = { name: item.tokoName, items: {} };
        byToko[item.tokoId].items[id] = item;
        grandTotal += item.harga * item.qty;
        totalItems += item.qty;
    }

    let html = '';
    for (let tid in byToko) {
        const toko = byToko[tid];
        html += `
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-yellow-400 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="font-black text-sm">${toko.name}</span>
                </div>
                <button onclick="clearTokoFromCart(${tid})"
                    class="text-[11px] font-bold text-neutral-400 hover:text-red-500 transition-colors">
                    Hapus
                </button>
            </div>
            <div class="divide-y divide-neutral-50">`;

        for (let id in toko.items) {
            const item = toko.items[id];
            const sub  = item.harga * item.qty;
            html += `
                <div class="flex items-center gap-4 px-5 py-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm truncate">${item.name}</p>
                        <p class="text-xs text-neutral-400 mt-0.5">Rp ${item.harga.toLocaleString('id-ID')} / item</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button onclick="decreaseQty(${id})"
                            class="w-8 h-8 flex items-center justify-center bg-neutral-100 hover:bg-neutral-200 text-neutral-600 font-black text-lg rounded-xl transition-all active:scale-95">
                            &minus;
                        </button>
                        <span class="w-7 text-center font-black text-sm">${item.qty}</span>
                        <button onclick="increaseQty(${id})"
                            class="w-8 h-8 flex items-center justify-center bg-yellow-400 hover:bg-yellow-300 text-black font-black text-lg rounded-xl transition-all active:scale-95">
                            +
                        </button>
                    </div>
                    <div class="text-right flex-shrink-0 w-24">
                        <span class="font-black text-sm">Rp ${sub.toLocaleString('id-ID')}</span>
                    </div>
                </div>`;
        }

        html += `</div></div>`;
    }

    cartContent.innerHTML = html;
    document.getElementById('grand-total').textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
    document.getElementById('item-count').textContent  = `${totalItems} item`;
}

function increaseQty(menuId) {
    const cart = getCart();
    if (!cart[menuId]) return;
    cart[menuId].qty += 1;
    saveCart(cart);
    render();
}

function decreaseQty(menuId) {
    const cart = getCart();
    if (!cart[menuId]) return;
    cart[menuId].qty -= 1;
    if (cart[menuId].qty <= 0) delete cart[menuId];
    saveCart(cart);
    render();
}

function clearTokoFromCart(tokoId) {
    if (!confirm('Hapus semua item dari toko ini?')) return;
    const cart = getCart();
    for (let id in cart) {
        if (cart[id].tokoId == tokoId) delete cart[id];
    }
    saveCart(cart);
    render();
    updateNavBadge();
}

function clearAllCart() {
    if (!confirm('Kosongkan semua keranjang?')) return;
    localStorage.removeItem(CART_KEY);
    render();
    updateNavBadge();
}

function checkout() {
    const cart = getCart();
    if (Object.keys(cart).length === 0) { alert('Keranjang kosong!'); return; }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = CHECKOUT_URL;

    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = CSRF;
    form.appendChild(csrf);

    let i = 0;
    for (let id in cart) {
        const m = document.createElement('input');
        m.type = 'hidden'; m.name = `items[${i}][menu_id]`; m.value = id;
        form.appendChild(m);
        const q = document.createElement('input');
        q.type = 'hidden'; q.name = `items[${i}][quantity]`; q.value = cart[id].qty;
        form.appendChild(q);
        i++;
    }

    document.body.appendChild(form);
    form.submit();
}

// Update nav badge on the cart icon
function updateNavBadge() {
    const badge = document.getElementById('nav-cart-badge');
    if (!badge) return;
    const cart = getCart();
    const total = Object.values(cart).reduce((s, v) => s + v.qty, 0);
    if (total > 0) {
        badge.textContent = total;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    render();
    updateNavBadge();
});
</script>
@endsection
