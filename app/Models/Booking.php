<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'kode_booking',
        'id_user',
        'id_device',
        'id_staff',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'subtotal',
        'total_harga',
        'status',
        'status_konfirmasi',
        'jenis_booking',
        'diskon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'id_device');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'id_staff');
    }
}
