@extends('layouts.app')

@section('title', 'Kelola Menu - ' . $toko->nama_toko)

@section('content')
<div class="max-w-5xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black tracking-tight">Kelola Menu</h1>
            <p class="text-neutral-500 text-sm font-medium mt-1">{{ $toko->nama_toko }}</p>
        </div>
        <a href="{{ route('penjual.menus.create') }}"
            class="bg-black text-white px-5 py-2.5 rounded-2xl text-sm font-bold hover:bg-neutral-800 transition-all active:scale-[0.97] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Menu
        </a>
    </div>

    @if($menus->total() === 0)
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <h3 class="text-lg font-black mb-2">Belum ada menu</h3>
        <p class="text-neutral-400 text-sm mb-6">Tambahkan menu pertama untuk mulai berjualan</p>
        <a href="{{ route('penjual.menus.create') }}"
            class="inline-flex items-center gap-2 bg-yellow-400 text-black px-6 py-3 rounded-2xl font-bold text-sm hover:bg-yellow-300 transition-all">
            Tambah Menu Pertama
        </a>
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-neutral-50 border-b border-neutral-100">
                    <th class="text-left px-6 py-4 font-black text-neutral-600 text-xs uppercase tracking-wide">Menu</th>
                    <th class="text-left px-6 py-4 font-black text-neutral-600 text-xs uppercase tracking-wide">Kategori</th>
                    <th class="text-left px-6 py-4 font-black text-neutral-600 text-xs uppercase tracking-wide">Harga</th>
                    <th class="text-left px-6 py-4 font-black text-neutral-600 text-xs uppercase tracking-wide">Status</th>
                    <th class="text-right px-6 py-4 font-black text-neutral-600 text-xs uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-50">
                @foreach($menus as $menu)
                <tr class="hover:bg-neutral-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            @if($menu->foto)
                            <img src="{{ Storage::url($menu->foto) }}" alt="{{ $menu->nama_menu }}"
                                class="w-12 h-12 rounded-2xl object-cover bg-neutral-100 flex-shrink-0">
                            @else
                            <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-bold">{{ $menu->nama_menu }}</p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">{{ $menu->kategori }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">{{ $menu->kategori }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('penjual.menus.toggle-status', $menu) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $menu->status === 'tersedia' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-600 hover:bg-red-200' }}">
                                {{ $menu->status === 'tersedia' ? '✓ Tersedia' : '✗ Habis' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('penjual.menus.edit', $menu) }}"
                                class="px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded-xl text-xs font-bold transition-all">
                                Edit
                            </a>
                            <form action="{{ route('penjual.menus.destroy', $menu) }}" method="POST"
                                onsubmit="return confirm('Hapus menu {{ $menu->nama_menu }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl text-xs font-bold transition-all">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($menus->hasPages())
    <div class="mt-6">
        {{ $menus->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
