<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $email Email tujuan OTP
 * @var array|null $infoPanel
 */
?>

<div class="auth-info-panel">
    <div class="auth-info-content">
        <div class="auth-logo">
            <img src="/logo_app/mazu-icon.svg" alt="Psyco-Test Logo">
            <span>Psyco-Test</span>
        </div>

        <?php
        $info = $infoPanel ?? [
            'title' => 'Email Verifikasi Terkirim! ✉️',
            'description' => 'Kami telah mengirimkan instruksi verifikasi ke alamat email Anda.',
            'features' => [
                ['icon' => '📬', 'title' => 'Periksa Email', 'description' => 'Jangan lupa cek folder Spam jika tidak menemukannya di Inbox.'],
                ['icon' => '🔄', 'title' => 'Belum Terima?', 'description' => 'Anda dapat meminta pengiriman ulang kode setelah beberapa saat.'],
            ]
        ];
        ?>

        <h1><?= $info['title'] ?></h1>
        <p><?= $info['description'] ?></p>

        <?php if (!empty($info['features'])): ?>
            <div class="auth-features">
                <?php foreach ($info['features'] as $feature): ?>
                    <div class="feature-item">
                        <span class="feature-icon"><?= $feature['icon'] ?></span>
                        <div class="feature-text">
                            <h3><?= $feature['title'] ?></h3>
                            <p><?= $feature['description'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="auth-info-footer">
        &copy; <?= date('Y') ?> Psyco-Test. Powered by Mazu Framework.
    </div>
</div>

<div class="auth-form-panel">
    <div class="auth-form-content">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 64px; margin-bottom: 24px;">📧</div>
            <h1 class="auth-title" style="margin-bottom: 8px;">Email Terkirim!</h1>
            <p style="color: var(--text-secondary);">
                Kami telah mengirim kode verifikasi ke<br>
                <strong style="color: var(--text-primary);"><?= htmlspecialchars($email ?? '') ?></strong>
            </p>
        </div>

        <div style="background-color: #f8fafc; border-radius: 20px; padding: 24px; margin-bottom: 32px;">
            <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                <span style="background-color: var(--primary-main); color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;">1</span>
                <span style="font-size: 0.95rem; color: var(--text-primary);">Buka inbox email Anda</span>
            </div>
            <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                <span style="background-color: var(--primary-main); color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;">2</span>
                <span style="font-size: 0.95rem; color: var(--text-primary);">Cari email dari <strong>Psyco-Test</strong></span>
            </div>
            <div style="display: flex; gap: 16px;">
                <span style="background-color: var(--primary-main); color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;">3</span>
                <span style="font-size: 0.95rem; color: var(--text-primary);">Salin kode 6 digit dan masukkan di halaman verifikasi</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            <a data-spa href="/verify-otp?email=<?= urlencode($email ?? '') ?>" class="auth-button" data-spa style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Buka Halaman Verifikasi
            </a>

            <button type="button" class="google-button" id="resend-from-sent" disabled>
                <span class="button-text">Kirim Ulang Email</span>
                <span class="button-countdown" style="font-size: 0.85rem; opacity: 0.7;">(60s)</span>
            </button>
        </div>

        <div class="auth-links" style="margin-top: 32px;">
            <a data-spa href="/register" class="auth-link" style="font-size: 0.875rem; color: var(--text-secondary);">
                ← Kembali ke Register
            </a>
        </div>
    </div>
</div>

<script>
    (function() {
        const resendButton = document.getElementById('resend-from-sent');
        const countdownSpan = resendButton.querySelector('.button-countdown');
        let cooldown = 60;

        const interval = setInterval(() => {
            if (cooldown <= 0) {
                clearInterval(interval);
                resendButton.disabled = false;
                countdownSpan.textContent = '';
                return;
            }
            cooldown--;
            countdownSpan.textContent = `(${cooldown}s)`;
        }, 1000);

        resendButton.addEventListener('click', () => {
            window.location.href = '/resend-otp?email=' + encodeURIComponent('<?= htmlspecialchars($email ?? '') ?>');
        });
    })();
</script>