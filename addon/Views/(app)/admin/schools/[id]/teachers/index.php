<?php

/**
 * @var array $school
 * @var array $teachers
 * Halaman daftar guru per sekolah
 */
?>

<div class="teachers-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>" class="back-link">
                ← Kembali ke Detail Sekolah
            </a>
            <h1>Guru BK - <?= htmlspecialchars($school['name']) ?></h1>
            <p class="page-description">Daftar guru bimbingan konseling yang terdaftar di sekolah ini</p>
        </div>
        <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/create" class="btn btn-primary">
            <span>➕</span> Tambah Guru
        </a>
    </div>

    <div class="teachers-grid">
        <?php if (empty($teachers)): ?>
            <div class="empty-state">
                <div class="empty-message">
                    <span class="empty-icon">👨‍🏫</span>
                    <h3>Belum ada guru terdaftar</h3>
                    <p>Belum ada guru bimbingan konseling yang terdaftar di sekolah ini.</p>
                    <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/create" class="btn btn-primary">
                        <span>➕</span> Tambah Guru Pertama
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($teachers as $teacher): ?>
                <a class="teacher-card" data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/<?= $teacher['user_id'] ?>">
                    <div class="teacher-avatar">
                        <span>👨‍🏫</span>
                    </div>
                    <div class="teacher-info">
                        <h3><?= htmlspecialchars($teacher['name'] ?? 'N/A') ?></h3>
                        <p class="teacher-email"><?= htmlspecialchars($teacher['email'] ?? 'N/A') ?></p>
                        <div class="teacher-details">
                            <span class="detail-item">
                                <strong>ID Guru:</strong> <?= htmlspecialchars($teacher['teacher_id'] ?? 'N/A') ?>
                            </span>
                            <span class="detail-item">
                                <strong>Mapel:</strong> <?= htmlspecialchars($teacher['subject_specialty'] ?? 'N/A') ?>
                            </span>
                        </div>
                        <?php if (!empty($teacher['certification'])): ?>
                            <span class="badge">📜 <?= htmlspecialchars($teacher['certification']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>