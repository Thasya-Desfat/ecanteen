<div class="px-6 py-4 flex items-start justify-between gap-4">
    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2 flex-wrap mb-1">
            <p class="font-bold text-sm">Pesanan #{{ $order->id }}</p>
            <span class="text-[10px] font-bold text-neutral-400">{{ $order->created_at->diffForHumans() }}</span>
        </div>
        <p class="text-xs text-neutral-500">
            <span class="font-semibold">{{ $order->user->name ?? 'Siswa' }}</span>
            &mdash; {{ $order->waktu_pengambilan }}
        </p>
        {{-- Catatan --}}
        @if($order->catatan)
        <p class="text-xs mt-1 bg-yellow-50 border border-yellow-200 rounded-xl px-2.5 py-1.5 text-yellow-800 inline-block">
            <span class="font-black">Catatan:</span> {{ $order->catatan }}
        </p>
        @endif
        {{-- Items from this toko only --}}
        <div class="mt-2 space-y-0.5">
            @foreach($order->orderDetails->whereIn('menu_id', $toko->menus->pluck('id')) as $det)
            <p class="text-xs text-neutral-400">
                <span class="font-semibold text-neutral-600">{{ $det->menu->nama_menu ?? '-' }}</span>
                <span>x{{ $det->quantity }}</span>
                <span class="text-neutral-300 mx-1">&middot;</span>
                <span>Rp {{ number_format($det->subtotal, 0, ',', '.') }}</span>
            </p>
            @endforeach
        </div>
    </div>
    <form id="order-form-{{ $order->id }}" action="{{ route('penjual.orders.update-status', $order) }}" method="POST" class="flex-shrink-0 mt-0.5">
        @csrf
        <input type="hidden" name="status" value="{{ $nextStatus }}">
        @if($nextStatus === 'selesai')
        <button type="button"
            onclick="openSelesaiModal({{ $order->id }}, '{{ $order->user->name ?? 'Siswa' }}')"
            class="text-xs font-bold px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $btnClass }}">
            {{ $btnText }}
        </button>
        @else
        <button type="submit"
            class="text-xs font-bold px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $btnClass }}">
            {{ $btnText }}
        </button>
        @endif
    </form>
</div>
