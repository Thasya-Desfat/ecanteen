@extends('layouts.app')

@section('title', 'Tambah User - E-Canteen')

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
        <h1 class="font-black text-2xl">Tambah User Baru</h1>
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

    <div class="bg-white rounded-3xl border border-neutral-100 p-6">
        <form method="POST" action="{{ route('admin-toko.users.store') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                    placeholder="Nama lengkap">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                    placeholder="contoh@email.com">
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Password</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                    placeholder="Minimal 8 karakter">
            </div>

            {{-- Password Confirm --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                    placeholder="Ulangi password">
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Role</label>
                <select name="role" required
                    class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all">
                    <option value="">Pilih role...</option>
                    <option value="user"  {{ old('role') == 'user'  ? 'selected' : '' }}>Siswa (User)</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Bendahara)</option>
                    <option value="toko"  {{ old('role') == 'toko'  ? 'selected' : '' }}>Penjual (Toko)</option>
                </select>
            </div>

            {{-- Saldo --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Saldo Awal</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-neutral-400">Rp</span>
                    <input type="number" name="saldo" value="{{ old('saldo', 0) }}" min="0"
                        class="w-full rounded-2xl border border-neutral-200 bg-neutral-50 pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all"
                        placeholder="0">
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full bg-black hover:bg-neutral-800 text-white font-black py-3 px-6 rounded-2xl transition-all">
                Simpan User
            </button>
        </form>
    </div>

</div>
@endsection
