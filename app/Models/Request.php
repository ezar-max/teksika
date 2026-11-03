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
        'id_user', 'layanan', 'lokasi', 'urgensi', 'detail', 'status', 'status_admin',
        'tanggal', 'foto', 'jumlah_pembayaran', 'bayar_sekarang', 'metode_pembayaran'
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
