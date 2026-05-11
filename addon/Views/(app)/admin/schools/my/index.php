<?php

/**
 * School Admin Dashboard View
 * 
 * @var array $school
 * @var array $teachers
 * @var array $students
 * @var int $totalTeachers
 * @var int $totalStudents
 */

// Calculate additional stats
$recentStudents = array_slice($students, 0, 5);
$accreditationClass = strtolower(e($school['accreditation']));
$accreditationBadgeClass = 'badge-' . $accreditationClass;
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Dashboard Sekolah</h1>
            <p class="page-description">Kelola informasi sekolah dan data siswa</p>
        </div>
        <div class="page-header-actions">
            <a data-spa href="/admin/schools/my/edit" class="btn btn-primary">
                <span class="btn-icon">✏️</span>
                Edit Sekolah
            </a>
        </div>
    </div>

    <!-- School Info Hero Card -->
    <div class="card school-hero-card">
        <div class="school-hero-header">
            <div class="school-icon-wrapper">
                <span class="school-icon">🏫</span>
            </div>
            <div class="school-hero-info">
                <h2 class="school-hero-title"><?= e($school['name']) ?></h2>
                <p class="school-hero-subtitle">NPSN: <?= e($school['npsn']) ?></p>
            </div>
            <div class="school-hero-badges">
                <span class="accreditation-badge <?= $accreditationBadgeClass ?>">
                    <?= e($school['accreditation']) ?>
                </span>
            </div>
        </div>
        <div class="school-hero-body">
            <div class="school-info-grid">
                <div class="info-item">
                    <span class="info-icon">📍</span>
                    <div class="info-content">
                        <span class="info-label">Alamat</span>
                        <span class="info-value"><?= e($school['address']) ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">👔</span>
                    <div class="info-content">
                        <span class="info-label">Kepala Sekolah</span>
                        <span class="info-value"><?= e($school['principal_name']) ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📞</span>
                    <div class="info-content">
                        <span class="info-label">Kontak</span>
                        <span class="info-value"><?= e($school['contact']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-teachers">
            <div class="stat-card-bg-icon">👨‍🏫</div>
            <div class="stat-content">
                <div class="stat-header">
                    <span class="stat-icon-small">👨‍🏫</span>
                    <span class="stat-label">Total Guru</span>
                </div>
                <span class="stat-value"><?= $totalTeachers ?></span>
                <span class="stat-trend stat-trend-positive">
                    <span class="trend-icon">📈</span>
                    <span>Aktif mengajar</span>
                </span>
            </div>
        </div>
        <div class="stat-card stat-card-students">
            <div class="stat-card-bg-icon">👨‍🎓</div>
            <div class="stat-content">
                <div class="stat-header">
                    <span class="stat-icon-small">👨‍🎓</span>
                    <span class="stat-label">Total Siswa</span>
                </div>
                <span class="stat-value"><?= $totalStudents ?></span>
                <span class="stat-trend stat-trend-positive">
                    <span class="trend-icon">📈</span>
                    <span>Terbentuk aktif</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="card quick-actions-card">
        <div class="card-header">
            <h2 class="card-title">⚡ Aksi Cepat</h2>
        </div>
        <div class="card-body">
            <div class="quick-actions-grid">
                <a data-spa href="/admin/students" class="quick-action-item">
                    <div class="action-icon-wrapper action-icon-students">
                        <span class="action-icon">👨‍🎓</span>
                    </div>
                    <div class="action-content">
                        <span class="action-title">Kelola Siswa</span>
                        <span class="action-desc">Lihat dan edit data siswa</span>
                    </div>
                    <span class="action-arrow">→</span>
                </a>
                <a data-spa href="/admin/students/create" class="quick-action-item">
                    <div class="action-icon-wrapper action-icon-add">
                        <span class="action-icon">➕</span>
                    </div>
                    <div class="action-content">
                        <span class="action-title">Tambah Siswa</span>
                        <span class="action-desc">Daftarkan siswa baru</span>
                    </div>
                    <span class="action-arrow">→</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Students Card -->
    <?php if (!empty($recentStudents)): ?>
        <div class="card recent-students-card">
            <div class="card-header">
                <div class="card-header-content">
                    <h2 class="card-title">🎓 Siswa Terbaru</h2>
                    <a data-spa href="/admin/students" class="view-all-link">
                        Lihat Semua
                        <span class="link-arrow">→</span>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="students-table-wrapper">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>NIS/NISN</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudents as $student): ?>
                                <tr>
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">
                                                <?= strtoupper(substr($student['user_name'], 0, 2)) ?>
                                            </div>
                                            <span class="student-name"><?= e($student['user_name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="class-badge"><?= e($student['grade_level'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <span class="nis-text"><?= e($student['nis'] ?? $student['nisn'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-active">Aktif</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>