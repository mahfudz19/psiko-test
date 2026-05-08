<?php

/**
 * @var array $school
 * @var array $teachers
 * @var array $students
 */
?>

<div class="school-detail-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools" class="back-link">
                ← Kembali ke Daftar Sekolah
            </a>
            <h1><?= htmlspecialchars($school['name']) ?></h1>
            <p class="page-description">NPSN: <?= htmlspecialchars($school['npsn']) ?></p>
        </div>
        <div class="header-actions">
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/edit" class="btn btn-warning">
                <span>✏️</span> Edit Sekolah
            </a>
            <form data-spa method="POST" action="/admin/schools/<?= $school['id'] ?>/delete" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah ini? Tindakan ini tidak dapat dibatalkan.')">
                <button type="submit" class="btn btn-danger">
                    <span>🗑️</span> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="school-info-grid">
        <div class="info-card">
            <div class="info-header">
                <span class="info-icon">📍</span>
                <h3>Alamat</h3>
            </div>
            <p><?= nl2br(htmlspecialchars($school['address'])) ?></p>
            <?php if (!empty($school['city']) || !empty($school['province'])): ?>
                <p class="text-muted">
                    <?= htmlspecialchars($school['city'] ?? '') ?>
                    <?= htmlspecialchars($school['province'] ?? '') ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="info-card">
            <div class="info-header">
                <span class="info-icon">📞</span>
                <h3>Kontak</h3>
            </div>
            <p><strong>Telepon:</strong> <?= htmlspecialchars($school['contact'] ?? '-') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($school['email'] ?? '-') ?></p>
            <p><strong>Website:</strong>
                <?php if (!empty($school['website'])): ?>
                    <a href="<?= htmlspecialchars($school['website']) ?>" target="_blank" rel="noopener">
                        <?= htmlspecialchars($school['website']) ?>
                    </a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </p>
        </div>

        <div class="info-card">
            <div class="info-header">
                <span class="info-icon">🎓</span>
                <h3>Akreditasi</h3>
            </div>
            <span class="badge badge-<?= strtolower(htmlspecialchars($school['accreditation'])) ?>">
                <?= htmlspecialchars($school['accreditation']) ?>
            </span>
        </div>

        <div class="info-card">
            <div class="info-header">
                <span class="info-icon">👨‍💼</span>
                <h3>Kepala Sekolah</h3>
            </div>
            <p><?= htmlspecialchars($school['principal_name']) ?></p>
        </div>
    </div>

    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-value"><?= count($teachers) ?></div>
            <div class="stat-label">Total Guru</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= count($students) ?></div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>

    <div class="quick-links">
        <h2>Kelola Data</h2>
        <div class="link-cards">
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers" class="link-card">
                <div class="link-icon">👨‍🏫</div>
                <div class="link-info">
                    <h4>Guru BK</h4>
                    <p><?= count($teachers) ?> guru terdaftar</p>
                </div>
                <div class="link-arrow">→</div>
            </a>

            <a data-spa href="/admin/schools/<?= $school['id'] ?>/students" class="link-card">
                <div class="link-icon">👨‍🎓</div>
                <div class="link-info">
                    <h4>Siswa</h4>
                    <p><?= count($students) ?> siswa terdaftar</p>
                </div>
                <div class="link-arrow">→</div>
            </a>
        </div>
    </div>
</div>