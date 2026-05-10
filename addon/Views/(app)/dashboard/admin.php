<?php

/**
 * Dashboard Siswa - Main View
 * Menampilkan overview profil, match score, quick actions, dan next steps
 * 
 * @var mixed $userName
 * @var mixed $role
 * @var array|null $school
 * @var array|null $stats
 * @var array|null $topMajors
 * @var array|null $recentStudents
 */
?>
<div class="admin-dashboard-container">
    <main class="dashboard-main">
        <!-- Welcome Section -->
        <section class="dashboard-welcome">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1 class="welcome-title">Dashboard <?= htmlspecialchars($school['name'] ?? 'Sekolah') ?></h1>
                    <p class="welcome-subtitle">Selamat datang kembali, <strong><?= htmlspecialchars($userName) ?></strong>. Berikut adalah ringkasan aktivitas siswa Anda hari ini.</p>
                </div>
                <div class="welcome-badge">
                    <span class="accreditation-badge">Akreditasi <?= htmlspecialchars($school['accreditation'] ?? '-') ?></span>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
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
                    <span class="stat-label">Total Siswa</span>
                    <div class="stat-value"><?= $stats['totalStudents'] ?></div>
                    <a href="/admin/students" class="stat-link">Kelola Siswa &rarr;</a>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Kelengkapan Profil</span>
                    <div class="stat-value"><?= $stats['completionRate'] ?>%</div>
                    <div class="stat-progress">
                        <div class="stat-progress-bar" style="width: <?= $stats['completionRate'] ?>%; background: #10b981;"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Sudah Psikotes</span>
                    <div class="stat-value"><?= $stats['psychotestRate'] ?>%</div>
                    <div class="stat-progress">
                        <div class="stat-progress-bar" style="width: <?= $stats['psychotestRate'] ?>%; background: #f59e0b;"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Analisis AI</span>
                    <div class="stat-value"><?= $stats['aiAnalysisRate'] ?>%</div>
                    <div class="stat-progress">
                        <div class="stat-progress-bar" style="width: <?= $stats['aiAnalysisRate'] ?>%; background: #ec4899;"></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="dashboard-grid-layout">
            <!-- Top Majors Distribution -->
            <section class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Minat Jurusan</h3>
                    <p class="card-subtitle">Berdasarkan rekomendasi AI untuk siswa</p>
                </div>
                <div class="major-list">
                    <?php if (!empty($topMajors)): ?>
                        <?php foreach ($topMajors as $major => $count): ?>
                            <div class="major-item">
                                <div class="major-info">
                                    <span class="major-name"><?= htmlspecialchars($major) ?></span>
                                    <span class="major-count"><?= $count ?> Siswa</span>
                                </div>
                                <div class="major-bar-container">
                                    <?php $percent = ($count / $stats['totalStudents']) * 100; ?>
                                    <div class="major-bar-fill" style="width: <?= $percent ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada data analisis AI.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Recent Students -->
            <section class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Siswa Terbaru</h3>
                    <p class="card-subtitle">Aktivitas pendaftaran siswa baru</p>
                </div>
                <div class="student-list">
                    <?php if (!empty($recentStudents)): ?>
                        <?php foreach ($recentStudents as $student): ?>
                            <div class="student-item">
                                <div class="student-avatar">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($student['user_name']) ?>&background=random" alt="Avatar">
                                </div>
                                <div class="student-info">
                                    <span class="student-name"><?= htmlspecialchars($student['user_name']) ?></span>
                                    <span class="student-meta">Kelas <?= htmlspecialchars($student['grade_level']) ?> - <?= htmlspecialchars($student['major'] ?? 'Umum') ?></span>
                                </div>
                                <a href="/admin/students/<?= $student['user_id'] ?>" class="btn-view">Detail</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada data siswa.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/admin/students" class="view-all-link">Lihat Semua Siswa &rarr;</a>
                </div>
            </section>
        </div>

        <!-- Quick Actions -->
        <section class="quick-actions-section">
            <h3 class="section-title">Aksi Cepat</h3>
            <div class="quick-actions-grid">
                <a href="/admin/students/create" class="action-card">
                    <div class="action-icon" style="background: #eef2ff; color: #6366f1;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </div>
                    <span>Tambah Siswa</span>
                </a>
                <a href="/admin/schools/my/edit" class="action-card">
                    <div class="action-icon" style="background: #f0fdf4; color: #10b981;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <span>Profil Sekolah</span>
                </a>
                <a href="/profile/students" class="action-card">
                    <div class="action-icon" style="background: #fff7ed; color: #f59e0b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <span>Laporan Kolektif</span>
                </a>
            </div>
        </section>
    </main>
</div>