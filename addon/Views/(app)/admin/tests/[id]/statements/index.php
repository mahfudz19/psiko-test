<?php

/**
 * Halaman kelola butir soal (statements)
 * @var array $config - Konfigurasi tes
 * @var array $statements - Daftar semua statements
 * @var array $dimensions - Dimensions dari config (key => label)
 */

// Group statements by dimension
$statementsByDimension = [];
$dimensionCounts = [];
foreach ($dimensions as $key => $label) {
    $statementsByDimension[$key] = [];
    $dimensionCounts[$key] = 0;
}
foreach ($statements as $statement) {
    $dim = $statement['dimension'];
    if (!isset($statementsByDimension[$dim])) {
        $statementsByDimension[$dim] = [];
        $dimensionCounts[$dim] = 0;
    }
    $statementsByDimension[$dim][] = $statement;
    $dimensionCounts[$dim]++;
}
?>

<div class="statements-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/tests" class="back-link">
                ← Kembali ke Daftar Konfigurasi
            </a>
            <h1>Kelola Butir Soal</h1>
            <p class="page-description"><?= htmlspecialchars($config['name']) ?></p>
        </div>
    </div>

    <!-- Config Summary Card -->
    <div class="config-summary-card">
        <div class="config-summary-content">
            <div class="config-icon">📋</div>
            <div class="config-info">
                <h2><?= htmlspecialchars($config['name']) ?></h2>
                <div class="config-meta">
                    <span class="badge badge-type badge-<?= htmlspecialchars($config['test_type']) ?>">
                        <?= htmlspecialchars($config['test_type']) ?>
                    </span>
                    <span class="stat-info">
                        <span class="stat-label">Total Soal:</span>
                        <span class="stat-value"><?= count($statements) ?></span>
                    </span>
                    <span class="stat-info">
                        <span class="stat-label">Dimensi:</span>
                        <span class="stat-value"><?= count($dimensions) ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dimensions Overview -->
    <div class="dimensions-overview">
        <h3 class="section-title">Overview Dimensi</h3>
        <div class="dimensions-grid">
            <?php foreach ($dimensions as $key => $label): ?>
                <?php
                // Handle case where label might be an array with {color, label} structure
                $labelText = is_array($label) ? ($label['label'] ?? $key) : (string) $label;
                ?>
                <div class="dimension-card <?= $dimensionCounts[$key] > 0 ? 'has-statements' : 'no-statements' ?>">
                    <div class="dimension-header">
                        <span class="dimension-key"><?= htmlspecialchars($key) ?></span>
                        <span class="dimension-count"><?= $dimensionCounts[$key] ?> soal</span>
                    </div>
                    <p class="dimension-label"><?= htmlspecialchars($labelText) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Statements List Grouped by Dimension -->
    <div class="statements-list-section">
        <h3 class="section-title">
            <span class="section-icon">📝</span>
            Daftar Butir Soal
        </h3>

        <?php foreach ($dimensions as $key => $label): ?>
            <?php
            // Handle case where label might be an array with {color, label} structure
            $labelText = is_array($label) ? ($label['label'] ?? $key) : (string) $label;
            // Calculate next display order for this dimension
            $nextDisplayOrder = $dimensionCounts[$key] + 1;
            ?>
            <div class="dimension-section" id="dimension-<?= htmlspecialchars($key) ?>">
                <div class="dimension-section-header">
                    <h4 class="dimension-section-title">
                        <span class="dimension-key-small"><?= htmlspecialchars($key) ?></span>
                        <span class="dimension-label-small"><?= htmlspecialchars($labelText) ?></span>
                        <span class="statement-count-badge"><?= $dimensionCounts[$key] ?> soal</span>
                    </h4>
                </div>

                <div class="statements-table-container">
                    <!-- Inline Add Form for this dimension -->
                    <form data-spa method="POST" action="/admin/tests/<?= $config['id'] ?>/statements" class="inline-add-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="dimension" value="<?= htmlspecialchars($key) ?>">

                        <div class="inline-form-row">
                            <div class="inline-form-group">
                                <label class="inline-form-label">Urutan</label>
                                <input
                                    type="number"
                                    name="display_order"
                                    class="form-input inline-input"
                                    min="1"
                                    value="<?= $nextDisplayOrder ?>"
                                    required>
                            </div>
                            <div class="inline-form-group inline-form-textarea-group">
                                <label class="inline-form-label">Pernyataan/Soal <span class="required">*</span></label>
                                <textarea
                                    name="statement_text"
                                    class="form-input form-textarea inline-textarea"
                                    rows="2"
                                    placeholder="Tulis pernyataan untuk dimensi <?= htmlspecialchars($key) ?>..."
                                    required></textarea>
                            </div>
                            <div class="inline-form-actions">
                                <button type="submit" class="btn btn-primary btn-add-statement">
                                    <span>➕</span> Tambah
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if (empty($statementsByDimension[$key])): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>Belum ada soal untuk dimensi ini</p>
                            <p class="empty-hint">Gunakan form di atas untuk menambahkan</p>
                        </div>
                    <?php else: ?>
                        <table class="statements-table">
                            <thead>
                                <tr>
                                    <th class="col-order">Urutan</th>
                                    <th class="col-statement">Pernyataan</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-actions">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statementsByDimension[$key] as $statement): ?>
                                    <tr class="statement-row <?= $statement['is_active'] ? 'active' : 'inactive' ?>">
                                        <td class="order-cell"><?= $statement['display_order'] ?></td>
                                        <td class="statement-cell"><?= htmlspecialchars($statement['statement_text']) ?></td>
                                        <td class="status-cell">
                                            <?php if ($statement['is_active']): ?>
                                                <span class="status-badge status-active">Aktif</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions-cell">
                                            <form data-spa method="POST" action="/admin/tests/<?= $config['id'] ?>/statements/<?= $statement['id'] ?>/delete" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus butir soal ini?')">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn-icon btn-delete" title="Hapus">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .statements-page {
        padding: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 20px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 12px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #374151;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 4px 0;
    }

    .page-description {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    /* Config Summary Card */
    .config-summary-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        border-left: 4px solid #3b82f6;
    }

    .config-summary-content {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
    }

    .config-icon {
        font-size: 40px;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        border-radius: 12px;
    }

    .config-info h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 8px 0;
    }

    .config-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .badge-type {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
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

    .stat-info {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 13px;
        color: #6b7280;
    }

    .stat-value {
        font-weight: 600;
        color: #3b82f6;
    }

    /* Dimensions Overview */
    .dimensions-overview {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-icon {
        font-size: 18px;
    }

    .dimensions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .dimension-card {
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        transition: all 0.2s;
    }

    .dimension-card.has-statements {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .dimension-card.no-statements {
        opacity: 0.7;
    }

    .dimension-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .dimension-key {
        display: inline-block;
        padding: 4px 10px;
        background: #3b82f6;
        color: #fff;
        border-radius: 6px;
        font-weight: 700;
        font-size: 14px;
    }

    .dimension-card.has-statements .dimension-key {
        background: #10b981;
    }

    .dimension-count {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
    }

    .dimension-card.has-statements .dimension-count {
        color: #10b981;
    }

    .dimension-label {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
        line-height: 1.4;
    }

    /* Inline Add Form within each dimension */
    .inline-add-form {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .inline-form-row {
        display: grid;
        grid-template-columns: 100px 1fr auto;
        gap: 12px;
        align-items: start;
    }

    .inline-form-group {
        margin-bottom: 0;
    }

    .inline-form-textarea-group {
        display: flex;
        flex-direction: column;
    }

    .inline-form-label {
        display: block;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 4px;
        font-size: 12px;
    }

    .inline-input {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
    }

    .inline-textarea {
        resize: vertical;
        font-family: inherit;
        line-height: 1.4;
        min-height: 60px;
    }

    .inline-form-actions {
        display: flex;
        align-items: flex-end;
        padding-bottom: 2px;
    }

    .btn-add-statement {
        white-space: nowrap;
        padding: 8px 16px;
        font-size: 13px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 150px;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .required {
        color: #ef4444;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        resize: vertical;
        font-family: inherit;
        line-height: 1.5;
    }

    .form-hint {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 12px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 8px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-primary {
        background: #3b82f6;
        color: #fff;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    /* Statements List */
    .statements-list-section {
        margin-bottom: 20px;
    }

    .dimension-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .dimension-section-header {
        padding: 16px 20px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .dimension-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    .dimension-key-small {
        display: inline-block;
        padding: 3px 8px;
        background: #3b82f6;
        color: #fff;
        border-radius: 5px;
        font-weight: 700;
        font-size: 12px;
    }

    .dimension-label-small {
        font-weight: 500;
        color: #6b7280;
        font-size: 13px;
    }

    .statement-count-badge {
        margin-left: auto;
        background: #e5e7eb;
        color: #374151;
        padding: 2px 10px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .statements-table-container {
        padding: 12px;
    }

    .statements-table {
        width: 100%;
        border-collapse: collapse;
    }

    .statements-table thead tr {
        border-bottom: 2px solid #e5e7eb;
    }

    .statements-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .statements-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
    }

    .statements-table tbody tr:hover {
        background: #f9fafb;
    }

    .statements-table td {
        padding: 14px 16px;
        font-size: 14px;
    }

    .col-order {
        width: 80px;
    }

    .order-cell {
        font-weight: 600;
        color: #3b82f6;
    }

    .col-statement {
        width: auto;
    }

    .statement-cell {
        color: #1a1a1a;
        line-height: 1.5;
    }

    .col-status {
        width: 100px;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #f3f4f6;
        color: #6b7280;
    }

    .col-actions {
        width: 60px;
        text-align: right;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 16px;
    }

    .btn-icon:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }

    .btn-delete:hover {
        background: #fee2e2;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-hint {
        font-size: 13px;
        margin-top: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .dimensions-grid {
            grid-template-columns: 1fr;
        }

        .inline-form-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .inline-form-actions {
            justify-content: flex-end;
        }

        .statements-table {
            font-size: 13px;
        }

        .statements-table th,
        .statements-table td {
            padding: 10px;
        }

        .col-order {
            width: 60px;
        }

        .col-actions {
            width: 50px;
        }
    }
</style>

<script>
    // Initialize function for SPA navigation compatibility
    function initStatementsPage() {
        // Prevent duplicate initialization
        if (window.isStatementsPageInitialized) return;
        window.isStatementsPageInitialized = true;

        // Confirm delete
        const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Yakin ingin menghapus butir soal ini?')) {
                    e.preventDefault();
                }
            });
        });

        // Auto-increment display order when form is submitted (page reloads with new count)
        // This is handled server-side by calculating next order per dimension
    }

    // Initialize on initial page load
    document.addEventListener('DOMContentLoaded', initStatementsPage);

    // Initialize on SPA navigation (Mazu custom event)
    window.addEventListener('spa:navigated', initStatementsPage);
</script>