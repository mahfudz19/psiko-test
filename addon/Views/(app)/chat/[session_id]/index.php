<?php

/**
 * View: Detail Chat Konsultasi
 * 
 * @var array $chat Data sesi chat
 * @var array $messages Daftar pesan
 * @var string $sessionId Session ID
 */
?>

<div class="chat-room-wrapper">
    <!-- Header -->
    <header class="chat-room-header">
        <div class="chat-room-header-left">
            <a href="/chat" class="back-button" data-spa>
                <span class="back-icon">←</span>
                <span>Kembali</span>
            </a>
            <div class="chat-room-title">
                <h1>
                    <?php
                    $topicIcons = [
                        'potential_analysis' => '🎯',
                        'career_guidance' => '💼',
                        'study_tips' => '📚',
                        'personal_development' => '🌱',
                    ];
                    $icon = $topicIcons[$chat['topic']] ?? '💬';
                    echo $icon;
                    ?>
                    <?php
                    $topicLabels = [
                        'potential_analysis' => 'Analisis Potensi',
                        'career_guidance' => 'Bimbingan Karir',
                        'study_tips' => 'Tips Belajar',
                        'personal_development' => 'Pengembangan Diri',
                    ];
                    echo $topicLabels[$chat['topic']] ?? 'Konsultasi';
                    ?>
                </h1>
                <span><?= date('d F Y, H:i', strtotime($chat['created_at'])) ?></span>
            </div>
        </div>
        <div class="chat-room-header-right">
            <button class="btn-new-chat" onclick="window.location.href='/chat/create'" data-spa>
                <span>✨</span>
                <span>Chat Baru</span>
            </button>
        </div>
    </header>

    <!-- Messages Area -->
    <div class="chat-messages-wrapper">
        <div class="chat-messages-container" id="chatMessages">
            <?php if (empty($messages)): ?>
                <div class="chat-empty-state">
                    <div class="empty-icon">💭</div>
                    <h3>Belum Ada Pesan</h3>
                    <p>Mulai percakapan dengan AI konselor Anda</p>
                </div>
            <?php else: ?>
                <?php
                // Group messages by date for separators
                $lastDate = null;
                foreach ($messages as $message):
                    $msgDate = date('d F Y', strtotime($message['created_at']));
                    if ($msgDate !== $lastDate):
                        $lastDate = $msgDate;
                ?>
                        <div class="date-separator">
                            <span><?= $msgDate ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="chat-message <?= $message['role'] ?>" data-message-id="<?= $message['id'] ?>">
                        <div class="message-avatar">
                            <div class="avatar-wrapper">
                                <?php if ($message['role'] === 'user'): ?>
                                    👤
                                <?php else: ?>
                                    🤖
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-role">
                                    <?php if ($message['role'] === 'user'): ?>
                                        Anda
                                    <?php else: ?>
                                        AI Konselor
                                    <?php endif; ?>
                                </span>
                                <span class="message-time"><?= date('H:i', strtotime($message['created_at'])) ?></span>
                            </div>
                            <div class="message-bubble">
                                <div class="message-text"><?= nl2br(htmlspecialchars($message['content'])) ?></div>
                            </div>
                            <?php if (!empty($message['context_data'])): ?>
                                <?php
                                $contextData = is_string($message['context_data'])
                                    ? json_decode($message['context_data'], true)
                                    : $message['context_data'];
                                ?>
                                <?php if (!empty($contextData)): ?>
                                    <div class="message-context">
                                        <details>
                                            <summary>📊 Data Konteks</summary>
                                            <pre><?= htmlspecialchars(json_encode($contextData, JSON_PRETTY_PRINT)) ?></pre>
                                        </details>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Input Area -->
    <div class="chat-input-wrapper">
        <form id="chatInputForm" class="chat-input-form">
            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId) ?>">
            <textarea
                id="messageInput"
                name="message"
                class="chat-input-textarea"
                rows="1"
                placeholder="Ketik pesan Anda di sini..."
                required></textarea>
            <button type="submit" class="btn-send" id="btnSend">
                <span class="btn-icon">📤</span>
                <span class="btn-text">Kirim</span>
            </button>
        </form>
    </div>

    <!-- Scroll to Bottom Button -->
    <button class="scroll-to-bottom" id="scrollToBottomBtn" title="Scroll ke pesan terbaru">
        ⬇️
    </button>
</div>

<script>
    // Chat room logic - Original logic preserved with enhancements

    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const btnSend = document.getElementById('btnSend');
    const scrollToBottomBtn = document.getElementById('scrollToBottomBtn');

    // Auto scroll to bottom on page load
    function scrollToBottom(smooth = false) {
        if (chatMessages) {
            chatMessages.scrollTo({
                top: chatMessages.scrollHeight,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    }

    // Initial scroll - to latest message
    scrollToBottom();

    // Show/hide scroll to bottom button
    function updateScrollButton() {
        if (!chatMessages) return;

        const threshold = 200; // Show button when scrolled up more than this
        const scrollPos = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight;

        if (scrollPos > threshold) {
            scrollToBottomBtn.classList.add('visible');
        } else {
            scrollToBottomBtn.classList.remove('visible');
        }
    }

    // Listen for scroll events
    if (chatMessages) {
        chatMessages.addEventListener('scroll', updateScrollButton);
    }

    // Scroll to bottom button click
    scrollToBottomBtn.addEventListener('click', () => {
        scrollToBottom(true);
    });

    // Handle form submit - Original logic preserved
    document.getElementById('chatInputForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        // Disable input
        messageInput.disabled = true;
        btnSend.disabled = true;
        btnSend.classList.add('loading');

        // Add temporary user message
        const tempUserMessage = document.createElement('div');
        tempUserMessage.className = 'chat-message user temp';
        tempUserMessage.innerHTML = `
            <div class="message-avatar">
                <div class="avatar-wrapper">👤</div>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-role">Anda</span>
                    <span class="message-time">Baru saja</span>
                </div>
                <div class="message-bubble">
                    <div class="message-text">${escapeHtml(message)}</div>
                </div>
            </div>
        `;
        chatMessages.appendChild(tempUserMessage);
        scrollToBottom();

        try {
            const formData = new FormData(this);
            formData.set('message', message);

            const payload = {
                session_id: formData.get('session_id'),
                message: message
            };
            console.log('Payload to be sent:', payload);

            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                // Remove temp message
                tempUserMessage.remove();

                // Add real user message
                const userMessage = document.createElement('div');
                userMessage.className = 'chat-message user new-message';
                userMessage.dataset.messageId = Date.now();
                userMessage.innerHTML = `
                    <div class="message-avatar">
                        <div class="avatar-wrapper">👤</div>
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-role">Anda</span>
                            <span class="message-time">${formatTime(new Date())}</span>
                        </div>
                        <div class="message-bubble">
                            <div class="message-text">${escapeHtml(message)}</div>
                        </div>
                    </div>
                `;
                chatMessages.appendChild(userMessage);

                // Add AI response
                const aiMessage = document.createElement('div');
                aiMessage.className = 'chat-message assistant new-message';
                aiMessage.dataset.messageId = result.message_id;
                aiMessage.innerHTML = `
                    <div class="message-avatar">
                        <div class="avatar-wrapper">🤖</div>
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-role">AI Konselor</span>
                            <span class="message-time">${formatTime(new Date())}</span>
                        </div>
                        <div class="message-bubble">
                            <div class="message-text">${escapeHtml(result.content).replace(/\n/g, '<br>')}</div>
                        </div>
                    </div>
                `;
                chatMessages.appendChild(aiMessage);

                // Scroll to bottom
                scrollToBottom(true);
            } else {
                tempUserMessage.remove();
                alert('Gagal mengirim pesan: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            tempUserMessage.remove();
            alert('Terjadi kesalahan saat mengirim pesan');
        } finally {
            messageInput.value = '';
            messageInput.disabled = false;
            btnSend.disabled = false;
            btnSend.classList.remove('loading');
            messageInput.focus();
            messageInput.style.height = 'auto';
        }
    });

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Handle Enter key (send with Enter, new line with Shift+Enter)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chatInputForm').dispatchEvent(new Event('submit'));
        }
    });

    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(date) {
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
</script>