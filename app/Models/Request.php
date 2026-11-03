<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Request extends Model
{
    use HasFactory;

    protected $table = 'request';
    protected $primaryKey = 'id_request';
    protected $fillable = [
        'id_user', 'id_worker', 'layanan', 'lokasi', 'latitude', 'longitude',
        'kompleksitas', 'urgensi', 'detail', 'jarak_km', 'harga_layanan',
        'faktor_urgensi', 'biaya_jarak', 'estimasi_total', 'estimasi_durasi_menit',
        'skor_tukang', 'status', 'status_admin', 'tanggal', 'foto',
        'jumlah_pembayaran', 'bayar_sekarang', 'metode_pembayaran'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
    
    public function worker()
    {
        return $this->belongsTo(\App\Models\Worker::class, 'id_worker');
    }
}
