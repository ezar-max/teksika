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
        'jumlah_pembayaran', 'bayar_sekarang', 'metode_pembayaran',
        'tingkat_kerusakan', 'penawaran_keahlian', 'biaya_material', 'penawaran_catatan',
        'id_worker_estimasi', 'biaya_urgensi_estimasi', 'biaya_kerusakan_estimasi',
        'harga_dasar_estimasi', 'biaya_kualifikasi_estimasi',
        'biaya_additional_estimasi', 'poin_estimasi',
        'biaya_rating_estimasi', 'biaya_pengalaman_estimasi',
        'pengalaman_persen_estimasi', 'poin_persen_estimasi',
        'rating_estimasi', 'pengalaman_estimasi',
        'durasi_perjalanan_menit', 'penawaran_dikirim_at', 'persetujuan_user_at', 'verifikasi_admin_at',
        'id_penawaran_terpilih'
    ];

    protected $casts = [
        'penawaran_dikirim_at' => 'datetime',
        'persetujuan_user_at' => 'datetime',
        'verifikasi_admin_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
    
    public function worker()
    {
        return $this->belongsTo(\App\Models\Worker::class, 'id_worker');
    }

    public function preferredWorker()
    {
        return $this->belongsTo(\App\Models\Worker::class, 'id_worker_pilihan');
    }

    public function estimateWorker()
    {
        return $this->belongsTo(\App\Models\Worker::class, 'id_worker_estimasi');
    }

    public function offers()
    {
        return $this->hasMany(WorkerOffer::class, 'id_request', 'id_request');
    }

    public function selectedOffer()
    {
        return $this->belongsTo(WorkerOffer::class, 'id_penawaran_terpilih');
    }

    public function review()
    {
        return $this->hasOne(WorkerReview::class, 'id_request', 'id_request');
    }
}
