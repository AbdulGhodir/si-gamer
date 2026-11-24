<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'nama_device',
        'tipe',
        'harga_per_jam',
        'status',
        'spesifikasi',
        'kategori',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_device');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'id_device');
    }
}