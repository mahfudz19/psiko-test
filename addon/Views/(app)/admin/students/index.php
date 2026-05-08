<?php

/**
 * @var array $students
 * @var string $keyword
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Daftar Siswa</h1>
            <p class="page-description">Kelola data siswa di sekolah Anda</p>
        </div>
        <div class="page-header-actions">
            <a data-spa href="/admin/students/create" class="btn btn-primary">
                <span class="btn-icon">➕</span>
                Tambah Siswa
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card">
        <div class="card-body">
            <form data-spa action="/admin/students" method="GET" class="search-form">
                <div class="search-input-wrapper">
                    <input
                        type="text"
                        name="search"
                        class="form-input"
                        placeholder="Cari siswa berdasarkan nama..."
                        value="<?= e($keyword) ?>" />
                    <button type="submit" class="btn btn-icon">
                        <span>🔍</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-body card-body-padding">
            <?php if (empty($students)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <h3 class="empty-state-title">Belum Ada Siswa</h3>
                    <p class="empty-state-description">Mulai tambahkan siswa ke sekolah Anda</p>
                    <a data-spa href="/admin/students/create" class="btn btn-primary">
                        Tambah Siswa Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS/NISN</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <span class="text-mono"><?= e($student['student_id']) ?></span>
                                    </td>
                                    <td>
                                        <div class="table-cell-primary">
                                            <span class="font-medium"><?= e($student['user_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= e($student['grade_level']) ?></td>
                                    <td><?= e($student['major']) ?: '-' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a
                                                data-spa
                                                href="/admin/students/<?= $student['user_id'] ?>"
                                                class="btn btn-sm btn-secondary"
                                                title="Detail">
                                                👁️
                                            </a>
                                            <a
                                                data-spa
                                                href="/admin/students/<?= $student['user_id'] ?>/edit"
                                                class="btn btn-sm btn-primary"
                                                title="Edit">
                                                ✏️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>