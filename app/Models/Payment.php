<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'id_booking',
        'kode_transaksi',
        'metode_pembayaran',
        'jumlah',
        'status_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pembayaran' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }
}
