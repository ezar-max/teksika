<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan {{ $worker->name }} — TEKSIKA</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background:#f4f7fb; color:#172033; font-family:Arial,sans-serif; }
        .reviews-page { width:min(900px, calc(100% - 32px)); margin:40px auto; }
        .reviews-header, .review-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:22px; }
        .reviews-header { margin-bottom:18px; }
        .reviews-header h1 { margin:0 0 8px; font-size:1.55rem; }
        .reviews-rating { color:#b45309; font-weight:700; }
        .review-list { display:grid; gap:12px; }
        .review-card-top { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; color:#64748b; font-size:.88rem; }
        .review-stars { color:#eab308; margin:10px 0; }
        .review-text { margin:0; line-height:1.65; white-space:pre-wrap; }
        .review-empty { color:#64748b; }
        .reviews-footer { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:20px; }
        .reviews-back { color:#2563eb; font-weight:700; text-decoration:none; }
    </style>
</head>
<body>
    <main class="reviews-page">
        <a class="reviews-back" href="{{ url()->previous() }}"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <section class="reviews-header" style="margin-top:16px;">
            <h1>Ulasan untuk {{ $worker->name }}</h1>
            <div class="reviews-rating">
                <i class="fa-solid fa-star"></i>
                {{ $averageRating !== null ? number_format((float) $averageRating, 1) : 'Belum ada rating' }}
                @if($worker->reviews_count)
                    · {{ $worker->reviews_count }} ulasan
                @endif
            </div>
            <p style="margin:10px 0 0;color:#64748b;">Ulasan diberikan pelanggan setelah pekerjaan selesai.</p>
        </section>

        <section class="review-list" aria-label="Daftar ulasan">
            @forelse($reviews as $review)
                <article class="review-card">
                    <div class="review-card-top">
                        <strong>Pelanggan TEKSIKA</strong>
                        <span>{{ $review->created_at?->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="review-stars" aria-label="Rating {{ $review->rating }} dari 5">
                        @for($star = 1; $star <= 5; $star++)
                            <i class="fa-solid fa-star" style="{{ $star <= $review->rating ? '' : 'color:#cbd5e1;' }}"></i>
                        @endfor
                        <strong style="color:#334155;">{{ $review->rating }}/5</strong>
                    </div>
                    <p class="review-text">{{ $review->ulasan ?: 'Pelanggan memberikan rating tanpa komentar.' }}</p>
                    @if($review->request?->layanan)
                        <div style="margin-top:12px;color:#64748b;font-size:.84rem;">Layanan: {{ $review->request->layanan }}</div>
                    @endif
                </article>
            @empty
                <div class="review-card review-empty">Belum ada ulasan untuk teknisi ini.</div>
            @endforelse
        </section>

        @if($reviews->hasPages())
            <div class="reviews-footer">
                {{ $reviews->links() }}
            </div>
        @endif
    </main>
</body>
</html>
