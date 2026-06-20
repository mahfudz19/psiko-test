<?php

/**
 * Settings Page - Pengaturan Akun & Preferensi
 * 
 * @var \App\Core\View\PageMeta $meta
 */

$userName = $_SESSION['auth.user_name'] ?? 'User';
$userEmail = $_SESSION['auth.user_email'] ?? 'user@example.com';
$memberSince = 'Januari 2025';

// Handle success/error messages from URL
$successMessage = $_GET['success'] ?? null;
$errorMessage = $_GET['error'] ?? null;
?>

<main class="settings-main">
    <!-- Page Header -->
    <header class="settings-header">
        <div class="header-content">
            <h1 class="page-title">⚙️ Pengaturan</h1>
            <p class="page-subtitle">Kelola pengaturan akun dan preferensi Anda</p>
        </div>
    </header>

    <!-- Account Settings Section -->
    <section class="settings-section">
        <h2 class="section-title">👤 Akun Saya</h2>
        <div class="settings-card">
            <div class="account-overview">
                <div class="account-avatar">
                    <img src="<?= $_SESSION['auth.user_avatar'] ?? $_SESSION['auth.user_avatar_url'] ?? '/logo_app/mazu-icon.svg'; ?>" alt="Avatar" class="avatar-large">
                </div>
                <div class="account-info">
                    <h3 class="account-name"><?= htmlspecialchars($userName) ?></h3>
                    <p class="account-email"><?= htmlspecialchars($userEmail) ?></p>
                    <p class="account-meta">Member sejak <?= $memberSince ?></p>
                </div>
                <a data-spa href="/profile/edit" class="btn btn-secondary btn-sm">Edit Profil</a>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">🔐</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Ubah Password</h3>
                        <p class="settings-item-desc">Gunakan password minimal 8 karakter dengan kombinasi huruf, angka, dan simbol</p>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-modal="change-password">Ubah</button>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">📧</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Email Address</h3>
                        <p class="settings-item-desc">Email digunakan untuk login dan notifikasi</p>
                    </div>
                    <div class="settings-item-value"><?= htmlspecialchars($userEmail) ?></div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Change Password Modal -->
<div class="modal-overlay" id="modal-change-password" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🔐 Ubah Password</h2>
            <button type="button" class="modal-close" data-modal-close="change-password">&times;</button>
        </div>
        <form data-spa method="POST" action="/settings/change-password">
            <div class="modal-body">
                <div class="form-group">
                    <label for="current-password">Password Saat Ini</label>
                    <input type="password" id="current-password" name="current_password" class="form-input" placeholder="Masukkan password saat ini" required>
                </div>
                <div class="form-group">
                    <label for="new-password">Password Baru</label>
                    <input type="password" id="new-password" name="new_password" class="form-input" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm-password" name="new_password_confirmation" class="form-input" placeholder="Ulangi password baru" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close="change-password">Batal</button>
                <button type="submit" class="btn btn-primary">Ubah Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Modal handler - membuka modal saat tombol diklik
     * @param {Event} e - Click event
     */
    document.querySelectorAll('[data-modal]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalName = this.getAttribute('data-modal');
            document.getElementById('modal-' + modalName).style.display = 'flex';
        });
    });

    /**
     * Modal handler - menutup modal saat tombol close diklik
     * @param {Event} e - Click event
     */
    document.querySelectorAll('[data-modal-close]').forEach(close => {
        close.addEventListener('click', function() {
            const modalName = this.getAttribute('data-modal-close');
            document.getElementById('modal-' + modalName).style.display = 'none';
        });
    });

    /**
     * Menutup modal saat overlay diklik
     * @param {Event} e - Click event
     */
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    /**
     * Tampilkan notifikasi success/error dari URL parameter
     */
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        if (success) {
            // Hapus parameter dari URL tanpa reload
            window.history.replaceState({}, document.title, window.location.pathname);

            // Tampilkan toast success (jika tersedia)
            if (typeof window.showToast === 'function') {
                window.showToast('success', success.replace(/\+/g, ' '));
            } else {
                alert('✅ ' + success.replace(/\+/g, ' '));
            }
        }

        if (error) {
            // Hapus parameter dari URL tanpa reload
            window.history.replaceState({}, document.title, window.location.pathname);

            // Tampilkan toast error (jika tersedia)
            if (typeof window.showToast === 'function') {
                window.showToast('error', error.replace(/\+/g, ' '));
            } else {
                alert('⚠️ ' + error.replace(/\+/g, ' '));
            }
        }
    })();
</script>