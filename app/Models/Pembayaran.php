<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pembayaran',
        'jumlah',
        'metode_pembayaran',
        'nama_pembayar',
        'email_pembayar',
        'keterangan',
        'status',
        'user_id',
    ];

    // Generate kode pembayaran unik
    public static function generateKode()
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
}