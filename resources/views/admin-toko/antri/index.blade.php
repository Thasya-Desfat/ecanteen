@extends('layouts.app')

@section('title', 'Antri Pesanan - E-Canteen')

@php
    $totalAktif = $counts['pending'] + $counts['diproses'] + $counts['siap'];
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400">Manajemen</p>
            <h1 class="font-black text-3xl tracking-tight">Antri Pesanan</h1>
            <p class="text-neutral-500 text-sm font-medium mt-1">Pantau status pesanan semua toko &mdash; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2">Menunggu</p>
            <p class="text-3xl font-black">{{ $counts['pending'] }}</p>
            <p class="text-xs text-neutral-400 mt-1">Belum diproses</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2">Diproses</p>
            <p class="text-3xl font-black text-blue-600">{{ $counts['diproses'] }}</p>
            <p class="text-xs text-neutral-400 mt-1">Sedang disiapkan</p>
        </div>
        <div class="bg-yellow-400 rounded-3xl p-5">
            <p class="text-xs font-black text-yellow-800/70 uppercase tracking-widest mb-2">Siap Diambil</p>
            <p class="text-3xl font-black">{{ $counts['siap'] }}</p>
            <p class="text-xs text-yellow-800/60 mt-1">Menunggu pelanggan</p>
        </div>
    </div>

    @if($totalAktif === 0)
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <p class="text-5xl mb-4">&#9749;</p>
        <h3 class="text-lg font-black mb-2">Tidak ada pesanan aktif</h3>
        <p class="text-neutral-400 text-sm">Semua antrian bersih!</p>
    </div>
    @else
    <div class="space-y-6">

        {{-- Pending --}}
        @if($pendingOrders->count() > 0)
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h2 class="font-black text-base">Pesanan Masuk &mdash; Pending</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Menunggu diproses oleh penjual</p>
                </div>
                <span class="bg-amber-100 text-amber-700 text-xs font-black px-3 py-1 rounded-full">{{ $pendingOrders->count() }}</span>
            </div>
            <div class="divide-y divide-neutral-50">
                @foreach($pendingOrders as $order)
                @include('admin-toko.antri._order_row', ['order' => $order])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Diproses --}}
        @if($diprosesOrders->count() > 0)
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h2 class="font-black text-base">Sedang Diproses</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Penjual sedang menyiapkan pesanan</p>
                </div>
                <span class="bg-blue-100 text-blue-700 text-xs font-black px-3 py-1 rounded-full">{{ $diprosesOrders->count() }}</span>
            </div>
            <div class="divide-y divide-neutral-50">
                @foreach($diprosesOrders as $order)
                @include('admin-toko.antri._order_row', ['order' => $order])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Siap --}}
        @if($siapOrders->count() > 0)
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h2 class="font-black text-base">Siap Diambil</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Menunggu siswa mengambil pesanan</p>
                </div>
                <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3 py-1 rounded-full">{{ $siapOrders->count() }}</span>
            </div>
            <div class="divide-y divide-neutral-50">
                @foreach($siapOrders as $order)
                @include('admin-toko.antri._order_row', ['order' => $order])
                @endforeach
            </div>
        </div>
        @endif

    </div>
    @endif

</div>
@endsection
