@extends('layouts.app')

@section('title', 'Pencairan Saldo Penjual')

@section('content')
<div class="max-w-5xl mx-auto px-6">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tight">Pencairan Saldo</h1>
        <p class="text-neutral-500 text-sm font-medium mt-1">Kelola permintaan pencairan dari penjual</p>
    </div>

    {{-- Pending Requests --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
            <div>
                <h2 class="font-black text-base">Permintaan Masuk</h2>
                <p class="text-xs text-neutral-400 mt-0.5">Menunggu persetujuan Anda</p>
            </div>
            @if($pending->count() > 0)
            <span class="bg-red-100 text-red-600 text-xs font-black px-3 py-1 rounded-full">{{ $pending->count() }} menunggu</span>
            @endif
        </div>

        @if($pending->isEmpty())
        <div class="px-6 py-10 text-center">
            <p class="text-neutral-400 text-sm">Tidak ada permintaan yang menunggu.</p>
        </div>
        @else
        <div class="divide-y divide-neutral-100">
            @foreach($pending as $w)
            <div class="px-6 py-5">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="text-sm font-black">{{ $w->toko->nama_toko }}</p>
                        <p class="text-xs text-neutral-400">{{ $w->user->name }} &bull; {{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                        @if($w->catatan)
                        <p class="text-xs text-neutral-600 mt-1 bg-neutral-50 rounded-xl px-3 py-1.5 inline-block">{{ $w->catatan }}</p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xl font-black">Rp {{ number_format($w->jumlah, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-neutral-400">Saldo penjual: Rp {{ number_format($w->user->saldo, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    {{-- Approve --}}
                    <button type="button"
                        onclick="openApproveModal({{ $w->id }}, '{{ $w->toko->nama_toko }}', 'Rp {{ number_format($w->jumlah, 0, ',', '.') }}')"
                        class="bg-emerald-500 text-white text-xs font-black px-5 py-2.5 rounded-xl hover:bg-emerald-400 transition-all active:scale-[0.97]">
                        Setujui
                    </button>
                    {{-- Reject --}}
                    <button type="button" onclick="toggleReject({{ $w->id }})"
                        class="bg-red-50 text-red-500 text-xs font-black px-5 py-2.5 rounded-xl hover:bg-red-100 transition-all">
                        Tolak
                    </button>
                </div>
                {{-- Hidden approve form --}}
                <form id="approve-form-{{ $w->id }}" action="{{ route('admin-toko.pencairan.approve', $w) }}" method="POST" class="hidden">
                    @csrf
                </form>
                {{-- Reject form (hidden) --}}
                <div id="reject-{{ $w->id }}" class="hidden mt-3">
                    <form action="{{ route('admin-toko.pencairan.reject', $w) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="keterangan" placeholder="Alasan penolakan (opsional)"
                            class="flex-grow px-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:outline-none focus:border-red-300 focus:ring-2 focus:ring-red-50">
                        <button type="submit"
                            class="bg-red-500 text-white text-xs font-black px-5 py-2.5 rounded-xl hover:bg-red-400 transition-all">
                            Konfirmasi Tolak
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Processed Requests --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-neutral-100">
            <h2 class="font-black text-base">Riwayat Pencairan</h2>
        </div>

        @if($processed->isEmpty())
        <div class="px-6 py-10 text-center">
            <p class="text-neutral-400 text-sm">Belum ada riwayat pencairan yang diproses.</p>
        </div>
        @else
        <div class="divide-y divide-neutral-100">
            @foreach($processed as $w)
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-bold">{{ $w->toko->nama_toko }} <span class="text-neutral-400 font-normal">&mdash; {{ $w->user->name }}</span></p>
                    <p class="text-xs text-neutral-400 mt-0.5">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                    @if($w->keterangan && $w->status === 'rejected')
                    <p class="text-xs text-red-400 mt-0.5">Alasan: {{ $w->keterangan }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-base font-black">Rp {{ number_format($w->jumlah, 0, ',', '.') }}</p>
                    @if($w->status === 'approved')
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3 py-1 rounded-full">Disetujui</span>
                    @else
                    <span class="bg-red-100 text-red-600 text-xs font-black px-3 py-1 rounded-full">Ditolak</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if($processed->hasPages())
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $processed->links() }}
        </div>
        @endif
        @endif
    </div>

</div>

{{-- Custom Approve Modal --}}
<style>
@keyframes modal-scale-in {
    from { transform: scale(0.85); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
@keyframes check-pop {
    0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
    60%  { transform: scale(1.25) rotate(5deg); opacity: 1; }
    80%  { transform: scale(0.92) rotate(-2deg); }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
}
@keyframes ring-pulse {
    0%   { transform: scale(0.9); opacity: 0.7; }
    100% { transform: scale(1.8); opacity: 0; }
}
@keyframes success-text-in {
    from { transform: translateY(8px); opacity: 0; }
    to   { transform: translateY(0);   opacity: 1; }
}
.modal-card-animate { animation: modal-scale-in 0.25s cubic-bezier(0.34,1.56,0.64,1) both; }
.check-animate      { animation: check-pop 0.55s cubic-bezier(0.34,1.56,0.64,1) 0.1s both; }
.ring-animate       { animation: ring-pulse 0.7s ease-out 0.15s both; }
.success-text-animate { animation: success-text-in 0.35s ease-out 0.45s both; }
</style>

<div id="approve-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="modal-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeApproveModal()"></div>
    <div id="modal-card" class="relative bg-white rounded-3xl p-7 w-full max-w-sm shadow-2xl modal-card-animate">

        {{-- Default state --}}
        <div id="modal-default-state">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-black mb-1">Setujui Pencairan?</h3>
            <p class="text-sm text-neutral-500 mb-1">Pencairan dari <span id="modal-toko" class="font-bold text-neutral-800"></span></p>
            <p class="text-2xl font-black text-emerald-600 mb-5" id="modal-jumlah"></p>
            <p class="text-xs text-neutral-400 mb-6">Saldo penjual akan langsung berkurang setelah disetujui.</p>
            <div class="flex gap-3">
                <button onclick="closeApproveModal()"
                    class="flex-1 py-3 rounded-2xl border border-neutral-200 text-sm font-bold text-neutral-500 hover:bg-neutral-50 transition-all">
                    Batal
                </button>
                <button id="modal-confirm-btn" onclick="submitApprove()"
                    class="flex-1 py-3 rounded-2xl bg-emerald-500 text-white text-sm font-black hover:bg-emerald-400 transition-all active:scale-[0.98]">
                    Ya, Setujui
                </button>
            </div>
        </div>

        {{-- Success animation state --}}
        <div id="modal-success-state" class="hidden text-center py-4">
            <div class="relative inline-flex items-center justify-center mb-5">
                <div class="absolute w-20 h-20 bg-emerald-400 rounded-full ring-animate opacity-0"></div>
                <div class="absolute w-20 h-20 bg-emerald-300 rounded-full ring-animate opacity-0" style="animation-delay:0.25s"></div>
                <div class="w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center check-animate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-lg font-black text-neutral-800 success-text-animate">Berhasil Disetujui!</p>
            <p class="text-sm text-neutral-400 mt-1 success-text-animate" style="animation-delay:0.55s">Memproses...</p>
        </div>

    </div>
</div>

<script>
let activeApproveId = null;

function openApproveModal(id, toko, jumlah) {
    activeApproveId = id;
    // Reset to default state
    document.getElementById('modal-default-state').classList.remove('hidden');
    document.getElementById('modal-success-state').classList.add('hidden');
    document.getElementById('modal-backdrop').onclick = closeApproveModal;
    // Re-trigger card entrance animation
    const card = document.getElementById('modal-card');
    card.classList.remove('modal-card-animate');
    void card.offsetWidth; // reflow
    card.classList.add('modal-card-animate');

    document.getElementById('modal-toko').textContent = toko;
    document.getElementById('modal-jumlah').textContent = jumlah;
    const modal = document.getElementById('approve-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeApproveModal() {
    activeApproveId = null;
    const modal = document.getElementById('approve-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitApprove() {
    if (!activeApproveId) return;
    const formId = activeApproveId;

    // Disable backdrop click so it can't be dismissed during animation
    document.getElementById('modal-backdrop').onclick = null;

    // Switch to success animation state
    document.getElementById('modal-default-state').classList.add('hidden');
    const successState = document.getElementById('modal-success-state');
    successState.classList.remove('hidden');

    // Re-trigger animations by cloning the rings
    successState.querySelectorAll('.ring-animate').forEach(el => {
        const clone = el.cloneNode(true);
        el.parentNode.replaceChild(clone, el);
    });
    const checkEl = successState.querySelector('.check-animate');
    const checkClone = checkEl.cloneNode(true);
    checkEl.parentNode.replaceChild(checkClone, checkEl);

    // Submit form after animation plays
    setTimeout(() => {
        document.getElementById('approve-form-' + formId).submit();
    }, 1300);
}

function toggleReject(id) {
    const el = document.getElementById('reject-' + id);
    el.classList.toggle('hidden');
}
</script>
@endsection
