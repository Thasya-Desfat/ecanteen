@extends('layouts.app')

@section('title', 'Pilih Toko - E-Canteen')
@section('page-title', 'Pilih Menu')

@section('content')

<div class="max-w-7xl mx-auto px-6 pb-28">
    {{-- Hero Banner --}}
    <section class="mb-8 bg-yellow-400 rounded-3xl p-8 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-black mb-1">Lapar? Pesan Sekarang! 😋</h2>
            <p class="text-black/60 text-sm font-medium">Pilih menu dari toko kantin favoritmu, bayar dengan saldo, dan ambil saat jam istirahat.</p>
        </div>
        <div class="absolute -right-4 -bottom-4 text-8xl opacity-10 select-none">🍜</div>
    </section>

    {{-- Search Bar --}}
    <div class="mb-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                </svg>
            </div>
            <input
                id="search-input"
                type="text"
                placeholder="Cari menu, toko, atau kategori..."
                class="w-full bg-white border border-neutral-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm font-medium placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                oninput="filterMenus(this.value)"
            >
            <button id="search-clear" onclick="clearSearch()" class="hidden absolute inset-y-0 right-4 flex items-center text-neutral-400 hover:text-neutral-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Category Filter Pills --}}
    <div class="flex flex-wrap gap-2 mb-8" id="kategori-pills">
        @php
        $kategoriList = ['Makanan Berat', 'Makanan Ringan', 'Lauk', 'Minuman', 'Dessert', 'Lainnya'];
        @endphp
        <button
            onclick="filterByKategori(null, this)"
            class="kategori-pill px-4 py-1.5 rounded-full text-xs font-bold border-2 border-black bg-black text-white transition-all"
            data-active="true">
            Semua
        </button>
        @foreach($kategoriList as $kat)
        <button
            onclick="filterByKategori('{{ $kat }}', this)"
            class="kategori-pill px-4 py-1.5 rounded-full text-xs font-bold border-2 border-neutral-200 text-neutral-500 hover:border-yellow-400 hover:text-yellow-700 bg-white transition-all">
            {{ $kat }}
        </button>
        @endforeach
    </div>

    {{-- Search Results --}}
    <div id="search-results-section" class="hidden mb-10">
        <h2 class="text-base font-black flex items-center gap-2 mb-5">
            <span>🔍</span> Hasil Pencarian
        </h2>
        <div id="grid-search-results" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"></div>
        <div id="no-menu-results" class="hidden bg-white rounded-3xl border border-neutral-100 p-10 text-center">
            <p class="text-neutral-400 font-medium text-sm">Tidak ada menu yang cocok dengan pencarian.</p>
        </div>
    </div>

    {{-- Menu Terlaris --}}
    @if($menuLaris->count() > 0)
    <div class="mb-10">
        <h2 class="text-base font-black flex items-center gap-2 mb-5">
            <span class="text-orange-500">🔥</span> Menu Terlaris
        </h2>
        <div id="grid-laris" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($menuLaris as $i => $item)
            <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col" data-search="{{ strtolower($item->menu->nama_menu ?? '') }} {{ strtolower($item->menu->toko->nama_toko ?? '') }}">
                @if($item->menu->foto ?? false)
                <img src="{{ asset('storage/' . $item->menu->foto) }}" alt="{{ $item->menu->nama_menu }}" class="w-full h-44 object-cover">
                @else
                <div class="w-full h-44 bg-neutral-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                @endif
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">{{ $item->menu->toko->nama_toko ?? '-' }}</p>
                            <span class="text-[10px] font-black text-amber-600 bg-amber-50 rounded-full px-2 py-0.5">🔥 {{ $item->total_terjual }}x terjual</span>
                        </div>
                        <h3 class="text-base font-bold">{{ $item->menu->nama_menu ?? '-' }}</h3>
                        <p class="text-lg font-black text-yellow-600 mt-1">Rp {{ number_format($item->menu->harga ?? 0, 0, ',', '.') }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">{{ $item->menu->kategori ?? 'Lainnya' }}</span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button
                            id="cart-btn-laris-{{ $item->menu_id }}"
                            onclick="addLarisToCart(event, {{ $item->menu_id }}, '{{ addslashes($item->menu->nama_menu) }}', {{ $item->menu->harga }}, {{ $item->menu->toko->id }}, '{{ addslashes($item->menu->toko->nama_toko) }}')"
                            class="flex-1 flex items-center justify-center gap-1.5 border-2 border-neutral-200 text-neutral-600 hover:border-yellow-400 hover:text-yellow-700 font-semibold py-2.5 rounded-xl text-xs transition-all active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span id="cart-label-laris-{{ $item->menu_id }}">Keranjang</span>
                        </button>
                        <a href="{{ route('checkout.show', ['menu_id' => $item->menu_id]) }}"
                            class="flex-1 flex items-center justify-center gap-1.5 bg-black hover:bg-neutral-800 text-white font-bold py-2.5 rounded-xl text-xs transition-all active:scale-95 shadow-lg shadow-black/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Beli
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Toko Cards --}}
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h1 class="text-xl font-black">Pilih Toko</h1>
            <p class="text-xs text-neutral-400 mt-1 font-medium">Klik toko untuk melihat menu yang tersedia</p>
        </div>
    </div>
    {{-- No toko results message --}}
    <div id="no-toko-results" class="hidden bg-white rounded-3xl border border-neutral-100 p-10 text-center mb-4">
        <p class="text-neutral-400 font-medium text-sm">Tidak ada toko yang cocok dengan pencarian.</p>
    </div>
    @if($tokos->count() > 0)
    <div id="grid-tokos" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tokos as $toko)
        <a href="{{ route('menus.toko', $toko) }}" class="group block bg-white rounded-3xl border border-neutral-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-200" data-search="{{ strtolower($toko->nama_toko) }}">
            <div class="bg-yellow-400 h-32 flex items-center justify-center relative overflow-hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-black/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="p-5">
                <h2 class="font-black text-base group-hover:text-yellow-600 transition-colors">{{ $toko->nama_toko }}</h2>
                <p class="mt-1 text-xs text-neutral-400 font-medium">{{ $toko->menus_count }} menu tersedia</p>
                <div class="flex items-center justify-between mt-4">
                    <span class="text-xs font-bold text-neutral-500">Lihat Menu</span>
                    <div class="w-8 h-8 rounded-full bg-neutral-50 flex items-center justify-center group-hover:bg-yellow-400 group-hover:text-black transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
            </svg>
        </div>
        <p class="text-neutral-400 font-medium text-sm">Belum ada toko yang tersedia.</p>
    </div>
    @endif
</div>

{{-- Checkout Modal --}}
<div id="checkout-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white w-full max-w-lg rounded-t-3xl sm:rounded-3xl p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-black">Konfirmasi Pesanan</h2>
            <button onclick="closeCheckoutModal()" class="w-8 h-8 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 hover:bg-neutral-200 transition-colors text-lg leading-none">&times;</button>
        </div>

        <div id="modal-cart-items" class="space-y-1 text-sm max-h-64 overflow-y-auto mb-4 no-scrollbar"></div>

        <div class="flex justify-between items-center py-3 border-t border-neutral-100 mb-5">
            <span class="font-bold text-sm">Total Pembayaran</span>
            <span id="modal-total" class="text-xl font-black text-yellow-600"></span>
        </div>

        <div class="mb-5">
            <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Waktu Pengambilan</label>
            <select id="modal-waktu" class="w-full border border-neutral-200 rounded-xl py-3 px-4 text-sm bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400">
                <option value="">-- Pilih Waktu --</option>
                <option value="Istirahat 1">Istirahat 1</option>
                <option value="Istirahat 2">Istirahat 2</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button onclick="clearAndClose()" class="flex-1 border-2 border-red-200 text-red-500 hover:bg-red-50 font-bold py-3 rounded-2xl text-sm transition-all">
                Kosongkan
            </button>
            <button onclick="checkoutFromModal()" class="flex-[2] bg-black hover:bg-neutral-800 text-white font-bold py-3 rounded-2xl text-sm transition-all shadow-lg shadow-black/10">
                Pesan Sekarang
            </button>
        </div>
    </div>
</div>

<script>
const CART_KEY = 'ecanteen_cart';

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || '{}');
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateNavBadge();
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

function addLarisToCart(event, menuId, menuName, harga, tokoId, tokoName) {
    const cart = getCart();
    // Guard: only one toko per cart
    const existing = Object.values(cart);
    if (existing.length > 0 && existing[0].tokoId !== tokoId) {
        if (!confirm(`Keranjangmu sudah berisi item dari "${existing[0].tokoName}". Hapus keranjang dan ganti ke toko ini?`)) return;
        localStorage.removeItem(CART_KEY);
    }
    const freshCart = getCart();
    const cur  = freshCart[menuId] ? freshCart[menuId].qty : 0;
    freshCart[menuId] = { name: menuName, harga: harga, qty: cur + 1, tokoId: tokoId, tokoName: tokoName };
    saveCart(freshCart);
    const lbl = document.getElementById('cart-label-laris-' + menuId);
    if (lbl) lbl.textContent = 'Keranjang (' + freshCart[menuId].qty + ')';
    flyToCart(event.currentTarget, null);
}

function openCheckoutModal() {
    const cart = getCart();
    if (Object.keys(cart).length === 0) return;

    // Group by toko
    const byToko = {};
    let total = 0;
    for (let id in cart) {
        const item = cart[id];
        if (!byToko[item.tokoId]) byToko[item.tokoId] = { name: item.tokoName, items: [] };
        byToko[item.tokoId].items.push(item);
        total += item.harga * item.qty;
    }

    let html = '';
    for (let tid in byToko) {
        html += `<div class="font-semibold text-gray-700 mt-2 mb-1 text-xs uppercase tracking-wide">${byToko[tid].name}</div>`;
        byToko[tid].items.forEach(item => {
            const sub = item.harga * item.qty;
            html += `<div class="flex justify-between text-gray-600"><span>${item.name} &times;${item.qty}</span><span>Rp ${sub.toLocaleString('id-ID')}</span></div>`;
        });
    }

    document.getElementById('modal-cart-items').innerHTML = html;
    document.getElementById('modal-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
    document.getElementById('checkout-modal').classList.remove('hidden');
}

function closeCheckoutModal() {
    document.getElementById('checkout-modal').classList.add('hidden');
}

function clearAndClose() {
    if (!confirm('Kosongkan semua keranjang?')) return;
    localStorage.removeItem(CART_KEY);
    closeCheckoutModal();
    updateNavBadge();
}

function checkoutFromModal() {
    const waktu = document.getElementById('modal-waktu').value;
    if (!waktu) { alert('Silakan pilih waktu pengambilan!'); return; }

    const cart = getCart();
    if (Object.keys(cart).length === 0) { alert('Keranjang kosong!'); return; }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("orders.checkout") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const waktuInput = document.createElement('input');
    waktuInput.type = 'hidden'; waktuInput.name = 'waktu_pengambilan'; waktuInput.value = waktu;
    form.appendChild(waktuInput);

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
    localStorage.removeItem(CART_KEY);
    updateNavBadge();
    form.submit();
}

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

const ALL_MENUS = @json($allMenusData);

// Run on load
document.addEventListener('DOMContentLoaded', function () {
    updateNavBadge();
});

let activeKategori = null;

function filterByKategori(kat, btn) {
    activeKategori = kat;
    // Update pill styles
    document.querySelectorAll('.kategori-pill').forEach(p => {
        p.classList.remove('bg-black', 'text-white', 'border-black');
        p.classList.add('border-neutral-200', 'text-neutral-500', 'bg-white');
    });
    btn.classList.remove('border-neutral-200', 'text-neutral-500', 'bg-white');
    btn.classList.add('bg-black', 'text-white', 'border-black');
    // Re-run filter with current search text
    const q = (document.getElementById('search-input').value || '').trim();
    filterMenus(q);
}

function filterMenus(query) {
    const q = query.trim().toLowerCase();
    const clearBtn = document.getElementById('search-clear');
    if (clearBtn) clearBtn.classList.toggle('hidden', q === '' && !activeKategori);

    const searchSection  = document.getElementById('search-results-section');
    const larisSection   = document.getElementById('grid-laris')  ? document.getElementById('grid-laris').closest('.mb-10')  : null;
    const tokoHeader     = document.querySelector('.mb-6.flex.items-end.justify-between');
    const gridTokos      = document.getElementById('grid-tokos');
    const noTokoResults  = document.getElementById('no-toko-results');

    if (q === '' && !activeKategori) {
        // Reset: hide search results, show normal sections
        if (searchSection) searchSection.classList.add('hidden');
        if (larisSection)  larisSection.style.display  = '';
        if (tokoHeader)    tokoHeader.style.display     = '';
        if (gridTokos)     gridTokos.style.display      = '';
        if (noTokoResults) noTokoResults.classList.add('hidden');
        return;
    }

    // Hide normal sections, show search results
    if (larisSection)  larisSection.style.display  = 'none';
    if (tokoHeader)    tokoHeader.style.display     = 'none';
    if (gridTokos)     gridTokos.style.display      = 'none';
    if (noTokoResults) noTokoResults.classList.add('hidden');
    if (searchSection) searchSection.classList.remove('hidden');

    // Filter all menus by search query AND active kategori
    const matches = ALL_MENUS.filter(m => {
        const matchesQ   = q === '' || m.search.includes(q);
        const matchesKat = !activeKategori || m.kategori === activeKategori;
        return matchesQ && matchesKat;
    });
    const grid    = document.getElementById('grid-search-results');
    const noRes   = document.getElementById('no-menu-results');

    if (matches.length === 0) {
        grid.innerHTML = '';
        if (noRes) noRes.classList.remove('hidden');
        return;
    }
    if (noRes) noRes.classList.add('hidden');

    grid.innerHTML = matches.map(function(m) {
        const fotoHtml = m.foto
            ? `<img src="/storage/${m.foto}" alt="${escHtml(m.name)}" class="w-full h-44 object-cover">`
            : `<div class="w-full h-44 bg-neutral-100 flex items-center justify-center">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                 </svg>
               </div>`;
        return `<div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col">
            ${fotoHtml}
            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide mb-1">${escHtml(m.toko_name)}</p>
                    <h3 class="text-base font-bold">${escHtml(m.name)}</h3>
                    <p class="text-lg font-black text-yellow-600 mt-1">Rp ${m.harga.toLocaleString('id-ID')}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">${escHtml(m.kategori)}</span>
                </div>
                <div class="mt-4 flex gap-2">
                    <button
                        id="cart-btn-search-${m.id}"
                        onclick="addSearchToCart(event, ${m.id}, '${escJs(m.name)}', ${m.harga}, ${m.toko_id}, '${escJs(m.toko_name)}')"
                        class="flex-1 flex items-center justify-center gap-1.5 border-2 border-neutral-200 text-neutral-600 hover:border-yellow-400 hover:text-yellow-700 font-semibold py-2.5 rounded-xl text-xs transition-all active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span id="cart-label-search-${m.id}">Keranjang</span>
                    </button>
                    <a href="/checkout?menu_id=${m.id}"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-black hover:bg-neutral-800 text-white font-bold py-2.5 rounded-xl text-xs transition-all active:scale-95 shadow-lg shadow-black/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Beli
                    </a>
                </div>
            </div>
        </div>`;
    }).join('');

    // Sync cart labels
    const cart = getCart();
    matches.forEach(function(m) {
        if (cart[m.id]) {
            const lbl = document.getElementById('cart-label-search-' + m.id);
            if (lbl) lbl.textContent = 'Keranjang (' + cart[m.id].qty + ')';
        }
    });
}

function addSearchToCart(event, menuId, menuName, harga, tokoId, tokoName) {
    const cart = getCart();
    // Guard: only one toko per cart
    const existing = Object.values(cart);
    if (existing.length > 0 && existing[0].tokoId !== tokoId) {
        if (!confirm(`Keranjangmu sudah berisi item dari "${existing[0].tokoName}". Hapus keranjang dan ganti ke toko ini?`)) return;
        localStorage.removeItem(CART_KEY);
    }
    const freshCart = getCart();
    const cur  = freshCart[menuId] ? freshCart[menuId].qty : 0;
    freshCart[menuId] = { name: menuName, harga: harga, qty: cur + 1, tokoId: tokoId, tokoName: tokoName };
    saveCart(freshCart);
    const lbl = document.getElementById('cart-label-search-' + menuId);
    if (lbl) lbl.textContent = 'Keranjang (' + freshCart[menuId].qty + ')';
    flyToCart(event.currentTarget, null);
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escJs(str) {
    return String(str).replace(/\\/g,'\\\\').replace(/'/g,"\\'");
}

function clearSearch() {
    const input = document.getElementById('search-input');
    if (input) { input.value = ''; }
    activeKategori = null;
    // Reset all pills to default
    document.querySelectorAll('.kategori-pill').forEach((p, i) => {
        if (i === 0) {
            p.classList.add('bg-black', 'text-white', 'border-black');
            p.classList.remove('border-neutral-200', 'text-neutral-500', 'bg-white');
        } else {
            p.classList.remove('bg-black', 'text-white', 'border-black');
            p.classList.add('border-neutral-200', 'text-neutral-500', 'bg-white');
        }
    });
    filterMenus('');
    if (input) input.focus();
}
</script>
@endsection
