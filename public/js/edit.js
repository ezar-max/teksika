document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("popup");
    const cancelBtn = document.querySelector(".btn-cancel");
    const editBtns = document.querySelectorAll(".service-actions .fa-pen");

    // buka popup pas klik icon edit
    editBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const card = e.target.closest(".service-card");
            const nama = card.querySelector("h4").innerText;
            const desc = card.querySelector("p").innerText;

            // isi form dengan data dari card
            document.getElementById("namaLayanan").value = nama;
            document.getElementById("deskripsi").value = desc;

            popup.style.display = "flex";
        });
    });

    // tutup popup
    cancelBtn.addEventListener("click", () => {
        popup.style.display = "none";
    });

    // klik luar popup -> tutup
    popup.addEventListener("click", (e) => {
        if (e.target === popup) {
            popup.style.display = "none";
        }
    });
});
