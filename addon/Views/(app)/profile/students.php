<?php

/**
 * Teacher - Managed Students View
 * 
 * @var array $profile Profile data
 * @var array|null $teacherProfile Teacher profile data
 * @var array $students List of managed students
 */
?>

<div class="students-container">
    <div class="students-header">
        <div class="breadcrumb">
            <a data-spa href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Siswa Bimbingan</span>
        </div>
        <h1>Siswa Bimbingan</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <span class="stat-value"><?= count($students) ?></span>
                <span class="stat-label">Total Siswa</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <span class="stat-value">
                    <?= count(array_filter($students, fn($s) => !empty($s['ai_analysis']))) ?>
                </span>
                <span class="stat-label">Dengan Analisis AI</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <span class="stat-value">
                    <?= count(array_filter($students, fn($s) => !empty($s['has_riasec_test']))) ?>
                </span>
                <span class="stat-label">Sudah Test</span>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="students-section">
        <h2>Daftar Siswa</h2>

        <?php if (!empty($students)): ?>
            <div class="students-table-container">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Status Test</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="student-name">
                                        <?php if (!empty($student['avatar'])): ?>
                                            <img src="<?= $student['avatar'] ?>" alt="Avatar" class="student-avatar">
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($student['user_name'] ?? 'Unknown') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($student['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars(ucfirst($student['grade_level'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars($student['major'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($student['ai_analysis'])): ?>
                                        <span class="badge badge-success">Sudah Analisis</span>
                                    <?php elseif (!empty($student['has_riasec_test'])): ?>
                                        <span class="badge badge-warning">Menunggu Analisis</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Belum Test</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a data-spa href="/profile/results?id=<?= $student['profile_id'] ?>"
                                            class="btn btn-sm btn-secondary"
                                            title="Lihat Hasil">
                                            📊
                                        </a>
                                        <a data-spa href="/profile?id=<?= $student['profile_id'] ?>"
                                            class="btn btn-sm btn-secondary"
                                            title="Lihat Profile">
                                            👤
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-students-card">
                <div class="no-students-icon">👥</div>
                <h3>Belum Ada Siswa Bimbingan</h3>
                <p>Anda belum memiliki siswa bimbingan. Hubungi administrator untuk menambahkan siswa.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .students-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    .students-header {
        margin-bottom: 24px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 8px;
    }

    .breadcrumb a {
        color: var(--md-sys-color-primary, #0066cc);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb .separator {
        color: var(--md-sys-color-on-surface-variant, #999);
    }

    .breadcrumb .current {
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .students-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        font-size: 36px;
    }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--md-sys-color-primary, #0066cc);
    }

    .stat-label {
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    /* Students Section */
    .students-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .students-section h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    /* Table */
    .students-table-container {
        overflow-x: auto;
    }

    .students-table {
        width: 100%;
        border-collapse: collapse;
    }

    .students-table thead {
        border-bottom: 2px solid var(--md-sys-color-outline-variant, #e0e0e0);
    }

    .students-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface-variant, #666);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .students-table td {
        padding: 16px;
        border-bottom: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        font-size: 14px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .students-table tbody tr:hover {
        background: var(--md-sys-color-surface-container, #f5f5f5);
    }

    .student-name {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
    }

    .badge-warning {
        background: var(--md-sys-color-tertiary-container, #fff3e0);
        color: var(--md-sys-color-on-tertiary-container, #e65100);
    }

    .badge-secondary {
        background: var(--md-sys-color-surface-container-highest, #f0f0f0);
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-secondary {
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
    }

    .btn-secondary:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    /* No Students */
    .no-students-card {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 12px;
        padding: 48px 24px;
        text-align: center;
    }

    .no-students-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }

    .no-students-card h3 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .no-students-card p {
        margin: 0;
        font-size: 15px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
        border: 1px solid var(--md-sys-color-secondary, #4caf50);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .students-table-container {
            overflow-x: scroll;
        }

        .students-table {
            min-width: 700px;
        }

        .action-buttons {
            flex-wrap: nowrap;
        }
    }
</style>