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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Profil
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-bottom: 2rem; border-radius: 16px; padding: 1rem 1.5rem; background: rgba(var(--success-main-rgb), 0.1); color: var(--success-main); border: 1px solid rgba(var(--success-main-rgb), 0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <?= htmlspecialchars($_GET['success']) ?>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div style="overflow: hidden; text-overflow: ellipsis;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Email</div>
                            <div style="font-size: 0.875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($profile['email'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(var(--primary-main-rgb), 0.1); color: var(--primary-main); display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
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
                    <h2><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;">
                            <circle cx="12" cy="8" r="5"></circle>
                            <path d="M20 21a8 8 0 1 0-16 0"></path>
                        </svg> Informasi Pribadi</h2>
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
                        <h2><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg> Informasi Akademik</h2>
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
                        <h2><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg> Akses Cepat</h2>
                    </div>
                    <div class="actions-grid">
                        <a data-spa href="/profile/academic" class="action-btn" data-spa>
                            <div class="action-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg></div>
                            <span class="action-text">Data Akademik</span>
                        </a>
                        <a data-spa href="/profile/achievements" class="action-btn" data-spa>
                            <div class="action-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                    <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                    <path d="M4 22h16"></path>
                                    <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                    <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                    <path d="M18 2H6v7a6 6 0 0 0 12 0V2z"></path>
                                </svg></div>
                            <span class="action-text">Prestasi</span>
                        </a>
                        <a data-spa href="/profile/results" class="action-btn" data-spa>
                            <div class="action-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="20" x2="12" y2="10"></line>
                                    <line x1="18" y1="20" x2="18" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="16"></line>
                                </svg></div>
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
                        <h2><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;">
                                <path d="M2 20h20"></path>
                                <path d="M17 20v-9a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v9"></path>
                                <path d="M12 3v5"></path>
                                <path d="M8 7h8"></path>
                            </svg> Informasi Guru BK</h2>
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

            <?php elseif ($role === 'super-admin'): ?>
                <!-- Staff Info -->
                <section class="content-card">
                    <div class="card-header">
                        <h2><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg> Informasi Staff</h2>
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