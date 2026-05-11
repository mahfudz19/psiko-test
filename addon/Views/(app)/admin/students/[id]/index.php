<?php

/**
 * Student Detail View
 * 
 * @var array $student
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="header-breadcrumb">
                <a data-spa href="/admin/students" class="breadcrumb-link">
                    <span class="breadcrumb-icon">←</span>
                    Daftar Siswa
                </a>
            </div>
            <h1 class="page-title">🎓 Detail Siswa</h1>
            <p class="page-description">Informasi lengkap siswa</p>
        </div>
        <div class="page-header-actions">
            <a data-spa href="/admin/students/<?= $student['user_id'] ?>/edit" class="btn btn-primary">
                <span class="btn-icon">✏️</span>
                Edit Data
            </a>
            <a data-spa href="/admin/students" class="btn btn-secondary">
                <span class="btn-icon">↩️</span>
                Kembali
            </a>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="card profile-card">
        <div class="profile-header">
            <div class="student-avatar">
                <?= strtoupper(substr($student['user_name'], 0, 2)) ?>
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?= e($student['user_name']) ?></h2>
                <p class="profile-email"><?= e($student['email']) ?></p>
                <div class="profile-badges">
                    <span class="badge badge-nis">
                        <span class="badge-icon">🔢</span>
                        NIS/NISN: <?= e($student['student_id']) ?>
                    </span>
                    <span class="badge badge-class">
                        <span class="badge-icon">📚</span>
                        Kelas <?= e($student['grade_level']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="info-grid">
        <!-- Student Info Card -->
        <div class="card info-card">
            <div class="card-header">
                <span class="card-icon">👨‍🎓</span>
                <div class="card-title-wrapper">
                    <h3 class="card-title">Informasi Siswa</h3>
                    <p class="card-subtitle">Data akademik dan kontak</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <span class="info-icon">📚</span>
                            Kelas
                        </span>
                        <span class="info-value">Kelas <?= e($student['grade_level']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <span class="info-icon">🎯</span>
                            Jurusan
                        </span>
                        <span class="info-value"><?= e($student['major']) ?: '-' ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <span class="info-icon">📱</span>
                            No. Telepon
                        </span>
                        <span class="info-value"><?= e($student['phone']) ?: '-' ?></span>
                    </div>
                    <div class="info-item info-item-full">
                        <span class="info-label">
                            <span class="info-icon">📍</span>
                            Alamat
                        </span>
                        <span class="info-value"><?= nl2br(e($student['address'])) ?: '-' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent Info Card -->
        <div class="card info-card">
            <div class="card-header">
                <span class="card-icon">👨‍👩‍👧</span>
                <div class="card-title-wrapper">
                    <h3 class="card-title">Informasi Orang Tua/Wali</h3>
                    <p class="card-subtitle">Data kontak wali siswa</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <span class="info-icon">👤</span>
                            Nama Lengkap
                        </span>
                        <span class="info-value"><?= e($student['parent_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <span class="info-icon">📞</span>
                            No. Telepon
                        </span>
                        <span class="info-value"><?= e($student['parent_phone']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <span class="info-icon">📧</span>
                            Email
                        </span>
                        <span class="info-value"><?= e($student['parent_email']) ?: '-' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Section -->
    <div class="card danger-card">
        <div class="card-header">
            <span class="card-icon card-icon-danger">⚠️</span>
            <div class="card-title-wrapper">
                <h3 class="card-title card-title-danger">Zona Bahaya</h3>
                <p class="card-subtitle card-subtitle-danger">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <div class="card-body">
            <p class="danger-description">
                Menghapus siswa akan menghapus semua data terkait termasuk nilai, pencapaian, dan riwayat konseling.
                Pastikan Anda telah melakukan backup data sebelum melanjutkan.
            </p>
            <form data-spa action="/admin/students/<?= $student['user_id'] ?>/delete" method="POST" class="danger-form">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Tindakan ini tidak dapat dibatalkan.')">
                    <span class="btn-icon">🗑️</span>
                    Hapus Siswa
                </button>
            </form>
        </div>
    </div>
</div>