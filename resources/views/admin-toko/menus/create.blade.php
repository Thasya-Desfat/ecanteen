@extends('layouts.app')

@section('title', 'Tambah Menu - E-Canteen')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <a href="{{ route('admin-toko.tokos.menus', $toko) }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700">&larr; Kembali</a>
    <h1 class="font-black text-2xl mt-1 mb-6">Tambah Menu Baru</h1>

    <div class="bg-white rounded-3xl border border-neutral-100 p-6">
        <form method="POST" action="{{ route('admin-toko.tokos.menus.store', $toko) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="nama_menu" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Nama Menu</label>
                <input type="text" name="nama_menu" id="nama_menu" value="{{ old('nama_menu') }}" required
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition @error('nama_menu') border-red-400 @enderror">
                @error('nama_menu')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="kategori" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Kategori</label>
                <select name="kategori" id="kategori" required
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition @error('kategori') border-red-400 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(['Makanan Berat','Makanan Ringan','Lauk','Minuman','Dessert','Lainnya'] as $kat)
                    <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="harga_display" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Harga</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400 text-sm font-bold">Rp</span>
                    <input type="text" id="harga_display" value="{{ old('harga') ? number_format(old('harga'), 0, ',', '.') : '' }}"
                        placeholder="Contoh: 10.000"
                        class="w-full bg-neutral-50 border border-neutral-200 rounded-xl py-3 pl-10 pr-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition @error('harga') border-red-400 @enderror"
                        oninput="formatHarga(this)">
                </div>
                <input type="hidden" name="harga" id="harga" value="{{ old('harga') }}">
                @error('harga')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="foto" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Foto Menu</label>
                <input type="file" name="foto" id="foto" accept="image/*"
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition @error('foto') border-red-400 @enderror">
                @error('foto')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-neutral-400 mt-1">Format: JPG, JPEG, PNG. Max: 2MB</p>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Status</label>
                <select name="status" id="status" required
                    class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition">
                    <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ old('status') == 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
                @error('status')
                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-black hover:bg-neutral-800 text-white font-black py-3 px-4 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
                    Simpan Menu
                </button>
                <a href="{{ route('admin-toko.tokos.menus', $toko) }}" class="flex-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-bold py-3 px-4 rounded-2xl text-sm text-center transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function formatHarga(input) {
    let raw = input.value.replace(/\D/g, '');
    document.getElementById('harga').value = raw;
    input.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
}
</script>
@endsection
