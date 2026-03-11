@extends('layouts.app')

@section('title', 'Validasi Top-Up - E-Canteen')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Validasi Kode Top-Up</h1>

    @if($errors->has('error'))
    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-300 text-red-800 rounded-xl px-4 py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-medium">{{ $errors->first('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('admin-toko.validate-topup.process') }}">
            @csrf

            <div class="mb-6">
                <label for="kode_virtual" class="block text-gray-700 text-sm font-bold mb-2">Kode Virtual</label>
                <input type="text" name="kode_virtual" id="kode_virtual" value="{{ old('kode_virtual') }}" required autofocus
                    class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 text-lg leading-tight focus:outline-none focus:shadow-outline @error('kode_virtual') border-red-500 @enderror"
                    placeholder="CN-XXXXXX">
                @error('kode_virtual')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-lg">
                Validasi Top-Up
            </button>
        </form>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <h3 class="font-semibold text-yellow-900 mb-2">Petunjuk:</h3>
            <ol class="list-decimal list-inside text-sm text-yellow-800 space-y-1">
                <li>Minta siswa menunjukkan kode virtual mereka</li>
                <li>Masukkan kode tersebut pada form di atas</li>
                <li>Klik "Validasi Top-Up" untuk memproses</li>
                <li>Saldo siswa akan otomatis bertambah jika kode valid</li>
            </ol>
        </div>
    </div>
</div>

{{-- Success Modal --}}
@if(session('success'))
@php
    preg_match('/Rp\s[\d.,]+/', session('success'), $nom);
    preg_match('/Top-up berhasil.*$/', session('success'), $full);
@endphp
<div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center">

        {{-- Animated checkmark --}}
        <div class="flex justify-center mb-5">
            <div class="relative flex items-center justify-center w-24 h-24">
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="46" fill="none" stroke="#d1fae5" stroke-width="8"/>
                    <circle id="progress-circle" cx="50" cy="50" r="46" fill="none" stroke="#10b981" stroke-width="8"
                        stroke-dasharray="289" stroke-dashoffset="289"
                        stroke-linecap="round"
                        style="transform: rotate(-90deg); transform-origin: 50% 50%; transition: stroke-dashoffset 0.6s ease;"/>
                </svg>
                <svg id="check-icon" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-500 opacity-0 scale-50 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Validasi Berhasil!</h2>
        <p class="text-sm text-gray-500 mb-5">Saldo siswa telah berhasil ditambahkan</p>

        <div class="bg-green-50 border border-green-200 rounded-xl py-4 px-6 mb-6">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Nominal Top-Up</p>
            <p class="text-3xl font-extrabold text-green-600">{{ $nom[0] ?? '-' }}</p>
        </div>

        <button onclick="closeModal()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition mb-3">
            Validasi Kode Lain
        </button>
        <button onclick="document.getElementById('success-modal').classList.add('hidden')" class="w-full border border-gray-300 hover:bg-gray-50 text-gray-600 font-semibold py-3 rounded-xl text-sm transition">
            Tutup
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animate circle
    setTimeout(() => {
        document.getElementById('progress-circle').style.strokeDashoffset = '0';
    }, 100);

    // Show check icon after circle completes
    setTimeout(() => {
        const icon = document.getElementById('check-icon');
        icon.classList.remove('opacity-0', 'scale-50');
        icon.classList.add('opacity-100', 'scale-100');
    }, 700);
});

function closeModal() {
    document.getElementById('success-modal').classList.add('hidden');
    document.getElementById('kode_virtual').value = '';
    document.getElementById('kode_virtual').focus();
}
</script>
@endif

@endsection
