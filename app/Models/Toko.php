<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Toko extends Model
{
    protected $fillable = [
        'nama_toko',
        'user_id',
    ];

    /**
     * Get the owner (user with toko role) of this toko
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all menus for this toko
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'toko_id');
    }

    /**
     * Get all orders that have items from this toko
     */
    public function orders()
    {
        return Order::whereHas('orderDetails.menu', fn($q) => $q->where('toko_id', $this->id));
    }
}
