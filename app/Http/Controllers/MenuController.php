<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\OrderDetail;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Show all tokos as cards
     */
    public function index()
    {
        $tokos = Toko::withCount(['menus' => function ($q) {
            $q->where('status', 'tersedia');
        }])->get();

        $menuLaris = OrderDetail::select(
            'menu_id',
            DB::raw('SUM(quantity) as total_terjual')
        )
            ->whereHas('order', fn($q) => $q->where('status', 'selesai'))
            ->with('menu.toko')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->limit(3)
            ->get();

        $allMenus = Menu::where('status', 'tersedia')->with('toko')->get();

        $allMenusData = $allMenus->map(fn($m) => [
            'id'        => $m->id,
            'name'      => $m->nama_menu,
            'kategori'  => $m->kategori ?? 'Lainnya',
            'harga'     => $m->harga,
            'foto'      => $m->foto,
            'toko_id'   => $m->toko->id ?? null,
            'toko_name' => $m->toko->nama_toko ?? '-',
            'search'    => strtolower($m->nama_menu . ' ' . ($m->toko->nama_toko ?? '') . ' ' . ($m->kategori ?? '')),
        ])->values();

        return view('menus.index', compact('tokos', 'menuLaris', 'allMenusData'));
    }

    /**
     * Show menus of a specific toko
     */
    public function byToko(Toko $toko)
    {
        $menus = $toko->menus()->where('status', 'tersedia')->get();
        return view('menus.toko', compact('toko', 'menus'));
    }

    /**
     * Show menu detail
     */
    public function show(Menu $menu)
    {
        $menu->load('toko');
        return view('menus.show', compact('menu'));
    }
}
