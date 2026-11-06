<?php

namespace App\Http\Controllers;

use App\Models\Worker;

class WorkerReviewController extends Controller
{
    public function index(int $id)
    {
        $worker = Worker::withCount('reviews')->findOrFail($id);
        $averageRating = $worker->reviews()->avg('rating');
        $reviews = $worker->reviews()
            ->with('request:id_request,layanan')
            ->paginate(10);

        return view('worker-reviews.index', compact('worker', 'reviews', 'averageRating'));
    }
}
