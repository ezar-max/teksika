// Chat Image Preview & Modal Handlers
function handleChatImageSelect(input) {
    const previewBar = document.getElementById('chatImagePreviewBar');
    const thumb = document.getElementById('chatPreviewThumb');
    const nameEl = document.getElementById('chatPreviewName');
    if (!input.files || !input.files[0]) {
        if (previewBar) previewBar.style.display = 'none';
        return;
    }
    const file = input.files[0];
    if (thumb) thumb.src = URL.createObjectURL(file);
    if (nameEl) nameEl.textContent = `${file.name} (${(file.size / 1024).toFixed(0)} KB)`;
    if (previewBar) previewBar.style.display = 'flex';
}

function removeChatImage() {
    const input = document.getElementById('chatImageInput');
    const previewBar = document.getElementById('chatImagePreviewBar');
    const thumb = document.getElementById('chatPreviewThumb');
    if (input) input.value = '';
    if (thumb) thumb.src = '';
    if (previewBar) previewBar.style.display = 'none';
}

function validateChatSubmit(form) {
    const msgInput = form.querySelector('input[name="message"]');
    const msg = msgInput ? msgInput.value.trim() : '';
    const fileInput = form.querySelector('input[name="image"]');
    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
    if (!msg && !hasFile) {
        alert('Silakan tulis pesan atau pilih gambar untuk dikirim.');
        return false;
    }
    return true;
}

function openChatImageModal(event, src) {
    if (event) event.preventDefault();
    let modal = document.getElementById('chatLightboxModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'chatLightboxModal';
        modal.className = 'chat-lightbox';
        modal.innerHTML = `
            <span class="chat-lightbox-close" onclick="closeChatImageModal()">&times;</span>
            <img class="chat-lightbox-img" id="chatLightboxImg" src="" alt="Foto Pembesaran">
        `;
        modal.addEventListener('click', function (e) {
            if (e.target === modal || e.target.classList.contains('chat-lightbox-close')) {
                closeChatImageModal();
            }
        });
        document.body.appendChild(modal);
    }
    const img = document.getElementById('chatLightboxImg');
    if (img) img.src = src;
    modal.style.display = 'flex';
}

function closeChatImageModal() {
    const modal = document.getElementById('chatLightboxModal');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.querySelector('.chat-container[data-messages-url]');
    if (!chatContainer) return;

    let lastId = Number(chatContainer.dataset.lastId || 0);
    let isPolling = false;

    function appendMessage(message) {
        const wasAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 80;
        const emptyMessage = chatContainer.querySelector('.chat-empty');
        if (emptyMessage) emptyMessage.remove();

        const row = document.createElement('div');
        row.className = `bubble-row ${message.sender === chatContainer.dataset.ownSender ? 'user' : 'admin'}`;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';

        if (message.image_url) {
            bubble.classList.add('chat-bubble-has-image');
            const imgWrap = document.createElement('a');
            imgWrap.href = message.image_url;
            imgWrap.target = '_blank';
            imgWrap.style.display = 'inline-block';
            imgWrap.style.borderRadius = '10px';
            imgWrap.style.overflow = 'hidden';
            imgWrap.onclick = function (e) {
                openChatImageModal(e, message.image_url);
            };

            const img = document.createElement('img');
            img.src = message.image_url;
            img.alt = 'Foto Chat';
            img.className = 'chat-img-bubble';
            img.style.maxWidth = '220px';
            img.style.maxHeight = '200px';
            img.style.width = 'auto';
            img.style.height = 'auto';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '10px';
            img.style.display = 'block';
            imgWrap.appendChild(img);
            bubble.appendChild(imgWrap);

            if (message.message) {
                const caption = document.createElement('div');
                caption.className = 'chat-bubble-caption';
                caption.textContent = message.message;
                bubble.appendChild(caption);
            }
        } else {
            bubble.textContent = message.message;
        }

        const time = document.createElement('div');
        time.className = chatContainer.dataset.ownSender === 'admin' ? 'muter' : 'bubble-time';
        time.style.cssText = 'font-size:12px;margin-top:4px;';
        time.textContent = message.time;

        row.append(bubble, time);
        chatContainer.appendChild(row);
        if (wasAtBottom) chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    async function refreshMessages() {
        if (isPolling || document.hidden) return;
        isPolling = true;

        try {
            const url = new URL(chatContainer.dataset.messagesUrl, window.location.href);
            url.searchParams.set('after_id', String(lastId));
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
            });
            if (!response.ok) return;

            const data = await response.json();
            data.messages.forEach(function (message) {
                appendMessage(message);
                lastId = Math.max(lastId, Number(message.id));
            });
            chatContainer.dataset.lastId = String(lastId);
        } catch (error) {
            console.error('Gagal memperbarui chat:', error);
        } finally {
            isPolling = false;
        }
    }

    window.setInterval(refreshMessages, 3000);
    document.addEventListener('visibilitychange', refreshMessages);
    refreshMessages();
});
