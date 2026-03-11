@extends('layouts.app')

@section('title', 'Rekap Pendapatan - ' . $toko->nama_toko)

@section('content')
<div class="max-w-5xl mx-auto px-6 pb-12">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-[9px] font-black uppercase tracking-widest text-neutral-400">{{ $toko->nama_toko }}</p>
            <h1 class="font-black text-3xl tracking-tight">Rekap Pendapatan</h1>
            <p class="text-neutral-500 text-sm font-medium mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('penjual.dashboard') }}"
            class="inline-flex items-center gap-2 text-sm text-neutral-400 hover:text-black font-bold transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Dashboard
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-1">Hari Ini</p>
            <p class="text-xl font-black leading-tight">Rp {{ number_format($revenueToday, 0, ',', '.') }}</p>
            <p class="text-[10px] text-neutral-400 mt-1">{{ now()->isoFormat('D MMM Y') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-neutral-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-1">Bulan Ini</p>
            <p class="text-xl font-black leading-tight">Rp {{ number_format($revenueMonth, 0, ',', '.') }}</p>
            <p class="text-[10px] text-neutral-400 mt-1">{{ now()->isoFormat('MMMM Y') }}</p>
        </div>
        <div class="bg-yellow-400 rounded-3xl p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-yellow-800/70 mb-1">Tahun Ini</p>
            <p class="text-xl font-black leading-tight">Rp {{ number_format($revenueYear, 0, ',', '.') }}</p>
            <p class="text-[10px] text-yellow-800/60 mt-1">{{ now()->year }}</p>
        </div>
        <div class="bg-neutral-900 rounded-3xl p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-1">Total Semua</p>
            <p class="text-xl font-black leading-tight text-white">Rp {{ number_format($revenueTotal, 0, ',', '.') }}</p>
            <p class="text-[10px] text-neutral-500 mt-1">Sejak pertama</p>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-neutral-50 flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-black text-base">Grafik Pendapatan</h2>
            {{-- Tab Buttons --}}
            <div class="flex gap-1 bg-neutral-100 p-1 rounded-xl">
                <button onclick="showChart('harian')" id="tab-harian"
                    class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all bg-white text-black shadow-sm">
                    7 Hari
                </button>
                <button onclick="showChart('bulanan')" id="tab-bulanan"
                    class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all text-neutral-500 hover:text-neutral-800">
                    12 Bulan
                </button>
                <button onclick="showChart('tahunan')" id="tab-tahunan"
                    class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all text-neutral-500 hover:text-neutral-800">
                    Tahunan
                </button>
            </div>
        </div>
        <div class="p-6">

            {{-- Harian Chart --}}
            <div id="chart-harian">
                @php
                    $maxDaily = $dailyRevenue->max('revenue') ?: 1;
                @endphp
                @if($dailyRevenue->sum('revenue') == 0)
                <p class="text-center text-neutral-400 text-sm py-8">Belum ada pendapatan 7 hari terakhir.</p>
                @else
                <div class="space-y-3">
                    @foreach($dailyRevenue as $item)
                    <div class="flex items-center gap-3">
                        <p class="text-[11px] font-bold text-neutral-500 w-16 flex-shrink-0 text-right">
                            {{ $item['date']->isoFormat('ddd D/M') }}
                        </p>
                        <div class="flex-1 bg-neutral-100 rounded-full h-7 overflow-hidden">
                            <div class="bg-yellow-400 h-full rounded-full flex items-center px-2"
                                style="width: {{ max(($item['revenue'] / $maxDaily * 100), ($item['revenue'] > 0 ? 2 : 0)) }}%; min-width: {{ $item['revenue'] > 0 ? '2rem' : '0' }}; transition: width 0.6s ease;">
                                @if($item['revenue'] > 0)
                                <span class="text-[10px] font-black text-black/70 whitespace-nowrap overflow-hidden">
                                    Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @if($item['revenue'] == 0)
                        <p class="text-[11px] text-neutral-300 font-bold w-24 text-right">—</p>
                        @else
                        <p class="text-[11px] font-black w-24 text-right">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Bulanan Chart --}}
            <div id="chart-bulanan" class="hidden">
                @php
                    $maxMonthly = $monthlyRevenue->max('revenue') ?: 1;
                @endphp
                @if($monthlyRevenue->sum('revenue') == 0)
                <p class="text-center text-neutral-400 text-sm py-8">Belum ada pendapatan 12 bulan terakhir.</p>
                @else
                <div class="space-y-3">
                    @foreach($monthlyRevenue as $item)
                    <div class="flex items-center gap-3">
                        <p class="text-[11px] font-bold text-neutral-500 w-16 flex-shrink-0 text-right">
                            {{ $item['date']->isoFormat('MMM YY') }}
                        </p>
                        <div class="flex-1 bg-neutral-100 rounded-full h-7 overflow-hidden">
                            <div class="bg-yellow-400 h-full rounded-full flex items-center px-2"
                                style="width: {{ max(($item['revenue'] / $maxMonthly * 100), ($item['revenue'] > 0 ? 2 : 0)) }}%;">
                                @if($item['revenue'] > 0)
                                <span class="text-[10px] font-black text-black/70 whitespace-nowrap overflow-hidden">
                                    Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @if($item['revenue'] == 0)
                        <p class="text-[11px] text-neutral-300 font-bold w-24 text-right">—</p>
                        @else
                        <p class="text-[11px] font-black w-24 text-right">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Tahunan Chart --}}
            <div id="chart-tahunan" class="hidden">
                @php
                    $maxYearly = $yearlyRevenue->max('revenue') ?: 1;
                @endphp
                @if($yearlyRevenue->sum('revenue') == 0)
                <p class="text-center text-neutral-400 text-sm py-8">Belum ada data tahunan.</p>
                @else
                <div class="space-y-4">
                    @foreach($yearlyRevenue as $item)
                    <div class="flex items-center gap-3">
                        <p class="text-[11px] font-bold text-neutral-500 w-16 flex-shrink-0 text-right">
                            {{ $item['date']->year }}
                        </p>
                        <div class="flex-1 bg-neutral-100 rounded-full h-9 overflow-hidden">
                            <div class="bg-yellow-400 h-full rounded-full flex items-center px-3"
                                style="width: {{ max(($item['revenue'] / $maxYearly * 100), ($item['revenue'] > 0 ? 2 : 0)) }}%;">
                                @if($item['revenue'] > 0)
                                <span class="text-xs font-black text-black/70 whitespace-nowrap overflow-hidden">
                                    Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @if($item['revenue'] == 0)
                        <p class="text-[11px] text-neutral-300 font-bold w-24 text-right">—</p>
                        @else
                        <p class="text-[11px] font-black w-24 text-right">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Top Menu --}}
    @if($topMenus->count() > 0)
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-neutral-50">
            <h2 class="font-black text-base">Menu Terlaris</h2>
            <p class="text-xs text-neutral-400 mt-0.5">Berdasarkan total pendapatan dari pesanan selesai</p>
        </div>
        <div class="divide-y divide-neutral-50">
            @php $topRev = $topMenus->max('total_rev') ?: 1; $rank = 0; @endphp
            @foreach($topMenus as $item)
            @php $rank++ @endphp
            <div class="px-6 py-4 flex items-center gap-4">
                <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0
                    {{ $rank === 1 ? 'bg-yellow-400 text-black' : 'bg-neutral-100 text-neutral-500' }}">
                    {{ $rank }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-sm truncate">{{ $item->menu->nama_menu ?? '—' }}</p>
                    <div class="mt-1.5 bg-neutral-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-yellow-400 h-full rounded-full" style="width: {{ $item->total_rev / $topRev * 100 }}%"></div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-black">Rp {{ number_format($item->total_rev, 0, ',', '.') }}</p>
                    <p class="text-[10px] text-neutral-400">{{ $item->total_qty }}x terjual</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Rekap Pesanan Selesai --}}
    <div class="bg-white rounded-3xl border border-neutral-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-neutral-50 flex items-center justify-between">
            <div>
                <h2 class="font-black text-base">Pesanan Selesai</h2>
                <p class="text-xs text-neutral-400 mt-0.5">Total {{ $selesaiOrders->total() }} pesanan telah diselesaikan</p>
            </div>
        </div>

        @if($selesaiOrders->count() === 0)
        <div class="p-16 text-center">
            <p class="text-4xl mb-4">📋</p>
            <p class="font-black text-base mb-1">Belum ada pesanan selesai</p>
            <p class="text-neutral-400 text-sm">Pesanan yang sudah diambil akan muncul di sini.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-50">
                        <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Pesanan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Pembeli</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Item</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Waktu</th>
                        <th class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-widest text-neutral-400">Pendapatan</th>
                        <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-50">
                    @foreach($selesaiOrders as $order)
                    @php
                        $myDetails = $order->orderDetails->filter(fn($d) => $toko->menus->pluck('id')->contains($d->menu_id));
                        $myRevenue = $myDetails->sum('subtotal');
                    @endphp
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-black">#{{ $order->id }}</p>
                            <p class="text-[10px] text-neutral-400">{{ $order->created_at->isoFormat('D MMM Y') }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-semibold">{{ $order->user->name ?? 'Siswa' }}</p>
                            <p class="text-[10px] text-neutral-400">{{ $order->waktu_pengambilan }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="space-y-0.5">
                                @foreach($myDetails as $det)
                                <p class="text-xs text-neutral-600">
                                    <span class="font-semibold">{{ $det->menu->nama_menu ?? '—' }}</span>
                                    <span class="text-neutral-400">×{{ $det->quantity }}</span>
                                </p>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-xs text-neutral-500">{{ $order->created_at->isoFormat('HH:mm') }}</p>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <p class="text-sm font-black">Rp {{ number_format($myRevenue, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-neutral-500">{{ $order->updated_at->isoFormat('D MMM, HH:mm') }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($selesaiOrders->hasPages())
        <div class="px-6 py-4 border-t border-neutral-50">
            {{ $selesaiOrders->links() }}
        </div>
        @endif
        @endif
    </div>

</div>

@section('scripts')
<script>
function showChart(period) {
    ['harian', 'bulanan', 'tahunan'].forEach(function(p) {
        const chart = document.getElementById('chart-' + p);
        const tab   = document.getElementById('tab-' + p);
        if (p === period) {
            chart.classList.remove('hidden');
            tab.classList.add('bg-white', 'text-black', 'shadow-sm');
            tab.classList.remove('text-neutral-500', 'hover:text-neutral-800');
        } else {
            chart.classList.add('hidden');
            tab.classList.remove('bg-white', 'text-black', 'shadow-sm');
            tab.classList.add('text-neutral-500', 'hover:text-neutral-800');
        }
    });
}
</script>
@endsection

@endsection
