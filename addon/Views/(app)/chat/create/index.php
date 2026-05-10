<?php

/**
 * View: Buat Chat Konsultasi Baru
 * 
 * @var array|null $studentProfile Data profil siswa
 */
?>

<div class="chat-create-wrapper">
    <div class="chat-create-container">
        <!-- Hero Header -->
        <div class="chat-create-hero">
            <div class="back-button-wrapper">
                <a href="/chat" class="back-button" data-spa>
                    <span class="back-icon">←</span>
                    <span>Kembali</span>
                </a>
            </div>
            <h1>💬 Buat Chat Konsultasi Baru</h1>
            <p>Pilih topik konsultasi dan mulailah berbicara dengan AI</p>
        </div>

        <div class="chat-create-content">
            <!-- Main Form -->
            <div class="chat-create-form-card">
                <h2>Formulir Konsultasi</h2>

                <form id="chatCreateForm">
                    <div class="form-group">
                        <label for="topic" class="form-label form-label-required">Topik Konsultasi</label>
                        <select id="topic" name="topic" class="form-select" required>
                            <option value="potential_analysis">🎯 Analisis Potensi Diri</option>
                            <option value="career_guidance">💼 Bimbingan Karir</option>
                            <option value="study_tips">📚 Tips Belajar</option>
                            <option value="personal_development">🌱 Pengembangan Diri</option>
                        </select>
                        <p class="form-help">Pilih topik yang ingin Anda konsultasikan</p>

                        <!-- Topic Preview -->
                        <div class="topic-preview">
                            <div class="topic-preview-title">Topik Dipilih</div>
                            <div class="topic-preview-content">
                                <div class="topic-preview-icon" id="topicPreviewIcon" data-topic="potential_analysis">🎯</div>
                                <span class="topic-preview-text" id="topicPreviewText">Analisis Potensi Diri</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Pesan Awal <span style="font-weight: 400; color: var(--text-secondary);">(Opsional)</span></label>
                        <textarea
                            id="message"
                            name="message"
                            class="form-textarea"
                            rows="4"
                            placeholder="Contoh: Halo, saya ingin mengetahui potensi saya berdasarkan hasil tes psikologi..."></textarea>
                        <p class="form-help">Kosongkan untuk memulai dengan pesan default</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <span class="btn-icon">✨</span>
                            <span class="btn-text">Mulai Chat</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tips Sidebar -->
            <div class="chat-tips-card">
                <h3>💡 Tips Konsultasi</h3>
                <ul class="tips-list">
                    <li>
                        <span class="tip-icon">📝</span>
                        <div class="tip-content">
                            <strong>Bersikaplah Terbuka</strong>
                            <p>Jangan ragu untuk berbagi perasaan dan pemikiran Anda</p>
                        </div>
                    </li>
                    <li>
                        <span class="tip-icon">🎯</span>
                        <div class="tip-content">
                            <strong>Fokus pada Tujuan</strong>
                            <p>Tentukan apa yang ingin Anda capai dari konsultasi ini</p>
                        </div>
                    </li>
                    <li>
                        <span class="tip-icon">💭</span>
                        <div class="tip-content">
                            <strong>Refleksikan Jawaban</strong>
                            <p>Pikirkan baik-baik setiap saran yang diberikan AI</p>
                        </div>
                    </li>
                    <li>
                        <span class="tip-icon">🔒</span>
                        <div class="tip-content">
                            <strong>Privasi Terjamin</strong>
                            <p>Semua percakapan Anda tersimpan dengan aman</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Topic preview update
    const topicSelect = document.getElementById('topic');
    const topicPreviewIcon = document.getElementById('topicPreviewIcon');
    const topicPreviewText = document.getElementById('topicPreviewText');

    const topicData = {
        'potential_analysis': {
            icon: '🎯',
            text: 'Analisis Potensi Diri'
        },
        'career_guidance': {
            icon: '💼',
            text: 'Bimbingan Karir'
        },
        'study_tips': {
            icon: '📚',
            text: 'Tips Belajar'
        },
        'personal_development': {
            icon: '🌱',
            text: 'Pengembangan Diri'
        }
    };

    topicSelect.addEventListener('change', function() {
        const selected = this.value;
        const data = topicData[selected];
        if (data) {
            topicPreviewIcon.textContent = data.icon;
            topicPreviewIcon.setAttribute('data-topic', selected);
            topicPreviewText.textContent = data.text;
        }
    });

    // Form submit handler - Original logic preserved
    document.getElementById('chatCreateForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('.btn-submit');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnIcon = submitBtn.querySelector('.btn-icon');
        const originalIcon = btnIcon.textContent;
        const originalText = btnText.textContent;

        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        btnIcon.textContent = '⏳';
        btnText.textContent = 'Memproses...';

        const formData = new FormData(this);

        try {
            const response = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData).toString()
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                alert('Gagal memulai chat: ' + (result.error || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                btnIcon.textContent = originalIcon;
                btnText.textContent = originalText;
            }
        } catch (error) {
            alert('Terjadi kesalahan saat memulai chat');
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            btnIcon.textContent = originalIcon;
            btnText.textContent = originalText;
        }
    });
</script>