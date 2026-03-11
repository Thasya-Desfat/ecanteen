@extends('layouts.app')

@section('title', 'Arsip Penjualan - E-Canteen')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">

    <div class="mb-6">
        <a href="{{ route('admin-toko.dashboard') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Kembali ke Dashboard</a>
        <h1 class="font-black text-2xl mt-1">Arsip Penjualan</h1>
        <p class="text-xs text-neutral-400 mt-1">Riwayat pesanan yang sudah selesai, dikelompokkan per toko.</p>
    </div>

    @if($tokos->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tokos as $toko)
        <a href="{{ route('admin-toko.arsip.toko', $toko) }}"
            class="bg-white rounded-3xl border border-neutral-100 p-6 hover:shadow-lg transition-shadow group flex flex-col">

            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-200 group-hover:text-yellow-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>

            <h2 class="font-black text-base group-hover:text-yellow-600 transition-colors mb-1">
                {{ $toko->nama_toko }}
            </h2>
            <p class="text-xs text-neutral-400 mb-4">{{ $toko->menus_count }} menu terdaftar</p>

            <div class="mt-auto grid grid-cols-2 gap-3 pt-4 border-t border-neutral-100">
                <div class="text-center">
                    <p class="text-2xl font-black text-yellow-500">{{ number_format($toko->selesai_count) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-0.5">Order Selesai</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-black text-emerald-600 leading-tight">Rp {{ number_format($toko->total_pendapatan, 0, ',', '.') }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mt-0.5">Total Pendapatan</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center text-neutral-400 text-sm">
        Belum ada toko terdaftar.
    </div>
    @endif

</div>
@endsection
