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

    <!-- Peta Interaktif (Google Maps Utama / Leaflet OpenStreetMap Fallback) -->
    <div id="mapError" role="alert" style="display:none; color:#991b1b; background:#fef2f2; border:1px solid #fecaca; padding:10px 12px; border-radius:8px; margin-bottom:10px;"></div>
    <div id="requestMap"></div>

    <!-- Info Rute & Jarak -->
    <div class="route-status-pill" id="routeStatus">
        <i class="fa-solid fa-route"></i>
        <span>Isi alamat di bawah atau pilih titik rumah pada peta.</span>
    </div>
</div>

<script>
const officePoint = [-6.4239, 106.9401];
let requestMap;
let homeMarker;
let officeMarker;
let routeLine;
let accuracyCircle = null;
let activeMapEngine = null; // 'google' | 'leaflet'
let lastRenderedRoute = null;
let keepHomeInViewAfterRoute = false;
window.currentRouteDistanceKm = null;
window.currentRouteUnavailable = false;
let addressLookupDebounce = null;
let addressLookupSequence = 0;
let addressGeocodeDebounce = null;
let addressGeocodeSequence = 0;
let lastAddressGeocodeKey = '';

function clearAccuracyCircle() {
    if (activeMapEngine === 'google' && accuracyCircle && typeof accuracyCircle.setMap === 'function') {
        accuracyCircle.setMap(null);
    } else if (activeMapEngine === 'leaflet' && accuracyCircle && requestMap) {
        requestMap.removeLayer(accuracyCircle);
    }
    accuracyCircle = null;
}

function syncLocationAddress(options = {}) {
    clearTimeout(addressLookupDebounce);
    addressLookupSequence++;
    const address = document.getElementById('alamatLengkap')?.value.trim() || '';
    const lokasiInput = document.getElementById('lokasiInput');
    if (lokasiInput) lokasiInput.value = address;

    if (options.lookupMap === true) lookupCoordinatesFromAddress(address);
}

function clearLocationDetails() {
    clearTimeout(addressGeocodeDebounce);
    addressGeocodeSequence++;
    lastAddressGeocodeKey = '';
    setAddressSearchLoading(false);
    const details = document.getElementById('locationDetails');
    if (details) details.hidden = true;
}

function renderLocationDetails(location, latitude, longitude) {
    const details = document.getElementById('locationDetails');
    const name = document.getElementById('locationDetailsName');
    const address = document.getElementById('locationDetailsAddress');
    const grid = document.getElementById('locationDetailsGrid');
    const coordinates = document.getElementById('locationDetailsCoordinates');
    if (!details || !name || !address || !grid || !coordinates) return;

    const detailEntries = Object.entries(location.address_details || {})
        .filter(([, value]) => String(value || '').trim() !== '');
    name.textContent = location.address_details?.Jalan
        || location.address_details?.Kelurahan
        || location.address_details?.['Kota/Kabupaten']
        || location.display_name
        || 'Lokasi ditemukan';
    address.textContent = location.display_name || '';
    grid.replaceChildren();

    detailEntries.forEach(([label, value]) => {
        const item = document.createElement('div');
        item.className = 'location-detail-item';
        const labelElement = document.createElement('strong');
        labelElement.textContent = label;
        const valueElement = document.createElement('span');
        valueElement.textContent = value;
        item.append(labelElement, valueElement);
        grid.appendChild(item);
    });

    const providerName = location.provider === 'google_maps'
        ? 'Google Maps'
        : (location.provider === 'photon' ? 'Photon OpenStreetMap' : 'OpenStreetMap');
    coordinates.textContent = `Koordinat: ${Number(latitude).toFixed(7)}, ${Number(longitude).toFixed(7)}${location.provider ? ` · Sumber: ${providerName}` : ''}`;
    details.hidden = false;
}

function setAddressSearchLoading(isLoading) {
    const button = document.getElementById('btnSearchLocation');
    if (!button) return;
    button.disabled = isLoading;
    button.innerHTML = isLoading
        ? '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i><span>Mencari...</span>'
        : '<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span>Cari Lokasi</span>';
}

function searchAddressLocation() {
    const address = document.getElementById('alamatLengkap')?.value.trim() || '';
    const status = document.getElementById('addressStatus');
    if (!address) {
        if (status) status.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="color:#ef4444;"></i> Masukkan alamat yang ingin dicari.';
        document.getElementById('alamatLengkap')?.focus();
        return;
    }

    syncLocationAddress({ lookupMap: false });
    clearLocationDetails();
    setAddressSearchLoading(true);
    if (status) status.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="color:#087cf0;"></i> Sedang mencari alamat...';
    lookupCoordinatesFromAddress(address, {
        immediate: true,
        onComplete: () => setAddressSearchLoading(false)
    });
}

function lookupCoordinatesFromAddress(address, options = {}) {
    const status = document.getElementById('addressStatus');
    const finish = typeof options.onComplete === 'function' ? options.onComplete : () => {};

    if (!address) {
        clearTimeout(addressGeocodeDebounce);
        addressGeocodeSequence++;
        lastAddressGeocodeKey = '';
        if (status) status.innerHTML = '<i class="fa-solid fa-circle-info" style="color:#64748b;"></i> Masukkan alamat lengkap untuk mengarahkan titik di peta secara otomatis.';
        finish();
        return;
    }

    const addressKey = address.toLowerCase();
    if (addressKey === lastAddressGeocodeKey && !options.onComplete) return;
    clearTimeout(addressGeocodeDebounce);
    const sequence = ++addressGeocodeSequence;
    lastAddressGeocodeKey = addressKey;

    const performLookup = function () {
        const fullQuery = /\bindonesia\b/i.test(address) ? address : `${address}, Indonesia`;

        // 1. Coba pencarian langsung di client-side Google Maps jika Google Maps aktif dan Geocoder tersedia
        if (activeMapEngine === 'google' && window.google && window.google.maps && window.google.maps.Geocoder) {
            try {
                const gGeocoder = new google.maps.Geocoder();
                gGeocoder.geocode({
                    address: fullQuery,
                    componentRestrictions: { country: 'ID' }
                }, function (results, gStatus) {
                    if (sequence !== addressGeocodeSequence) return;
                    if (gStatus === 'OK' && results && results[0] && results[0].geometry && results[0].geometry.location) {
                        const loc = results[0].geometry.location;
                        setHomeLocation({
                            lat: loc.lat(),
                            lng: loc.lng()
                        }, { clearAddress: false, lookupAddress: false, zoom: 16, keepHomeInViewAfterRoute: true });
                        const components = {};
                        (results[0].address_components || []).forEach(function (component) {
                            const type = component.types.find(item => ({
                                street_number: 'Nomor',
                                route: 'Jalan',
                                neighborhood: 'Lingkungan',
                                sublocality_level_1: 'Kelurahan',
                                administrative_area_level_4: 'Kelurahan',
                                administrative_area_level_3: 'Kecamatan',
                                locality: 'Kota',
                                administrative_area_level_2: 'Kabupaten/Kota',
                                administrative_area_level_1: 'Provinsi',
                                postal_code: 'Kode Pos',
                                country: 'Negara'
                            })[item]);
                            if (type && !components[type]) components[type] = component.long_name;
                        });
                        renderLocationDetails({
                            display_name: results[0].formatted_address || address,
                            address_details: components,
                            provider: 'google_maps'
                        }, loc.lat(), loc.lng());
                        if (status) status.innerHTML = '<i class="fa-solid fa-circle-check" style="color:#10b981;"></i> Lokasi ditemukan. Periksa detail alamat di bawah.';
                        finish();
                        return;
                    }
                    // Jika client-side tidak menemukan, fallback ke backend server
                    fetchServerGeocode(address, sequence, status, finish);
                });
                return;
            } catch (e) {
                fetchServerGeocode(address, sequence, status, finish);
                return;
            }
        }

        // 2. Gunakan backend endpoint (cascading Google -> Photon -> OpenStreetMap)
        fetchServerGeocode(address, sequence, status, finish);
    };

    if (options.immediate) {
        performLookup();
    } else {
        addressGeocodeDebounce = setTimeout(performLookup, 600);
    }
}

function fetchServerGeocode(address, sequence, status, finish = () => {}) {
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
        body: JSON.stringify({ alamat: address })
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
        }, { clearAddress: false, lookupAddress: false, zoom: 16, keepHomeInViewAfterRoute: true });
        renderLocationDetails(data.location, data.location.latitude, data.location.longitude);
        if (status) {
            const provider = data.location.provider === 'google_maps'
                ? 'Google Maps'
                : (data.location.provider === 'photon' ? 'Photon OpenStreetMap' : 'OpenStreetMap');
            status.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#10b981;"></i> Lokasi ditemukan (${provider}). Periksa detail alamat di bawah.`;
        }
        finish();
    })
    .catch(function (error) {
        if (sequence === addressGeocodeSequence && status) {
            lastAddressGeocodeKey = '';
            status.innerHTML = `<i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;"></i> ${error.message || 'Lokasi belum ditemukan. Lengkapi alamat atau geser pin pada peta.'}`;
        }
        finish();
    });
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
            const addressParts = [
                address.jalan,
                [address.rt ? `RT ${address.rt}` : '', address.rw ? `RW ${address.rw}` : ''].filter(Boolean).join('/'),
                address.kecamatan ? `Kecamatan ${address.kecamatan}` : '',
                address.kabupaten
            ].filter(Boolean);
            const addressInput = document.getElementById('alamatLengkap');
            if (addressInput) addressInput.value = addressParts.join(', ');
            syncLocationAddress({ lookupMap: false });
            renderLocationDetails({
                display_name: addressParts.join(', '),
                address_details: {
                    'Jalan': address.jalan,
                    'RT/RW': [address.rt ? `RT ${address.rt}` : '', address.rw ? `RW ${address.rw}` : ''].filter(Boolean).join(' / '),
                    'Kecamatan': address.kecamatan,
                    'Kabupaten/Kota': address.kabupaten
                },
                provider: address.provider === 'google_maps' ? 'google_maps' : 'openstreetmap'
            }, latitude, longitude);
            if (status) {
                const providerName = address.provider === 'google_maps' ? 'Google Maps' : 'OpenStreetMap';
                status.innerText = address.rt && address.rw
                    ? `Alamat lengkap termasuk RT/RW ditemukan dari ${providerName}.`
                    : `Alamat wilayah ditemukan dari ${providerName}. Lengkapi RT/RW bila perlu.`;
            }
        })
        .catch(function () {
            if (sequence === addressLookupSequence && status) {
                status.innerText = 'Alamat otomatis tidak tersedia. Isi alamat lengkap secara manual.';
            }
        });
    }, 1200);
}

function clearCurrentRouteLine() {
    if (!routeLine) return;
    if (activeMapEngine === 'google' && typeof routeLine.setMap === 'function') {
        routeLine.setMap(null);
    } else if (activeMapEngine === 'leaflet' && requestMap && typeof requestMap.removeLayer === 'function') {
        requestMap.removeLayer(routeLine);
    }
    routeLine = null;
}

function showMapError(message) {
    const error = document.getElementById('mapError');
    if (error) {
        error.textContent = message;
        error.style.display = 'block';
    }
}

window.gm_authFailure = function () {
    console.warn('Google Maps JavaScript API authentication/billing limitation detected. Switching to Leaflet OpenStreetMap fallback.');
    initializeLeafletFallbackMap();
};

function initializeRequestMap() {
    const lat = Number(document.getElementById('latInput').value);
    const lon = Number(document.getElementById('lonInput').value);
    const homePoint = Number.isFinite(lat) && Number.isFinite(lon) ? [lat, lon] : officePoint;

    if (window.google && window.google.maps && typeof window.google.maps.Map === 'function') {
        try {
            initializeGoogleMap(homePoint);
            // Pantau jika Google Maps mengganti container dengan .gm-err-container karena keterbatasan Billing/API Key
            window.setTimeout(function () {
                const container = document.getElementById('requestMap');
                if (container && container.querySelector('.gm-err-container')) {
                    console.warn('Google Maps error container detected. Switching to Leaflet OpenStreetMap fallback.');
                    initializeLeafletFallbackMap();
                }
            }, 1200);
            return;
        } catch (err) {
            console.warn('Google Maps initialization error, falling back to Leaflet:', err);
        }
    }

    initializeLeafletFallbackMap();
}

function initializeGoogleMap(homePoint) {
    activeMapEngine = 'google';
    const mapEl = document.getElementById('requestMap');
    requestMap = new google.maps.Map(mapEl, {
        center: { lat: homePoint[0], lng: homePoint[1] },
        zoom: 14,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true
    });

    officeMarker = new google.maps.Marker({
        position: { lat: officePoint[0], lng: officePoint[1] },
        map: requestMap,
        title: 'Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri',
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: '#22c55e',
            fillOpacity: 1,
            strokeColor: '#166534',
            strokeWeight: 2
        }
    });

    const homeInfo = new google.maps.InfoWindow({
        content: '<strong>Lokasi rumah Anda</strong>'
    });

    homeMarker = new google.maps.Marker({
        position: { lat: homePoint[0], lng: homePoint[1] },
        map: requestMap,
        draggable: true,
        title: 'Lokasi rumah Anda'
    });
    homeMarker.addListener('click', function () {
        homeInfo.open(requestMap, homeMarker);
    });

    requestMap.addListener('click', function (event) {
        if (event.latLng) {
            setHomeLocation({ lat: event.latLng.lat(), lng: event.latLng.lng() });
        }
    });

    homeMarker.addListener('dragend', function (event) {
        if (event.latLng) {
            setHomeLocation({ lat: event.latLng.lat(), lng: event.latLng.lng() });
        }
    });
}

function initializeLeafletFallbackMap() {
    if (activeMapEngine === 'leaflet' && requestMap) return;
    activeMapEngine = 'leaflet';

    const lat = Number(document.getElementById('latInput').value);
    const lon = Number(document.getElementById('lonInput').value);
    const homePoint = Number.isFinite(lat) && Number.isFinite(lon) ? [lat, lon] : officePoint;

    const mapEl = document.getElementById('requestMap');
    if (mapEl) {
        mapEl.innerHTML = '';
        if (mapEl._leaflet_id) {
            mapEl._leaflet_id = null;
        }
    }

    if (!window.L) {
        showMapError('Peta tidak dapat dimuat. Pastikan koneksi internet aktif untuk memuat peta.');
        return;
    }

    requestMap = L.map('requestMap').setView(homePoint, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        updateWhenZooming: false,
        updateWhenIdle: true,
        keepBuffer: 1,
        attribution: '&copy; OpenStreetMap'
    }).addTo(requestMap);

    officeMarker = L.circleMarker(officePoint, {
        radius: 8,
        color: '#166534',
        fillColor: '#22c55e',
        fillOpacity: 1
    }).bindPopup('<strong>Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri</strong>').addTo(requestMap);

    homeMarker = L.marker(homePoint, { draggable: true })
        .bindPopup('<strong>Lokasi rumah Anda</strong>')
        .addTo(requestMap);

    requestMap.on('click', function (event) {
        setHomeLocation(event.latlng);
    });
    homeMarker.on('dragend', function (event) {
        setHomeLocation(event.target.getLatLng());
    });

    window.setTimeout(function () {
        if (requestMap && typeof requestMap.invalidateSize === 'function') {
            requestMap.invalidateSize();
        }
        if (lastRenderedRoute) {
            renderOsrmRoute(lastRenderedRoute);
        }
    }, 300);
}

function setHomeLocation(point, options = {}) {
    const lat = Number(point.lat).toFixed(7);
    const lon = Number(point.lng || point.lon).toFixed(7);
    clearCurrentRouteLine();
    keepHomeInViewAfterRoute = options.keepHomeInViewAfterRoute === true;
    window.currentRouteDistanceKm = null;
    window.currentRouteUnavailable = false;

    if (activeMapEngine === 'google' && homeMarker && typeof homeMarker.setPosition === 'function') {
        homeMarker.setPosition({ lat: Number(lat), lng: Number(lon) });
    } else if (activeMapEngine === 'leaflet' && homeMarker && typeof homeMarker.setLatLng === 'function') {
        homeMarker.setLatLng([Number(lat), Number(lon)]);
    }

    document.getElementById('latInput').value = lat;
    document.getElementById('lonInput').value = lon;
    document.getElementById('coordDisplay').innerText = `${lat}, ${lon}`;

    if (options.accuracy && options.accuracy > 0) {
        clearAccuracyCircle();
        const displayRadius = Math.min(Number(options.accuracy), 5000);
        if (activeMapEngine === 'google' && requestMap && window.google && window.google.maps) {
            accuracyCircle = new google.maps.Circle({
                map: requestMap,
                center: { lat: Number(lat), lng: Number(lon) },
                radius: displayRadius,
                fillColor: '#3b82f6',
                fillOpacity: 0.15,
                strokeColor: '#2563eb',
                strokeOpacity: 0.45,
                strokeWeight: 1
            });
        } else if (activeMapEngine === 'leaflet' && requestMap && window.L) {
            accuracyCircle = L.circle([Number(lat), Number(lon)], {
                radius: displayRadius,
                color: '#2563eb',
                weight: 1,
                opacity: 0.45,
                fillColor: '#3b82f6',
                fillOpacity: 0.15
            }).addTo(requestMap);
        }
    } else if (options.clearAccuracy !== false) {
        clearAccuracyCircle();
    }

    if (options.clearAddress !== false) {
        clearLocationDetails();
        clearTimeout(addressGeocodeDebounce);
        addressGeocodeSequence++;
        lastAddressGeocodeKey = '';
        const addressInput = document.getElementById('alamatLengkap');
        if (addressInput) addressInput.value = '';
        syncLocationAddress({ lookupMap: false });
    }
    if (requestMap && options.zoom) {
        if (activeMapEngine === 'google' && typeof requestMap.setCenter === 'function') {
            requestMap.setCenter({ lat: Number(lat), lng: Number(lon) });
            requestMap.setZoom(options.zoom);
        } else if (activeMapEngine === 'leaflet' && typeof requestMap.setView === 'function') {
            requestMap.setView([Number(lat), Number(lon)], options.zoom);
        }
    }
    if (options.lookupAddress !== false) lookupAddressFromCoordinates(lat, lon);
    if (typeof updatePricingBreakdown === 'function') updatePricingBreakdown();
    const routeStatus = document.getElementById('routeStatus');
    if (routeStatus) {
        routeStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Lokasi diperbarui. Menghitung rute perjalanan dari kantor pusat...</span>';
    }
    triggerEstimasi();
}

function renderOsrmRoute(route) {
    lastRenderedRoute = route;
    clearCurrentRouteLine();
    const keepHomeInView = keepHomeInViewAfterRoute;
    keepHomeInViewAfterRoute = false;
    const homeLat = Number(document.getElementById('latInput').value);
    const homeLon = Number(document.getElementById('lonInput').value);

    if (activeMapEngine === 'google' && requestMap && window.google && window.google.maps) {
        const googlePath = route.geometry.map(function (coordinate) {
            return { lat: Number(coordinate[1]), lng: Number(coordinate[0]) };
        });
        routeLine = new google.maps.Polyline({
            path: googlePath,
            geodesic: true,
            strokeColor: '#007bff',
            strokeOpacity: 0.85,
            strokeWeight: 5,
            map: requestMap
        });
        if (keepHomeInView) {
            requestMap.setCenter({ lat: homeLat, lng: homeLon });
            requestMap.setZoom(16);
        } else if (googlePath.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            googlePath.forEach(function (pt) {
                bounds.extend(pt);
            });
            requestMap.fitBounds(bounds, 24);
        }
    } else if (activeMapEngine === 'leaflet' && requestMap && window.L) {
        const routePoints = route.geometry.map(function (coordinate) {
            return [coordinate[1], coordinate[0]];
        });
        routeLine = L.polyline(routePoints, {
            color: '#007bff',
            weight: 5,
            opacity: 0.85
        }).addTo(requestMap);
        if (keepHomeInView) {
            requestMap.setView([homeLat, homeLon], 16);
        } else if (routePoints.length > 0) {
            requestMap.fitBounds(routeLine.getBounds(), { padding: [24, 24] });
        }
    }

    const sourceLabel = route.source === 'google_routes' ? 'Google Maps Compute Routes' : 'OSRM Fallback';

    const routeStatus = document.getElementById('routeStatus');
    if (routeStatus) {
        routeStatus.innerHTML = `<i class="fa-solid fa-route"></i> <span>Rute Jalan (${sourceLabel}): <strong>${route.distance_km} km</strong> (~${route.duration_minutes} menit). Titik awal: Kantor Pusat TEKSIKA</span>`;
    }
}

// Deteksi Lokasi Pengguna via Geolocation API Presisi Tinggi Perangkat
function detectUserLocation() {
    const btn = document.getElementById('btnGetLocation');
    const notice = document.getElementById('locationNotice');
    const display = document.getElementById('coordDisplay');

    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    const isHttps = window.location.protocol === 'https:';
    const isInsecureContext = typeof window.isSecureContext !== 'undefined' && !window.isSecureContext && !isLocalhost && !isHttps;

    function renderInsecureNotice() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> <span>Gunakan Lokasi Saya</span>';
        if (notice) {
            notice.style.display = 'block';
            const originUrl = window.location.origin;
            notice.innerHTML = `
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px; color:#991b1b; text-align:left;">
                    <div style="display:flex; align-items:center; gap:8px; font-weight:600; margin-bottom:6px;">
                        <i class="fa-solid fa-shield-halved" style="color:#ef4444; font-size:1.1rem;"></i>
                        <span>Browser HP Memerlukan Izin Khusus (Akses HTTP)</span>
                    </div>
                    <div style="font-size:0.83rem; color:#475569; line-height:1.5;">
                        Chrome di Android memblokir chip GPS jika web diakses via IP LAN (HTTP).<br>
                        <strong>Cara cepat mengaktifkan GPS HP Anda (Cukup 1x):</strong>
                        <ol style="margin:6px 0 8px 18px; padding:0;">
                            <li>Buka tab baru di Chrome HP, ketik: <code>chrome://flags</code></li>
                            <li>Cari: <em>Insecure origins treated as secure</em>, pilih <strong>Enabled</strong>.</li>
                            <li>Ketik atau paste URL: <code id="insecureOriginCode">${originUrl}</code> di kotaknya.</li>
                            <li>Tekan tombol <strong>Relaunch</strong> di pojok bawah Chrome HP.</li>
                        </ol>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="navigator.clipboard.writeText('${originUrl}').then(() => alert('URL disalin: ${originUrl}'));" style="font-size:0.78rem; padding:4px 10px;">
                            <i class="fa-regular fa-copy"></i> Salin URL
                        </button>
                    </div>
                    <div style="margin-top:8px; font-size:0.8rem; color:#64748b; font-style:italic;">
                        Atau geser pin merah pada peta langsung ke atap rumah Anda untuk titik akurat.
                    </div>
                </div>
            `;
        }
    }

    if (isInsecureContext) {
        renderInsecureNotice();
        return;
    }

    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Membaca GPS...</span>';
    if (notice) {
        notice.style.display = 'block';
        notice.innerHTML = '<i class="fa-solid fa-satellite-dish fa-spin" style="color:#0284c7;"></i> <span>Mengaktifkan sensor GPS perangkat... Mohon tunggu penguncian satelit.</span>';
    }

    let bestPos = null;
    let watchId = null;
    let locked = false;

    function finalizeLock(position) {
        if (locked) return;
        locked = true;
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }

        const lat = position.coords.latitude.toFixed(7);
        const lon = position.coords.longitude.toFixed(7);
        const accuracy = Math.round(position.coords.accuracy || 0);

        document.getElementById('latInput').value = lat;
        document.getElementById('lonInput').value = lon;
        if (display) display.innerText = `${lat}, ${lon}`;
        setHomeLocation({ lat: Number(lat), lng: Number(lon) }, { zoom: 17, accuracy: accuracy, lookupAddress: true });

        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Lokasi Terkunci</span>';
        btn.style.background = '#10b981';

        if (notice) {
            notice.style.display = 'block';
            if (accuracy <= 30) {
                notice.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#16a34a;"></i> <strong>Sensor GPS Perangkat Terkunci Sempurna!</strong> Akurasi sangat tinggi: ±${accuracy} meter.`;
            } else if (accuracy <= 100) {
                notice.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#16a34a;"></i> <strong>GPS Perangkat Berhasil Didapat!</strong> Akurasi presisi: ±${accuracy} meter.`;
            } else if (accuracy <= 1000) {
                notice.innerHTML = `<i class="fa-solid fa-location-dot" style="color:#0284c7;"></i> Sinyal lokasi perangkat didapat (Akurasi: ±${accuracy} m). Anda dapat menggeser pin merah jika ingin mengarahkan tepat ke atap rumah.`;
            } else {
                const km = (accuracy / 1000).toFixed(1);
                notice.innerHTML = `<i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i> <strong>Perangkat PC/Laptop ini belum memiliki sensor GPS fisik</strong> (memakai estimasi IP provider: ±${km} km). Untuk titik akurat: silakan <strong>geser pin merah pada peta</strong> atau <strong>ketik nama jalan Anda di bawah</strong>.`;
            }
        }
    }

    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 20000,
        maximumAge: 0
    };

    // Beri waktu sensor hardware GPS hingga 8 detik untuk menyempurnakan akurasi
    const maxWaitTimer = setTimeout(function () {
        if (!locked && bestPos) {
            finalizeLock(bestPos);
        }
    }, 8000);

    watchId = navigator.geolocation.watchPosition(
        function (pos) {
            // Live update koordinat saat sinyal membaik
            if (!bestPos || pos.coords.accuracy < bestPos.coords.accuracy) {
                bestPos = pos;
                const lat = pos.coords.latitude.toFixed(7);
                const lon = pos.coords.longitude.toFixed(7);
                const acc = Math.round(pos.coords.accuracy || 0);

                document.getElementById('latInput').value = lat;
                document.getElementById('lonInput').value = lon;
                if (display) display.innerText = `${lat}, ${lon}`;
                setHomeLocation({ lat: Number(lat), lng: Number(lon) }, { zoom: 17, accuracy: acc, lookupAddress: false });

                if (notice && !locked) {
                    notice.innerHTML = `<i class="fa-solid fa-satellite-dish fa-spin" style="color:#0284c7;"></i> Menerima sinyal sensor perangkat (Akurasi: ±${acc} m)... Menyempurnakan koordinat...`;
                }
            }

            // Jika akurasi hardware GPS sudah sangat tinggi (<= 25 meter), langsung kunci segera!
            if (pos.coords.accuracy <= 25) {
                clearTimeout(maxWaitTimer);
                finalizeLock(pos);
            }
        },
        function (err) {
            clearTimeout(maxWaitTimer);
            if (bestPos) {
                finalizeLock(bestPos);
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> <span>Gunakan Lokasi Saya</span>';
                if (notice) {
                    notice.style.display = 'block';
                    const isSecureErr = (err.message && err.message.toLowerCase().includes('secure origin')) || (typeof window.isSecureContext !== 'undefined' && !window.isSecureContext);
                    if (isSecureErr) {
                        renderInsecureNotice();
                    } else {
                        notice.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="color:#ef4444;"></i> Akses GPS perangkat ditolak / tidak tersedia (${err.message}). Pastikan izin lokasi aktif di browser atau geser pin merah pada peta.`;
                    }
                }
            }
        },
        geoOptions
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
            clearCurrentRouteLine();
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
    const initLat = document.getElementById('latInput') ? document.getElementById('latInput').value : '';
    const initLon = document.getElementById('lonInput') ? document.getElementById('lonInput').value : '';
    const initAddress = document.getElementById('alamatLengkap')?.value.trim() || '';
    if (initAddress) searchAddressLocation(); else if (initLat && initLon) {
        lookupAddressFromCoordinates(initLat, initLon);
    }
    window.addEventListener('resize', function() {
        if (activeMapEngine === 'leaflet' && requestMap && typeof requestMap.invalidateSize === 'function') {
            requestMap.invalidateSize();
        } else if (activeMapEngine === 'google' && requestMap && window.google && window.google.maps) {
            google.maps.event.trigger(requestMap, 'resize');
        }
    });
});
</script>
