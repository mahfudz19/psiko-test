<?php

/**
 * @var array $configs - List of test configurations
 */
?>

<div class="tests-page">
    <div class="page-header">
        <div>
            <h1>Kelola Konfigurasi Tes</h1>
            <p class="page-description">Daftar semua konfigurasi tes yang tersedia</p>
        </div>
        <a data-spa href="/admin/tests/create" class="btn btn-primary">
            <span>➕</span> Tambah Konfigurasi
        </a>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        <span class="results-count">
            Menampilkan <strong><?= count($configs) ?></strong> konfigurasi
        </span>
    </div>

    <!-- Tests Table -->
    <div class="tests-table-container">
        <table class="tests-table">
            <thead>
                <tr>
                    <th>Nama Konfigurasi</th>
                    <th>Tipe Tes</th>
                    <th>Dimensi</th>
                    <th>Butir Soal</th>
                    <th>Sekolah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($configs)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-message">
                                <div class="empty-icon">📋</div>
                                <p>Belum ada konfigurasi tes terdaftar</p>
                                <a data-spa href="/admin/tests/create" class="btn btn-primary btn-sm">
                                    <span>➕</span> Tambah Konfigurasi Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($configs as $config): ?>
                        <?php
                        $dimensions = json_decode($config['dimensions'], true) ?? [];
                        $dimensionKeys = array_keys($dimensions);
                        ?>
                        <tr>
                            <td>
                                <div class="config-name">
                                    <strong><?= htmlspecialchars($config['name']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-type badge-<?= htmlspecialchars($config['test_type']) ?>">
                                    <?= htmlspecialchars($config['test_type']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="dimension-tags">
                                    <?php foreach ($dimensionKeys as $key): ?>
                                        <span class="dimension-tag" title="<?= htmlspecialchars($dimensions[$key]['label'] ?? $key) ?>">
                                            <?= htmlspecialchars($key) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <span class="stat-badge"><?= $config['statement_count'] ?? 0 ?></span>
                            </td>
                            <td>
                                <span class="stat-badge"><?= $config['school_count'] ?? 0 ?></span>
                            </td>
                            <td>
                                <?php if ($config['is_active']): ?>
                                    <span class="status-badge status-active">Aktif</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        data-spa
                                        href="/admin/tests/<?= $config['id'] ?>/statements"
                                        class="btn btn-sm btn-secondary"
                                        title="Kelola Butir Soal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                            <polyline points="14 2 14 8 20 8" />
                                        </svg>
                                    </a>
                                    <a
                                        data-spa
                                        href="/admin/tests/<?= $config['id'] ?>/assign"
                                        class="btn btn-sm btn-info"
                                        title="Assign ke Sekolah">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                    </a>
                                    <a
                                        data-spa
                                        href="/admin/tests/<?= $config['id'] ?>/edit"
                                        class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>
                                    <form data-spa method="POST" action="/admin/tests/<?= $config['id'] ?>/toggle-active" class="inline-form" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm <?= $config['is_active'] ? 'btn-danger' : 'btn-success' ?>"
                                            title="<?= $config['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                            onclick="return confirm('<?= $config['is_active'] ? 'Nonaktifkan konfigurasi ini?' : 'Aktifkan konfigurasi ini?' ?>')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .tests-page {
        padding: 24px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }

    .page-description {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .results-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        font-size: 14px;
        color: #666;
    }

    .tests-table-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .tests-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tests-table th {
        background: #f8f9fa;
        padding: 12px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .tests-table td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .config-name {
        font-weight: 500;
    }

    .badge-type {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .badge-riasec {
        background: #dcfce7;
        color: #166534;
    }

    .badge-iq {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-learning_style {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-personality {
        background: #f3e8ff;
        color: #6b21a8;
    }

    .dimension-tags {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .dimension-tag {
        display: inline-block;
        padding: 2px 6px;
        background: #f1f5f9;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        padding: 4px 8px;
        background: #f8fafc;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 13px;
    }

    .btn-info {
        background: #0ea5e9;
        color: #fff;
    }

    .btn-info:hover {
        background: #0284c7;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
    }

    .empty-message {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .empty-icon {
        font-size: 48px;
        opacity: 0.5;
    }

    .empty-message p {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .inline-form {
        display: inline;
    }

    .btn-success {
        background: #16a34a;
        color: #fff;
    }

    .btn-success:hover {
        background: #15803d;
    }

    .btn-danger {
        background: #dc2626;
        color: #fff;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }
</style>