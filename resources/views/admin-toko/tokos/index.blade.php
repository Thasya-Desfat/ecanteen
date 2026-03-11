@extends('layouts.app')

@section('title', 'Kelola Toko - E-Canteen')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400">Manajemen</p>
            <h1 class="font-black text-2xl">Kelola Toko</h1>
        </div>
        <a href="{{ route('admin-toko.tokos.create') }}" class="bg-black hover:bg-neutral-800 text-white font-black py-2.5 px-5 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
            + Tambah Toko
        </a>
    </div>

    @if($tokos->count() > 0)
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-100">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-[9px] font-black text-neutral-400 uppercase tracking-widest">#</th>
                    <th class="px-6 py-3 text-left text-[9px] font-black text-neutral-400 uppercase tracking-widest">Nama Toko</th>
                    <th class="px-6 py-3 text-left text-[9px] font-black text-neutral-400 uppercase tracking-widest">Pemilik (Penjual)</th>
                    <th class="px-6 py-3 text-left text-[9px] font-black text-neutral-400 uppercase tracking-widest">Jumlah Menu</th>
                    <th class="px-6 py-3 text-right text-[9px] font-black text-neutral-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-100">
                @foreach($tokos as $toko)
                <tr>
                    <td class="px-6 py-4 text-xs text-neutral-400">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-bold text-sm">{{ $toko->nama_toko }}</td>
                    <td class="px-6 py-4">
                        @if($toko->owner)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-700 font-black text-xs flex-shrink-0">
                                {{ strtoupper(substr($toko->owner->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold">{{ $toko->owner->name }}</p>
                                <p class="text-[10px] text-neutral-400">{{ $toko->owner->email }}</p>
                            </div>
                        </div>
                        @else
                        <span class="text-xs text-neutral-300 italic">Belum ada pemilik</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-neutral-500">{{ $toko->menus_count }} menu</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin-toko.tokos.menus', $toko) }}" class="inline-block bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold py-1.5 px-3 rounded-xl transition">
                            Kelola Menu
                        </a>
                        <a href="{{ route('admin-toko.tokos.edit', $toko) }}" class="inline-block bg-amber-400 hover:bg-amber-500 text-white text-xs font-bold py-1.5 px-3 rounded-xl transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin-toko.tokos.destroy', $toko) }}" class="inline" onsubmit="return confirm('Hapus toko ini? Semua menu ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded-xl transition">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 p-10 text-center">
        <p class="text-neutral-400 text-sm mb-4">Belum ada toko. Mulai tambahkan toko pertama.</p>
        <a href="{{ route('admin-toko.tokos.create') }}" class="inline-block bg-black hover:bg-neutral-800 text-white font-bold py-2.5 px-6 rounded-2xl text-sm shadow-lg shadow-black/10 transition-all">
            Tambah Toko
        </a>
    </div>
    @endif
</div>
@endsection
