<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoHistory extends Model
{
    protected $fillable = [
        'user_id',
        'jenis',
        'nominal',
        'keterangan',
        'saldo_akhir',
    ];

    /**
     * Get the user that owns this saldo history
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
