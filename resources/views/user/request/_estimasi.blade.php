<!-- Partial: resources/views/user/request/_estimasi.blade.php -->
<div class="algorithm-estimasi-wrapper" style="margin-bottom: 24px;">
    
    <!-- Hidden inputs for coordinates -->
    <input type="hidden" name="latitude" id="latInput" value="{{ old('latitude', '-6.2000') }}">
    <input type="hidden" name="longitude" id="lonInput" value="{{ old('longitude', '106.8000') }}">

    <!-- Dropdown Tingkat Pekerjaan (Kompleksitas) -->
    <div class="form-group-item" style="margin-bottom: 16px;">
        <label for="kompleksitas" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
            <i class="fa-solid fa-layer-group" style="color: #007bff; margin-right: 6px;"></i>
            Tingkat Kesulitan Pekerjaan
        </label>
        <select name="kompleksitas" id="kompleksitas" onchange="triggerEstimasi()" style="width: 100%; padding: 12px 14px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1f2937;">
            <option value="rendah" {{ old('kompleksitas') == 'rendah' ? 'selected' : '' }}>Ringan (Kerusakan kecil / perawatan minor)</option>
            <option value="sedang" {{ old('kompleksitas', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang (Perbaikan standar / penggantian sparepart)</option>
            <option value="tinggi" {{ old('kompleksitas') == 'tinggi' ? 'selected' : '' }}>Berat (Bongkar pasang besar / instalasi baru)</option>
        </select>
    </div>

    <!-- Tombol & Status Deteksi Lokasi GPS -->
    <div class="location-picker-box" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: 14px; margin-bottom: 18px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b;">
                    <i class="fa-solid fa-map-pin" style="color: #ef4444; margin-right: 6px;"></i>
                    Titik Koordinat Lokasi
                </div>
                <div id="locationStatusText" style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                    Koordinat aktif: <span id="coordDisplay">-6.2000, 106.8000</span>
                </div>
            </div>
            <button type="button" id="btnGetLocation" onclick="detectUserLocation()" style="background: #007bff; color: #fff; border: none; padding: 9px 15px; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: background 0.2s;">
                <i class="fa-solid fa-location-crosshairs"></i>
                <span>Gunakan Lokasi Saya</span>
            </button>
        </div>
        <div id="locationNotice" style="margin-top: 8px; font-size: 0.8rem; color: #94a3b8; display: none;"></div>
    </div>

    <!-- Container Pesan Error Radius (Jika Tidak Ada Tukang) -->
    <div id="estimasiErrorBox" style="display: none; background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; color: #991b1b; font-size: 0.9rem;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.25rem; color: #ef4444;"></i>
            <span id="estimasiErrorMessage">Belum ada tukang yang tersedia dalam radius 15 km dari lokasi Anda.</span>
        </div>
    </div>

    <!-- Kartu Hasil Estimasi Harga & Durasi -->
    <div id="estimasiCard" style="display: none; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 18px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dcfce7; padding-bottom: 10px; margin-bottom: 12px;">
            <span style="font-weight: 700; color: #166534; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator" style="color: #22c55e;"></i>
                Estimasi Transparan (Sistem Cerdas)
            </span>
            <span id="badgeTukang" style="background: #dcfce7; color: #15803d; font-size: 0.8rem; font-weight: 600; padding: 3px 8px; border-radius: 6px;">
                Tukang Terdekat: <b id="valNamaTukang">-</b> (<span id="valJarakKm">0 km</span>)
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 14px;">
            <div style="background: #fff; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.78rem; color: #64748b; text-transform: uppercase;">Harga Layanan</div>
                <div id="valHargaLayanan" style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-top: 2px;">Rp 0</div>
            </div>
            <div style="background: #fff; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.78rem; color: #64748b; text-transform: uppercase;">Faktor Urgensi</div>
                <div id="valFaktorUrgensi" style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-top: 2px;">x1.00</div>
            </div>
            <div style="background: #fff; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.78rem; color: #64748b; text-transform: uppercase;">Biaya Jarak</div>
                <div id="valBiayaJarak" style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-top: 2px;">Rp 0 (Gratis)</div>
            </div>
            <div style="background: #fff; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.78rem; color: #64748b; text-transform: uppercase;">Estimasi Durasi</div>
                <div id="valDurasi" style="font-size: 1.05rem; font-weight: 700; color: #0284c7; margin-top: 2px;">0 menit</div>
                <small id="valDurasiDetail" style="font-size: 0.72rem; color: #64748b;">(0 mnt kerja + 0 mnt tempuh)</small>
            </div>
        </div>

        <div style="background: #15803d; color: #fff; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600; font-size: 0.95rem;">Total Estimasi Biaya</span>
            <span id="valTotalEstimasi" style="font-size: 1.35rem; font-weight: 800;">Rp 0</span>
        </div>
    </div>
</div>

<script>
// Format angka ke format Rupiah
function formatUang(angka) {
    return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
}

// Deteksi Lokasi Pengguna via Geolocation API
function detectUserLocation() {
    const btn = document.getElementById('btnGetLocation');
    const notice = document.getElementById('locationNotice');
    const display = document.getElementById('coordDisplay');

    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi...';
    if (notice) {
        notice.style.display = 'block';
        notice.innerText = 'Sedang mencari koordinat GPS presisi...';
    }

    navigator.geolocation.getCurrentPosition(
        function (position) {
            const lat = position.coords.latitude.toFixed(7);
            const lon = position.coords.longitude.toFixed(7);

            document.getElementById('latInput').value = lat;
            document.getElementById('lonInput').value = lon;
            display.innerText = `${lat}, ${lon}`;

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Lokasi Terkunci';
            btn.style.background = '#10b981';

            if (notice) {
                notice.innerText = 'Koordinat berhasil diperbarui dari GPS perangkat.';
            }

            // Panggil API estimasi otomatis
            triggerEstimasi();
        },
        function (err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Gunakan Lokasi Saya';
            if (notice) {
                notice.style.display = 'block';
                notice.innerText = 'Gagal mengakses GPS (' + err.message + '). Menggunakan koordinat default.';
            }
            // Tetap jalankan estimasi dengan default koordinat
            triggerEstimasi();
        },
        { timeout: 10000, enableHighAccuracy: true }
    );
}

// Request AJAX untuk kalkulasi estimasi ke server
let estimasiDebounce = null;
function triggerEstimasi() {
    clearTimeout(estimasiDebounce);
    estimasiDebounce = setTimeout(runEstimasiAjax, 300);
}

function runEstimasiAjax() {
    const layananEl = document.getElementById('layananInput') || document.getElementById('id_service') || document.querySelector('[name="layanan"]');
    const layanan = layananEl ? layananEl.value : '';

    const lat = document.getElementById('latInput').value;
    const lon = document.getElementById('lonInput').value;
    const kompleksitas = document.getElementById('kompleksitas') ? document.getElementById('kompleksitas').value : 'sedang';

    // Dapatkan urgensi dari radio button atau select
    let urgensi = 'rendah';
    const checkedUrgensi = document.querySelector('input[name="urgensi"]:checked');
    if (checkedUrgensi) {
        urgensi = checkedUrgensi.value;
    } else {
        const selectUrgensi = document.getElementById('urgensi');
        if (selectUrgensi) urgensi = selectUrgensi.value;
    }

    if (!layanan || !lat || !lon) {
        return;
    }

    const card = document.getElementById('estimasiCard');
    const errBox = document.getElementById('estimasiErrorBox');
    const errMsg = document.getElementById('estimasiErrorMessage');

    const csrfToken = document.querySelector('meta[name="csrf-token"]') 
        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
        : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

    fetch("{{ route('user.request.hitung') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            layanan: layanan,
            latitude: lat,
            longitude: lon,
            kompleksitas: kompleksitas,
            urgensi: urgensi
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            // Sembunyikan kartu, tampilkan pesan error radius
            if (card) card.style.display = 'none';
            if (errBox) {
                errBox.style.display = 'block';
                errMsg.innerText = data.message || 'Belum ada tukang yang tersedia dalam radius 15 km dari lokasi Anda.';
            }
        } else {
            // Tampilkan kartu estimasi
            if (errBox) errBox.style.display = 'none';
            if (card) card.style.display = 'block';

            const est = data.estimasi;
            const tukang = data.tukang_terdekat;

            document.getElementById('valNamaTukang').innerText = tukang.name || 'Tukang Mitra';
            document.getElementById('valJarakKm').innerText = est.jarak_km + ' km';
            document.getElementById('valHargaLayanan').innerText = formatUang(est.harga_layanan);
            document.getElementById('valFaktorUrgensi').innerText = 'x' + Number(est.faktor_urgensi).toFixed(2);
            document.getElementById('valBiayaJarak').innerText = est.biaya_jarak > 0 ? formatUang(est.biaya_jarak) : 'Rp 0 (Gratis)';
            document.getElementById('valDurasi').innerText = est.estimasi_durasi_menit + ' menit';
            document.getElementById('valDurasiDetail').innerText = `(${est.durasi_kerja_menit} mnt kerja + ${est.durasi_tempuh_menit} mnt tempuh)`;
            document.getElementById('valTotalEstimasi').innerText = formatUang(est.estimasi_total);
        }
    })
    .catch(err => {
        console.error("Error estimasi:", err);
    });
}

// Jalankan estimasi awal saat halaman selesai dimuat jika layanan sudah terpilih
document.addEventListener('DOMContentLoaded', function () {
    const layananEl = document.getElementById('layananInput') || document.querySelector('[name="layanan"]');
    if (layananEl && layananEl.value) {
        triggerEstimasi();
    }
});
</script>

