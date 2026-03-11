@extends('layouts.app')

@section('title', 'Top-Up Saldo - E-Canteen')
@section('page-title', 'Top-Up Saldo')

@section('content')
<div class="max-w-3xl mx-auto px-6">
    <h1 class="text-xl font-black mb-6">Top-Up Saldo</h1>

    {{-- Saldo Card --}}
    <div class="bg-yellow-400 rounded-3xl p-6 mb-6 relative overflow-hidden">
        <p class="text-[10px] font-black uppercase tracking-widest text-black/50 mb-1">Saldo Anda</p>
        <p class="text-3xl font-black">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</p>
        <div class="absolute -right-4 -bottom-4 text-7xl opacity-10 select-none">💳</div>
    </div>

    {{-- Form Top Up --}}
    <div class="bg-white rounded-3xl border border-neutral-100 p-6 mb-6">
        <form id="topup-form" method="POST" action="{{ route('topup.generate') }}">
            @csrf
            <div class="mb-5">
                <label for="nominal_display" class="block text-xs font-black uppercase tracking-widest text-neutral-400 mb-2">Nominal Top-Up</label>
                <div id="nominal-wrap" class="flex items-center border border-neutral-200 rounded-xl overflow-hidden bg-neutral-50">
                    <span class="px-4 text-neutral-400 text-sm font-bold bg-neutral-100 border-r border-neutral-200 py-3">Rp</span>
                    <input type="text" id="nominal_display" inputmode="numeric" autocomplete="off"
                        placeholder="0"
                        class="flex-1 py-3 px-4 text-sm bg-transparent focus:outline-none font-semibold">
                </div>
                <input type="hidden" name="nominal" id="nominal">
                <p id="nominal-error" class="text-red-500 text-xs mt-1.5 hidden"></p>
                <p class="text-[10px] text-neutral-400 font-medium mt-1.5">Minimal Rp 5.000</p>
            </div>

            <button type="submit" id="topup-btn" class="w-full bg-black hover:bg-neutral-800 text-white font-bold py-3 rounded-2xl text-sm transition-all hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-black/10 flex items-center justify-center gap-2">
                <span id="topup-btn-text">Generate Kode Virtual</span>
                <svg id="topup-spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </button>
        </form>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl">
            <p class="text-xs font-black text-yellow-800 mb-2">Cara Top-Up</p>
            <ol class="space-y-1.5">
                <li class="text-xs text-yellow-700 flex items-start gap-2"><span class="w-4 h-4 bg-yellow-400 text-black rounded flex items-center justify-center font-black text-[9px] flex-shrink-0 mt-0.5">1</span>Masukkan nominal yang ingin di-top up</li>
                <li class="text-xs text-yellow-700 flex items-start gap-2"><span class="w-4 h-4 bg-yellow-400 text-black rounded flex items-center justify-center font-black text-[9px] flex-shrink-0 mt-0.5">2</span>Klik "Generate Kode Virtual" untuk mendapatkan kode unik</li>
                <li class="text-xs text-yellow-700 flex items-start gap-2"><span class="w-4 h-4 bg-yellow-400 text-black rounded flex items-center justify-center font-black text-[9px] flex-shrink-0 mt-0.5">3</span>Tunjukkan kode tersebut ke admin toko</li>
                <li class="text-xs text-yellow-700 flex items-start gap-2"><span class="w-4 h-4 bg-yellow-400 text-black rounded flex items-center justify-center font-black text-[9px] flex-shrink-0 mt-0.5">4</span>Admin akan memvalidasi dan saldo Anda akan bertambah</li>
            </ol>
        </div>
    </div>
    
    {{-- Riwayat --}}
    <div class="bg-white rounded-3xl border border-neutral-100 p-6">
        <h2 class="font-black mb-5">Riwayat Top-Up</h2>

        @if($topUps->count() > 0)
        <div class="space-y-3">
            @foreach($topUps as $topUp)
            <div class="rounded-2xl p-4 border
                @if($topUp->status == 'success') bg-emerald-50 border-emerald-200
                @else bg-amber-50 border-amber-200
                @endif">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-black text-base tracking-widest">{{ $topUp->kode_virtual }}</p>
                        <p class="text-[10px] text-neutral-400 mt-0.5">{{ $topUp->created_at->format('d M Y H:i') }}</p>
                        <p class="text-[10px] text-neutral-400">Exp: {{ $topUp->expired_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-base">Rp {{ number_format($topUp->nominal, 0, ',', '.') }}</p>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest
                            @if($topUp->status == 'success') bg-emerald-100 text-emerald-700
                            @else bg-amber-100 text-amber-700
                            @endif">
                            {{ $topUp->status == 'success' ? 'Berhasil' : 'Menunggu' }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-neutral-400 text-sm text-center py-6">Belum ada riwayat top-up.</p>
        @endif
    </div>

    <div class="mt-4 text-right">
        <a href="{{ route('topup.history') }}" class="text-yellow-600 hover:text-yellow-700 text-xs font-bold">
            Lihat Riwayat Lengkap &rarr;
        </a>
    </div>
</div>

{{-- Modal Kode Virtual (always in DOM, shown via JS) --}}
<div id="kode-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center">
        <div class="mb-5 flex justify-center">
            <div class="bg-emerald-100 rounded-2xl p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <h2 class="text-lg font-black mb-1">Kode Virtual Berhasil Dibuat!</h2>
        <p class="text-xs text-neutral-400 mb-6">Tunjukkan kode ini ke admin untuk validasi top-up</p>

        <div class="bg-yellow-50 border-2 border-yellow-300 border-dashed rounded-2xl py-5 px-4 mb-4">
            <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400 mb-2">Kode Virtual</p>
            <p id="kode-text" class="text-3xl font-black tracking-widest text-yellow-600 select-all">—</p>
        </div>

        <div class="flex justify-between text-xs text-neutral-500 mb-6">
            <span>Nominal: <strong id="kode-nominal" class="text-neutral-800">—</strong></span>
            <span>Exp: <strong id="kode-expired" class="text-neutral-800">—</strong></span>
        </div>

        <button onclick="copyKode()" class="w-full mb-3 bg-black hover:bg-neutral-800 text-white font-bold py-3 px-4 rounded-2xl text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-black/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <span id="copy-label">Salin Kode</span>
        </button>
        <button onclick="closeKodeModal()" class="w-full text-neutral-400 hover:text-neutral-600 text-xs font-bold py-2 transition-all">
            Tutup
        </button>
    </div>
</div>

{{-- Approval Celebration Popup --}}
<div id="approval-popup" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4);">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-xs w-full mx-4 text-center">
        <div class="flex justify-center mb-4">
            <div id="approval-circle" class="bg-emerald-100 rounded-2xl p-5 transition-all duration-500 scale-0 opacity-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <h2 class="text-xl font-black mb-1">Top-Up Berhasil!</h2>
        <p class="text-xs text-neutral-400 mb-4">Saldo Anda telah ditambahkan</p>
        <p id="approval-nominal" class="text-3xl font-black text-emerald-500 mb-2"></p>
        <p class="text-xs text-neutral-400 mb-5">Saldo baru: <span id="approval-saldo" class="font-bold text-neutral-700"></span></p>
        <div class="w-full bg-neutral-200 rounded-full h-1.5 mb-1">
            <div id="approval-bar" class="bg-yellow-400 h-1.5 rounded-full w-full"></div>
        </div>
        <p class="text-[10px] text-neutral-300 font-medium">Menutup otomatis...</p>
    </div>
</div>

<script>
const displayInput = document.getElementById('nominal_display');
const hiddenInput  = document.getElementById('nominal');

displayInput.addEventListener('input', function () {
    const raw = this.value.replace(/\./g, '').replace(/\D/g, '');
    hiddenInput.value = raw;
    this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
});

// ---- AJAX form submit ----
document.getElementById('topup-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const val = parseInt(hiddenInput.value) || 0;
    const errEl   = document.getElementById('nominal-error');
    const wrapEl  = document.getElementById('nominal-wrap');
    const btnText = document.getElementById('topup-btn-text');
    const spinner = document.getElementById('topup-spinner');
    const btn     = document.getElementById('topup-btn');

    // Client-side validation
    if (val < 5000) {
        errEl.textContent = 'Nominal minimal Rp 5.000';
        errEl.classList.remove('hidden');
        wrapEl.classList.add('border-red-300', 'bg-red-50');
        wrapEl.classList.remove('border-neutral-200');
        displayInput.focus();
        return;
    }

    errEl.classList.add('hidden');
    wrapEl.classList.remove('border-red-300', 'bg-red-50');
    wrapEl.classList.add('border-neutral-200');

    // Show loading state
    btn.disabled  = true;
    btnText.textContent = 'Membuat kode...';
    spinner.classList.remove('hidden');

    try {
        const formData = new FormData(this);
        const resp = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await resp.json();

        if (!resp.ok) {
            // Laravel validation error
            const msg = data.errors?.nominal?.[0] || 'Terjadi kesalahan. Coba lagi.';
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
            return;
        }

        // Populate modal
        document.getElementById('kode-text').textContent    = data.kode_virtual;
        document.getElementById('kode-nominal').textContent = 'Rp ' + parseInt(data.nominal).toLocaleString('id-ID');
        document.getElementById('kode-expired').textContent = data.expired_at;

        // Reset form
        displayInput.value = '';
        hiddenInput.value  = '';

        // Show modal
        openKodeModal();

        // Refresh riwayat section on background (optional simple reload after close)
    } catch (err) {
        errEl.textContent = 'Koneksi gagal. Periksa jaringan Anda.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Generate Kode Virtual';
        spinner.classList.add('hidden');
    }
});

function openKodeModal() {
    const modal = document.getElementById('kode-modal');
    modal.classList.remove('hidden');
    // reset copy label
    document.getElementById('copy-label').textContent = 'Salin Kode';
    // close on backdrop click
    modal.addEventListener('click', function backdropClose(ev) {
        if (ev.target === modal) {
            closeKodeModal();
            modal.removeEventListener('click', backdropClose);
        }
    });
}

function closeKodeModal() {
    document.getElementById('kode-modal').classList.add('hidden');
    // Reload to refresh riwayat list
    window.location.reload();
}

function copyKode() {
    const kode = document.getElementById('kode-text').textContent.trim();
    navigator.clipboard.writeText(kode).then(() => {
        const label = document.getElementById('copy-label');
        label.textContent = 'Tersalin! ✓';
        setTimeout(() => label.textContent = 'Salin Kode', 2000);
    });
}

// ---- Approval live notification ----
const seenApprovals = JSON.parse(localStorage.getItem('seen_approvals') || '[]');

function showApprovalPopup(nominal, saldo) {
    // Tutup kode-modal jika masih terbuka
    const kodeModal = document.getElementById('kode-modal');
    if (kodeModal) kodeModal.remove();

    const popup  = document.getElementById('approval-popup');
    const circle = document.getElementById('approval-circle');
    const bar    = document.getElementById('approval-bar');

    document.getElementById('approval-nominal').textContent = '+Rp ' + parseInt(nominal).toLocaleString('id-ID');
    document.getElementById('approval-saldo').textContent   = 'Rp '  + parseInt(saldo).toLocaleString('id-ID');

    popup.classList.remove('hidden');

    // Bounce-in animation
    requestAnimationFrame(() => requestAnimationFrame(() => {
        circle.classList.remove('scale-0', 'opacity-0');
        circle.classList.add('scale-110', 'opacity-100');
        setTimeout(() => { circle.classList.remove('scale-110'); circle.classList.add('scale-100'); }, 300);
    }));

    // Progress bar drains over 5 s
    bar.style.transition = 'none';
    bar.style.width = '100%';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        bar.style.transition = 'width 3s linear';
        bar.style.width = '0%';
    }));

    // Auto-close after 5 s then reload
    setTimeout(() => {
        circle.classList.add('scale-0', 'opacity-0');
        circle.classList.remove('scale-100');
        setTimeout(() => {
            popup.classList.add('hidden');
            window.location.reload();
        }, 400);
    }, 3000);
}

function pollApproval() {
    const params = seenApprovals.map(id => `seen[]=${id}`).join('&');
    fetch(`{{ route('topup.check-approval') }}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.approved) {
            seenApprovals.push(data.id);
            localStorage.setItem('seen_approvals', JSON.stringify(seenApprovals));
            clearInterval(pollInterval);
            showApprovalPopup(data.nominal, data.saldo);
        }
    })
    .catch(() => {});
}

let pollInterval = setInterval(pollApproval, 1500);
document.addEventListener('visibilitychange', () => {
    clearInterval(pollInterval);
    if (!document.hidden) pollInterval = setInterval(pollApproval, 1500);
});
</script>
@endsection
