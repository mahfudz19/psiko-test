<?php

/**
 * Profile Index View
 * 
 * @var array $profile Profile data
 * @var string $role User role
 */
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Profile</h1>
        <a href="/profile/edit" class="btn btn-primary">Edit Profile</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <div class="profile-content">
        <!-- Avatar Section -->
        <div class="profile-avatar-section">
            <div class="avatar-wrapper">
                <?php if (!empty($profile['avatar'])): ?>
                    <img src="<?= $profile['avatar'] ?>" alt="Avatar" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">
                        <?= strtoupper(substr($profile['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <form id="avatar-upload-form" class="avatar-upload-form">
                <input type="file" id="avatar-input" name="avatar" accept="image/*" hidden>
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('avatar-input').click()">
                    Upload Avatar
                </button>
            </form>
            <div id="avatar-upload-status" class="upload-status"></div>
        </div>

        <!-- Personal Information -->
        <div class="profile-section">
            <h2>Informasi Pribadi</h2>
            <div class="profile-grid">
                <div class="profile-item">
                    <label>Nama Lengkap</label>
                    <p><?= htmlspecialchars($profile['user_name'] ?? '-') ?></p>
                </div>
                <div class="profile-item">
                    <label>Email</label>
                    <p><?= htmlspecialchars($profile['email'] ?? '-') ?></p>
                </div>
                <div class="profile-item">
                    <label>Role</label>
                    <p><span class="badge badge-<?= $profile['role'] ?>"><?= htmlspecialchars($profile['role'] ?? '-') ?></span></p>
                </div>
                <div class="profile-item">
                    <label>No. Telepon</label>
                    <p><?= htmlspecialchars($profile['phone'] ?? '-') ?></p>
                </div>
                <div class="profile-item">
                    <label>Alamat</label>
                    <p><?= htmlspecialchars($profile['address'] ?? '-') ?></p>
                </div>
                <div class="profile-item">
                    <label>Tempat, Tanggal Lahir</label>
                    <p>
                        <?= htmlspecialchars($profile['birth_place'] ?? '-') ?>,
                        <?= !empty($profile['birth_date']) ? date('d F Y', strtotime($profile['birth_date'])) : '-' ?>
                    </p>
                </div>
                <div class="profile-item">
                    <label>Jenis Kelamin</label>
                    <p>
                        <?php
                        $genderMap = ['male' => 'Laki-laki', 'female' => 'Perempuan'];
                        echo htmlspecialchars($genderMap[$profile['gender'] ?? ''] ?? '-');
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Role-Specific Sections -->
        <?php if ($role === 'user'): ?>
            <!-- Student Profile Section -->
            <div class="profile-section">
                <h2>Informasi Siswa</h2>
                <?php if (!empty($profile['role_data'])): ?>
                    <div class="profile-grid">
                        <div class="profile-item">
                            <label>Sekolah</label>
                            <p><?= htmlspecialchars($profile['role_data']['school_name'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>NIS/NISN</label>
                            <p><?= htmlspecialchars($profile['role_data']['student_id'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Jenjang</label>
                            <p><?= htmlspecialchars(ucfirst($profile['role_data']['grade_level'] ?? '-')) ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Jurusan</label>
                            <p><?= htmlspecialchars($profile['role_data']['major'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Nama Orang Tua</label>
                            <p><?= htmlspecialchars($profile['role_data']['parent_name'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>No. Telepon Orang Tua</label>
                            <p><?= htmlspecialchars($profile['role_data']['parent_phone'] ?? '-') ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada data siswa. Silakan lengkapi data akademik.</p>
                    <a href="/profile/academic" class="btn btn-primary btn-sm">Lengkapi Data Akademik</a>
                <?php endif; ?>
            </div>

            <!-- Quick Actions for Student -->
            <div class="profile-section">
                <h2>Aksi Cepat</h2>
                <div class="quick-actions">
                    <a href="/profile/academic" class="btn btn-secondary">
                        <span class="icon">📚</span> Data Akademik
                    </a>
                    <a href="/profile/achievements" class="btn btn-secondary">
                        <span class="icon">🏆</span> Prestasi & Ekstrakurikuler
                    </a>
                    <a href="/profile/results" class="btn btn-secondary">
                        <span class="icon">📊</span> Hasil Psykotest
                    </a>
                </div>
            </div>

            <!-- PMB Journey Preview Widget -->
            <div class="profile-section pmb-preview-section">
                <div class="pmb-preview-header">
                    <h2>🎯 Journey ke Univeral</h2>
                    <a href="/pmb/journey" class="btn btn-primary btn-sm">Lihat Lengkap →</a>
                </div>
                <div class="pmb-preview-card">
                    <div class="match-score-badge">
                        <span class="score">92%</span>
                        <span class="label">Match dengan TI</span>
                    </div>
                    <div class="preview-content">
                        <p>Berdasarkan analisis potensi dan minat kamu,</p>
                        <strong>Teknik Informatika di Univeral</strong> sangat cocok!
                    </div>
                    <div class="pmb-preview-actions">
                        <a href="/pmb/journey" class="btn btn-primary btn-sm">
                            <span class="icon">🎯</span> Lihat Journey
                        </a>
                        <a href="/pmb/simulation" class="btn btn-secondary btn-sm">
                            <span class="icon">📝</span> Simulasi PMB
                        </a>
                    </div>
                </div>
            </div>

        <?php elseif ($role === 'admin'): ?>
            <!-- Teacher Profile Section -->
            <div class="profile-section">
                <h2>Informasi Guru BK</h2>
                <?php if (!empty($profile['role_data'])): ?>
                    <div class="profile-grid">
                        <div class="profile-item">
                            <label>Sekolah</label>
                            <p><?= htmlspecialchars($profile['role_data']['school_name'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>NIP</label>
                            <p><?= htmlspecialchars($profile['role_data']['teacher_id'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Mata Pelajaran</label>
                            <p><?= htmlspecialchars($profile['role_data']['subject_specialty'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Sertifikasi</label>
                            <p><?= htmlspecialchars($profile['role_data']['certification'] ?? '-') ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada data guru BK.</p>
                <?php endif; ?>
            </div>

            <!-- Quick Actions for Teacher -->
            <div class="profile-section">
                <h2>Aksi Cepat</h2>
                <div class="quick-actions">
                    <a href="/profile/students" class="btn btn-secondary">
                        <span class="icon">👥</span> Siswa Bimbingan
                    </a>
                    <a href="/profile/schedule" class="btn btn-secondary">
                        <span class="icon">📅</span> Jadwal Konseling
                    </a>
                </div>
            </div>

        <?php elseif ($role === 'super-admin'): ?>
            <!-- Staff Profile Section -->
            <div class="profile-section">
                <h2>Informasi Staff</h2>
                <?php if (!empty($profile['role_data'])): ?>
                    <div class="profile-grid">
                        <div class="profile-item">
                            <label>NIP</label>
                            <p><?= htmlspecialchars($profile['role_data']['employee_id'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Departemen</label>
                            <p><?= htmlspecialchars($profile['role_data']['department'] ?? '-') ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Jabatan</label>
                            <p><?= htmlspecialchars($profile['role_data']['position'] ?? '-') ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada data staff.</p>
                <?php endif; ?>
            </div>

            <!-- Quick Actions for Super Admin -->
            <div class="profile-section">
                <h2>Aksi Cepat</h2>
                <div class="quick-actions">
                    <a href="/profile/permissions" class="btn btn-secondary">
                        <span class="icon">🔐</span> Permissions
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .profile-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .profile-content {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .profile-section {
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .profile-section h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .profile-avatar-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .avatar-wrapper {
        margin-bottom: 16px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--md-sys-color-primary, #0066cc);
    }

    .profile-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: 600;
        border: 4px solid var(--md-sys-color-primary-container, #e6f0ff);
    }

    .upload-status {
        margin-top: 12px;
        font-size: 14px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .profile-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .profile-item label {
        font-size: 12px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface-variant, #666);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .profile-item p {
        margin: 0;
        font-size: 16px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .badge-super-admin {
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-on-error-container, #c62828);
    }

    .badge-admin {
        background: var(--md-sys-color-primary-container, #e6f0ff);
        color: var(--md-sys-color-on-primary-container, #004c99);
    }

    .badge-user {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
    }

    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .quick-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .quick-actions .btn .icon {
        font-size: 18px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
        border: 1px solid var(--md-sys-color-secondary, #4caf50);
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-primary:hover {
        background: var(--md-sys-color-on-primary, #0052a3);
    }

    .btn-secondary {
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
    }

    .btn-secondary:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .text-muted {
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .profile-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            flex-direction: column;
        }

        .quick-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    // Avatar upload handler
    document.getElementById('avatar-input').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const statusEl = document.getElementById('avatar-upload-status');
        const formData = new FormData();
        formData.append('avatar', file);

        statusEl.textContent = 'Mengupload...';
        statusEl.className = 'upload-status uploading';

        try {
            const response = await fetch('/profile/avatar', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                statusEl.textContent = 'Avatar berhasil diupload!';
                statusEl.className = 'upload-status success';

                // Reload page to show new avatar
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                statusEl.textContent = result.error || 'Gagal mengupload avatar';
                statusEl.className = 'upload-status error';
            }
        } catch (error) {
            statusEl.textContent = 'Terjadi kesalahan saat mengupload';
            statusEl.className = 'upload-status error';
        }
    });
</script>