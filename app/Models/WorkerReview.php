<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerReview extends Model
{
    protected $table = 'worker_reviews';

    protected $fillable = ['id_request', 'id_worker', 'id_user', 'rating', 'ulasan'];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'id_worker');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'id_request', 'id_request');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}