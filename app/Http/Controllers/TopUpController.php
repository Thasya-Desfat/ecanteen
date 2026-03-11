<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\SaldoHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopUpController extends Controller
{
    /**
     * Show top-up page for siswa
     */
    public function index()
    {
        $topUps = Auth::user()->topUps()->latest()->get();
        return view('topup.index', compact('topUps'));
    }

    /**
     * Generate virtual code for top-up
     */
    public function generateCode(Request $request)
    {
        $request->validate([
            'nominal' => 'required|integer|min:5000',
        ]);

        $user = Auth::user();

        $topUp = TopUp::create([
            'user_id' => $user->id,
            'nominal' => $request->nominal,
            'kode_virtual' => TopUp::generateKodeVirtual(),
            'status' => 'pending',
            'expired_at' => Carbon::now()->addHours(24),
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'kode_virtual'   => $topUp->kode_virtual,
                'nominal'        => $topUp->nominal,
                'expired_at'     => $topUp->expired_at->format('d M Y H:i'),
            ]);
        }

        // Fallback: redirect back with session (non-JS)
        return back()
            ->with('kode_virtual', $topUp->kode_virtual)
            ->with('topup_nominal', $topUp->nominal)
            ->with('topup_expired', $topUp->expired_at->format('d M Y H:i'));
    }

    /**
     * Show validation page for admin toko
     */
    public function showValidationForm()
    {
        return view('topup.validate');
    }

    /**
     * Validate top-up code
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'kode_virtual' => 'required|string',
        ]);

        $topUp = TopUp::where('kode_virtual', $request->kode_virtual)
            ->where('status', 'pending')
            ->first();

        if (!$topUp) {
            return back()->withErrors(['error' => 'Kode virtual tidak ditemukan atau sudah digunakan.']);
        }

        // Check if expired
        if ($topUp->expired_at < Carbon::now()) {
            return back()->withErrors(['error' => 'Kode virtual sudah kadaluarsa.']);
        }

        // Process top-up with transaction
        DB::transaction(function () use ($topUp) {
            // Update top-up status
            $topUp->status = 'success';
            $topUp->save();

            // Add saldo to user
            $user = $topUp->user;
            $user->saldo += $topUp->nominal;
            $user->save();

            // Record saldo history
            SaldoHistory::create([
                'user_id' => $user->id,
                'jenis' => 'masuk',
                'nominal' => $topUp->nominal,
                'keterangan' => "Top-Up dengan kode {$topUp->kode_virtual}",
                'saldo_akhir' => $user->saldo,
            ]);
        });

        return back()->with('success', "Top-up berhasil! Saldo sebesar Rp " . number_format($topUp->nominal, 0, ',', '.') . " telah ditambahkan.");
    }

    /**
     * Show saldo history
     */
    public function history()
    {
        $histories = Auth::user()->saldoHistories()->latest()->paginate(20);
        return view('topup.history', compact('histories'));
    }

    /**
     * Polling: check if any pending top-up was recently approved (for live notification)
     */
    public function checkApproval(Request $request)
    {
        $seenIds = $request->query('seen', []);

        $approved = Auth::user()->topUps()
            ->where('status', 'success')
            ->where('updated_at', '>=', Carbon::now()->subMinutes(5))
            ->when(!empty($seenIds), fn($q) => $q->whereNotIn('id', $seenIds))
            ->first();

        if ($approved) {
            return response()->json([
                'approved' => true,
                'id'       => $approved->id,
                'nominal'  => $approved->nominal,
                'saldo'    => Auth::user()->fresh()->saldo,
            ]);
        }

        return response()->json(['approved' => false]);
    }
}
