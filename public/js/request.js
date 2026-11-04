function setLayanan(btn, val) {
    const input = document.getElementById('layananInput');
    if (input) input.value = val;
    document.querySelectorAll('.option-btn').forEach(function(b){
        b.classList.remove('active');
    });
    if (btn) btn.classList.add('active');
    if (typeof updateWorkerChoices === 'function') updateWorkerChoices();
}

function triggerUpload() {
    const input = document.getElementById('uploadInput');
    if (input) input.click();
}

function previewImages(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('previewContainer');
    const placeholder = document.getElementById('placeholder') || document.getElementById('placeholderText');

    if (!previewContainer) return;

    // Batasi maksimal 5 file
    if (files.length > 5) {
        alert('Maksimal upload 5 foto!');
        event.target.value = '';
        previewContainer.innerHTML = '';
        if (placeholder) placeholder.style.display = 'block';
        return;
    }

    // Bersihkan preview sebelumnya
    previewContainer.innerHTML = '';

    // Tampilkan preview gambar
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = `<img src="${e.target.result}" alt="Preview Foto ${i+1}">`;
                previewContainer.appendChild(item);
            }
            reader.readAsDataURL(file);
        }
    }
}
