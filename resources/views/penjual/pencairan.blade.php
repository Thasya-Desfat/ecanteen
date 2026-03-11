@extends('layouts.app')

@section('title', 'Pencairan Saldo - ' . $toko->nama_toko)

@section('content')
<div class="max-w-4xl mx-auto px-6">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tight">Pencairan Saldo</h1>
        <p class="text-neutral-500 text-sm font-medium mt-1">Cairkan pendapatan toko ke rekening Anda</p>
    </div>

    {{-- Saldo Card + Form --}}
    <div class="grid sm:grid-cols-2 gap-6 mb-8">

        {{-- Saldo Sekarang --}}
        <div class="bg-neutral-900 rounded-3xl p-6 flex flex-col justify-between">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2">Saldo Tersedia</p>
            <p class="text-4xl font-black text-white">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</p>
            <p class="text-xs text-neutral-500 mt-3">Pendapatan terkumpul dari pesanan yang selesai</p>
        </div>

        {{-- Form Pencairan --}}
        @php
            $hasPending = $withdrawals->where('status', 'pending')->count() > 0;
        @endphp
        <div class="bg-white rounded-3xl p-6 border border-neutral-100">
            <p class="text-sm font-black mb-4">Ajukan Pencairan</p>

            @if($hasPending)
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 text-sm text-yellow-700 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ada permintaan pencairan yang sedang menunggu persetujuan admin.
            </div>
            @elseif(auth()->user()->saldo < 10000)
            <div class="bg-neutral-50 border border-neutral-200 rounded-2xl p-4 text-sm text-neutral-500 font-medium">
                Saldo minimal untuk pencairan adalah <strong>Rp 10.000</strong>.
            </div>
            @else
            <form id="pencairan-form" action="{{ route('penjual.pencairan.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-neutral-500 uppercase tracking-widest block mb-1.5">Jumlah Pencairan</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-neutral-400">Rp</span>
                        <input type="number" name="jumlah" id="pencairan-jumlah" min="10000" max="{{ auth()->user()->saldo }}" step="1000"
                            value="{{ old('jumlah') }}"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-neutral-200 text-sm font-semibold focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100 @error('jumlah') border-red-300 @enderror"
                            placeholder="Masukkan nominal...">
                    </div>
                    @error('jumlah')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-neutral-400 mt-1">Minimal Rp 10.000 &bull; Maksimal Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-neutral-500 uppercase tracking-widest block mb-1.5">Catatan <span class="font-normal normal-case text-neutral-400">(opsional)</span></label>
                    <input type="text" name="catatan" value="{{ old('catatan') }}"
                        class="w-full px-4 py-3 rounded-2xl border border-neutral-200 text-sm font-semibold focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-100"
                        placeholder="Misal: Transfer BCA 12345...">
                </div>
                <button type="button" onclick="openPencairanModal()"
                    class="w-full bg-yellow-400 text-black py-3 rounded-2xl text-sm font-black hover:bg-yellow-300 transition-all active:scale-[0.98]">
                    Ajukan Pencairan
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Riwayat Pencairan --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-neutral-100">
            <h2 class="font-black text-base">Riwayat Pencairan</h2>
        </div>

        @if($withdrawals->isEmpty())
        <div class="px-6 py-12 text-center">
            <p class="text-neutral-400 text-sm">Belum ada riwayat pencairan.</p>
        </div>
        @else
        <div class="divide-y divide-neutral-100">
            @foreach($withdrawals as $w)
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-bold">Rp {{ number_format($w->jumlah, 0, ',', '.') }}</p>
                    @if($w->catatan)
                    <p class="text-xs text-neutral-400 mt-0.5 truncate">{{ $w->catatan }}</p>
                    @endif
                    @if($w->keterangan && $w->status === 'rejected')
                    <p class="text-xs text-red-400 mt-0.5">Alasan: {{ $w->keterangan }}</p>
                    @endif
                    <p class="text-[11px] text-neutral-400 mt-1">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                </div>
                <div class="flex-shrink-0">
                    @if($w->status === 'pending')
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-black px-3 py-1 rounded-full">Menunggu</span>
                    @elseif($w->status === 'approved')
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3 py-1 rounded-full">Disetujui</span>
                    @else
                    <span class="bg-red-100 text-red-600 text-xs font-black px-3 py-1 rounded-full">Ditolak</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if($withdrawals->hasPages())
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $withdrawals->links() }}
        </div>
        @endif
        @endif
    </div>

</div>

{{-- Pencairan Confirmation Modal --}}
<style>
@keyframes pc-modal-in {
    from { transform: scale(0.85); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
@keyframes pc-icon-pop {
    0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
    60%  { transform: scale(1.25) rotate(5deg); opacity: 1; }
    80%  { transform: scale(0.92) rotate(-2deg); }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
}
@keyframes pc-ring {
    0%   { transform: scale(0.9); opacity: 0.6; }
    100% { transform: scale(2); opacity: 0; }
}
@keyframes pc-text-in {
    from { transform: translateY(8px); opacity: 0; }
    to   { transform: translateY(0);   opacity: 1; }
}
.pc-card-animate  { animation: pc-modal-in 0.25s cubic-bezier(0.34,1.56,0.64,1) both; }
.pc-icon-animate  { animation: pc-icon-pop 0.55s cubic-bezier(0.34,1.56,0.64,1) 0.1s both; }
.pc-ring-animate  { animation: pc-ring 0.7s ease-out 0.15s both; }
.pc-text-animate  { animation: pc-text-in 0.35s ease-out 0.45s both; }
</style>

<div id="pc-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="pc-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div id="pc-card" class="relative bg-white rounded-3xl p-7 w-full max-w-sm shadow-2xl pc-card-animate">

        {{-- Default confirm state --}}
        <div id="pc-default">
            <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-black mb-1">Ajukan Pencairan?</h3>
            <p class="text-sm text-neutral-500 mb-1">Nominal yang akan dicairkan</p>
            <p id="pc-nominal" class="text-2xl font-black text-yellow-500 mb-5"></p>
            <p class="text-xs text-neutral-400 mb-6">Permintaan akan dikirim ke admin untuk disetujui.</p>
            <div class="flex gap-3">
                <button onclick="closePencairanModal()"
                    class="flex-1 py-3 rounded-2xl border border-neutral-200 text-sm font-bold text-neutral-500 hover:bg-neutral-50 transition-all">
                    Batal
                </button>
                <button onclick="confirmPencairan()"
                    class="flex-1 py-3 rounded-2xl bg-yellow-400 text-black text-sm font-black hover:bg-yellow-300 transition-all active:scale-[0.98]">
                    Ya, Ajukan
                </button>
            </div>
        </div>

        {{-- Success animation state --}}
        <div id="pc-success" class="hidden text-center py-4">
            <div class="relative inline-flex items-center justify-center mb-5">
                <div class="absolute w-20 h-20 bg-yellow-300 rounded-full pc-ring-animate opacity-0"></div>
                <div class="absolute w-20 h-20 bg-yellow-200 rounded-full pc-ring-animate opacity-0" style="animation-delay:0.22s"></div>
                <div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center pc-icon-animate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-lg font-black text-neutral-800 pc-text-animate">Permintaan Dikirim!</p>
            <p class="text-sm text-neutral-400 mt-1 pc-text-animate" style="animation-delay:0.55s">Menunggu persetujuan admin...</p>
        </div>

    </div>
</div>

<script>
function formatRupiah(val) {
    if (!val || isNaN(val)) return 'Rp 0';
    return 'Rp ' + parseInt(val).toLocaleString('id-ID');
}

function openPencairanModal() {
    const jumlah = document.getElementById('pencairan-jumlah').value;
    if (!jumlah || jumlah < 10000) {
        document.getElementById('pencairan-jumlah').focus();
        return;
    }
    document.getElementById('pc-nominal').textContent = formatRupiah(jumlah);
    document.getElementById('pc-default').classList.remove('hidden');
    document.getElementById('pc-success').classList.add('hidden');
    document.getElementById('pc-backdrop').onclick = closePencairanModal;

    const card = document.getElementById('pc-card');
    card.classList.remove('pc-card-animate');
    void card.offsetWidth;
    card.classList.add('pc-card-animate');

    const modal = document.getElementById('pc-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePencairanModal() {
    const modal = document.getElementById('pc-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmPencairan() {
    document.getElementById('pc-backdrop').onclick = null;
    document.getElementById('pc-default').classList.add('hidden');

    const success = document.getElementById('pc-success');
    success.classList.remove('hidden');

    // Re-trigger ring animations
    success.querySelectorAll('.pc-ring-animate').forEach(el => {
        const c = el.cloneNode(true); el.parentNode.replaceChild(c, el);
    });
    const icon = success.querySelector('.pc-icon-animate');
    const iconC = icon.cloneNode(true); icon.parentNode.replaceChild(iconC, icon);

    setTimeout(() => {
        document.getElementById('pencairan-form').submit();
    }, 1400);
}
</script>
@endsection
