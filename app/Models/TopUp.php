<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    protected $fillable = [
        'user_id',
        'nominal',
        'kode_virtual',
        'status',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    /**
     * Get the user that owns this top up
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate unique virtual code
     */
    public static function generateKodeVirtual()
    {
        do {
            $code = 'CN-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (self::where('kode_virtual', $code)->exists());

        return $code;
    }
}
