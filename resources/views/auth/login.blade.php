@extends('layouts.app')

@section('title', 'Login - E-Canteen')
@section('page-title', 'Login')

@section('content')
<div class="max-w-md mx-auto px-6">
    <div class="bg-white rounded-3xl border border-neutral-100 px-8 py-8 shadow-sm">
        <div class="mb-8 text-center">
            <div class="w-14 h-14 bg-yellow-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-yellow-400/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-black">Masuk ke E-Canteen</h2>
            <p class="text-sm text-neutral-400 mt-1">Pesan makanan favoritmu sekarang</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full py-3 px-4 bg-neutral-50 border @error('email') border-red-300 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all">
                @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="w-full py-3 px-4 pr-10 bg-neutral-50 border @error('password') border-red-300 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400/30 focus:border-yellow-400 transition-all">
                    <button type="button" onclick="togglePassword('password', 'eye-login')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-neutral-600">
                        <svg id="eye-login" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 accent-yellow-400 rounded">
                    <span class="text-xs text-neutral-500 font-medium">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-black hover:bg-neutral-800 text-white font-bold py-3 px-4 rounded-2xl text-sm transition-all hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-black/10">
                Masuk
            </button>
        </form>
        
        <div class="text-center mt-5">
            <span class="text-neutral-400 text-xs">Belum punya akun? </span>
            <a href="{{ route('register') }}" class="text-yellow-600 hover:text-yellow-700 text-xs font-bold">Daftar disini</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
    }
}
</script>
@endsection
