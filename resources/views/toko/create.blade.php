@extends('layouts.app')

@section('title', 'Buat Toko - E-Canteen')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <h1 class="font-black text-2xl mb-6">Buat Toko Baru</h1>

    <div class="bg-white rounded-3xl border border-neutral-100 p-6">
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl">
            <p class="text-xs font-bold text-yellow-800">
                Anda belum memiliki toko. Silakan buat toko untuk mulai berjualan di E-Canteen.
            </p>
        </div>

        <form method="POST" action="{{ route('toko.store') }}">
            @csrf

            <div class="mb-6">
                <label for="nama_toko" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Nama Toko</label>
                <input type="text" name="nama_toko" id="nama_toko" value="{{ old('nama_toko') }}" required autofocus
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition @error('nama_toko') border-red-400 @enderror">
                @error('nama_toko')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-black hover:bg-neutral-800 text-white font-black py-3 px-4 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
                Buat Toko
            </button>
        </form>
    </div>
</div>
@endsection
