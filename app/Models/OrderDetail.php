<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'subtotal',
    ];

    /**
     * Get the order that owns this order detail
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the menu for this order detail
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
