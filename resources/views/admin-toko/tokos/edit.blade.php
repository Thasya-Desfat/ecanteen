@extends('layouts.app')

@section('title', 'Edit Toko - E-Canteen')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <a href="{{ route('admin-toko.tokos') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Kembali</a>
    <h1 class="font-black text-2xl mt-1 mb-6">Edit Toko</h1>

    <div class="bg-white rounded-3xl border border-neutral-100 p-6">
        <form method="POST" action="{{ route('admin-toko.tokos.update', $toko) }}">
            @csrf
            @method('PUT')
            <div class="mb-5">
                <label for="nama_toko" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Nama Toko</label>
                <input type="text" name="nama_toko" id="nama_toko" value="{{ old('nama_toko', $toko->nama_toko) }}" required autofocus
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition @error('nama_toko') border-red-400 @enderror">
                @error('nama_toko')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="user_id" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Pemilik / Penjual <span class="text-neutral-300 font-normal normal-case">(opsional)</span></label>
                <select name="user_id" id="user_id"
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition">
                    <option value="">-- Belum ada pemilik --</option>
                    @foreach($penjuals as $penjual)
                    @php $sudahPunyaToko = $penjual->toko && $penjual->toko->id !== $toko->id; @endphp
                    <option value="{{ $penjual->id }}"
                        {{ old('user_id', $toko->user_id) == $penjual->id ? 'selected' : '' }}
                        {{ $sudahPunyaToko ? 'disabled' : '' }}>
                        {{ $penjual->name }} ({{ $penjual->email }}){{ $sudahPunyaToko ? ' — sudah punya toko lain' : '' }}
                    </option>
                    @endforeach
                </select>
                @if($penjuals->isEmpty())
                <p class="text-xs text-neutral-400 mt-1.5">Belum ada akun penjual. Buat akun dengan role <strong>toko</strong> terlebih dahulu.</p>
                @endif
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-black hover:bg-neutral-800 text-white font-black py-3 px-4 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
                    Update
                </button>
                <a href="{{ route('admin-toko.tokos') }}" class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-bold py-3 px-4 rounded-2xl text-sm text-center transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
