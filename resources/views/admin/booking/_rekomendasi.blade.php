<!-- Partial: resources/views/admin/booking/_rekomendasi.blade.php -->
<div class="card-rekomendasi" style="background:#ffffff; border-radius:18px; border:1px solid #e2e8f0; padding:26px 28px; margin-top:24px; box-shadow:0 2px 12px rgba(15, 23, 42, 0.04); transition:all 0.2s ease;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px; border-bottom:1px solid #f1f5f9; padding-bottom:16px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#eff6ff; color:#007bff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; border:1px solid #bfdbfe;">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#0f172a; letter-spacing:-0.01em;">
                    Rekomendasi Tukang Berdasarkan Rating
                </h3>
                <p style="margin:3px 0 0 0; font-size:0.85rem; color:#64748b;">
                    Tukang yang cocok dengan layanan diurutkan dari rating tertinggi. Jarak kantor tetap digunakan sebagai batas wilayah layanan.
                </p>
            </div>
        </div>
        <div>
            <form action="{{ route('admin.booking.hitung-ulang', $booking->id_request) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:#f1f5f9; color:#1e293b; border:1.5px solid #cbd5e1; padding:9px 16px; border-radius:10px; font-weight:700; font-size:0.86rem; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:all 0.2s ease;" onmouseover="this.style.background='#e2e8f0'; this.style.borderColor='#94a3b8';" onmouseout="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';">
                    <i class="fa-solid fa-arrows-rotate"></i> Perbarui Rekomendasi
                </button>
            </form>
        </div>
    </div>

    @if(isset($rekomendasi) && count($rekomendasi) > 0)
        <div style="display:flex; flex-direction:column; gap:14px;">
            @foreach($rekomendasi as $rek)
                @php
                    $isTop = $loop->first;
                    $isAssigned = isset($booking->id_worker) && $booking->id_worker == $rek->id_worker;
                @endphp
                <div style="border:1.5px solid {{ $isTop ? '#93c5fd' : '#e2e8f0' }}; background:{{ $isTop ? '#f8faff' : '#ffffff' }}; border-radius:14px; padding:18px 20px; position:relative; transition:all 0.2s ease; box-shadow:0 2px 8px rgba(0,0,0,0.02);">
                    
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
                        
                        <!-- Info Profil Tukang -->
                        <div style="display:flex; gap:14px; align-items:center;">
                            <div style="width:48px; height:48px; border-radius:50%; background:{{ $isTop ? 'linear-gradient(135deg, #007bff 0%, #005ecf 100%)' : '#64748b' }}; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem; flex-shrink:0; box-shadow:0 4px 10px rgba(0, 123, 255, 0.25);">
                                #{{ $loop->iteration }}
                            </div>
                            <div>
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <h4 style="margin:0; font-size:1.05rem; font-weight:800; color:#0f172a;">{{ $rek->name }}</h4>
                                    @if($isTop)
                                        <span style="background:#dbeafe; color:#1d4ed8; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:999px; border:1px solid #bfdbfe; display:inline-flex; align-items:center; gap:4px;">
                                            <i class="fa-solid fa-crown" style="color:#f59e0b;"></i> Pilihan Utama
                                        </span>
                                    @endif
                                    @if($isAssigned)
                                        <span style="background:#dcfce7; color:#15803d; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:999px; border:1px solid #86efac; display:inline-flex; align-items:center; gap:4px;">
                                            <i class="fa-solid fa-check"></i> Sedang Ditugaskan
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:0.85rem; color:#64748b; margin-top:4px; display:flex; gap:14px; flex-wrap:wrap;">
                                    <span><i class="fa-solid fa-wrench" style="color:#007bff;"></i> <b>{{ $rek->skills }}</b></span>
                                    <span><i class="fa-solid fa-phone" style="color:#10b981;"></i> {{ $rek->phone ?? '-' }}</span>
                                    <span><i class="fa-solid fa-map-pin" style="color:#ef4444;"></i> Jarak: <b>{{ $rek->jarak_km }} km</b></span>
                                </div>
                            </div>
                        </div>

                        <!-- Skor & Tombol Aksi -->
                        <div style="display:flex; align-items:center; gap:18px;">
                            <div style="text-align:right;">
                                <div style="font-size:0.76rem; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:0.04em;">Rating Tukang</div>
                                <div style="font-size:1.4rem; font-weight:800; color:{{ $isTop ? '#007bff' : '#0f172a' }};">
                                    {{ number_format($rek->skor_rating * 5, 1) }} <span style="font-size:.85rem;">/ 5</span>
                                </div>
                            </div>

                            @if($booking->status_admin === 'diterima' && $isAssigned)
                                <span style="background:#f1f5f9; color:#475569; padding:8px 16px; border-radius:10px; font-weight:700; font-size:0.85rem; border:1px solid #cbd5e1;">
                                    Telah Ditugaskan
                                </span>
                            @else
                                <span style="background:#fffbeb; color:#92400e; padding:8px 16px; border-radius:10px; font-weight:700; font-size:0.85rem; border:1px solid #fde68a;">
                                    Urutan berdasarkan rating
                                </span>
                            @endif
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:10px; margin-top:14px; padding-top:14px; border-top:1px dashed #e2e8f0; font-size:0.82rem; color:#475569;">
                        <div style="background:#f8fafc; padding:6px 12px; border-radius:8px; border:1px solid #e2e8f0;">
                            <span style="color:#64748b;">Rating:</span> <b><i class="fa-solid fa-star" style="color:#f59e0b;"></i> {{ number_format($rek->skor_rating * 5, 1) }} / 5</b>
                        </div>
                        <div style="background:#f8fafc; padding:6px 12px; border-radius:8px; border:1px solid #e2e8f0;">
                            <span style="color:#64748b;">Pengalaman:</span> <b>{{ $rek->pengalaman }} thn</b>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center; padding:36px 20px; background:#f8fafc; border-radius:14px; border:1.5px dashed #cbd5e1; color:#64748b;">
            <i class="fa-solid fa-person-circle-question" style="font-size:2.4rem; color:#94a3b8; margin-bottom:12px;"></i>
            <div style="font-weight:700; color:#1e293b; font-size:1.05rem; margin-bottom:4px;">Belum Ada Rekomendasi Tukang</div>
            <p style="font-size:0.88rem; margin:0 0 16px 0; color:#64748b;">
                Tukang belum terdeteksi dalam radius 15 km atau keahlian belum cocok dengan layanan ini (<b>{{ $booking->layanan }}</b>).
            </p>
            <form action="{{ route('admin.booking.hitung-ulang', $booking->id_request) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:linear-gradient(135deg, #007bff 0%, #005ecf 100%); color:#fff; border:none; padding:10px 22px; border-radius:10px; font-weight:700; font-size:0.9rem; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(0, 123, 255, 0.25);">
                    <i class="fa-solid fa-calculator"></i> Coba Hitung Rekomendasi
                </button>
            </form>
        </div>
    @endif
</div>
