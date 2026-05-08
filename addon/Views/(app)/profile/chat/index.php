<div class="chat-history-container">
    <div class="chat-history-header">
        <h1 class="chat-history-title">💬 Riwayat Chat Konsultasi</h1>
        <p class="chat-history-subtitle">Konsultasikan potensi, minat, dan bakatmu dengan AI</p>
    </div>

    <div class="chat-history-actions">
        <a href="/profile/chat/create" class="btn btn-primary" data-spa>
            <span class="btn-icon">✨</span>
            <span>Chat Baru</span>
        </a>
    </div>

    <?php if (empty($chatHistory)): ?>
        <div class="chat-history-empty">
            <div class="empty-icon">💭</div>
            <h3>Belum Ada Riwayat Chat</h3>
            <p>Mulai konsultasi pertama Anda dengan AI untuk membahas potensi, minat, dan bakat Anda.</p>
            <a href="/profile/chat/create" class="btn btn-primary" data-spa>
                <span class="btn-icon">✨</span>
                <span>Mulai Chat Sekarang</span>
            </a>
        </div>
    <?php else: ?>
        <div class="chat-history-list">
            <?php foreach ($chatHistory as $chat): ?>
                <div class="chat-history-item" data-session-id="<?= htmlspecialchars($chat['session_id']) ?>">
                    <div class="chat-item-header">
                        <div class="chat-item-topic">
                            <span class="topic-icon">
                                <?php
                                $topicIcons = [
                                    'potential_analysis' => '🎯',
                                    'career_guidance' => '💼',
                                    'study_tips' => '📚',
                                    'personal_development' => '🌱',
                                ];
                                echo $topicIcons[$chat['topic']] ?? '💬';
                                ?>
                            </span>
                            <span class="topic-label">
                                <?php
                                $topicLabels = [
                                    'potential_analysis' => 'Analisis Potensi',
                                    'career_guidance' => 'Bimbingan Karir',
                                    'study_tips' => 'Tips Belajar',
                                    'personal_development' => 'Pengembangan Diri',
                                ];
                                echo $topicLabels[$chat['topic']] ?? 'Konsultasi';
                                ?>
                            </span>
                        </div>
                        <span class="chat-item-date"><?= date('d M Y', strtotime($chat['created_at'])) ?></span>
                    </div>

                    <div class="chat-item-footer">
                        <span class="chat-item-time"><?= date('H:i', strtotime($chat['created_at'])) ?></span>
                        <div class="chat-item-actions">
                            <a href="/profile/chat/<?= htmlspecialchars($chat['session_id']) ?>" class="btn btn-sm btn-outline" data-spa>
                                <span class="btn-icon">👁️</span>
                                <span>Lihat</span>
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete-chat" data-session-id="<?= htmlspecialchars($chat['session_id']) ?>">
                                <span class="btn-icon">🗑️</span>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Delete chat handler
    document.querySelectorAll('.btn-delete-chat').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Apakah Anda yakin ingin menghapus riwayat chat ini?')) {
                return;
            }

            const sessionId = this.dataset.sessionId;

            try {
                const response = await fetch('/profile/chat/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `session_id=${encodeURIComponent(sessionId)}`
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect || '/profile/chat';
                } else {
                    alert('Gagal menghapus chat: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                alert('Terjadi kesalahan saat menghapus chat');
            }
        });
    });
</script>