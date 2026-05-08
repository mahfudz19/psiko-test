<?php

/**
 * @var int $totalSchools
 * @var int $totalTeachers
 * @var int $totalStudents
 */
?>

<div class="admin-dashboard">
    <h1>Dashboard Admin</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🏫</div>
            <div class="stat-info">
                <h3>Total Sekolah</h3>
                <p class="stat-value"><?= $totalSchools ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👨‍🏫</div>
            <div class="stat-info">
                <h3>Total Guru</h3>
                <p class="stat-value"><?= $totalTeachers ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👨‍🎓</div>
            <div class="stat-info">
                <h3>Total Siswa</h3>
                <p class="stat-value"><?= $totalStudents ?></p>
            </div>
        </div>
    </div>

    <div class="quick-actions">
        <h2>Aksi Cepat</h2>
        <div class="action-buttons">
            <a data-spa href="/admin/schools" class="btn btn-primary">
                <span>🏫</span> Kelola Sekolah
            </a>
        </div>
    </div>
</div>