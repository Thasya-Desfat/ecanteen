<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokoController extends Controller
{
    /**
     * Show create toko form
     */
    public function create()
    {
        $user = Auth::user();

        // Check if user already has a toko
        if ($user->toko) {
            return redirect()->route('admin-toko.dashboard')->with('error', 'Anda sudah memiliki toko.');
        }

        return view('toko.create');
    }

    /**
     * Store new toko
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user already has a toko
        if ($user->toko) {
            return redirect()->route('admin-toko.dashboard')->with('error', 'Anda sudah memiliki toko.');
        }

        $request->validate([
            'nama_toko' => 'required|string|max:255',
        ]);

        Toko::create([
            'nama_toko' => $request->nama_toko,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin-toko.dashboard')->with('success', 'Toko berhasil dibuat!');
    }

    /**
     * Show all tokos (for siswa to browse)
     */
    public function index()
    {
        $tokos = Toko::with('menus')->get();
        return view('toko.index', compact('tokos'));
    }

    /**
     * Show toko detail with menus
     */
    public function show(Toko $toko)
    {
        $menus = $toko->menus()->where('status', 'tersedia')->get();
        return view('toko.show', compact('toko', 'menus'));
    }
}
