<?php

/**
 * Super Admin Dashboard - Main View
 * Menampilkan statistik platform-wide, growth metrics, dan quick actions
 * 
 * @var mixed $userName
 * @var mixed $role
 * @var array|null $stats
 * @var array|null $newestSchools
 * @var array|null $newestUsers
 */
?>
<div class="super-admin-dashboard-container">
    <main class="dashboard-main">
        <!-- Welcome Section -->
        <section class="dashboard-welcome">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1 class="welcome-title">Super Admin Dashboard</h1>
                    <p class="welcome-subtitle">Selamat datang kembali, <strong><?= htmlspecialchars($userName) ?></strong>. Berikut adalah ringkasan platform Psyco-Test.</p>
                </div>
                <div class="welcome-badge">
                    <span class="role-badge">Super Administrator</span>
                </div>
            </div>
        </section>

        <!-- Primary Stats Grid -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Pengguna</span>
                    <div class="stat-value"><?= $stats['totalUsers'] ?? 0 ?></div>
                    <div class="stat-sublabel"><?= $stats['usersByRole']['user'] ?? 0 ?> Siswa, <?= $stats['usersByRole']['admin'] ?? 0 ?> Guru</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Sekolah</span>
                    <div class="stat-value"><?= $stats['totalSchools'] ?? 0 ?></div>
                    <div class="stat-sublabel"><?= $stats['totalStudents'] ?? 0 ?> Siswa Terdaftar</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Konsultasi</span>
                    <div class="stat-value"><?= $stats['totalConsultations'] ?? 0 ?></div>
                    <div class="stat-sublabel">Sesi Chat Terjadi</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Analisis AI</span>
                    <div class="stat-value"><?= $stats['aiAnalysisRate'] ?? 0 ?>%</div>
                    <div class="stat-sublabel">Siswa dengan Rekomendasi</div>
                </div>
            </div>
        </section>

        <div class="dashboard-grid-layout">
            <!-- Newest Schools -->
            <section class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Sekolah Terbaru</h3>
                    <p class="card-subtitle">Sekolah yang baru bergabung dengan platform</p>
                </div>
                <div class="school-list">
                    <?php if (!empty($newestSchools)): ?>
                        <?php foreach ($newestSchools as $school): ?>
                            <div class="school-item">
                                <div class="school-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </div>
                                <div class="school-info">
                                    <span class="school-name"><?= htmlspecialchars($school['name'] ?? 'N/A') ?></span>
                                    <span class="school-meta">Akreditasi: <?= htmlspecialchars($school['accreditation'] ?? '-') ?></span>
                                </div>
                                <a href="/admin/schools/<?= $school['id'] ?>" class="btn-view">Detail</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada data sekolah.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/admin/schools" class="view-all-link">Lihat Semua Sekolah &rarr;</a>
                </div>
            </section>

            <!-- Newest Users -->
            <section class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Pengguna Terbaru</h3>
                    <p class="card-subtitle">Pengguna yang baru registrasi</p>
                </div>
                <div class="user-list">
                    <?php if (!empty($newestUsers)): ?>
                        <?php foreach ($newestUsers as $user): ?>
                            <div class="user-item">
                                <div class="user-avatar">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name'] ?? 'User') ?>&background=random" alt="Avatar">
                                </div>
                                <div class="user-info">
                                    <span class="user-name"><?= htmlspecialchars($user['name'] ?? 'N/A') ?></span>
                                    <span class="user-email"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                                </div>
                                <span class="role-badge-small role-<?= htmlspecialchars($user['role'] ?? 'user') ?>"><?= htmlspecialchars($user['role'] ?? 'user') ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada data pengguna.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/admin" class="view-all-link">Kelola Pengguna &rarr;</a>
                </div>
            </section>
        </div>

        <!-- Quick Actions -->
        <section class="quick-actions-section">
            <h3 class="section-title">Aksi Cepat</h3>
            <div class="quick-actions-grid">
                <a href="/admin/schools" class="action-card">
                    <div class="action-icon" style="background: #eef2ff; color: #6366f1;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <span>Kelola Sekolah</span>
                </a>
                <a href="/admin" class="action-card">
                    <div class="action-icon" style="background: #f0fdf4; color: #10b981;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="/settings" class="action-card">
                    <div class="action-icon" style="background: #fff7ed; color: #f59e0b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    <span>Pengaturan Sistem</span>
                </a>
                <a href="/dashboard" class="action-card">
                    <div class="action-icon" style="background: #fce7f3; color: #ec4899;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <span>Laporan Platform</span>
                </a>
            </div>
        </section>
    </main>
</div>