@extends('layouts.app')

@section('title', 'Kelola Menu - E-Canteen')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin-toko.tokos') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700 mb-1 inline-block">&larr; Kembali ke Kelola Toko</a>
            <h1 class="font-black text-2xl">Kelola Menu</h1>
            <p class="text-xs text-neutral-400 mt-0.5">{{ $toko->nama_toko }}</p>
        </div>
        <a href="{{ route('admin-toko.tokos.menus.create', $toko) }}" class="bg-black hover:bg-neutral-800 text-white font-black py-2.5 px-5 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
            Tambah Menu
        </a>
    </div>

    @if($menus->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($menus as $menu)
        <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden flex flex-col">
            @if($menu->foto)
            <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-neutral-100 flex items-center justify-center">
                <span class="text-neutral-300 text-xs font-bold">No Image</span>
            </div>
            @endif

            <div class="p-4 flex-1 flex flex-col">
                <h3 class="font-black text-base">{{ $menu->nama_menu }}</h3>
                <p class="font-black text-yellow-500 text-lg mt-0.5 mb-2">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>

                <div class="mb-3 flex gap-2 flex-wrap">
                    <span class="inline-block px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-100 text-blue-700">
                        {{ $menu->kategori }}
                    </span>
                    <span class="inline-block px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                        {{ $menu->status == 'tersedia' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($menu->status) }}
                    </span>
                </div>

                <div class="mt-auto flex gap-2">
                    <a href="{{ route('admin-toko.tokos.menus.edit', [$toko, $menu]) }}" class="flex-1 bg-amber-400 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded-2xl text-xs text-center transition">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin-toko.tokos.menus.destroy', [$toko, $menu]) }}" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-2xl text-xs transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-10 text-center">
        <p class="text-neutral-400 text-sm mb-4">Belum ada menu.</p>
        <a href="{{ route('admin-toko.tokos.menus.create', $toko) }}" class="inline-block bg-black hover:bg-neutral-800 text-white font-bold py-2.5 px-6 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
            Tambah Menu Pertama
        </a>
    </div>
    @endif
</div>
@endsection
