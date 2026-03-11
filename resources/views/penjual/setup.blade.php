@extends('layouts.app')

@section('title', 'Setup Toko - E-Canteen')

@section('content')
<div class="max-w-lg mx-auto px-6 py-8">

    {{-- Hero --}}
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-yellow-400 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-yellow-400/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h1 class="text-3xl font-black tracking-tight mb-2">Buat Toko Anda</h1>
        <p class="text-neutral-500 text-sm font-medium">Mulai berjualan di E-Canteen Sekolah</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-neutral-100">
        <form action="{{ route('penjual.setup.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="nama_toko" class="block text-sm font-bold text-neutral-800 mb-2">Nama Toko</label>
                <input type="text" id="nama_toko" name="nama_toko" value="{{ old('nama_toko') }}"
                    placeholder="Contoh: Toko Makan Siang"
                    class="w-full border border-neutral-200 rounded-2xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all"
                    required>
                @error('nama_toko')
                <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-black text-white py-3.5 rounded-2xl text-sm font-bold hover:bg-neutral-800 transition-all active:scale-[0.98]">
                Buat Toko Sekarang
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-neutral-400 font-medium mt-6">
        Nama toko dapat diubah oleh administrator. Hubungi admin jika ada perubahan.
    </p>
</div>
@endsection
