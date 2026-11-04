<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerOffer extends Model
{
    protected $table = 'worker_offers';

    protected $fillable = [
        'id_request', 'id_worker', 'biaya_jasa_material', 'biaya_jasa_snapshot', 'biaya_material_snapshot',
        'harga_dasar_snapshot', 'biaya_urgensi_snapshot', 'biaya_kerusakan_snapshot',
        'biaya_kualifikasi_snapshot',
        'biaya_transportasi', 'total_estimasi',
        'jarak_km', 'estimasi_durasi_menit', 'rating_snapshot', 'pengalaman_snapshot',
        'jumlah_ulasan_snapshot', 'keahlian_snapshot', 'faktor_harga_snapshot', 'status',
        'dipilih_at', 'ditugaskan_at',
    ];

    protected $casts = [
        'dipilih_at' => 'datetime',
        'ditugaskan_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class, 'id_request', 'id_request');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'id_worker');
    }
}