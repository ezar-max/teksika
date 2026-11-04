<!-- Partial: resources/views/user/request/_estimasi.blade.php -->
<div class="algorithm-estimasi-wrapper">
    @php
        $savedLat = old('latitude', (isset($user) && !empty($user->latitude)) ? $user->latitude : (auth()->user()->latitude ?? '-6.4239'));
        $savedLon = old('longitude', (isset($user) && !empty($user->longitude)) ? $user->longitude : (auth()->user()->longitude ?? '106.9401'));
    @endphp
    <!-- Hidden inputs for coordinates -->
    <input type="hidden" name="latitude" id="latInput" value="{{ $savedLat }}">
    <input type="hidden" name="longitude" id="lonInput" value="{{ $savedLon }}">

    <!-- Tombol & Status Deteksi Lokasi GPS -->
    <div class="location-status-bar">
        <div class="location-status-left">
            <i class="fa-solid fa-map-pin" style="color: #ef4444; margin-right: 4px;"></i>
            <strong>Koordinat:</strong> <span id="coordDisplay">{{ $savedLat }}, {{ $savedLon }}</span>
        </div>
        <button type="button" id="btnGetLocation" class="btn-get-location" onclick="detectUserLocation()">
            <i class="fa-solid fa-location-crosshairs"></i>
            <span>Gunakan Lokasi Saya</span>
        </button>
    </div>
    <div id="locationNotice" style="display:none; font-size:0.8rem; color:#0284c7; background:#e0f2fe; padding:6px 12px; border-radius:8px; margin-bottom:10px;"></div>

    <!-- Peta Leaflet Interaktif -->
    <div id="requestMap"></div>

    <!-- Info Rute OSRM -->
    <div class="route-status-pill" id="routeStatus">
        <i class="fa-solid fa-route"></i>
        <span>Isi alamat di bawah atau pilih titik rumah pada peta (Titik awal: Kantor Pusat TEKSIKA).</span>
    </div>
</div>

<script>
const officePoint = [-6.4239, 106.9401];
let requestMap;
let homeMarker;
let routeLine;
window.currentRouteDistanceKm = null;
window.currentRouteUnavailable = false;
let addressLookupDebounce = null;
let addressLookupSequence = 0;
let addressGeocodeDebounce = null;
let addressGeocodeSequence = 0;
let lastAddressGeocodeKey = '';

function syncLocationAddress(options = {}) {
    clearTimeout(addressLookupDebounce);
    addressLookupSequence++;
    const jalan = document.getElementById('alamatJalan') ? document.getElementById('alamatJalan').value.trim() : '';
    const rt = document.getElementById('alamatRt') ? document.getElementById('alamatRt').value.trim() : '';
    const rw = document.getElementById('alamatRw') ? document.getElementById('alamatRw').value.trim() : '';
    const kecamatan = document.getElementById('alamatKecamatan') ? document.getElementById('alamatKecamatan').value.trim() : '';
    const kabupaten = document.getElementById('alamatKabupaten') ? document.getElementById('alamatKabupaten').value.trim() : '';
    const parts = [];

    if (jalan) parts.push(jalan);
    if (rt || rw) parts.push([rt ? `RT ${rt}` : '', rw ? `RW ${rw}` : ''].filter(Boolean).join('/'));
    if (kecamatan) parts.push(`Kecamatan ${kecamatan}`);
    if (kabupaten) parts.push(kabupaten);

    const lokasiInput = document.getElementById('lokasiInput');
    if (lokasiInput) lokasiInput.value = parts.join(', ');

    if (options.lookupMap !== false) lookupCoordinatesFromAddress(jalan, kecamatan, kabupaten);
}

function lookupCoordinatesFromAddress(jalan, kecamatan, kabupaten) {
    const status = document.getElementById('addressStatus');

    if (!jalan || !kecamatan || !kabupaten) {
        clearTimeout(addressGeocodeDebounce);
        addressGeocodeSequence++;
        lastAddressGeocodeKey = '';
        if (status) status.innerText = 'Lengkapi kecamatan, kota/kabupaten, dan jalan untuk mencari titik pada peta.';
        return;
    }

    const addressKey = [jalan, kecamatan, kabupaten].join('|').toLowerCase();
    if (addressKey === lastAddressGeocodeKey) return;
    clearTimeout(addressGeocodeDebounce);
    const sequence = ++addressGeocodeSequence;
    lastAddressGeocodeKey = addressKey;
    if (status) status.innerText = 'Mencari titik lokasi dari alamat...';

    addressGeocodeDebounce = setTimeout(function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

        fetch("{{ route('user.request.lokasi-alamat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ jalan: jalan, kecamatan: kecamatan, kabupaten: kabupaten })
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Titik alamat belum ditemukan.');
            return response.json();
        })
        .then(function (data) {
            if (sequence !== addressGeocodeSequence || !data.success) return;
            setHomeLocation({
                lat: data.location.latitude,
                lng: data.location.longitude
            }, { clearAddress: false, lookupAddress: false, zoom: 16 });
            if (status) {
                const provider = data.location.provider === 'photon' ? 'Photon OpenStreetMap' : 'OpenStreetMap';
                status.innerText = `Titik ditemukan lewat ${provider}. Geser pin pada peta bila perlu menyesuaikan posisi rumah.`;
            }
        })
        .catch(function () {
            if (sequence === addressGeocodeSequence && status) {
                lastAddressGeocodeKey = '';
                status.innerText = 'Titik alamat belum ditemukan. Coba lengkapi nama jalan atau geser pin pada peta.';
            }
        });
    }, 1200);
}

function lookupAddressFromCoordinates(latitude, longitude) {
    clearTimeout(addressLookupDebounce);
    const sequence = ++addressLookupSequence;
    const status = document.getElementById('addressStatus');
    if (status) status.innerText = 'Mencari jalan, kecamatan, dan kabupaten/kota dari titik peta...';

    addressLookupDebounce = setTimeout(function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]') 
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

        fetch("{{ route('user.request.alamat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ latitude: latitude, longitude: longitude })
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Alamat otomatis tidak tersedia.');
            return response.json();
        })
        .then(function (data) {
            if (sequence !== addressLookupSequence || !data.success) return;
            const address = data.address;
            if (document.getElementById('alamatKabupaten')) document.getElementById('alamatKabupaten').value = address.kabupaten || '';
            if (document.getElementById('alamatKecamatan')) document.getElementById('alamatKecamatan').value = address.kecamatan || '';
            if (document.getElementById('alamatJalan')) document.getElementById('alamatJalan').value = address.jalan || '';
            if (document.getElementById('alamatRt')) document.getElementById('alamatRt').value = address.rt || '';
            if (document.getElementById('alamatRw')) document.getElementById('alamatRw').value = address.rw || '';
            syncLocationAddress({ lookupMap: false });
            if (status) {
                status.innerText = address.rt && address.rw
                    ? 'Alamat lengkap termasuk RT/RW ditemukan dari OpenStreetMap.'
                    : 'Alamat wilayah ditemukan dari OpenStreetMap. RT/RW dapat dilengkapi manual.';
            }
        })
        .catch(function () {
            if (sequence === addressLookupSequence && status) {
                status.innerText = 'Alamat otomatis tidak tersedia. Isi rincian alamat pada kolom di bawah.';
            }
        });
    }, 1200);
}

function initializeRequestMap() {
    const lat = Number(document.getElementById('latInput').value);
    const lon = Number(document.getElementById('lonInput').value);
    const homePoint = Number.isFinite(lat) && Number.isFinite(lon) ? [lat, lon] : officePoint;

    requestMap = L.map('requestMap').setView(homePoint, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        updateWhenZooming: false,
        updateWhenIdle: true,
        keepBuffer: 1,
        attribution: '&copy; OpenStreetMap'
    }).addTo(requestMap);

    L.circleMarker(officePoint, {
        radius: 8,
        color: '#166534',
        fillColor: '#22c55e',
        fillOpacity: 1
    }).bindPopup('Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri').addTo(requestMap);

    homeMarker = L.marker(homePoint, { draggable: true })
        .bindPopup('Lokasi rumah Anda')
        .addTo(requestMap);

    requestMap.on('click', function (event) {
        setHomeLocation(event.latlng);
    });
    homeMarker.on('dragend', function (event) {
        setHomeLocation(event.target.getLatLng());
    });

    window.setTimeout(function () { 
        if (requestMap) requestMap.invalidateSize(); 
    }, 300);
}

function setHomeLocation(point, options = {}) {
    const lat = Number(point.lat).toFixed(7);
    const lon = Number(point.lng).toFixed(7);
    if (routeLine) requestMap.removeLayer(routeLine);
    routeLine = null;
    window.currentRouteDistanceKm = null;
    window.currentRouteUnavailable = false;
    homeMarker.setLatLng([lat, lon]);
    document.getElementById('latInput').value = lat;
    document.getElementById('lonInput').value = lon;
    document.getElementById('coordDisplay').innerText = `${lat}, ${lon}`;

    if (options.clearAddress !== false) {
        clearTimeout(addressGeocodeDebounce);
        addressGeocodeSequence++;
        lastAddressGeocodeKey = '';
        ['alamatKabupaten', 'alamatKecamatan', 'alamatJalan', 'alamatRt', 'alamatRw'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        syncLocationAddress({ lookupMap: false });
    }
    if (requestMap && options.zoom) requestMap.setView([lat, lon], options.zoom);
    if (options.lookupAddress !== false) lookupAddressFromCoordinates(lat, lon);
    if (typeof updatePricingBreakdown === 'function') updatePricingBreakdown();
    const routeStatus = document.getElementById('routeStatus');
    if (routeStatus) {
        routeStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Lokasi diperbarui. Menghitung rute perjalanan dari kantor pusat...</span>';
    }
    triggerEstimasi();
}

function renderOsrmRoute(route) {
    if (routeLine) requestMap.removeLayer(routeLine);
    const routePoints = route.geometry.map(function (coordinate) {
        return [coordinate[1], coordinate[0]];
    });
    routeLine = L.polyline(routePoints, { color: '#007bff', weight: 5, opacity: 0.85 }).addTo(requestMap);
    requestMap.fitBounds(routeLine.getBounds(), { padding: [24, 24] });
    const routeStatus = document.getElementById('routeStatus');
    if (routeStatus) {
        routeStatus.innerHTML = `<i class="fa-solid fa-route"></i> <span>Rute OSRM: <strong>${route.distance_km} km</strong> (sekitar ${route.duration_minutes} menit perjalanan). Titik awal: ${route.office.name}</span>`;
    }
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
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Mencari...</span>';
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
            if (display) display.innerText = `${lat}, ${lon}`;
            setHomeLocation({ lat: Number(lat), lng: Number(lon) });

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Lokasi Terkunci</span>';
            btn.style.background = '#10b981';

            if (notice) {
                notice.innerText = 'Lokasi GPS berhasil didapat. Alamat sedang dicari dari peta.';
            }
        },
        function (err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> <span>Gunakan Lokasi Saya</span>';
            if (notice) {
                notice.style.display = 'block';
                notice.innerText = 'Gagal mengakses GPS (' + err.message + '). Menggunakan koordinat default.';
            }
        },
        { timeout: 10000, enableHighAccuracy: true }
    );
}

// Request AJAX untuk kalkulasi estimasi ke server
let estimasiDebounce = null;
function triggerEstimasi() {
    clearTimeout(estimasiDebounce);
    estimasiDebounce = setTimeout(runEstimasiAjax, 1000);
}

function runEstimasiAjax() {
    const lat = document.getElementById('latInput').value;
    const lon = document.getElementById('lonInput').value;
    if (!lat || !lon) return;

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
            latitude: lat,
            longitude: lon
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            if (routeLine) requestMap.removeLayer(routeLine);
            routeLine = null;
            window.currentRouteDistanceKm = null;
            window.currentRouteUnavailable = true;
            if (typeof updatePricingBreakdown === 'function') updatePricingBreakdown();
            const routeStatus = document.getElementById('routeStatus');
            if (routeStatus) {
                routeStatus.innerHTML = `<i class="fa-solid fa-circle-info"></i> <span>${data.message || 'Rute jalan tidak dapat dihitung saat ini.'}</span>`;
            }
            return;
        }
        window.currentRouteDistanceKm = Number(data.route.distance_km);
        window.currentRouteUnavailable = false;
        if (typeof updatePricingBreakdown === 'function') updatePricingBreakdown();
        renderOsrmRoute(data.route);
    })
    .catch(err => {
        console.error("Error estimasi:", err);
        window.currentRouteDistanceKm = null;
        window.currentRouteUnavailable = true;
        if (typeof updatePricingBreakdown === 'function') updatePricingBreakdown();
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initializeRequestMap();
    triggerEstimasi();
    window.addEventListener('resize', function() {
        if (requestMap) requestMap.invalidateSize();
    });
});
</script>
