@extends('layouts.app')

@section('title', 'Edit User - E-Canteen')

@section('content')
<div class="max-w-lg mx-auto px-6">

    {{-- Back --}}
    <a href="{{ route('admin-toko.users') }}" class="inline-flex items-center gap-2 text-sm text-neutral-400 hover:text-black font-bold mb-6 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>

    <div class="mb-6">
        <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400">Kelola User</p>
        <h1 class="font-black text-2xl">Edit User</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-6">
        <ul class="text-sm text-red-600 font-semibold space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- User Identity Card --}}
    <div class="bg-neutral-900 text-white rounded-3xl p-5 mb-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl font-black text-sm flex items-center justify-center bg-white/10 uppercase">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div>
            <p class="font-black">{{ $user->name }}</p>
            <p class="text-xs text-white/50">{{ $user->email }} &bull; Bergabung {{ $user->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-neutral-100 p-6">
        <form method="POST" action="{{ route('admin-toko.users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- Name --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all">
            </div>

            {{-- Password (optional) --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">
                    Password Baru
                    <span class="text-neutral-300 font-medium normal-case tracking-normal ml-1">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" minlength="8"
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                    placeholder="Minimal 8 karakter">
            </div>

            {{-- Password Confirm --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                    placeholder="Ulangi password baru">
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Role</label>
                <select name="role" required
                    @if($user->id === auth()->id()) disabled @endif
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="user"  {{ old('role', $user->role) == 'user'  ? 'selected' : '' }}>Siswa (User)</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Bendahara)</option>
                    <option value="toko"  {{ old('role', $user->role) == 'toko'  ? 'selected' : '' }}>Penjual (Toko)</option>
                </select>
                @if($user->id === auth()->id())
                <p class="text-xs text-neutral-400 mt-1">Role tidak bisa diubah untuk akun sendiri.</p>
                <input type="hidden" name="role" value="{{ $user->role }}">
                @endif
            </div>

            {{-- Saldo --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Saldo</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-neutral-400">Rp</span>
                    <input type="number" name="saldo" value="{{ old('saldo', $user->saldo) }}" min="0"
                        class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all">
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full bg-black hover:bg-neutral-800 text-white font-black py-3 px-6 rounded-2xl transition-all">
                Simpan Perubahan
            </button>
        </form>
    </div>

</div>
@endsection
