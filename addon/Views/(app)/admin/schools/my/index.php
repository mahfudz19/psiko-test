<?php

/**
 * @var array $school
 * @var array $teachers
 * @var array $students
 * @var int $totalTeachers
 * @var int $totalStudents
 */
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

    <!-- School Info Card -->
    <div class="card school-info-card">
        <div class="card-header">
            <h2 class="card-title">🏫 Informasi Sekolah</h2>
        </div>
        <div class="card-body">
            <div class="school-detail-grid">
                <div class="school-detail-item">
                    <span class="school-detail-label">Nama Sekolah</span>
                    <span class="school-detail-value"><?= e($school['name']) ?></span>
                </div>
                <div class="school-detail-item">
                    <span class="school-detail-label">NPSN</span>
                    <span class="school-detail-value"><?= e($school['npsn']) ?></span>
                </div>
                <div class="school-detail-item">
                    <span class="school-detail-label">Akreditasi</span>
                    <span class="school-detail-value">
                        <span class="badge badge-<?= strtolower(e($school['accreditation'])) ?>">
                            <?= e($school['accreditation']) ?>
                        </span>
                    </span>
                </div>
                <div class="school-detail-item school-detail-item-full">
                    <span class="school-detail-label">Alamat</span>
                    <span class="school-detail-value"><?= e($school['address']) ?></span>
                </div>
                <div class="school-detail-item">
                    <span class="school-detail-label">Kepala Sekolah</span>
                    <span class="school-detail-value"><?= e($school['principal_name']) ?></span>
                </div>
                <div class="school-detail-item">
                    <span class="school-detail-label">Kontak</span>
                    <span class="school-detail-value"><?= e($school['contact']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-teachers">👨‍🏫</div>
            <div class="stat-content">
                <span class="stat-value"><?= $totalTeachers ?></span>
                <span class="stat-label">Total Guru</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-students">👨‍🎓</div>
            <div class="stat-content">
                <span class="stat-value"><?= $totalStudents ?></span>
                <span class="stat-label">Total Siswa</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">⚡ Aksi Cepat</h2>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a data-spa href="/admin/students" class="quick-action-btn">
                    <span class="quick-action-icon">👨‍🎓</span>
                    <span class="quick-action-text">Kelola Siswa</span>
                </a>
                <a data-spa href="/admin/students/create" class="quick-action-btn">
                    <span class="quick-action-icon">➕</span>
                    <span class="quick-action-text">Tambah Siswa</span>
                </a>
            </div>
        </div>
    </div>
</div>