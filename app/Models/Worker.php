<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $table = 'workers';

    protected $fillable = [
        'name',
        'role',
        'alamat',
        'latitude',
        'longitude',
        'phone',
        'skills',
        'tarif',
        'pengalaman',
        'rating',
        'poin',
        'umur',
        'status',
        'foto',
        'status_ketersediaan'
    ];

    public function reviews()
    {
        return $this->hasMany(WorkerReview::class, 'id_worker')->latest();
    }

    public function offers()
    {
        return $this->hasMany(WorkerOffer::class, 'id_worker');
    }
}
