document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById('popupError');
    if (popup) {
        setTimeout(function() {
            popup.style.display = 'none';
        }, 2500);
    }
});