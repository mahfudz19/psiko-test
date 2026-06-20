<?php

/**
 * Students List View
 * 
 * @var array $students
 * @var string $keyword
 */

// Calculate stats
$totalStudents = count($students);
$hasSearch = !empty($keyword);
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">📚 Daftar Siswa</h1>
            <p class="page-description">Kelola data siswa di sekolah Anda</p>
        </div>
        <div class="page-header-actions">
            <a data-spa href="/admin/students/bulk-create" class="btn btn-secondary">
                <span class="btn-icon">📥</span>
                Import Banyak Siswa
            </a>
            <a data-spa href="/admin/students/create" class="btn btn-primary">
                <span class="btn-icon">➕</span>
                Tambah Siswa
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-total">
            <div class="stat-card-bg-icon">👨‍🎓</div>
            <div class="stat-content">
                <div class="stat-header">
                    <span class="stat-icon-small">👨‍🎓</span>
                    <span class="stat-label">Total Siswa</span>
                </div>
                <span class="stat-value"><?= $totalStudents ?></span>
                <?php if ($hasSearch): ?>
                    <span class="stat-trend stat-trend-neutral">
                        <span class="trend-icon">🔍</span>
                        <span>Hasil pencarian: "<?= e($keyword) ?>"</span>
                    </span>
                <?php else: ?>
                    <span class="stat-trend stat-trend-positive">
                        <span class="trend-icon">✅</span>
                        <span>Data lengkap</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="stat-card stat-card-grade">
            <div class="stat-card-bg-icon">📊</div>
            <div class="stat-content">
                <div class="stat-header">
                    <span class="stat-icon-small">📊</span>
                    <span class="stat-label">Kelas Aktif</span>
                </div>
                <span class="stat-value">
                    <?php
                    $uniqueGrades = array_unique(array_column($students, 'grade_level'));
                    echo count($uniqueGrades);
                    ?>
                </span>
                <span class="stat-trend stat-trend-positive">
                    <span class="trend-icon">📈</span>
                    <span>Tingkat kelas</span>
                </span>
            </div>
        </div>
        <div class="stat-card stat-card-major">
            <div class="stat-card-bg-icon">🎓</div>
            <div class="stat-content">
                <div class="stat-header">
                    <span class="stat-icon-small">🎓</span>
                    <span class="stat-label">Jurusan</span>
                </div>
                <span class="stat-value">
                    <?php
                    $uniqueMajors = array_filter(array_column($students, 'major'));
                    echo count($uniqueMajors) > 0 ? count($uniqueMajors) : '-';
                    ?>
                </span>
                <span class="stat-trend stat-trend-neutral">
                    <span class="trend-icon">ℹ️</span>
                    <span><?= count($uniqueMajors) > 0 ? 'Ada' : 'Umum' ?></span>
                </span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card search-card">
        <div class="card-body">
            <form data-spa action="/admin/students" method="GET" class="search-form">
                <div class="search-form-content">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            class="form-input"
                            placeholder="Cari siswa berdasarkan nama, NIS, atau NISN..."
                            value="<?= e($keyword) ?>"
                            autocomplete="off" />
                        <?php if ($hasSearch): ?>
                            <a data-spa href="/admin/students" class="btn btn-clear-search">
                                <span>✕</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-search">
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table Card -->
    <div class="card students-table-card">
        <?php if (empty($students)): ?>
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <h3 class="empty-state-title">Belum Ada Siswa</h3>
                    <p class="empty-state-description">
                        <?php if ($hasSearch): ?>
                            Tidak ada siswa yang cocok dengan pencarian "<?= e($keyword) ?>"
                        <?php else: ?>
                            Mulai tambahkan siswa ke sekolah Anda
                        <?php endif; ?>
                    </p>
                    <?php if ($hasSearch): ?>
                        <a data-spa href="/admin/students" class="btn btn-secondary">
                            Lihat Semua Siswa
                        </a>
                    <?php else: ?>
                        <a data-spa href="/admin/students/create" class="btn btn-primary">
                            <span class="btn-icon">➕</span>
                            Tambah Siswa Pertama
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="table-header">
                <div class="table-header-content">
                    <h3 class="table-title">Data Siswa</h3>
                    <span class="table-badge"><?= $totalStudents ?> siswa ditemukan</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Siswa</th>
                            <th>NIS/NISN</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td>
                                    <span class="row-number"><?= $index + 1 ?></span>
                                </td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?= strtoupper(substr($student['user_name'], 0, 2)) ?>
                                        </div>
                                        <div class="student-details">
                                            <span class="student-name"><?= e($student['user_name']) ?></span>
                                            <?php if (!empty($student['email'])): ?>
                                                <span class="student-email"><?= e($student['email']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="id-badges">
                                        <?php if (!empty($student['nis'])): ?>
                                            <span class="id-badge id-badge-nis" title="NIS">
                                                NIS: <?= e($student['nis']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($student['nisn'])): ?>
                                            <span class="id-badge id-badge-nisn" title="NISN">
                                                NISN: <?= e($student['nisn']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (empty($student['nis']) && empty($student['nisn'])): ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="class-badge"><?= e($student['grade_level']) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($student['major'])): ?>
                                        <span class="major-badge"><?= e($student['major']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a
                                            data-spa
                                            href="/admin/students/<?= $student['user_id'] ?>"
                                            class="btn-icon-action btn-view"
                                            title="Detail Siswa">
                                            👁️
                                        </a>
                                        <a
                                            data-spa
                                            href="/admin/students/<?= $student['user_id'] ?>/edit"
                                            class="btn-icon-action btn-edit"
                                            title="Edit Data">
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

<script>
    // Auto-focus search input on page load if there's a keyword
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value) {
            searchInput.focus();
            searchInput.select();
        }
    });
</script>