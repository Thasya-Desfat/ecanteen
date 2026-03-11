<?php

namespace App\Http\Controllers;

use App\Models\SaldoHistory;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    // ─── PENJUAL ─────────────────────────────────────────────────────────────

    public function index()
    {
        $user  = auth()->user();
        $toko  = $user->toko;
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('penjual.pencairan', compact('toko', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $toko = $user->toko;

        $request->validate([
            'jumlah'  => ['required', 'integer', 'min:10000'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'jumlah.min' => 'Minimal pencairan adalah Rp 10.000.',
        ]);

        if ($request->jumlah > $user->saldo) {
            return back()->withErrors(['jumlah' => 'Jumlah melebihi saldo yang tersedia.'])->withInput();
        }

        // Cek apakah masih ada request pending
        $hasPending = Withdrawal::where('user_id', $user->id)->where('status', 'pending')->exists();
        if ($hasPending) {
            return back()->with('error', 'Masih ada permintaan pencairan yang sedang menunggu persetujuan.');
        }

        Withdrawal::create([
            'user_id' => $user->id,
            'toko_id' => $toko->id,
            'jumlah'  => $request->jumlah,
            'catatan' => $request->catatan,
            'status'  => 'pending',
        ]);

        return back()->with('success', 'Permintaan pencairan berhasil dikirim. Menunggu persetujuan admin.');
    }

    // ─── ADMIN ───────────────────────────────────────────────────────────────

    public function adminIndex()
    {
        $pending   = Withdrawal::with(['user', 'toko'])->where('status', 'pending')->latest()->get();
        $processed = Withdrawal::with(['user', 'toko'])->whereIn('status', ['approved', 'rejected'])->latest()->paginate(15);

        return view('admin-toko.pencairan', compact('pending', 'processed'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $penjual = $withdrawal->user;

        if ($withdrawal->jumlah > $penjual->saldo) {
            return back()->with('error', 'Saldo penjual tidak mencukupi untuk pencairan ini.');
        }

        DB::transaction(function () use ($withdrawal, $penjual) {
            $saldoBaru = $penjual->saldo - $withdrawal->jumlah;

            $penjual->decrement('saldo', $withdrawal->jumlah);

            SaldoHistory::create([
                'user_id'    => $penjual->id,
                'jenis'      => 'keluar',
                'nominal'    => $withdrawal->jumlah,
                'keterangan' => 'Pencairan saldo disetujui admin',
                'saldo_akhir' => $saldoBaru,
            ]);

            $withdrawal->update(['status' => 'approved']);
        });

        return back()->with('success', 'Pencairan berhasil disetujui dan saldo penjual telah dikurangi.');
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $request->validate([
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $withdrawal->update([
            'status'      => 'rejected',
            'keterangan'  => $request->keterangan ?? 'Ditolak oleh admin.',
        ]);

        return back()->with('success', 'Permintaan pencairan telah ditolak.');
    }
}
