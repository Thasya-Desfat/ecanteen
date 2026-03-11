<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'toko_id',
        'nama_menu',
        'kategori',
        'harga',
        'foto',
        'status',
    ];

    /**
     * Get the toko that owns this menu
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    /**
     * Get all order details for this menu
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Check if menu is available
     */
    public function isAvailable()
    {
        return $this->status === 'tersedia';
    }
}
