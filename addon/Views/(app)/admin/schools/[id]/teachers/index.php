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
                <?php
                $isActive = (bool) ($teacher['is_active'] ?? true);
                $initials = strtoupper(substr($teacher['name'] ?? 'N/A', 0, 2));
                ?>
                <a class="teacher-card" data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/<?= $teacher['user_id'] ?>">
                    <div class="teacher-card-left">
                        <div class="teacher-avatar <?= $isActive ? 'active' : 'inactive' ?>">
                            <?= htmlspecialchars($initials) ?>
                        </div>
                        <div class="teacher-basic-info">
                            <h3><?= htmlspecialchars($teacher['name'] ?? 'N/A') ?></h3>
                            <p class="teacher-email">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                <?= htmlspecialchars($teacher['email'] ?? 'N/A') ?>
                            </p>
                        </div>
                    </div>

                    <div class="teacher-card-right">
                        <div class="teacher-details">
                            <div class="detail-row">
                                <span class="detail-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21a8 8 0 1 0-16 0" />
                                        <circle cx="12" cy="8" r="5" />
                                    </svg>
                                </span>
                                <span class="detail-label">ID Guru:</span>
                                <span class="detail-value"><?= htmlspecialchars($teacher['teacher_id'] ?? 'N/A') ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                        <polyline points="16 3 16 9 22 9" />
                                    </svg>
                                </span>
                                <span class="detail-label">Mapel:</span>
                                <span class="detail-value"><?= htmlspecialchars($teacher['subject_specialty'] ?? 'N/A') ?></span>
                            </div>
                        </div>

                        <div class="teacher-card-footer">
                            <?php if (!empty($teacher['certification'])): ?>
                                <span class="badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="7" />
                                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" />
                                    </svg>
                                    <?= htmlspecialchars($teacher['certification']) ?>
                                </span>
                            <?php endif; ?>

                            <span class="status-badge <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                                <span class="status-dot"></span>
                                <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>