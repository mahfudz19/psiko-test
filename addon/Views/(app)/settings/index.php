<?php

/**
 * Settings Page - Pengaturan Akun & Preferensi
 * 
 * @var \App\Core\View\PageMeta $meta
 */

// Dummy data untuk presentasi
$userName = $_SESSION['auth.user_name'] ?? 'User';
$userEmail = $_SESSION['auth.user_email'] ?? 'user@example.com';
$memberSince = 'Januari 2025';

// Notification settings (dummy)
$notifications = [
    'email_notifications' => true,
    'login_notifications' => true,
    'pmb_updates' => true,
    'scholarship_alerts' => true,
    'marketing_emails' => false
];

// Privacy settings (dummy)
$privacy = [
    'profile_visibility' => 'public', // public, students, private
    'show_contact_info' => false,
    'show_academic_info' => true,
    'allow_messages' => true
];

// Active sessions (dummy)
$activeSessions = [
    [
        'device' => 'Chrome on Windows',
        'location' => 'Makassar, Indonesia',
        'lastActive' => 'Sekarang',
        'current' => true
    ],
    [
        'device' => 'Safari on iPhone',
        'location' => 'Makassar, Indonesia',
        'lastActive' => '2 jam yang lalu',
        'current' => false
    ],
    [
        'device' => 'Chrome on Android',
        'location' => 'Jakarta, Indonesia',
        'lastActive' => '1 hari yang lalu',
        'current' => false
    ]
];
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

    <!-- Notification Settings Section -->
    <section class="settings-section">
        <h2 class="section-title">🔔 Notifikasi</h2>
        <div class="settings-card">
            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">📬</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Email Notifications</h3>
                        <p class="settings-item-desc">Terima update penting via email</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $notifications['email_notifications'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">🔐</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Login Notifications</h3>
                        <p class="settings-item-desc">Dapatkan notifikasi saat ada login baru ke akun Anda</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $notifications['login_notifications'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">🎓</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">PMB Updates</h3>
                        <p class="settings-item-desc">Update status pendaftaran PMB</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $notifications['pmb_updates'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">💰</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Beasiswa Alerts</h3>
                        <p class="settings-item-desc">Notifikasi saat ada beasiswa baru yang sesuai</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $notifications['scholarship_alerts'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">📢</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Marketing Emails</h3>
                        <p class="settings-item-desc">Info promo, event, dan newsletter</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $notifications['marketing_emails'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </section>

    <!-- Privacy Settings Section -->
    <section class="settings-section">
        <h2 class="section-title">🔒 Privacy</h2>
        <div class="settings-card">
            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">👁️</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Profile Visibility</h3>
                        <p class="settings-item-desc">Siapa yang bisa melihat profil Anda</p>
                    </div>
                </div>
                <select class="settings-select">
                    <option value="public" <?= $privacy['profile_visibility'] === 'public' ? 'selected' : '' ?>>🌍 Public - Semua orang</option>
                    <option value="students" <?= $privacy['profile_visibility'] === 'students' ? 'selected' : '' ?>>👥 Students Only - Hanya siswa terdaftar</option>
                    <option value="private" <?= $privacy['profile_visibility'] === 'private' ? 'selected' : '' ?>>🔒 Private - Hanya saya</option>
                </select>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">📞</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Show Contact Info</h3>
                        <p class="settings-item-desc">Tampilkan email dan nomor telepon di profil</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $privacy['show_contact_info'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">📚</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Show Academic Info</h3>
                        <p class="settings-item-desc">Tampilkan nilai dan prestasi di profil</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $privacy['show_academic_info'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-item-content">
                    <div class="settings-item-icon">💬</div>
                    <div class="settings-item-info">
                        <h3 class="settings-item-title">Allow Messages</h3>
                        <p class="settings-item-desc">Izinkan siswa lain mengirim pesan</p>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" <?= $privacy['allow_messages'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </section>

    <!-- Active Sessions Section -->
    <section class="settings-section">
        <h2 class="section-title">🖥️ Active Sessions</h2>
        <p class="section-desc">Kelola device yang sedang login dengan akun Anda</p>
        <div class="settings-card">
            <div class="sessions-list">
                <?php foreach ($activeSessions as $index => $session): ?>
                    <div class="session-item <?= $session['current'] ? 'session-current' : '' ?>">
                        <div class="session-icon">
                            <?php if (strpos($session['device'], 'Chrome') !== false): ?>
                                🌐
                            <?php elseif (strpos($session['device'], 'Safari') !== false): ?>
                                🧭
                            <?php elseif (strpos($session['device'], 'Android') !== false): ?>
                                📱
                            <?php else: ?>
                                💻
                            <?php endif; ?>
                        </div>
                        <div class="session-info">
                            <h3 class="session-device"><?= htmlspecialchars($session['device']) ?></h3>
                            <p class="session-location">📍 <?= htmlspecialchars($session['location']) ?></p>
                            <p class="session-last-active"><?= $session['lastActive'] ?></p>
                        </div>
                        <?php if ($session['current']): ?>
                            <span class="session-badge session-badge--current">Active Now</span>
                        <?php else: ?>
                            <button type="button" class="btn btn-danger btn-sm session-revoke">Revoke</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Danger Zone Section -->
    <section class="settings-section">
        <h2 class="section-title">⚠️ Danger Zone</h2>
        <div class="settings-card danger-zone">
            <div class="danger-zone-content">
                <div class="danger-zone-info">
                    <h3 class="danger-zone-title">🗑️ Delete Account</h3>
                    <p class="danger-zone-desc">
                        Menghapus akun akan menghapus semua data Anda termasuk:
                    <ul>
                        <li>Profil dan data pribadi</li>
                        <li>Hasil analisis AI</li>
                        <li>Progress PMB simulation</li>
                        <li>Semua data akademik dan prestasi</li>
                    </ul>
                    <strong>Tindakan ini tidak dapat dibatalkan!</strong>
                    </p>
                </div>
                <button type="button" class="btn btn-danger" data-modal="delete-account">Delete Account</button>
            </div>
        </div>
    </section>

    <!-- Save Button -->
    <div class="settings-actions">
        <button type="button" class="btn btn-primary btn-lg" id="save-settings">
            💾 Simpan Perubahan
        </button>
    </div>
</main>

<!-- Change Password Modal (Hidden by default) -->
<div class="modal-overlay" id="modal-change-password" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🔐 Ubah Password</h2>
            <button type="button" class="modal-close" data-modal-close="change-password">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="current-password">Password Saat Ini</label>
                <input type="password" id="current-password" class="form-input" placeholder="Masukkan password saat ini">
            </div>
            <div class="form-group">
                <label for="new-password">Password Baru</label>
                <input type="password" id="new-password" class="form-input" placeholder="Minimal 8 karakter">
            </div>
            <div class="form-group">
                <label for="confirm-password">Konfirmasi Password Baru</label>
                <input type="password" id="confirm-password" class="form-input" placeholder="Ulangi password baru">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-modal-close="change-password">Batal</button>
            <button type="button" class="btn btn-primary">Ubah Password</button>
        </div>
    </div>
</div>

<!-- Delete Account Modal (Hidden by default) -->
<div class="modal-overlay" id="modal-delete-account" style="display: none;">
    <div class="modal-content modal-content--danger">
        <div class="modal-header">
            <h2>⚠️ Delete Account</h2>
            <button type="button" class="modal-close" data-modal-close="delete-account">&times;</button>
        </div>
        <div class="modal-body">
            <p class="delete-warning">
                Apakah Anda <strong>YAKIN</strong> ingin menghapus akun?
            </p>
            <p>Semua data akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
            <div class="form-group">
                <label for="confirm-delete">Ketik "DELETE" untuk konfirmasi:</label>
                <input type="text" id="confirm-delete" class="form-input" placeholder="Ketik DELETE di sini">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-modal-close="delete-account">Batal</button>
            <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete Account</button>
        </div>
    </div>
</div>

<script>
    // Modal handling
    document.querySelectorAll('[data-modal]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalName = this.getAttribute('data-modal');
            document.getElementById('modal-' + modalName).style.display = 'flex';
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(close => {
        close.addEventListener('click', function() {
            const modalName = this.getAttribute('data-modal-close');
            document.getElementById('modal-' + modalName).style.display = 'none';
        });
    });

    // Close modal when clicking overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    // Save settings button
    document.getElementById('save-settings')?.addEventListener('click', function() {
        // Dummy save action
        const btn = this;
        btn.textContent = '✅ Tersimpan!';
        btn.disabled = true;

        setTimeout(() => {
            btn.textContent = '💾 Simpan Perubahan';
            btn.disabled = false;
        }, 2000);
    });

    // Session revoke
    document.querySelectorAll('.session-revoke').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionItem = this.closest('.session-item');
            sessionItem.style.opacity = '0.5';
            this.textContent = 'Revoked';
            this.disabled = true;
        });
    });

    // Delete account confirmation
    document.getElementById('confirm-delete-btn')?.addEventListener('click', function() {
        const confirmInput = document.getElementById('confirm-delete');
        if (confirmInput.value === 'DELETE') {
            alert('Account deleted (dummy action)');
            document.getElementById('modal-delete-account').style.display = 'none';
        } else {
            confirmInput.style.borderColor = 'var(--md-sys-color-error)';
        }
    });
</script>