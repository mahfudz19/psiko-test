<div class="chat-history-wrapper">
    <div class="chat-history-container">
        <!-- Hero Header -->
        <div class="chat-history-hero">
            <h1>💬 Riwayat Chat Konsultasi</h1>
            <p>Konsultasikan potensi, minat, dan bakatmu dengan AI</p>
        </div>

        <!-- Toolbar -->
        <div class="chat-history-toolbar">
            <div class="chat-search-wrapper">
                <span class="chat-search-icon">🔍</span>
                <input type="text" class="chat-search-input" placeholder="Cari riwayat chat..." id="chatSearch">
            </div>
            <select class="chat-filter-select" id="chatFilter">
                <option value="all">Semua Topik</option>
                <option value="potential_analysis">🎯 Analisis Potensi</option>
                <option value="career_guidance">💼 Bimbingan Karir</option>
                <option value="study_tips">📚 Tips Belajar</option>
                <option value="personal_development">🌱 Pengembangan Diri</option>
            </select>
            <a href="/chat/create" class="chat-new-btn" data-spa>
                <span>✨</span>
                <span>Chat Baru</span>
            </a>
        </div>

        <?php if (empty($chatHistory)): ?>
            <!-- Empty State -->
            <div class="chat-history-empty">
                <div class="empty-state-icon">💭</div>
                <h3>Belum Ada Riwayat Chat</h3>
                <p>Mulai konsultasi pertama Anda dengan AI untuk membahas potensi, minat, dan bakat Anda.</p>
                <a href="/chat/create" class="chat-new-btn" data-spa>
                    <span>✨</span>
                    <span>Mulai Chat Sekarang</span>
                </a>
            </div>
        <?php else: ?>
            <!-- Chat Grid -->
            <div class="chat-history-grid">
                <?php foreach ($chatHistory as $chat): ?>
                    <div class="chat-card" data-session-id="<?= htmlspecialchars($chat['session_id']) ?>" data-topic="<?= htmlspecialchars($chat['topic']) ?>">
                        <div class="chat-card-topic">
                            <div class="chat-card-topic-icon" data-topic="<?= htmlspecialchars($chat['topic']) ?>">
                                <?php
                                $topicIcons = [
                                    'potential_analysis' => '🎯',
                                    'career_guidance' => '💼',
                                    'study_tips' => '📚',
                                    'personal_development' => '🌱',
                                ];
                                echo $topicIcons[$chat['topic']] ?? '💬';
                                ?>
                            </div>
                            <div class="chat-card-topic-info">
                                <div class="chat-card-topic-label">
                                    <?php
                                    $topicLabels = [
                                        'potential_analysis' => 'Analisis Potensi',
                                        'career_guidance' => 'Bimbingan Karir',
                                        'study_tips' => 'Tips Belajar',
                                        'personal_development' => 'Pengembangan Diri',
                                    ];
                                    echo $topicLabels[$chat['topic']] ?? 'Konsultasi';
                                    ?>
                                </div>
                                <span class="chat-card-topic-badge"><?= $topicLabels[$chat['topic']] ?? 'Konsultasi' ?></span>
                            </div>
                        </div>

                        <div class="chat-card-preview">
                            <?php
                            $previewText = $chat['preview'] ?? 'Klik untuk melihat detail percakapan...';
                            echo htmlspecialchars(substr($previewText, 0, 100));
                            ?>
                        </div>

                        <div class="chat-card-meta">
                            <div class="chat-card-date">
                                <span class="chat-card-date-day"><?= date('d M Y', strtotime($chat['created_at'])) ?></span>
                                <span class="chat-card-date-time"><?= date('H:i', strtotime($chat['created_at'])) ?> WIB</span>
                            </div>
                            <div class="chat-card-actions">
                                <a href="/chat/<?= htmlspecialchars($chat['session_id']) ?>" class="chat-card-btn chat-card-btn-view" data-spa title="Lihat">
                                    👁️
                                </a>
                                <button class="chat-card-btn chat-card-btn-delete btn-delete-chat" data-session-id="<?= htmlspecialchars($chat['session_id']) ?>" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Search functionality
    const chatSearch = document.getElementById('chatSearch');
    const chatFilter = document.getElementById('chatFilter');

    function filterChats() {
        const searchTerm = chatSearch.value.toLowerCase();
        const selectedTopic = chatFilter.value;
        const allCards = document.querySelectorAll('.chat-card');

        allCards.forEach(card => {
            const topic = card.dataset.topic;
            const topicLabel = card.querySelector('.chat-card-topic-label').textContent.toLowerCase();
            const preview = card.querySelector('.chat-card-preview').textContent.toLowerCase();

            const matchesSearch = searchTerm === '' || topicLabel.includes(searchTerm) || preview.includes(searchTerm);
            const matchesTopic = selectedTopic === 'all' || topic === selectedTopic;

            if (matchesSearch && matchesTopic) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (chatSearch) chatSearch.addEventListener('input', filterChats);
    if (chatFilter) chatFilter.addEventListener('change', filterChats);

    // Delete chat handler - Using event delegation for better reliability
    document.addEventListener('click', async function(e) {
        const deleteBtn = e.target.closest('.btn-delete-chat');
        if (!deleteBtn) return;

        e.preventDefault();
        e.stopPropagation();

        if (!confirm('Apakah Anda yakin ingin menghapus riwayat chat ini?')) {
            return;
        }

        const card = deleteBtn.closest('.chat-card');
        if (!card) return;

        card.classList.add('loading');

        const sessionId = deleteBtn.dataset.sessionId;
        console.log('Deleting session:', sessionId);

        try {
            const response = await fetch('/chat/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `session_id=${encodeURIComponent(sessionId)}`
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Delete result:', result);

            if (result.success) {
                // Add fade out animation
                card.style.transition = 'all 0.3s ease';
                card.style.transform = 'scale(0.9)';
                card.style.opacity = '0';

                setTimeout(() => {
                    card.remove();

                    // Check if no more chats
                    const remainingCards = document.querySelectorAll('.chat-card:not([style*="display: none"])').length;
                    if (remainingCards === 0) {
                        // Reload to show empty state
                        window.location.reload();
                    }
                }, 300);
            } else {
                card.classList.remove('loading');
                alert('Gagal menghapus chat: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Delete error:', error);
            card.classList.remove('loading');
            alert('Terjadi kesalahan saat menghapus chat: ' + error.message);
        }
    });

    // Click card to view - Using event delegation
    document.addEventListener('click', function(e) {
        const card = e.target.closest('.chat-card');
        if (!card) return;

        // Don't navigate if clicking on buttons
        if (e.target.closest('.chat-card-btn')) return;

        const sessionId = card.dataset.sessionId;
        if (sessionId) {
            window.location.href = `/chat/${sessionId}`;
        }
    });
</script>