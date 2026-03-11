@extends('layouts.app')

@section('title', 'Tambah Menu - ' . $toko->nama_toko)

@section('content')
<div class="max-w-2xl mx-auto px-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-neutral-400 mb-6">
        <a href="{{ route('penjual.menus') }}" class="hover:text-neutral-700 font-medium transition-colors">Kelola Menu</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-bold text-neutral-700">Tambah Menu</span>
    </div>

    <h1 class="text-3xl font-black tracking-tight mb-8">Tambah Menu Baru</h1>

    <div class="bg-white rounded-3xl border border-neutral-100 p-8">
        <form action="{{ route('penjual.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Nama Menu --}}
            <div>
                <label for="nama_menu" class="block text-sm font-bold text-neutral-800 mb-2">Nama Menu <span class="text-red-400">*</span></label>
                <input type="text" id="nama_menu" name="nama_menu" value="{{ old('nama_menu') }}"
                    placeholder="Contoh: Nasi Goreng Spesial"
                    class="w-full border border-neutral-200 rounded-2xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all"
                    required>
                @error('nama_menu')
                <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label for="kategori" class="block text-sm font-bold text-neutral-800 mb-2">Kategori <span class="text-red-400">*</span></label>
                <select id="kategori" name="kategori"
                    class="w-full border border-neutral-200 rounded-2xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(['Makanan Berat','Makanan Ringan','Lauk','Minuman','Dessert','Lainnya'] as $kat)
                    <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori')
                <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga --}}
            <div>
                <label for="harga" class="block text-sm font-bold text-neutral-800 mb-2">Harga (Rp) <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-neutral-400">Rp</span>
                    <input type="number" id="harga" name="harga" value="{{ old('harga') }}"
                        placeholder="15000"
                        min="0" step="500"
                        class="w-full border border-neutral-200 rounded-2xl pl-10 pr-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all"
                        required>
                </div>
                @error('harga')
                <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-bold text-neutral-800 mb-3">Status</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-3 px-5 py-3 bg-neutral-50 rounded-2xl cursor-pointer hover:bg-emerald-50 transition-all has-[:checked]:bg-emerald-100 has-[:checked]:ring-2 has-[:checked]:ring-emerald-400">
                        <input type="radio" name="status" value="tersedia" {{ old('status', 'tersedia') === 'tersedia' ? 'checked' : '' }} class="accent-emerald-500">
                        <span class="text-sm font-bold text-emerald-700">Tersedia</span>
                    </label>
                    <label class="flex items-center gap-3 px-5 py-3 bg-neutral-50 rounded-2xl cursor-pointer hover:bg-red-50 transition-all has-[:checked]:bg-red-50 has-[:checked]:ring-2 has-[:checked]:ring-red-300">
                        <input type="radio" name="status" value="habis" {{ old('status') === 'habis' ? 'checked' : '' }} class="accent-red-500">
                        <span class="text-sm font-bold text-red-600">Habis</span>
                    </label>
                </div>
            </div>

            {{-- Foto --}}
            <div>
                <label for="foto" class="block text-sm font-bold text-neutral-800 mb-2">Foto Menu <span class="text-neutral-400 font-normal">(opsional)</span></label>
                <input type="file" id="foto" name="foto" accept="image/*"
                    class="w-full border border-neutral-200 rounded-2xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-yellow-400 file:text-black">
                <p class="text-xs text-neutral-400 mt-1.5">Max 2MB. Format: JPG, PNG, WebP</p>
                @error('foto')
                <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('penjual.menus') }}"
                    class="flex-1 text-center border border-neutral-200 text-neutral-700 py-3 rounded-2xl text-sm font-bold hover:bg-neutral-50 transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-black text-white py-3 rounded-2xl text-sm font-bold hover:bg-neutral-800 transition-all active:scale-[0.98]">
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
