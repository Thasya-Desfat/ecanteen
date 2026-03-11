@extends('layouts.app')

@section('title', 'Riwayat Mutasi Saldo - E-Canteen')

@section('content')
<div class="max-w-3xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black tracking-tight">Riwayat Mutasi Saldo</h1>
            <p class="text-neutral-500 text-sm font-medium mt-1">Semua perubahan saldo akun Anda</p>
        </div>
        <a href="{{ route('topup.index') }}"
            class="bg-yellow-400 text-black px-5 py-2.5 rounded-2xl text-sm font-bold hover:bg-yellow-300 transition-all">
            Top-Up Saldo
        </a>
    </div>

    @if($histories->total() === 0)
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <h3 class="text-lg font-black mb-2">Belum ada riwayat transaksi</h3>
        <p class="text-neutral-400 text-sm">Riwayat akan muncul setelah ada top-up atau transaksi</p>
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <div class="divide-y divide-neutral-50">
            @foreach($histories as $history)
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 {{ $history->jenis === 'masuk' ? 'bg-emerald-100' : 'bg-red-100' }}">
                        @if($history->jenis === 'masuk')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-sm">{{ $history->keterangan }}</p>
                        <p class="text-xs text-neutral-400 mt-0.5">{{ $history->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-black text-base {{ $history->jenis === 'masuk' ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $history->jenis === 'masuk' ? '+' : '-' }}Rp {{ number_format($history->nominal, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-neutral-400 mt-0.5">Saldo: Rp {{ number_format($history->saldo_akhir, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pagination --}}
    @if($histories->hasPages())
    <div class="mt-6">
        {{ $histories->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
