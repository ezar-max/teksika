const sendBtn = document.getElementById("sendBtn");
const chatMessage = document.getElementById("chatMessage");
const chatContainer = document.getElementById("chatContainer");

sendBtn.addEventListener("click", sendMessage);
chatMessage.addEventListener("keypress", (e) => {
    if (e.key === "Enter") sendMessage();
});

function sendMessage() {
    const text = chatMessage.value.trim();
    if (text === "") return;

    // Tambahkan pesan user
    const userMsg = document.createElement("div");
    userMsg.className = "message user";
    userMsg.textContent = text;
    chatContainer.appendChild(userMsg);

    chatMessage.value = "";
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // Simulasi balasan admin
    setTimeout(() => {
        const adminMsg = document.createElement("div");
        adminMsg.className = "message admin";
        adminMsg.textContent = "Pesan kamu sudah saya terima.";
        chatContainer.appendChild(adminMsg);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }, 800);
}
