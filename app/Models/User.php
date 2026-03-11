<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'saldo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all orders for this user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all top ups for this user
     */
    public function topUps()
    {
        return $this->hasMany(TopUp::class);
    }

    /**
     * Get all saldo histories for this user
     */
    public function saldoHistories()
    {
        return $this->hasMany(SaldoHistory::class);
    }

    /**
     * Get the toko owned by this user (penjual/toko role)
     */
    public function toko()
    {
        return $this->hasOne(Toko::class);
    }

    /**
     * Check if user is a regular user (siswa/pembeli)
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /** Alias: siswa check */
    public function isSiswa()
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is admin (bendahara)
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a toko owner (penjual)
     */
    public function isToko()
    {
        return $this->role === 'toko';
    }
}
