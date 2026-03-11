@extends('layouts.app')

@section('title', 'Kelola User - E-Canteen')

@section('content')
<div class="max-w-5xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400">Manajemen</p>
            <h1 class="font-black text-2xl">Kelola User</h1>
        </div>
        <a href="{{ route('admin-toko.users.create') }}"
            class="inline-flex items-center gap-2 bg-black hover:bg-neutral-800 text-white text-sm font-bold py-2.5 px-5 rounded-2xl transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah User
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach([
            'all'   => 'Semua',
            'user'  => 'Siswa',
            'admin' => 'Admin',
            'toko'  => 'Penjual',
        ] as $key => $label)
        <a href="{{ route('admin-toko.users', ['role' => $key]) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-sm font-bold transition-all
            {{ $role === $key ? 'bg-black text-white shadow-sm' : 'bg-white text-neutral-500 border border-neutral-100 hover:bg-neutral-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($users->count() === 0)
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-black mb-2">Belum ada user</h3>
        <p class="text-neutral-400 text-sm">Klik tombol "Tambah User" untuk menambah pengguna baru.</p>
    </div>
    @else
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <div class="divide-y divide-neutral-50">
            @foreach($users as $user)
            <div class="flex items-center gap-4 px-6 py-4">
                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-2xl font-black text-sm flex items-center justify-center flex-shrink-0 uppercase
                    @if($user->role == 'admin') bg-yellow-100 text-yellow-700
                    @elseif($user->role == 'toko') bg-emerald-100 text-emerald-700
                    @else bg-blue-100 text-blue-700 @endif">
                    {{ substr($user->name, 0, 1) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm truncate">{{ $user->name }}</p>
                    <p class="text-xs text-neutral-400 truncate">{{ $user->email }}</p>
                </div>

                {{-- Role Badge --}}
                <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full flex-shrink-0
                    @if($user->role == 'admin') bg-yellow-100 text-yellow-700
                    @elseif($user->role == 'toko') bg-emerald-100 text-emerald-700
                    @else bg-blue-100 text-blue-700 @endif">
                    {{ $user->role == 'toko' ? 'Penjual' : ucfirst($user->role) }}
                </span>

                {{-- Saldo --}}
                @if($user->role !== 'toko')
                <div class="text-right flex-shrink-0 w-28 hidden sm:block">
                    <p class="text-xs text-neutral-400 font-semibold">Saldo</p>
                    <p class="text-sm font-black">Rp {{ number_format($user->saldo, 0, ',', '.') }}</p>
                </div>
                @else
                <div class="text-right flex-shrink-0 w-28 hidden sm:block"></div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('admin-toko.users.edit', $user) }}"
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-neutral-100 hover:bg-yellow-400 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin-toko.users.destroy', $user) }}"
                        onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl bg-neutral-100 hover:bg-red-100 hover:text-red-500 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                    @else
                    <div class="w-8 h-8"></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($users->hasPages())
    <div class="mt-6">
        {{ $users->appends(['role' => $role])->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
