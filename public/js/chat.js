document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.querySelector('.chat-container[data-messages-url]');
    if (!chatContainer) return;

    let lastId = Number(chatContainer.dataset.lastId || 0);
    let isPolling = false;

    function appendMessage(message) {
        const wasAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 60;
        const emptyMessage = chatContainer.querySelector('.chat-empty');
        if (emptyMessage) emptyMessage.remove();

        const row = document.createElement('div');
        row.className = `bubble-row ${message.sender === chatContainer.dataset.ownSender ? 'user' : 'admin'}`;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.textContent = message.message;

        const time = document.createElement('div');
        time.className = 'muter';
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