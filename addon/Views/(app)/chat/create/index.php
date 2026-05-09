<?php

/**
 * View: Buat Chat Konsultasi Baru
 * 
 * @var array|null $studentProfile Data profil siswa
 */
?>

<div class="chat-create-container">
    <div class="chat-create-header">
        <a href="/chat" class="back-button" data-spa>
            <span class="back-icon">←</span>
            <span>Kembali</span>
        </a>
        <h1 class="chat-create-title">💬 Chat Konsultasi Baru</h1>
        <p class="chat-create-subtitle">Pilih topik konsultasi dan mulailah berbicara dengan AI</p>
    </div>

    <div class="chat-create-content">
        <form id="chatCreateForm" class="chat-create-form">
            <div class="form-group">
                <label for="topic" class="form-label">Topik Konsultasi</label>
                <select id="topic" name="topic" class="form-select" required>
                    <option value="potential_analysis">🎯 Analisis Potensi Diri</option>
                    <option value="career_guidance">💼 Bimbingan Karir</option>
                    <option value="study_tips">📚 Tips Belajar</option>
                    <option value="personal_development">🌱 Pengembangan Diri</option>
                </select>
                <p class="form-help">Pilih topik yang ingin Anda konsultasikan</p>
            </div>

            <div class="form-group">
                <label for="message" class="form-label">Pesan Awal (Opsional)</label>
                <textarea
                    id="message"
                    name="message"
                    class="form-textarea"
                    rows="4"
                    placeholder="Contoh: Halo, saya ingin mengetahui potensi saya berdasarkan hasil tes psikologi..."></textarea>
                <p class="form-help">Kosongkan untuk memulai dengan pesan default</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-submit">
                    <span class="btn-icon">✨</span>
                    <span>Mulai Chat</span>
                </button>
            </div>
        </form>

        <div class="chat-tips">
            <h3>💡 Tips Konsultasi</h3>
            <ul class="tips-list">
                <li>
                    <span class="tip-icon">📝</span>
                    <div>
                        <strong>Bersikaplah Terbuka</strong>
                        <p>Jangan ragu untuk berbagi perasaan dan pemikiran Anda</p>
                    </div>
                </li>
                <li>
                    <span class="tip-icon">🎯</span>
                    <div>
                        <strong>Fokus pada Tujuan</strong>
                        <p>Tentukan apa yang ingin Anda capai dari konsultasi ini</p>
                    </div>
                </li>
                <li>
                    <span class="tip-icon">💭</span>
                    <div>
                        <strong>Refleksikan Jawaban</strong>
                        <p>Pikirkan baik-baik setiap saran yang diberikan AI</p>
                    </div>
                </li>
                <li>
                    <span class="tip-icon">🔒</span>
                    <div>
                        <strong>Privasi Terjamin</strong>
                        <p>Semua percakapan Anda tersimpan dengan aman</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.getElementById('chatCreateForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('.btn-submit');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="btn-icon">⏳</span><span>Memproses...</span>';

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
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            alert('Terjadi kesalahan saat memulai chat');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
</script>