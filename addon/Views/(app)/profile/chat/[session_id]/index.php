<?php

/**
 * View: Detail Chat Konsultasi
 * 
 * @var array $chat Data sesi chat
 * @var array $messages Daftar pesan
 * @var string $sessionId Session ID
 */
?>

<div class="chat-detail-container">
    <div class="chat-detail-header">
        <div class="chat-header-left">
            <a href="/profile/chat" class="back-button" data-spa>
                <span class="back-icon">←</span>
                <span>Kembali</span>
            </a>
            <div class="chat-header-info">
                <h1 class="chat-title">
                    <?php
                    $topicIcons = [
                        'potential_analysis' => '🎯',
                        'career_guidance' => '💼',
                        'study_tips' => '📚',
                        'personal_development' => '🌱',
                    ];
                    $icon = $topicIcons[$chat['topic']] ?? '💬';
                    echo $icon . ' ';

                    $topicLabels = [
                        'potential_analysis' => 'Analisis Potensi',
                        'career_guidance' => 'Bimbingan Karir',
                        'study_tips' => 'Tips Belajar',
                        'personal_development' => 'Pengembangan Diri',
                    ];
                    echo $topicLabels[$chat['topic']] ?? 'Konsultasi';
                    ?>
                </h1>
                <span class="chat-date"><?= date('d F Y, H:i', strtotime($chat['created_at'])) ?></span>
            </div>
        </div>
        <div class="chat-header-actions">
            <button class="btn btn-outline btn-new-chat" onclick="window.location.href='/profile/chat/create'" data-spa>
                <span class="btn-icon">✨</span>
                <span>Chat Baru</span>
            </button>
        </div>
    </div>

    <div class="chat-messages-container" id="chatMessages">
        <?php if (empty($messages)): ?>
            <div class="chat-empty-state">
                <div class="empty-icon">💭</div>
                <h3>Belum Ada Pesan</h3>
                <p>Mulai percakapan dengan AI konselor Anda</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="chat-message <?= $message['role'] ?>" data-message-id="<?= $message['id'] ?>">
                    <div class="message-avatar">
                        <?php if ($message['role'] === 'user'): ?>
                            <span class="avatar-icon">👤</span>
                        <?php else: ?>
                            <span class="avatar-icon">🤖</span>
                        <?php endif; ?>
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
                        <div class="message-text"><?= nl2br(htmlspecialchars($message['content'])) ?></div>
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

    <div class="chat-input-container">
        <form id="chatInputForm" class="chat-input-form">
            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId) ?>">
            <textarea
                id="messageInput"
                name="message"
                class="chat-input-textarea"
                rows="2"
                placeholder="Ketik pesan Anda di sini..."
                required></textarea>
            <button type="submit" class="btn btn-primary btn-send" id="btnSend">
                <span class="btn-icon">📤</span>
                <span>Kirim</span>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto scroll to bottom
    function scrollToBottom() {
        const container = document.getElementById('chatMessages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Initial scroll
    scrollToBottom();

    // Handle form submit
    document.getElementById('chatInputForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('btnSend');
        const message = messageInput.value.trim();

        if (!message) return;

        // Disable input
        messageInput.disabled = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="btn-icon">⏳</span><span>Mengirim...</span>';

        // Add temporary user message
        const messagesContainer = document.getElementById('chatMessages');
        const tempUserMessage = document.createElement('div');
        tempUserMessage.className = 'chat-message user temp';
        tempUserMessage.innerHTML = `
            <div class="message-avatar">
                <span class="avatar-icon">👤</span>
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-role">Anda</span>
                    <span class="message-time">Baru saja</span>
                </div>
                <div class="message-text">${message.replace(/</g, '<').replace(/>/g, '>')}</div>
            </div>
        `;
        messagesContainer.appendChild(tempUserMessage);
        scrollToBottom();

        try {
            // Buat FormData dan pastikan message terkirim
            const formData = new FormData(this);
            // Append message secara manual untuk memastikan terkirim
            formData.set('message', message);

            const payload = {
                session_id: formData.get('session_id'),
                message: message
            };
            console.log('Payload to be sent:', payload);

            const response = await fetch('/profile/chat/send', {
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
                userMessage.className = 'chat-message user';
                userMessage.dataset.messageId = Date.now();
                userMessage.innerHTML = `
                    <div class="message-avatar">
                        <span class="avatar-icon">👤</span>
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-role">Anda</span>
                            <span class="message-time">${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                        <div class="message-text">${message.replace(/</g, '<').replace(/>/g, '>')}</div>
                    </div>
                `;
                messagesContainer.appendChild(userMessage);

                // Add AI response
                const aiMessage = document.createElement('div');
                aiMessage.className = 'chat-message assistant';
                aiMessage.dataset.messageId = result.message_id;
                aiMessage.innerHTML = `
                <div class="message-avatar">
                    <span class="avatar-icon">🤖</span>
                </div>
                <div class="message-content">
                    <div class="message-header">
                        <span class="message-role">AI Konselor</span>
                        <span class="message-time">${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                    <div class="message-text">${result.content.replace(/</g, '<').replace(/>/g, '>').replace(/\n/g, '<br>')}</div>
                </div>
            `;
                messagesContainer.appendChild(aiMessage);

                // Scroll to bottom
                scrollToBottom();
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
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<span class="btn-icon">📤</span><span>Kirim</span>';
            messageInput.focus();
        }
    });

    // Auto-resize textarea
    document.getElementById('messageInput').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });

    // Handle Enter key (send with Shift+Enter, new line with Enter)
    document.getElementById('messageInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chatInputForm').dispatchEvent(new Event('submit'));
        }
    });
</script>