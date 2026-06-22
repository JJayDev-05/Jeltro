@auth
<div id="chat-widget" class="chat-widget">
    {{-- Launcher bubble --}}
    <button id="chat-toggle" type="button" class="chat-launcher" aria-label="Chat with Jeltro Assistant">
        <svg class="chat-launcher__icon chat-launcher__icon--open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
        </svg>
        <svg class="chat-launcher__icon chat-launcher__icon--close" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- Chat panel --}}
    <div id="chat-panel" class="chat-panel" aria-hidden="true">
        <div class="chat-panel__header">
            <div>
                <p class="chat-panel__title">Jeltro Assistant</p>
                <p class="chat-panel__subtitle">Ask about products, sizes &amp; prices</p>
            </div>
            <button id="chat-close" type="button" class="chat-panel__close" aria-label="Close chat">&times;</button>
        </div>

        <div id="chat-messages" class="chat-panel__messages">
            <div class="chat-msg chat-msg--bot">Hi {{ auth()->user()->first_name ?? 'there' }}! 👋 I can help you find products. just ask me about styles, colors, sizes or prices.</div>
        </div>

        <form id="chat-form" class="chat-panel__form">
            <input id="chat-input" type="text" class="chat-panel__input" placeholder="Type a message…" autocomplete="off" maxlength="1000" required>
            <button type="submit" class="chat-panel__send" aria-label="Send message" data-no-disable="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
(function () {
    const widget   = document.getElementById('chat-widget');
    const toggle   = document.getElementById('chat-toggle');
    const closeBtn = document.getElementById('chat-close');
    const panel    = document.getElementById('chat-panel');
    const form     = document.getElementById('chat-form');
    const input    = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const csrf     = document.querySelector('meta[name="csrf-token"]')?.content;

    // Conversation history sent to the server for context (memory).
    let history = [];
    let busy = false;

    function openChat() {
        widget.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
        setTimeout(() => input.focus(), 150);
    }
    function closeChat() {
        widget.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
    }
    toggle.addEventListener('click', () => widget.classList.contains('is-open') ? closeChat() : openChat());
    closeBtn.addEventListener('click', closeChat);

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    // Turn the bot's text into safe HTML: render Markdown links [text](url)
    // and make any leftover bare URLs clickable. Escape first to prevent XSS.
    function renderBotHtml(text) {
        let html = escapeHtml(text);
        html = html.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g,
            '<a href="$2" target="_blank" rel="noopener" class="chat-link">$1</a>');
        html = html.replace(/(^|\s)(https?:\/\/[^\s<]+)/g,
            '$1<a href="$2" target="_blank" rel="noopener" class="chat-link">$2</a>');
        return html;
    }

    function addMessage(text, who) {
        const el = document.createElement('div');
        el.className = 'chat-msg chat-msg--' + who;
        if (who === 'bot') {
            el.innerHTML = renderBotHtml(text);
        } else {
            el.textContent = text;
        }
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    function addTyping() {
        const el = document.createElement('div');
        el.className = 'chat-msg chat-msg--bot chat-msg--typing';
        el.innerHTML = '<span></span><span></span><span></span>';
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || busy) return;

        busy = true;
        input.value = '';
        addMessage(text, 'user');
        history.push({ role: 'user', content: text });
        const typing = addTyping();

        try {
            const res = await fetch('{{ route('chat.message') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text, history: history.slice(-10) }),
            });

            typing.remove();

            if (!res.ok) {
                addMessage("Sorry, something went wrong. Please try again.", 'bot');
                return;
            }

            const data = await res.json();
            const reply = data.reply || "Sorry, I didn't catch that.";
            addMessage(reply, 'bot');
            history.push({ role: 'assistant', content: reply });
        } catch (err) {
            typing.remove();
            addMessage("Sorry, I couldn't reach the server. Check your connection.", 'bot');
        } finally {
            busy = false;
            input.focus();
        }
    });
})();
</script>
@endauth
