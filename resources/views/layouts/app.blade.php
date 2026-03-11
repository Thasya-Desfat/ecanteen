<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Canteen Sekolah')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes cartBounce { 0%{transform:scale(1)} 35%{transform:scale(1.35)} 65%{transform:scale(0.9)} 100%{transform:scale(1)} }
        .cart-bounce { animation: cartBounce 0.35s ease forwards; }
        @keyframes badgePop { 0%{transform:scale(1)} 50%{transform:scale(1.7)} 100%{transform:scale(1)} }
        .badge-pop { animation: badgePop 0.3s ease; }
        @keyframes flyDot { 0%{transform:translate(0,0) scale(1);opacity:1} 100%{transform:translate(var(--fly-x),var(--fly-y)) scale(0.3);opacity:0} }
        .fly-dot { position:fixed;width:28px;height:28px;background:#facc15;border-radius:50%;padding:5px;box-shadow:0 2px 8px rgba(250,204,21,0.5);pointer-events:none;z-index:9999;animation:flyDot 0.55s cubic-bezier(.3,0,.6,1) forwards; }
    </style>
</head>
<body style="background-color:#FFFCF0;color:#1A1A1A;">

<div class="min-h-screen flex">

    {{-- ===== SIDEBAR (Desktop) ===== --}}
    @auth
    <nav class="hidden lg:flex flex-col w-64 bg-white border-r border-neutral-100 sticky top-0 h-screen overflow-y-auto no-scrollbar">
        {{-- Logo --}}
        <div class="px-6 py-6 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-400 rounded-2xl flex items-center justify-center shadow-lg shadow-yellow-400/30 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="font-black text-sm tracking-tight">E-Canteen</p>
                <p class="text-[9px] text-neutral-400 font-bold uppercase tracking-widest">Sekolah Digital</p>
            </div>
        </div>

        {{-- User Info --}}
        <div class="mx-4 mb-6 p-4 bg-neutral-50 rounded-2xl">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-700 font-black text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold truncate">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->isUser())
                    <p class="text-[10px] text-neutral-400 truncate">Saldo: <span class="text-emerald-600 font-bold">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</span></p>
                    @elseif(auth()->user()->isToko())
                    <p class="text-[10px] text-neutral-400 truncate">Pendapatan: <span class="text-emerald-600 font-bold">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</span></p>
                    @else
                    <p class="text-[10px] text-neutral-400">Administrator</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Nav Links --}}
        <div class="px-3 flex-grow">
            @php
                $__siapCount = (auth()->user()->isUser())
                    ? \App\Models\Order::where('user_id', auth()->id())->where('status', 'siap')->count()
                    : 0;
            @endphp
            @if(auth()->user()->isUser())
            <p class="text-[9px] text-neutral-400 font-bold uppercase tracking-widest px-3 mb-2">Menu</p>
            <a href="{{ route('menus.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('menus.*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Pilih Menu
            </a>
            <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('cart.*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Keranjang
                <span id="sidebar-cart-badge" class="hidden ml-auto bg-yellow-400 text-black text-[9px] font-black min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center"></span>
            </a>
            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('orders.index') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Pesanan Saya
                <span id="siap-sidebar-badge" class="{{ $__siapCount > 0 ? '' : 'hidden' }} ml-auto bg-emerald-500 text-white text-[10px] font-black min-w-[20px] h-5 px-1 rounded-full flex items-center justify-center">{{ $__siapCount > 9 ? '9+' : $__siapCount }}</span>
            </a>
            <a href="{{ route('orders.riwayat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('orders.riwayat') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat
            </a>
            <a href="{{ route('topup.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('topup.*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Top-Up Saldo
            </a>
            @elseif(auth()->user()->isAdmin())
            <p class="text-[9px] text-neutral-400 font-bold uppercase tracking-widest px-3 mb-2">Admin Panel</p>
            <a href="{{ route('admin-toko.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.dashboard') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin-toko.tokos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.tokos*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Kelola Toko
            </a>
            <a href="{{ route('admin-toko.antri') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.antri*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Antri Pesanan
            </a>
            <a href="{{ route('admin-toko.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.users*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola User
            </a>
            <a href="{{ route('admin-toko.validate-topup') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.validate-topup') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Validasi Top-Up
            </a>
            <a href="{{ route('admin-toko.arsip') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.arsip*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                Arsip Penjualan
            </a>
            <a href="{{ route('admin-toko.pencairan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('admin-toko.pencairan*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pencairan Saldo
                @php $__pencairanCount = \App\Models\Withdrawal::where('status','pending')->count(); @endphp
                @if($__pencairanCount > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center">{{ $__pencairanCount > 9 ? '9+' : $__pencairanCount }}</span>
                @endif
            </a>
            @elseif(auth()->user()->isToko())
            <p class="text-[9px] text-neutral-400 font-bold uppercase tracking-widest px-3 mb-2">Toko Saya</p>
            <a href="{{ route('penjual.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('penjual.dashboard') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('penjual.menus') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('penjual.menus*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Kelola Menu
            </a>
            <a href="{{ route('penjual.antri') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('penjual.antri') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Antri Pesanan
                @php
                    $__pendingCount = auth()->user()->toko
                        ? \App\Models\Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', auth()->user()->toko->id))->where('status', 'pending')->count()
                        : 0;
                @endphp
                @if($__pendingCount > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center">{{ $__pendingCount > 9 ? '9+' : $__pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('penjual.rekap') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('penjual.rekap') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Rekap Pendapatan
            </a>
            <a href="{{ route('penjual.pencairan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold mb-1 transition-all {{ request()->routeIs('penjual.pencairan*') ? 'bg-yellow-400 text-black shadow-sm' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pencairan Saldo
            </a>
            @endif
        </div>

        {{-- Logout --}}
        <div class="p-4 mt-auto border-t border-neutral-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-neutral-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>
    @endauth

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="flex-grow flex flex-col min-w-0">

        {{-- Top Header --}}
        <header class="glass px-6 py-4 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-4">
                {{-- Mobile menu logo (only visible when no sidebar) --}}
                @guest
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-yellow-400 rounded-xl flex items-center justify-center shadow shadow-yellow-400/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="font-black text-sm">E-Canteen</span>
                </a>
                @endguest
                @auth
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 bg-yellow-400 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="font-black text-sm">E-Canteen</span>
                </div>
                @endauth
                <div class="hidden lg:block">
                    <h1 class="text-sm font-bold tracking-tight">@yield('page-title', 'E-Canteen Sekolah')</h1>
                    <p class="text-[9px] text-neutral-400 font-bold uppercase tracking-widest">Smart Campus Ordering</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                @if(auth()->user()->isUser() || auth()->user()->isToko())
                <div class="hidden sm:flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="text-xs font-bold text-emerald-700">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="w-8 h-8 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-700 font-black text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                @else
                <a href="{{ route('login') }}" class="text-xs font-bold text-neutral-500 hover:text-neutral-800 transition-colors">Login</a>
                <a href="{{ route('register') }}" class="bg-black text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-neutral-800 transition-colors">Daftar</a>
                @endauth
            </div>
        </header>

        {{-- Mobile Nav (for authenticated users on small screens) --}}
        @auth
        <div class="lg:hidden flex items-center gap-1 px-4 py-2 bg-white border-b border-neutral-100 overflow-x-auto no-scrollbar">
            @if(auth()->user()->isUser())
            <a href="{{ route('menus.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('menus.*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Menu</a>
            <a href="{{ route('cart.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('cart.*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Keranjang</a>
            <a href="{{ route('orders.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('orders.index') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">
                Pesanan
                <span id="siap-mobile-badge" class="{{ $__siapCount > 0 ? '' : 'hidden' }} bg-emerald-500 text-white text-[9px] font-black min-w-[16px] h-4 px-0.5 rounded-full flex items-center justify-center">{{ $__siapCount > 9 ? '9+' : $__siapCount }}</span>
            </a>
            <a href="{{ route('orders.riwayat') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('orders.riwayat') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Riwayat</a>
            <a href="{{ route('topup.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('topup.*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Top-Up</a>
            @elseif(auth()->user()->isAdmin())
            <a href="{{ route('admin-toko.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.dashboard') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Dashboard</a>
            <a href="{{ route('admin-toko.tokos') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.tokos*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Kelola Toko</a>
            <a href="{{ route('admin-toko.antri') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.antri*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Antri Pesanan</a>
            <a href="{{ route('admin-toko.users') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.users*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Kelola User</a>
            <a href="{{ route('admin-toko.validate-topup') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.validate-topup') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Validasi Top-Up</a>
            <a href="{{ route('admin-toko.arsip') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.arsip*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Arsip</a>
            <a href="{{ route('admin-toko.pencairan') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('admin-toko.pencairan*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Pencairan</a>
            @elseif(auth()->user()->isToko())
            <a href="{{ route('penjual.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('penjual.dashboard') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Dashboard</a>
            <a href="{{ route('penjual.menus') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('penjual.menus*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Kelola Menu</a>
            <a href="{{ route('penjual.antri') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('penjual.antri') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Antri Pesanan</a>
            <a href="{{ route('penjual.rekap') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('penjual.rekap') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Rekap</a>
            <a href="{{ route('penjual.pencairan') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all {{ request()->routeIs('penjual.pencairan*') ? 'bg-yellow-400 text-black' : 'text-neutral-500 hover:bg-neutral-100' }}">Pencairan</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-red-400 hover:bg-red-50 whitespace-nowrap transition-all">Logout</button>
            </form>
        </div>
        @endauth

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
        @endif
        @if($errors->any())
        <div class="mx-6 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm font-medium">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Content --}}
        <main class="flex-grow py-8 pb-28">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-neutral-100 mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-6">
                <p class="text-center text-neutral-400 text-xs font-medium">
                    &copy; {{ date('Y') }} E-Canteen Sekolah &mdash; Smart Campus Ordering
                </p>
            </div>
        </footer>
    </div>

</div>

{{-- Floating Cart Button (mobile only) --}}
@auth
@if(auth()->user()->isUser())

{{-- Siap Order Notification Banner --}}
<div id="siap-banner"
    class="{{ $__siapCount > 0 ? '' : 'hidden' }} fixed bottom-24 left-1/2 -translate-x-1/2 z-50 lg:left-auto lg:translate-x-0 lg:right-6 lg:bottom-6 w-[calc(100%-3rem)] max-w-sm">
    <a href="{{ route('orders.index') }}"
        class="flex items-center gap-3 bg-emerald-500 text-white px-5 py-4 rounded-2xl shadow-2xl shadow-emerald-500/30 hover:bg-emerald-600 active:scale-[0.98] transition-all">
        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="font-black text-sm leading-tight">Pesanan Siap Diambil!</p>
            <p class="text-emerald-100 text-xs mt-0.5" id="siap-banner-text">
                {{ $__siapCount }} pesanan menunggu di kantin
            </p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 ml-auto opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </a>
</div>

{{-- Toast notification (appears when a new order becomes siap) --}}
<div id="siap-toast"
    class="hidden fixed top-6 left-1/2 -translate-x-1/2 z-[60] w-[calc(100%-3rem)] max-w-sm">
    <div class="flex items-center gap-3 bg-neutral-900 text-white px-5 py-4 rounded-2xl shadow-2xl animate-bounce-once">
        <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <p class="font-black text-sm">Makananmu Siap! 🎉</p>
            <p class="text-neutral-400 text-xs mt-0.5">Segera ambil pesananmu di kantin.</p>
        </div>
        <button onclick="document.getElementById('siap-toast').classList.add('hidden')" class="ml-auto text-neutral-500 hover:text-white transition-colors flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

@endif
@endauth

@auth
@if(auth()->user()->isUser())
<a href="{{ route('cart.index') }}" id="floating-cart-btn"
    class="lg:hidden fixed bottom-6 right-6 z-50 bg-black hover:bg-neutral-800 text-white p-4 rounded-2xl shadow-2xl shadow-black/20 transition-all hover:scale-105 active:scale-95 flex items-center justify-center" style="width:56px;height:56px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    <span id="nav-cart-badge" class="hidden absolute bg-yellow-400 text-black text-[9px] font-black min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center leading-none" style="top:-4px;right:-4px;"></span>
</a>
@endif
@endauth

<script>
(function () {
    function updateBadges() {
        try {
            const cart = JSON.parse(localStorage.getItem('ecanteen_cart') || '{}');
            const total = Object.values(cart).reduce(function (s, v) { return s + v.qty; }, 0);
            ['nav-cart-badge', 'sidebar-cart-badge'].forEach(function(id) {
                const el = document.getElementById(id);
                if (!el) return;
                if (total > 0) {
                    el.textContent = total;
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        } catch (e) {}
    }
    document.addEventListener('DOMContentLoaded', updateBadges);
    window.updateNavBadge = updateBadges;
})();
</script>
@yield('scripts')
@auth
@if(auth()->user()->isUser())
<script>
(function () {
    const POLL_INTERVAL = 30000; // 30 seconds
    const SIAP_COUNT_URL = '{{ route("orders.siap-count") }}';
    let lastKnownCount = {{ $__siapCount }};

    function updateSiapUI(count) {
        // Sidebar badge
        ['siap-sidebar-badge', 'siap-mobile-badge'].forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (count > 0) {
                el.textContent = count > 9 ? '9+' : count;
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });

        // Banner
        const banner = document.getElementById('siap-banner');
        const bannerText = document.getElementById('siap-banner-text');
        if (banner) {
            if (count > 0) {
                if (bannerText) bannerText.textContent = count + ' pesanan menunggu di kantin';
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }
        }
    }

    function showToast() {
        const toast = document.getElementById('siap-toast');
        if (!toast) return;
        toast.classList.remove('hidden');
        // Auto-dismiss after 7 seconds
        setTimeout(function () {
            toast.classList.add('hidden');
        }, 7000);
    }

    function pollSiapCount() {
        fetch(SIAP_COUNT_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                const newCount = data.count || 0;
                if (newCount > lastKnownCount) {
                    showToast();
                }
                lastKnownCount = newCount;
                updateSiapUI(newCount);
            })
            .catch(function () {}); // Silent fail
    }

    // Start polling after page loads
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(pollSiapCount, POLL_INTERVAL);
        setInterval(pollSiapCount, POLL_INTERVAL);
    });
})();
</script>
@endif
@endauth
</body>
</html>
