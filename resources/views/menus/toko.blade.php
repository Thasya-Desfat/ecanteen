@extends('layouts.app')

@section('title', $toko->nama_toko . ' - E-Canteen')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28">

    <div class="mb-8">
        <a href="{{ route('menus.index') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Kembali ke Daftar Toko</a>
        <h1 class="font-black text-2xl mt-1">{{ $toko->nama_toko }}</h1>
        <p class="text-xs text-neutral-400 mt-1">{{ $menus->count() }} menu tersedia</p>
    </div>

    @if($menus->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($menus as $menu)
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden hover:shadow-lg transition-shadow duration-200 flex flex-col">
            @if($menu->foto)
            <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-44 object-cover">
            @else
            <div class="w-full h-44 bg-neutral-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            @endif
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700 mb-1">{{ $menu->kategori }}</span>
                    <h3 class="font-black text-base">{{ $menu->nama_menu }}</h3>
                    <p class="font-black text-yellow-500 text-lg mt-0.5">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <button
                        id="cart-btn-{{ $menu->id }}"
                        onclick="addToCartAndGo(event, {{ $menu->id }}, '{{ addslashes($menu->nama_menu) }}', {{ $menu->harga }})"
                        class="flex-1 flex items-center justify-center gap-1.5 border-2 border-neutral-200 text-neutral-600 hover:border-yellow-400 hover:text-yellow-600 font-bold py-2.5 rounded-2xl text-sm transition active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span id="cart-label-{{ $menu->id }}">Keranjang</span>
                    </button>
                    <a href="{{ route('checkout.show', ['menu_id' => $menu->id]) }}"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-black hover:bg-neutral-800 text-white font-bold py-2.5 rounded-2xl text-sm transition active:scale-95 shadow-lg shadow-black/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Beli
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <p class="text-neutral-400 text-sm">Belum ada menu yang tersedia di toko ini.</p>
    </div>
    @endif

</div>


<script>
const TOKO_ID   = {{ $toko->id }};
const TOKO_NAME = '{{ addslashes($toko->nama_toko) }}';
const CART_KEY  = 'ecanteen_cart';

function getCart() { return JSON.parse(localStorage.getItem(CART_KEY) || '{}'); }
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateNavBadge(cart);
}

function updateNavBadge(cart) {
    const badge = document.getElementById('nav-cart-badge');
    if (!badge) return;
    const total = Object.values(cart).reduce((s,v) => s + v.qty, 0);
    badge.textContent = total;
    if (total > 0) badge.classList.remove('hidden');
    else           badge.classList.add('hidden');
}


function updateCartLabels(cart) {
    for (let id in cart) {
        const lbl = document.getElementById('cart-label-' + id);
        if (lbl) lbl.textContent = 'Keranjang (' + cart[id].qty + ')';
    }
}

function flyToCart(btn, callback) {
    const target = document.getElementById('floating-cart-btn');
    if (!target) { if (callback) callback(); return; }

    const btnRect    = btn.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();

    const startX = btnRect.left   + btnRect.width  / 2;
    const startY = btnRect.top    + btnRect.height / 2;
    const endX   = targetRect.left + targetRect.width  / 2;
    const endY   = targetRect.top  + targetRect.height / 2;

    const dot = document.createElement('div');
    dot.className = 'fly-dot';
    dot.style.left = (startX - 14) + 'px';
    dot.style.top  = (startY - 14) + 'px';
    dot.style.setProperty('--fly-x', (endX - startX) + 'px');
    dot.style.setProperty('--fly-y', (endY - startY) + 'px');
    dot.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:100%;height:100%;"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`;
    document.body.appendChild(dot);

    dot.addEventListener('animationend', function () {
        dot.remove();
        const badge = document.getElementById('nav-cart-badge');
        if (badge) {
            badge.classList.add('badge-pop');
            badge.addEventListener('animationend', () => badge.classList.remove('badge-pop'), { once: true });
        }
        target.classList.add('cart-bounce');
        target.addEventListener('animationend', () => target.classList.remove('cart-bounce'), { once: true });
        if (callback) callback();
    });
}

function addToCartAndGo(event, menuId, menuName, harga) {
    const cart = getCart();
    // Guard: only one toko per cart
    const existing = Object.values(cart);
    if (existing.length > 0 && existing[0].tokoId !== TOKO_ID) {
        if (!confirm(`Keranjangmu sudah berisi item dari "${existing[0].tokoName}". Hapus keranjang dan ganti ke toko ini?`)) return;
        localStorage.removeItem(CART_KEY);
    }
    const freshCart = getCart();
    const cur  = freshCart[menuId] ? freshCart[menuId].qty : 0;
    freshCart[menuId] = { name: menuName, harga: harga, qty: cur + 1, tokoId: TOKO_ID, tokoName: TOKO_NAME };
    saveCart(freshCart);
    updateCartLabels(freshCart);
    flyToCart(event.currentTarget, null);
}

document.addEventListener('DOMContentLoaded', function () {
    const cart = getCart();
    updateNavBadge(cart);
    updateCartLabels(cart);
});
</script>
@endsection
