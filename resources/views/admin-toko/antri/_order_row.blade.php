<div class="px-6 py-4">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="min-w-0">
            <p class="font-bold text-sm">Pesanan #{{ $order->id }}</p>
            <p class="text-xs text-neutral-500 mt-0.5">
                <span class="font-semibold">{{ $order->user->name ?? 'Siswa' }}</span>
                &mdash; {{ $order->waktu_pengambilan }}
                &mdash; <span class="text-neutral-400">{{ $order->created_at->format('d M, H:i') }}</span>
            </p>
            @if($order->catatan)
            <p class="text-xs mt-1.5 bg-yellow-50 border border-yellow-200 rounded-xl px-2.5 py-1.5 text-yellow-800 inline-block">
                <span class="font-black">Catatan:</span> {{ $order->catatan }}
            </p>
            @endif
        </div>
        {{-- Read-only total --}}
        <p class="text-sm font-black flex-shrink-0 text-neutral-600">
            Rp {{ number_format($order->total_harga, 0, ',', '.') }}
        </p>
    </div>

    {{-- Item breakdown grouped by toko --}}
    @php
        $grouped = $order->orderDetails->groupBy(fn($d) => $d->menu?->toko?->nama_toko ?? 'Toko Tidak Diketahui');
    @endphp
    <div class="bg-neutral-50 rounded-2xl p-3 space-y-2">
        @foreach($grouped as $namaToko => $details)
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-1">{{ $namaToko }}</p>
            @foreach($details as $detail)
            <div class="flex justify-between items-center text-xs py-0.5">
                <span class="text-neutral-600">
                    {{ $detail->menu->nama_menu ?? '-' }}
                    <span class="text-neutral-400">x{{ $detail->quantity }}</span>
                </span>
                <span class="font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
