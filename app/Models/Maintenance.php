<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'id_device',
        'keterangan',
        'waktu_mulai',
        'waktu_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'id_device');
    }
}