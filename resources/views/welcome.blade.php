@extends('layouts.app')

@section('title', 'Selamat Datang - E-Canteen Sekolah')
@section('page-title', 'Beranda')

@section('content')
<div class="max-w-5xl mx-auto px-6">

    {{-- Hero --}}
    <section class="mb-12 bg-yellow-400 rounded-3xl p-10 relative overflow-hidden">
        <div class="relative z-10 max-w-lg">
            <p class="text-[10px] font-black uppercase tracking-widest text-black/50 mb-3">Smart Campus Ordering</p>
            <h1 class="text-4xl font-black leading-tight mb-3">Kantin Sekolah
Go Digital! 🍱</h1>
            <p class="text-black/60 text-sm mb-8 font-medium leading-relaxed">Pesan makanan dari berbagai toko kantin tanpa harus antre. Bayar dengan saldo virtual, praktis dan aman.</p>
            <div class="flex flex-wrap gap-3">
                @guest
                <a href="{{ route('register') }}" class="bg-black text-white text-sm font-bold px-6 py-3 rounded-2xl hover:bg-neutral-800 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-black/20">
                    Daftar Sekarang
                </a>
                <a href="{{ route('login') }}" class="bg-white/80 text-black text-sm font-bold px-6 py-3 rounded-2xl hover:bg-white transition-all">
                    Login
                </a>
                @else
                <a href="{{ route('menus.index') }}" class="bg-black text-white text-sm font-bold px-6 py-3 rounded-2xl hover:bg-neutral-800 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-black/20">
                    Mulai Pesan Sekarang
                </a>
                @endguest
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 text-9xl opacity-10 select-none">🍜</div>
    </section>

    {{-- Feature Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-12">
        <div class="bg-white rounded-3xl border border-neutral-100 p-6 hover:shadow-lg transition-all">
            <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center text-2xl mb-4">🛒</div>
            <h3 class="text-base font-bold mb-2">Pre-Order Mudah</h3>
            <p class="text-sm text-neutral-500 leading-relaxed">Pesan makanan dari berbagai toko sebelum istirahat. Tidak perlu antre lagi!</p>
        </div>
        <div class="bg-white rounded-3xl border border-neutral-100 p-6 hover:shadow-lg transition-all">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-2xl mb-4">💳</div>
            <h3 class="text-base font-bold mb-2">Cashless</h3>
            <p class="text-sm text-neutral-500 leading-relaxed">Gunakan saldo virtual untuk transaksi. Aman, cepat, dan praktis!</p>
        </div>
        <div class="bg-white rounded-3xl border border-neutral-100 p-6 hover:shadow-lg transition-all">
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-2xl mb-4">🏪</div>
            <h3 class="text-base font-bold mb-2">Multi-Tenant</h3>
            <p class="text-sm text-neutral-500 leading-relaxed">Berbagai toko dalam satu platform. Lebih banyak pilihan untuk kamu!</p>
        </div>
    </div>

    {{-- How To --}}
    <div class="bg-white rounded-3xl border border-neutral-100 p-8">
        <h2 class="text-xl font-black mb-6">Cara Menggunakan E-Canteen</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-4">Untuk Siswa</p>
                <ol class="space-y-3">
                    @foreach(['Daftar dan login ke sistem', 'Top-up saldo dengan kode virtual', 'Pilih menu dari berbagai toko', 'Checkout dan tunggu pesanan siap'] as $i => $step)
                    <li class="flex items-center gap-3 text-sm text-neutral-700">
                        <span class="w-7 h-7 bg-yellow-400 text-black rounded-xl flex items-center justify-center font-black text-xs flex-shrink-0">{{ $i+1 }}</span>
                        {{ $step }}
                    </li>
                    @endforeach
                </ol>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-4">Untuk Admin Toko</p>
                <ol class="space-y-3">
                    @foreach(['Buat dan kelola toko Anda', 'Tambahkan menu yang dijual', 'Terima dan proses pesanan', 'Validasi top-up siswa'] as $i => $step)
                    <li class="flex items-center gap-3 text-sm text-neutral-700">
                        <span class="w-7 h-7 bg-neutral-100 text-neutral-600 rounded-xl flex items-center justify-center font-black text-xs flex-shrink-0">{{ $i+1 }}</span>
                        {{ $step }}
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

</div>
@endsection
