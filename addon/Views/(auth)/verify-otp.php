<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $email Email user yang akan diverifikasi
 * @var string|null $error Error message (jika ada)
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
            'title' => 'Verifikasi Akun Anda 🛡️',
            'description' => 'Satu langkah lagi untuk mengamankan akun Anda. Masukkan kode OTP yang kami kirimkan ke email Anda.',
            'features' => [
                ['icon' => '📧', 'title' => 'Cek Inbox', 'description' => 'Kode OTP dikirimkan ke email terdaftar Anda.'],
                ['icon' => '⏳', 'title' => 'Batas Waktu', 'description' => 'Kode berlaku selama 15 menit sejak dikirimkan.'],
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
        <h1 class="auth-title">Verifikasi OTP</h1>
        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 32px;">
            Kami telah mengirim kode 6-digit ke<br>
            <strong style="color: var(--text-primary);"><?= htmlspecialchars($email ?? '') ?></strong>
        </p>

        <?php if (isset($error)): ?>
            <div class="auth-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="auth-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form data-spa method="POST" action="/verify-otp" id="otp-form">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

            <div class="otp-inputs-wrapper">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required class="otp-digit" data-index="0">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required class="otp-digit" data-index="1">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required class="otp-digit" data-index="2">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required class="otp-digit" data-index="3">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required class="otp-digit" data-index="4">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required class="otp-digit" data-index="5">
            </div>

            <input type="hidden" name="otp_code" id="otp-code-hidden" required>

            <button type="submit" class="auth-button" id="verify-button" disabled>
                <span class="button-text">Verifikasi</span>
                <span class="button-loading" style="display: none;">Memverifikasi...</span>
            </button>
        </form>

        <div class="auth-links" style="margin-top: 32px;">
            <div class="otp-timer-text" id="otp-timer" style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 16px;">
                Kode berlaku <span id="timer-text" style="font-weight: 600; color: var(--primary-main);">15:00</span>
            </div>

            <button type="button" class="auth-link" id="resend-button" disabled
                style="background: none; border: none; cursor: pointer; padding: 0; font-family: inherit;">
                Kirim Ulang OTP <span class="resend-countdown">(60s)</span>
            </button>

            <div style="margin-top: 24px;">
                <a data-spa href="/register" class="auth-link" style="font-size: 0.875rem; color: var(--text-secondary);">
                    ← Kembali ke Register
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const inputs = document.querySelectorAll('.otp-digit');
        const form = document.getElementById('otp-form');
        const verifyButton = document.getElementById('verify-button');
        const otpHidden = document.getElementById('otp-code-hidden');
        const timerText = document.getElementById('timer-text');
        const resendButton = document.getElementById('resend-button');
        const resendCountdown = resendButton.querySelector('.resend-countdown');

        let timeLeft = 900; // 15 minutes
        let resendCooldown = 60; // 60 seconds

        inputs[0].focus();

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (!/^\d*$/.test(e.target.value)) {
                    e.target.value = '';
                    return;
                }
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                checkAllFilled();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = e.clipboardData.getData('text').slice(0, 6);
                if (/^\d{6}$/.test(pasted)) {
                    inputs.forEach((inp, i) => {
                        inp.value = pasted[i];
                        if (i < 5) inputs[i + 1].focus();
                    });
                    checkAllFilled();
                }
            });
        });

        function checkAllFilled() {
            const allFilled = Array.from(inputs).every(i => i.value.length === 1);
            if (allFilled) {
                verifyButton.disabled = false;
                otpHidden.value = Array.from(inputs).map(i => i.value).join('');
            } else {
                verifyButton.disabled = true;
            }
        }

        form.addEventListener('submit', () => {
            verifyButton.disabled = true;
            verifyButton.querySelector('.button-text').style.display = 'none';
            verifyButton.querySelector('.button-loading').style.display = 'inline-flex';
        });

        const interval = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(interval);
                timerText.textContent = 'Expired';
                return;
            }
            timeLeft--;
            const m = Math.floor(timeLeft / 60);
            const s = timeLeft % 60;
            timerText.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }, 1000);

        let cooldown = resendCooldown;
        const resendInterval = setInterval(() => {
            if (cooldown <= 0) {
                clearInterval(resendInterval);
                resendButton.disabled = false;
                resendCountdown.textContent = '';
                return;
            }
            cooldown--;
            resendCountdown.textContent = `(${cooldown}s)`;
        }, 1000);

        resendButton.addEventListener('click', () => {
            window.location.href = '/resend-otp?email=' + encodeURIComponent('<?= htmlspecialchars($email ?? '') ?>');
        });
    })();
</script>