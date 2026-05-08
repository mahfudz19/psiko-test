<?php

/**
 * @var array $student
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Detail Siswa</h1>
            <p class="page-description">Informasi lengkap siswa</p>
        </div>
        <div class="page-header-actions">
            <a data-spa href="/admin/students/<?= $student['id'] ?>/edit" class="btn btn-primary">
                <span class="btn-icon">✏️</span>
                Edit
            </a>
            <a data-spa href="/admin/students" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">👨‍🎓 Informasi Siswa</h2>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">NIS/NISN</span>
                    <span class="detail-value text-mono"><?= e($student['student_id']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value"><?= e($student['user_name']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= e($student['email']) ?: '-' ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Kelas</span>
                    <span class="detail-value"><?= e($student['grade_level']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jurusan</span>
                    <span class="detail-value"><?= e($student['major']) ?: '-' ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">No. Telepon</span>
                    <span class="detail-value"><?= e($student['phone']) ?: '-' ?></span>
                </div>
                <div class="detail-item detail-item-full">
                    <span class="detail-label">Alamat</span>
                    <span class="detail-value"><?= e($student['address']) ?: '-' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Info Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">👨‍👩‍👧 Informasi Orang Tua/Wali</h2>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Nama Orang Tua/Wali</span>
                    <span class="detail-value"><?= e($student['parent_name']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">No. Telepon</span>
                    <span class="detail-value"><?= e($student['parent_phone']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= e($student['parent_email']) ?: '-' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Section -->
    <div class="card card-danger">
        <div class="card-body">
            <div class="danger-zone">
                <div class="danger-zone-content">
                    <h3 class="danger-zone-title">⚠️ Zona Bahaya</h3>
                    <p class="danger-zone-description">
                        Tindakan ini tidak dapat dibatalkan. Hati-hati saat menghapus siswa.
                    </p>
                </div>
                <form data-spa action="/admin/students/<?= $student['id'] ?>/delete" method="POST" class="danger-zone-form">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Tindakan ini tidak dapat dibatalkan.')">
                        <span class="btn-icon">🗑️</span>
                        Hapus Siswa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>