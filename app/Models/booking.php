<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking'; // tanpa "s"
    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'id_user', 'layanan', 'lokasi', 'tanggal_booking', 'harga', 'status'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Jika ada relasi ke worker, tambahkan:
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'id_worker');
    }
}