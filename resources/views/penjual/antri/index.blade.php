@extends('layouts.app')

@section('title', 'Antri Pesanan - ' . $toko->nama_toko)

@section('content')
<div class="max-w-4xl mx-auto px-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400">{{ $toko->nama_toko }}</p>
            <h1 class="font-black text-3xl tracking-tight">Antri Pesanan</h1>
            <p class="text-neutral-500 text-sm font-medium mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span id="refresh-indicator" class="text-[10px] text-neutral-300 font-bold hidden">Memperbarui...</span>
            <a href="{{ route('penjual.dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-neutral-400 hover:text-black font-bold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Dashboard
            </a>
        </div>
    </div>

    {{-- Flow Steps --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
        <div class="flex items-center gap-2 flex-shrink-0 bg-white rounded-2xl border border-neutral-100 px-4 py-3">
            <span class="w-6 h-6 bg-neutral-900 text-white rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0">1</span>
            <div>
                <p class="text-xs font-black">Pending</p>
                <p class="text-[10px] text-neutral-400">Terima pesanan</p>
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <div class="flex items-center gap-2 flex-shrink-0 bg-white rounded-2xl border border-neutral-100 px-4 py-3">
            <span class="w-6 h-6 bg-blue-500 text-white rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0">2</span>
            <div>
                <p class="text-xs font-black">Diproses</p>
                <p class="text-[10px] text-neutral-400">Masak / siapkan</p>
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <div class="flex items-center gap-2 flex-shrink-0 bg-emerald-50 rounded-2xl border border-emerald-200 px-4 py-3">
            <span class="w-6 h-6 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0">3</span>
            <div>
                <p class="text-xs font-black text-emerald-700">Siap Diambil</p>
                <p class="text-[10px] text-emerald-500">Notif ke pembeli</p>
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <div class="flex items-center gap-2 flex-shrink-0 bg-white rounded-2xl border border-neutral-100 px-4 py-3">
            <span class="w-6 h-6 bg-yellow-400 text-black rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0">4</span>
            <div>
                <p class="text-xs font-black">Selesai</p>
                <p class="text-[10px] text-neutral-400">Pembeli ambil</p>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-4 gap-3 mb-8">
        <div class="bg-white rounded-3xl p-4 border border-neutral-100">
            <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Pending</p>
            <p class="text-2xl font-black">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-3xl p-4 border border-neutral-100">
            <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Diproses</p>
            <p class="text-2xl font-black text-blue-600">{{ $counts['diproses'] }}</p>
        </div>
        <div class="bg-emerald-50 rounded-3xl p-4 border border-emerald-200">
            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Siap</p>
            <p class="text-2xl font-black text-emerald-700">{{ $counts['siap'] }}</p>
        </div>
        <div class="bg-yellow-400 rounded-3xl p-4">
            <p class="text-[10px] font-black text-yellow-800/70 uppercase tracking-widest mb-1">Selesai Hari Ini</p>
            <p class="text-2xl font-black">{{ $counts['selesai'] }}</p>
        </div>
    </div>

    @if(($counts['pending'] + $counts['diproses'] + $counts['siap']) === 0)
    {{-- Empty state --}}
    <div class="bg-white rounded-3xl border border-neutral-100 p-16 text-center">
        <p class="text-5xl mb-4">&#9749;</p>
        <h3 class="text-lg font-black mb-2">Tidak ada pesanan aktif</h3>
        <p class="text-neutral-400 text-sm">Pesanan dari pembeli akan muncul di sini.</p>
    </div>
    @else

    {{-- Pending Orders --}}
    @if($pendingOrders->count() > 0)
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-neutral-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 bg-neutral-900 text-white rounded-xl flex items-center justify-center text-xs font-black">1</span>
                <div>
                    <h2 class="font-black text-base">Pesanan Masuk</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Perlu segera diproses</p>
                </div>
            </div>
            <span class="bg-red-100 text-red-600 text-xs font-black px-3 py-1 rounded-full">{{ $pendingOrders->count() }} baru</span>
        </div>
        <div class="divide-y divide-neutral-50">
            @foreach($pendingOrders as $order)
            @include('penjual.antri._order_row', ['order' => $order, 'nextStatus' => 'diproses', 'btnText' => 'Proses →', 'btnClass' => 'bg-black hover:bg-neutral-800 text-white'])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Diproses Orders --}}
    @if($diprosesOrders->count() > 0)
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-neutral-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 bg-blue-500 text-white rounded-xl flex items-center justify-center text-xs font-black">2</span>
                <div>
                    <h2 class="font-black text-base">Sedang Diproses</h2>
                    <p class="text-xs text-neutral-400 mt-0.5">Tandai siap jika sudah selesai disiapkan</p>
                </div>
            </div>
            <span class="bg-blue-100 text-blue-700 text-xs font-black px-3 py-1 rounded-full">{{ $diprosesOrders->count() }}</span>
        </div>
        <div class="divide-y divide-neutral-50">
            @foreach($diprosesOrders as $order)
            @include('penjual.antri._order_row', ['order' => $order, 'nextStatus' => 'siap', 'btnText' => 'Tandai Siap ✓', 'btnClass' => 'bg-emerald-500 hover:bg-emerald-600 text-white'])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Siap Orders — buyer gets notified here --}}
    @if($siapOrders->count() > 0)
    <div class="bg-emerald-50 rounded-3xl border border-emerald-200 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-emerald-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 bg-emerald-500 text-white rounded-xl flex items-center justify-center text-xs font-black">3</span>
                <div>
                    <h2 class="font-black text-base text-emerald-800">Siap Diambil</h2>
                    <p class="text-xs text-emerald-600 mt-0.5">Notifikasi telah dikirim ke pembeli &mdash; tekan Selesai setelah diambil</p>
                </div>
            </div>
            <span class="bg-emerald-200 text-emerald-700 text-xs font-black px-3 py-1 rounded-full">{{ $siapOrders->count() }}</span>
        </div>
        <div class="divide-y divide-emerald-100">
            @foreach($siapOrders as $order)
            @include('penjual.antri._order_row', ['order' => $order, 'nextStatus' => 'selesai', 'btnText' => 'Sudah Diambil ✓', 'btnClass' => 'bg-yellow-400 hover:bg-yellow-300 text-black font-black'])
            @endforeach
        </div>
    </div>
    @endif

    @endif

</div>

@section('scripts')
{{-- Custom Selesai Confirmation Modal --}}
<style>
@keyframes selesai-modal-in {
    from { transform: scale(0.85); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
@keyframes selesai-check-pop {
    0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
    60%  { transform: scale(1.25) rotate(5deg); opacity: 1; }
    80%  { transform: scale(0.92) rotate(-2deg); }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
}
@keyframes selesai-ring {
    0%   { transform: scale(0.9); opacity: 0.6; }
    100% { transform: scale(1.9); opacity: 0; }
}
@keyframes selesai-text-in {
    from { transform: translateY(8px); opacity: 0; }
    to   { transform: translateY(0);   opacity: 1; }
}
.sm-card-animate { animation: selesai-modal-in 0.25s cubic-bezier(0.34,1.56,0.64,1) both; }
.sm-check-animate { animation: selesai-check-pop 0.55s cubic-bezier(0.34,1.56,0.64,1) 0.1s both; }
.sm-ring-animate  { animation: selesai-ring 0.7s ease-out 0.15s both; }
.sm-text-animate  { animation: selesai-text-in 0.35s ease-out 0.45s both; }
</style>

<div id="selesai-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="selesai-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div id="selesai-card" class="relative bg-white rounded-3xl p-7 w-full max-w-sm shadow-2xl sm-card-animate">

        {{-- Default state --}}
        <div id="selesai-default">
            <div class="w-12 h-12 bg-yellow-100 rounded-2xl flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 11H4L5 9z" />
                </svg>
            </div>
            <h3 class="text-lg font-black mb-1">Pesanan Sudah Diambil?</h3>
            <p class="text-sm text-neutral-500 mb-1">Konfirmasi pesanan untuk <span id="selesai-nama" class="font-bold text-neutral-800"></span></p>
            <p class="text-xs text-neutral-400 mb-6">Pesanan akan ditandai selesai dan tidak bisa diubah lagi.</p>
            <div class="flex gap-3">
                <button onclick="closeSelesaiModal()"
                    class="flex-1 py-3 rounded-2xl border border-neutral-200 text-sm font-bold text-neutral-500 hover:bg-neutral-50 transition-all">
                    Batal
                </button>
                <button onclick="confirmSelesai()"
                    class="flex-1 py-3 rounded-2xl bg-yellow-400 text-black text-sm font-black hover:bg-yellow-300 transition-all active:scale-[0.98]">
                    Ya, Selesai
                </button>
            </div>
        </div>

        {{-- Success animation state --}}
        <div id="selesai-success" class="hidden text-center py-4">
            <div class="relative inline-flex items-center justify-center mb-5">
                <div class="absolute w-20 h-20 bg-yellow-300 rounded-full sm-ring-animate opacity-0"></div>
                <div class="absolute w-20 h-20 bg-yellow-200 rounded-full sm-ring-animate opacity-0" style="animation-delay:0.22s"></div>
                <div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center sm-check-animate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-lg font-black text-neutral-800 sm-text-animate">Pesanan Selesai!</p>
            <p class="text-sm text-neutral-400 mt-1 sm-text-animate" style="animation-delay:0.55s">Memproses...</p>
        </div>

    </div>
</div>

<script>
let activeSelesaiId = null;

function openSelesaiModal(id, nama) {
    activeSelesaiId = id;
    document.getElementById('selesai-nama').textContent = nama;
    document.getElementById('selesai-default').classList.remove('hidden');
    document.getElementById('selesai-success').classList.add('hidden');
    document.getElementById('selesai-backdrop').onclick = closeSelesaiModal;

    const card = document.getElementById('selesai-card');
    card.classList.remove('sm-card-animate');
    void card.offsetWidth;
    card.classList.add('sm-card-animate');

    const modal = document.getElementById('selesai-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSelesaiModal() {
    activeSelesaiId = null;
    const modal = document.getElementById('selesai-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmSelesai() {
    if (!activeSelesaiId) return;
    const formId = activeSelesaiId;

    document.getElementById('selesai-backdrop').onclick = null;
    document.getElementById('selesai-default').classList.add('hidden');

    const success = document.getElementById('selesai-success');
    success.classList.remove('hidden');

    // Re-trigger ring animations
    success.querySelectorAll('.sm-ring-animate').forEach(el => {
        const c = el.cloneNode(true); el.parentNode.replaceChild(c, el);
    });
    const chk = success.querySelector('.sm-check-animate');
    const chkC = chk.cloneNode(true); chk.parentNode.replaceChild(chkC, chk);

    setTimeout(() => {
        document.getElementById('order-form-' + formId).submit();
    }, 1300);
}
</script>

<script>
// Auto-refresh antri page every 20 seconds to show new orders
(function () {
    let countdown = 20;
    const indicator = document.getElementById('refresh-indicator');
    setInterval(function () {
        countdown--;
        if (countdown <= 3 && indicator) {
            indicator.textContent = 'Memperbarui dalam ' + countdown + 's...';
            indicator.classList.remove('hidden');
        }
        if (countdown <= 0) {
            window.location.reload();
        }
    }, 1000);
})();
</script>
@endsection

@endsection
