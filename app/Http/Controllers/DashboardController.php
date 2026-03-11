<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin-toko.dashboard');
        }

        if ($user->isToko()) {
            return redirect()->route('penjual.dashboard');
        }

        return redirect()->route('menus.index');
    }
}
