<?php

/**
 * Profile Index View - Radical Redesign
 * 
 * @var array $profile Profile data
 * @var string $role User role
 */
?>

<div class="profile-container">
    <!-- Header Section -->
    <div class="profile-header-wrapper">
        <div class="profile-title-section">
            <h1>Profil Saya</h1>
            <p>Kelola informasi pribadi dan pantau progres akademik Anda</p>
        </div>
        <div class="header-actions">
            <a data-spa href="/profile/edit" class="btn-white" style="border: 1px solid var(--border-light); color: var(--text-primary);">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-bottom: 2rem; border-radius: 16px; padding: 1rem 1.5rem; background: rgba(var(--success-main-rgb), 0.1); color: var(--success-main); border: 1px solid rgba(var(--success-main-rgb), 0.2);">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <div class="profile-layout">
        <!-- Sidebar: Main Profile Card -->
        <aside class="profile-sidebar">
            <div class="profile-card-main">
                <div class="avatar-container">
                    <?php if (!empty($profile['avatar'])): ?>
                        <img src="<?= $profile['avatar'] ?>" alt="Avatar" class="profile-avatar-img" id="profile-avatar-preview">
                    <?php else: ?>
                        <div class="profile-avatar-placeholder" id="profile-avatar-placeholder">
                            <?= strtoupper(substr($profile['user_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <label for="avatar-input" class="avatar-edit-btn" title="Ubah Foto">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" hidden>
                </div>

                <h2 class="profile-name"><?= htmlspecialchars($profile['user_name'] ?? 'User') ?></h2>
                <div class="profile-role-badge badge-<?= $profile['role'] ?>">
                    <?= htmlspecialchars($profile['role'] ?? 'User') ?>
                </div>

                <div class="profile-stats-mini">
                    <div class="stat-item">
                        <span class="stat-value"><?= $role === 'user' ? 'Siswa' : ($role === 'admin' ? 'Guru' : 'Staff') ?></span>
                        <span class="stat-label">Status</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">Aktif</span>
                        <span class="stat-label">Akun</span>
                    </div>
                </div>

                <div id="avatar-upload-status" class="upload-status"></div>
            </div>

            <!-- Quick Info Card -->
            <div class="content-card" style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; margin-bottom: 1rem; color: var(--text-secondary);">Kontak Cepat</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(var(--primary-main-rgb), 0.1); color: var(--primary-main); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-envelope" style="font-size: 0.875rem;"></i>
                        </div>
                        <div style="overflow: hidden; text-overflow: ellipsis;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Email</div>
                            <div style="font-size: 0.875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($profile['email'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(var(--primary-main-rgb), 0.1); color: var(--primary-main); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-phone" style="font-size: 0.875rem;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Telepon</div>
                            <div style="font-size: 0.875rem; font-weight: 600;"><?= htmlspecialchars($profile['phone'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="profile-main-content">
            <!-- Personal Information -->
            <section class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-user-circle"></i> Informasi Pribadi</h2>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value"><?= htmlspecialchars($profile['user_name'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tempat, Tanggal Lahir</span>
                        <span class="info-value">
                            <?= htmlspecialchars($profile['birth_place'] ?? '-') ?>,
                            <?= !empty($profile['birth_date']) ? date('d F Y', strtotime($profile['birth_date'])) : '-' ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Kelamin</span>
                        <span class="info-value">
                            <?php
                            $genderMap = ['male' => 'Laki-laki', 'female' => 'Perempuan'];
                            echo htmlspecialchars($genderMap[$profile['gender'] ?? ''] ?? '-');
                            ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Alamat</span>
                        <span class="info-value"><?= htmlspecialchars($profile['address'] ?? '-') ?></span>
                    </div>
                </div>
            </section>

            <!-- Role-Specific Sections -->
            <?php if ($role === 'user'): ?>
                <!-- Student Academic Info -->
                <section class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-graduation-cap"></i> Informasi Akademik</h2>
                        <?php if (empty($profile['role_data'])): ?>
                            <a data-spa href="/profile/academic" class="btn-white" style="font-size: 0.75rem; padding: 0.5rem 1rem; border: 1px solid var(--primary-main); color: var(--primary-main);">Lengkapi Data</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($profile['role_data'])): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Sekolah</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['school_name'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">NIS/NISN</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['student_id'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jenjang / Kelas</span>
                                <span class="info-value"><?= htmlspecialchars(ucfirst($profile['role_data']['grade_level'] ?? '-')) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jurusan</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['major'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nama Orang Tua</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['parent_name'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Kontak Orang Tua</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['parent_phone'] ?? '-') ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 2rem; background: var(--bg-default); border-radius: 16px; border: 1px dashed var(--border-light);">
                            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Belum ada data akademik yang tercatat.</p>
                            <a data-spa href="/profile/academic" class="btn-white" style="background: var(--primary-main); color: white;">Lengkapi Sekarang</a>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Quick Actions -->
                <section class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-bolt"></i> Akses Cepat</h2>
                    </div>
                    <div class="actions-grid">
                        <a data-spa href="/profile/academic" class="action-btn" data-spa>
                            <div class="action-icon"><i class="fas fa-book"></i></div>
                            <span class="action-text">Data Akademik</span>
                        </a>
                        <a data-spa href="/profile/achievements" class="action-btn" data-spa>
                            <div class="action-icon"><i class="fas fa-trophy"></i></div>
                            <span class="action-text">Prestasi</span>
                        </a>
                        <a data-spa href="/profile/results" class="action-btn" data-spa>
                            <div class="action-icon"><i class="fas fa-chart-bar"></i></div>
                            <span class="action-text">Hasil Tes</span>
                        </a>
                    </div>
                </section>

                <!-- PMB Journey Widget -->
                <section class="pmb-widget">
                    <div class="pmb-info">
                        <h3>🎯 Journey ke Univeral</h3>
                        <p>Berdasarkan analisis potensi kamu, Teknik Informatika sangat cocok untuk masa depanmu!</p>
                        <div class="pmb-actions">
                            <a data-spa href="/pmb/journey" class="btn-white" data-spa>Lihat Journey</a>
                            <a data-spa href="/pmb/simulation" class="btn-outline-white" data-spa>Simulasi PMB</a>
                        </div>
                    </div>
                    <div class="pmb-score-circle">
                        <span class="score">92%</span>
                        <span class="label">Match</span>
                    </div>
                </section>

            <?php elseif ($role === 'admin'): ?>
                <!-- Teacher Info -->
                <section class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-chalkboard-teacher"></i> Informasi Guru BK</h2>
                    </div>
                    <?php if (!empty($profile['role_data'])): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Sekolah</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['school_name'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">NIP</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['teacher_id'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Spesialisasi</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['subject_specialty'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Sertifikasi</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['certification'] ?? '-') ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-secondary);">Data guru belum lengkap.</p>
                    <?php endif; ?>
                </section>

                <section class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-bolt"></i> Akses Cepat</h2>
                    </div>
                    <div class="actions-grid">
                        <a data-spa href="/profile/students" class="action-btn" data-spa>
                            <div class="action-icon"><i class="fas fa-users"></i></div>
                            <span class="action-text">Siswa Bimbingan</span>
                        </a>
                        <a data-spa href="/profile/schedule" class="action-btn" data-spa>
                            <div class="action-icon"><i class="fas fa-calendar-alt"></i></div>
                            <span class="action-text">Jadwal Konseling</span>
                        </a>
                    </div>
                </section>

            <?php elseif ($role === 'super-admin'): ?>
                <!-- Staff Info -->
                <section class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-shield"></i> Informasi Staff</h2>
                    </div>
                    <?php if (!empty($profile['role_data'])): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">NIP / ID Pegawai</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['employee_id'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Departemen</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['department'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jabatan</span>
                                <span class="info-value"><?= htmlspecialchars($profile['role_data']['position'] ?? '-') ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-secondary);">Data staff belum lengkap.</p>
                    <?php endif; ?>
                </section>

                <section class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-bolt"></i> Akses Cepat</h2>
                    </div>
                    <div class="actions-grid">
                        <a data-spa href="/profile/permissions" class="action-btn" data-spa>
                            <div class="action-icon"><i class="fas fa-key"></i></div>
                            <span class="action-text">Permissions</span>
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
    /**
     * Avatar Upload Handler
     * Menggunakan fetch API untuk upload avatar secara async
     */
    function initAvatarUpload() {
        const avatarInput = document.getElementById('avatar-input');
        if (!avatarInput) return;

        avatarInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Preview local
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-avatar-preview');
                const placeholder = document.getElementById('profile-avatar-placeholder');

                if (preview) {
                    preview.src = e.target.result;
                } else if (placeholder) {
                    // Ganti placeholder dengan img
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'profile-avatar-img';
                    img.id = 'profile-avatar-preview';
                    placeholder.parentNode.replaceChild(img, placeholder);
                }
            };
            reader.readAsDataURL(file);

            const statusEl = document.getElementById('avatar-upload-status');
            const formData = new FormData();
            formData.append('avatar', file);

            statusEl.textContent = 'Mengupload...';
            statusEl.className = 'upload-status active';
            statusEl.style.display = 'block';

            try {
                const response = await fetch('/profile/avatar', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    statusEl.textContent = 'Avatar berhasil diperbarui!';
                    statusEl.className = 'upload-status active success';

                    setTimeout(() => {
                        statusEl.style.display = 'none';
                    }, 3000);
                } else {
                    statusEl.textContent = result.error || 'Gagal mengupload avatar';
                    statusEl.className = 'upload-status active error';
                }
            } catch (error) {
                statusEl.textContent = 'Terjadi kesalahan koneksi';
                statusEl.className = 'upload-status active error';
            }
        });
    }

    // Inisialisasi saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAvatarUpload);
    } else {
        initAvatarUpload();
    }

    // Re-init untuk SPA
    window.addEventListener('spa:navigated', initAvatarUpload);
</script>