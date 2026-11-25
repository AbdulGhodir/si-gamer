<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_user');
    }

    public function handledBookings()
    {
        return $this->hasMany(Booking::class, 'id_staff');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class, 'id_user');
    }
}
