<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_harga',
        'waktu_pengambilan',
        'catatan',
        'status',
        'payment_method',
        'payment_code',
    ];

    /**
     * Get the user that owns this order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all order details for this order
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Get unique tokos from this order
     */
    public function tokos()
    {
        return Toko::whereIn('id', function ($query) {
            $query->select('toko_id')
                ->from('menus')
                ->whereIn('id', function ($subQuery) {
                    $subQuery->select('menu_id')
                        ->from('order_details')
                        ->where('order_id', $this->id);
                });
        })->get();
    }
}
