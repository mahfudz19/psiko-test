<?php

/**
 * @var array $school
 * @var array $students
 * Halaman daftar siswa per sekolah
 */
?>

<div class="students-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>" class="back-link">
                ← Kembali ke Detail Sekolah
            </a>
            <h1>Siswa - <?= htmlspecialchars($school['name']) ?></h1>
            <p class="page-description">Daftar siswa yang terdaftar di sekolah ini</p>
        </div>
        <div class="header-actions">
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/students/bulk-create" class="btn btn-secondary">
                <span>📥</span> Import Banyak Siswa
            </a>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/students/create" class="btn btn-primary">
                <span>➕</span> Tambah Siswa
            </a>
        </div>
    </div>

    <div class="students-table-container">
        <table class="students-table">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>ID Siswa</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Email</th>
                    <th>Kontak Orang Tua</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-message">
                                <span class="empty-icon">👨‍🎓</span>
                                <h3>Belum ada siswa terdaftar</h3>
                                <p>Belum ada siswa yang terdaftar di sekolah ini.</p>
                                <a data-spa href="/admin/schools/<?= $school['id'] ?>/students/create" class="btn btn-primary btn-sm">
                                    <span>➕</span> Tambah Siswa Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td>
                                <div class="student-name">
                                    <strong><?= htmlspecialchars($student['name'] ?? 'N/A') ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($student['student_id'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge"><?= htmlspecialchars($student['grade_level'] ?? 'N/A') ?></span>
                            </td>
                            <td><?= htmlspecialchars($student['major'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($student['email'] ?? 'N/A') ?></td>
                            <td>
                                <div class="parent-contact">
                                    <div><?= htmlspecialchars($student['parent_name'] ?? 'N/A') ?></div>
                                    <small><?= htmlspecialchars($student['parent_phone'] ?? 'N/A') ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        data-spa
                                        href="/admin/students/<?= $student['id'] ?>"
                                        class="btn btn-sm btn-secondary"
                                        title="Lihat Detail">
                                        👁️
                                    </a>
                                    <a
                                        data-spa
                                        href="/admin/students/<?= $student['id'] ?>/edit"
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