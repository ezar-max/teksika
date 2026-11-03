<!-- Partial: resources/views/admin/booking/_rekomendasi.blade.php -->
<div class="card-rekomendasi" style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:22px; margin-top:24px; box-shadow:0 2px 10px rgba(0,0,0,0.03);">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px; border-bottom:1px solid #f1f5f9; padding-bottom:14px;">
        <div>
            <h3 style="margin:0; font-size:1.15rem; color:#1e293b; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-award" style="color:#007bff;"></i>
                Rekomendasi Tukang Terbaik (Algoritma Cerdas)
            </h3>
            <p style="margin:4px 0 0 0; font-size:0.85rem; color:#64748b;">
                Peringkat dihitung otomatis berdasarkan <b>Haversine Distance</b> dan <b>Weighted Scoring</b> (Jarak, Rating, Jam Terbang & Beban Kerja).
            </p>
        </div>
        <div>
            <form action="{{ route('admin.booking.hitung-ulang', $booking->id_request) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; padding:8px 14px; border-radius:8px; font-weight:600; font-size:0.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-arrows-rotate"></i> Hitung Ulang Skor
                </button>
            </form>
        </div>
    </div>

    @if(isset($rekomendasi) && count($rekomendasi) > 0)
        <div style="display:flex; flex-direction:column; gap:14px;">
            @foreach($rekomendasi as $rek)
                @php
                    $isTop = $rek->ranking == 1;
                    $isAssigned = isset($booking->id_worker) && $booking->id_worker == $rek->id_worker;
                @endphp
                <div style="border:1.5px solid {{ $isTop ? '#93c5fd' : '#e2e8f0' }}; background:{{ $isTop ? '#f8faff' : '#ffffff' }}; border-radius:12px; padding:16px; position:relative; transition:all 0.2s;">
                    
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
                        
                        <!-- Info Profil Tukang -->
                        <div style="display:flex; gap:14px; align-items:center;">
                            <div style="width:48px; height:48px; border-radius:50%; background:{{ $isTop ? '#007bff' : '#64748b' }}; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem; flex-shrink:0;">
                                #{{ $rek->ranking }}
                            </div>
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <h4 style="margin:0; font-size:1.05rem; color:#0f172a;">{{ $rek->name }}</h4>
                                    @if($isTop)
                                        <span style="background:#dbeafe; color:#1d4ed8; font-size:0.75rem; font-weight:700; padding:2px 8px; border-radius:6px;">
                                            <i class="fa-solid fa-star"></i> Pilihan Utama
                                        </span>
                                    @endif
                                    @if($isAssigned)
                                        <span style="background:#dcfce7; color:#15803d; font-size:0.75rem; font-weight:700; padding:2px 8px; border-radius:6px;">
                                            <i class="fa-solid fa-check"></i> Sedang Ditugaskan
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:0.85rem; color:#64748b; margin-top:3px; display:flex; gap:12px; flex-wrap:wrap;">
                                    <span><i class="fa-solid fa-wrench" style="color:#007bff;"></i> Keahlian: <b>{{ $rek->skills }}</b></span>
                                    <span><i class="fa-solid fa-phone" style="color:#10b981;"></i> {{ $rek->phone ?? '-' }}</span>
                                    <span><i class="fa-solid fa-map-pin" style="color:#ef4444;"></i> Jarak: <b>{{ $rek->jarak_km }} km</b></span>
                                </div>
                            </div>
                        </div>

                        <!-- Skor & Tombol Aksi -->
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div style="text-align:right;">
                                <div style="font-size:0.78rem; color:#64748b; text-transform:uppercase; font-weight:600;">Skor Keseluruhan</div>
                                <div style="font-size:1.35rem; font-weight:800; color:{{ $isTop ? '#007bff' : '#334155' }};">
                                    {{ number_format($rek->skor_total, 3) }}
                                </div>
                            </div>

                            @if($booking->status_admin !== 'diterima' || !$isAssigned)
                                <form action="{{ route('admin.booking.tugaskan', $booking->id_request) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="id_worker" value="{{ $rek->id_worker }}">
                                    <button type="submit" style="background:#007bff; color:#fff; border:none; padding:10px 18px; border-radius:8px; font-weight:700; font-size:0.9rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background 0.2s;" onclick="return confirm('Tugaskan {{ $rek->name }} untuk pekerjaan ini?')">
                                        <i class="fa-solid fa-user-check"></i> Tugaskan
                                    </button>
                                </form>
                            @else
                                <span style="background:#f1f5f9; color:#475569; padding:8px 14px; border-radius:8px; font-weight:600; font-size:0.85rem;">
                                    Telah Ditugaskan
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Breakdown Detail 4 Parameter Algoritma -->
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:8px; margin-top:12px; padding-top:12px; border-top:1px dashed #e2e8f0; font-size:0.8rem; color:#475569;">
                        <div>
                            <span style="color:#64748b;">Jarak:</span> <b>{{ $rek->jarak_km }} km</b> 
                            <span style="color:#0284c7;">({{ number_format($rek->skor_jarak, 2) }})</span>
                        </div>
                        <div>
                            <span style="color:#64748b;">Rating:</span> <b><i class="fa-solid fa-star" style="color:#f59e0b;"></i> {{ number_format($rek->rating, 1) }}</b>
                            <span style="color:#0284c7;">({{ number_format($rek->skor_rating, 2) }})</span>
                        </div>
                        <div>
                            <span style="color:#64748b;">Pengalaman:</span> <b>{{ $rek->pengalaman }} thn</b>
                            <span style="color:#0284c7;">({{ number_format($rek->skor_pengalaman, 2) }})</span>
                        </div>
                        <div>
                            <span style="color:#64748b;">Skor Beban:</span> <b>{{ number_format($rek->skor_beban, 2) }}</b>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center; padding:30px 16px; background:#f8fafc; border-radius:10px; border:1px dashed #cbd5e1; color:#64748b;">
            <i class="fa-solid fa-person-circle-question" style="font-size:2.2rem; color:#94a3b8; margin-bottom:10px;"></i>
            <div style="font-weight:600; color:#334155; margin-bottom:4px;">Belum Ada Rekomendasi Tukang</div>
            <p style="font-size:0.85rem; margin:0 0 12px 0;">
                Tukang belum terdeteksi dalam radius 15 km atau keahlian belum cocok dengan layanan ini ({{ $booking->layanan }}).
            </p>
            <form action="{{ route('admin.booking.hitung-ulang', $booking->id_request) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:#007bff; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:0.85rem; cursor:pointer;">
                    <i class="fa-solid fa-calculator"></i> Coba Hitung Rekomendasi
                </button>
            </form>
        </div>
    @endif
</div>

