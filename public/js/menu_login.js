function showTab(tab) {
    document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tabContent => tabContent.classList.remove('active'));
    document.querySelector('.tab-link[onclick="showTab(\''+tab+'\')"]').classList.add('active');
    document.getElementById(tab).classList.add('active');
}

function showPopup(id) {
    document.getElementById(id).style.display = 'flex';
}
function closePopup(id) {
    document.getElementById(id).style.display = 'none';
}

window.addEventListener('DOMContentLoaded', function() {
    var toast = document.getElementById('toast');
    if (toast) {
        setTimeout(function() {
            toast.style.display = 'none';
        }, 3000); // 3 detik
    }
})