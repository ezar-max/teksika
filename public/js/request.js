function setLayanan(btn, val) {
    document.getElementById('layananInput').value = val;
    document.querySelectorAll('.option-btn').forEach(function(b){
        b.classList.remove('active');
    });
    btn.classList.add('active');
}

function triggerUpload() {
    document.getElementById('uploadInput').click();
}

function previewImages(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('previewContainer');
    const placeholder = document.getElementById('placeholder');

    // Batasi maksimal 5 file
    if (files.length > 5) {
        alert('Maksimal upload 5 foto!');
        event.target.value = ''; // Reset input
        previewContainer.innerHTML = '';
        placeholder.style.display = 'block';
        return;
    }

    // Sembunyikan placeholder jika ada file yang diupload
    if (files.length > 0) {
        placeholder.style.display = 'none';
    } else {
        placeholder.style.display = 'block';
    }

    // Bersihkan preview sebelumnya
    previewContainer.innerHTML = '';

    // Tampilkan preview gambar
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            img.style.margin = '8px';
            previewContainer.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
}

let map, marker;
function initMap() {
    const defaultLoc = { lat: -6.200000, lng: 106.816666 }; // Jakarta
    map = new google.maps.Map(document.getElementById("map"), {
        center: defaultLoc,
        zoom: 12,
    });
    marker = new google.maps.Marker({
        position: defaultLoc,
        map: map,
        draggable: true,
    });

    map.addListener("click", function(e) {
        marker.setPosition(e.latLng);
        updateLokasiInput(e.latLng);
    });

    marker.addListener("dragend", function(e) {
        updateLokasiInput(e.latLng);
    });

    const input = document.getElementById('lokasiInput');
    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (place.geometry) {
            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);
            updateLokasiInput(place.geometry.location);
        }
    });
}

function updateLokasiInput(latLng) {
    document.getElementById('lokasiInput').value = latLng.lat() + ',' + latLng.lng();
}
