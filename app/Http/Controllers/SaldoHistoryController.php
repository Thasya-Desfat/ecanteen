<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaldoHistoryController extends Controller
{
    /**
     * Show paginated saldo mutation history for the authenticated user.
     */
    public function index()
    {
        $user      = Auth::user();
        $histories = $user->saldoHistories()->latest()->paginate(20);
        $saldo     = $user->saldo;

        return view('saldo.index', compact('histories', 'saldo'));
    }
}
