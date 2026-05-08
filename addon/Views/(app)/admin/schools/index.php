<?php

/**
 * @var array $schools
 * @var string $keyword
 */
?>

<div class="schools-page">
    <div class="page-header">
        <div>
            <h1>Kelola Sekolah</h1>
            <p class="page-description">Daftar semua sekolah yang terdaftar dalam sistem</p>
        </div>
        <a data-spa href="/admin/schools/create" class="btn btn-primary">
            <span>➕</span> Tambah Sekolah
        </a>
    </div>

    <div class="search-bar">
        <form data-spa method="GET" action="/admin/schools">
            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Cari sekolah berdasarkan nama..."
                value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn btn-search">
                <span>🔍</span> Cari
            </button>
        </form>
    </div>

    <div class="schools-table-container">
        <table class="schools-table">
            <thead>
                <tr>
                    <th>Nama Sekolah</th>
                    <th>NPSN</th>
                    <th>Akreditasi</th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>Guru</th>
                    <th>Siswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schools)): ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-message">
                                <span class="empty-icon">🏫</span>
                                <p>Belum ada sekolah terdaftar</p>
                                <a data-spa href="/admin/schools/create" class="btn btn-primary btn-sm">
                                    <span>➕</span> Tambah Sekolah Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schools as $school): ?>
                        <tr>
                            <td>
                                <div class="school-name">
                                    <strong><?= htmlspecialchars($school['name']) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($school['npsn']) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower(htmlspecialchars($school['accreditation'])) ?>">
                                    <?= htmlspecialchars($school['accreditation']) ?>
                                </span>
                            </td>
                            <td class="text-truncate" title="<?= htmlspecialchars($school['address']) ?>">
                                <?= htmlspecialchars($school['address']) ?>
                            </td>
                            <td><?= htmlspecialchars($school['contact'] ?? '-') ?></td>
                            <td>
                                <span class="count-badge">
                                    <span>👨‍🏫</span> <?= $school['teacher_count'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="count-badge">
                                    <span>👨‍🎓</span> <?= $school['student_count'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        data-spa
                                        href="/admin/schools/<?= $school['id'] ?>"
                                        class="btn btn-sm btn-secondary"
                                        title="Lihat Detail">
                                        👁️
                                    </a>
                                    <a
                                        data-spa
                                        href="/admin/schools/<?= $school['id'] ?>/edit"
                                        class="btn btn-sm btn-warning"
                                        title="Edit">
                                        ✏️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>