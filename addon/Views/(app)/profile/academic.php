<?php

/**
 * Student Academic Data View
 * 
 * @var array $profile Profile data
 * @var array|null $studentProfile Student profile data
 * @var array $schools List of schools
 */
?>

<div class="academic-container">
    <div class="academic-header">
        <div class="breadcrumb">
            <a href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Data Akademik</span>
        </div>
        <h1>Data Akademik</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <form id="academic-form" class="academic-form" method="POST">
        <div class="form-section">
            <h2>Informasi Sekolah</h2>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="school_id">Sekolah <span class="required">*</span></label>
                    <select id="school_id" name="school_id" required>
                        <option value="">Pilih Sekolah</option>
                        <?php foreach ($schools ?? [] as $school): ?>
                            <option value="<?= $school['id'] ?>"
                                <?= ($studentProfile['school_id'] ?? '') == $school['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($school['name']) ?> - <?= htmlspecialchars($school['npsn'] ?? 'N/A') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text">Sekolah belum ada? Hubungi admin untuk menambahkan.</small>
                </div>

                <div class="form-group">
                    <label for="student_id">NIS/NISN <span class="required">*</span></label>
                    <input type="text" id="student_id" name="student_id"
                        value="<?= htmlspecialchars($studentProfile['student_id'] ?? '') ?>"
                        required placeholder="Masukkan NIS/NISN">
                </div>

                <div class="form-group">
                    <label for="grade_level">Jenjang <span class="required">*</span></label>
                    <select id="grade_level" name="grade_level" required>
                        <option value="">Pilih Jenjang</option>
                        <option value="sd" <?= ($studentProfile['grade_level'] ?? '') === 'sd' ? 'selected' : '' ?>>SD</option>
                        <option value="smp" <?= ($studentProfile['grade_level'] ?? '') === 'smp' ? 'selected' : '' ?>>SMP</option>
                        <option value="sma" <?= ($studentProfile['grade_level'] ?? '') === 'sma' ? 'selected' : '' ?>>SMA</option>
                        <option value="smk" <?= ($studentProfile['grade_level'] ?? '') === 'smk' ? 'selected' : '' ?>>SMK</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="major">Jurusan</label>
                    <input type="text" id="major" name="major"
                        value="<?= htmlspecialchars($studentProfile['major'] ?? '') ?>"
                        placeholder="Contoh: IPA, IPS, RPL, TKJ">
                    <small class="form-text">Kosongkan jika tidak ada jurusan (SD/SMP)</small>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Informasi Orang Tua/Wali</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="parent_name">Nama Lengkap Orang Tua/Wali <span class="required">*</span></label>
                    <input type="text" id="parent_name" name="parent_name"
                        value="<?= htmlspecialchars($studentProfile['parent_name'] ?? '') ?>"
                        required placeholder="Nama lengkap">
                </div>

                <div class="form-group">
                    <label for="parent_phone">No. Telepon <span class="required">*</span></label>
                    <input type="tel" id="parent_phone" name="parent_phone"
                        value="<?= htmlspecialchars($studentProfile['parent_phone'] ?? '') ?>"
                        required placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label for="parent_email">Email</label>
                    <input type="email" id="parent_email" name="parent_email"
                        value="<?= htmlspecialchars($studentProfile['parent_email'] ?? '') ?>"
                        placeholder="email@example.com">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Nilai Akademik</h2>
            <p class="section-description">
                Masukkan nilai rapor untuk setiap mata pelajaran. Skala 0-100.
            </p>

            <!-- Smart Input Mode Toggle -->
            <div class="input-mode-toggle">
                <button type="button" class="mode-btn active" data-mode="smart" onclick="switchInputMode('smart')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Smart Paste
                </button>
                <button type="button" class="mode-btn" data-mode="manual" onclick="switchInputMode('manual')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Manual Entry
                </button>
            </div>

            <!-- Smart Textarea Mode -->
            <div id="smart-input-mode" class="smart-input-mode">
                <div class="smart-input-header">
                    <p class="smart-input-description">
                        <strong>Cara menggunakan:</strong> Copy data dari Excel/Google Sheets (2 kolom: Mata Pelajaran | Nilai),
                        lalu paste di bawah ini. Atau ketik manual dengan format: <code>Mata Pelajaran, Nilai</code>
                    </p>
                    <button type="button" class="btn btn-outline btn-sm" onclick="downloadTemplate()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download Template Excel
                    </button>
                </div>

                <textarea
                    id="smart-scores-input"
                    class="smart-textarea"
                    placeholder="Contoh paste dari Excel:
Mathematics    85
English    90
Physics    88

Atau ketik manual:
Mathematics, 85
English, 90"
                    rows="8"></textarea>

                <div class="smart-input-actions">
                    <button type="button" class="btn btn-primary" onclick="parseSmartInput()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Parse Data
                    </button>
                    <button type="button" class="btn btn-outline" onclick="clearSmartInput()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Clear
                    </button>
                    <span class="parse-status" id="parse-status"></span>
                </div>
            </div>

            <!-- Live Preview Table -->
            <div id="preview-container" class="preview-container" style="display: none;">
                <div class="preview-header">
                    <h3>Preview Data Nilai</h3>
                    <div class="preview-actions">
                        <span class="score-count" id="score-count">0 mata pelajaran</span>
                        <button type="button" class="btn btn-outline btn-sm" onclick="editFromPreview()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit
                        </button>
                    </div>
                </div>
                <div class="preview-table-wrapper">
                    <table class="preview-table" id="preview-table">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>Mata Pelajaran</th>
                                <th width="100">Nilai</th>
                                <th width="60">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="preview-tbody">
                        </tbody>
                    </table>
                </div>
                <!-- Hidden input untuk menyimpan data parsed -->
                <input type="hidden" id="parsed-scores-data" name="parsed_scores_data" value="">
            </div>

            <!-- Manual Entry Mode (Fallback) -->
            <div id="manual-input-mode" class="manual-input-mode" style="display: none;">
                <div id="academic-scores-container">
                    <div class="score-entry">
                        <div class="score-input-group">
                            <input type="text" name="academic_scores[subject][]" placeholder="Mata Pelajaran" class="subject-input">
                            <input type="number" name="academic_scores[grade][]" placeholder="Nilai" min="0" max="100" class="grade-input">
                            <button type="button" class="btn-remove-score" onclick="removeScoreRow(this)">×</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary btn-sm" onclick="addScoreRow()">
                    + Tambah Mata Pelajaran
                </button>
            </div>
        </div>

        <div class="form-actions">
            <a href="/profile" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Data Akademik</button>
        </div>
    </form>
</div>

<style>
    .academic-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 24px;
    }

    .academic-header {
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

    .academic-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .form-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-section h2 {
        margin: 0 0 16px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .section-description {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 16px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .form-group .required {
        color: var(--md-sys-color-error, #dc3545);
    }

    .form-group input,
    .form-group select {
        padding: 12px 16px;
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 8px;
        font-size: 15px;
        font-family: inherit;
        transition: all 0.2s;
        background: var(--md-sys-color-surface, #ffffff);
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .form-text {
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    /* Input Mode Toggle */
    .input-mode-toggle {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        padding: 4px;
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 8px;
    }

    .mode-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: none;
        background: transparent;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface-variant, #666);
        cursor: pointer;
        transition: all 0.2s;
    }

    .mode-btn:hover {
        background: var(--md-sys-color-surface-container-high, #e8e8e8);
    }

    .mode-btn.active {
        background: var(--md-sys-color-surface, #ffffff);
        color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .mode-btn svg {
        width: 16px;
        height: 16px;
    }

    /* Smart Input Mode */
    .smart-input-mode {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .smart-input-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
        background: var(--md-sys-color-surface-container-low, #f9f9f9);
        border-radius: 8px;
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
    }

    .smart-input-description {
        flex: 1;
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0;
        line-height: 1.6;
    }

    .smart-input-description code {
        background: var(--md-sys-color-surface-container-high, #e8e8e8);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 13px;
        color: var(--md-sys-color-primary, #0066cc);
    }

    .smart-textarea {
        width: 100%;
        padding: 16px;
        border: 2px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 8px;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 14px;
        line-height: 1.6;
        resize: vertical;
        transition: all 0.2s;
        background: var(--md-sys-color-surface, #ffffff);
        color: var(--md-sys-color-on-surface, #1a1a1a);
        box-sizing: border-box;
    }

    .smart-textarea:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .smart-textarea::placeholder {
        color: var(--md-sys-color-on-surface-variant, #999);
        font-family: inherit;
    }

    .smart-input-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--md-sys-color-outline, #ccc);
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .btn-outline:hover {
        background: var(--md-sys-color-surface-container-high, #e8e8e8);
    }

    .parse-status {
        font-size: 14px;
        margin-left: auto;
        padding: 8px 12px;
        border-radius: 6px;
        display: none;
    }

    .parse-status.success {
        display: inline-block;
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
    }

    .parse-status.error {
        display: inline-block;
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-on-error-container, #c62828);
    }

    /* Preview Container */
    .preview-container {
        margin-top: 24px;
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 8px;
        overflow: hidden;
    }

    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-bottom: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
    }

    .preview-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .preview-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .score-count {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .preview-table-wrapper {
        max-height: 400px;
        overflow-y: auto;
    }

    .preview-table {
        width: 100%;
        border-collapse: collapse;
    }

    .preview-table th {
        position: sticky;
        top: 0;
        background: var(--md-sys-color-surface-container-high, #e8e8e8);
        padding: 12px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
        border-bottom: 2px solid var(--md-sys-color-outline, #ccc);
        z-index: 1;
    }

    .preview-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        font-size: 14px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .preview-table tbody tr:hover {
        background: var(--md-sys-color-surface-container-low, #f9f9f9);
    }

    .preview-table tbody tr.editing {
        background: var(--md-sys-color-primary-container, #e6f0ff);
    }

    .btn-icon-sm {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-icon-sm.edit {
        color: var(--md-sys-color-primary, #0066cc);
    }

    .btn-icon-sm.edit:hover {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-icon-sm.delete {
        color: var(--md-sys-color-error, #dc3545);
    }

    .btn-icon-sm.delete:hover {
        background: var(--md-sys-color-error, #dc3545);
        color: white;
    }

    .preview-input-cell {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .preview-input-cell input {
        padding: 6px 10px;
        border: 1px solid var(--md-sys-color-outline, #ccc);
        border-radius: 4px;
        font-size: 14px;
    }

    .preview-input-cell input:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.1);
    }

    .preview-input-cell .subject-input {
        flex: 2;
        min-width: 150px;
    }

    .preview-input-cell .grade-input {
        flex: 1;
        width: 80px;
    }

    /* Manual Input Mode */
    .manual-input-mode {
        margin-top: 16px;
    }

    .score-entry {
        margin-bottom: 12px;
    }

    .score-input-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .subject-input {
        flex: 2;
    }

    .grade-input {
        flex: 1;
        max-width: 120px;
    }

    .btn-remove-score {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: none;
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-error, #dc3545);
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-remove-score:hover {
        background: var(--md-sys-color-error, #dc3545);
        color: white;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-primary:hover {
        background: var(--md-sys-color-on-primary, #0052a3);
    }

    .btn-secondary {
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
    }

    .btn-secondary:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 14px;
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
        .form-grid {
            grid-template-columns: 1fr;
        }

        .score-input-group {
            flex-wrap: wrap;
        }

        .subject-input,
        .grade-input {
            flex: 1;
            min-width: 150px;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    // Global state untuk smart textarea
    let parsedScores = [];
    let currentInputMode = 'smart';

    // Initialize with existing scores
    <?php if (!empty($studentProfile['academic_scores'])): ?>
        const existingScores = <?= $studentProfile['academic_scores'] ?>;
        existingScores.forEach(score => {
            addScoreRow(score.subject, score.grade);
        });
    <?php endif; ?>

    // Switch input mode
    function switchInputMode(mode) {
        currentInputMode = mode;
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });
        document.getElementById('smart-input-mode').style.display = mode === 'smart' ? 'flex' : 'none';
        document.getElementById('manual-input-mode').style.display = mode === 'manual' ? 'block' : 'none';
        document.getElementById('preview-container').style.display = (mode === 'smart' && parsedScores.length > 0) ? 'block' : 'none';
    }

    // Download template Excel (CSV format)
    function downloadTemplate() {
        const templateData = [
            ['Mata Pelajaran', 'Nilai'],
            ['Mathematics', '85'],
            ['English', '90'],
            ['Physics', '88'],
            ['Chemistry', '87'],
            ['Biology', '89'],
            ['History', '85'],
            ['Geography', '86'],
            ['Economics', '88'],
            ['Indonesian', '90'],
            ['Art', '85']
        ];

        const csvContent = templateData.map(row => row.join('\t')).join('\n');
        const blob = new Blob([csvContent], {
            type: 'text/tab-separated-values;charset=utf-8;'
        });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'template_nilai_akademik.tsv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Parse smart input
    function parseSmartInput() {
        const textarea = document.getElementById('smart-scores-input');
        const input = textarea.value.trim();
        const statusEl = document.getElementById('parse-status');

        if (!input) {
            showParseStatus('Silakan masukkan data terlebih dahulu', 'error');
            return;
        }

        try {
            const scores = parseInputData(input);

            if (scores.length === 0) {
                showParseStatus('Tidak ada data yang valid. Pastikan format: Mata Pelajaran, Nilai', 'error');
                return;
            }

            parsedScores = scores;
            renderPreviewTable();
            showParseStatus(`Berhasil parse ${scores.length} mata pelajaran`, 'success');

            // Update hidden input
            document.getElementById('parsed-scores-data').value = JSON.stringify(scores);

            // Show preview
            document.getElementById('preview-container').style.display = 'block';
        } catch (error) {
            showParseStatus('Error parsing: ' + error.message, 'error');
        }
    }

    // Parser untuk berbagai format input
    function parseInputData(input) {
        const lines = input.split('\n').filter(line => line.trim());
        const scores = [];

        for (const line of lines) {
            let subject = '';
            let grade = '';

            // Coba parse dengan tab (Excel paste)
            if (line.includes('\t')) {
                const parts = line.split('\t');
                if (parts.length >= 2) {
                    subject = parts[0].trim();
                    grade = parts[1].trim();
                }
            }
            // Coba parse dengan koma
            else if (line.includes(',')) {
                const parts = line.split(',');
                if (parts.length >= 2) {
                    subject = parts[0].trim();
                    grade = parts[parts.length - 1].trim();
                }
            }
            // Coba parse dengan pipe
            else if (line.includes('|')) {
                const parts = line.split('|');
                if (parts.length >= 2) {
                    subject = parts[0].trim();
                    grade = parts[1].trim();
                }
            }
            // Coba parse dengan titik dua
            else if (line.includes(':')) {
                const parts = line.split(':');
                if (parts.length >= 2) {
                    subject = parts[0].trim();
                    grade = parts[1].trim();
                }
            }
            // Default: ambil angka terakhir sebagai nilai
            else {
                const match = line.match(/(.+?)\s*(\d+)\s*$/);
                if (match) {
                    subject = match[1].trim();
                    grade = match[2].trim();
                }
            }

            // Validasi
            if (subject && grade && !isNaN(parseInt(grade))) {
                const gradeNum = parseInt(grade);
                if (gradeNum >= 0 && gradeNum <= 100) {
                    scores.push({
                        subject: subject,
                        grade: gradeNum,
                        _id: Date.now() + Math.random() // Unique ID untuk edit/delete
                    });
                }
            }
        }

        return scores;
    }

    function showParseStatus(message, type) {
        const statusEl = document.getElementById('parse-status');
        statusEl.textContent = message;
        statusEl.className = 'parse-status ' + type;
        setTimeout(() => {
            statusEl.className = 'parse-status';
        }, 5000);
    }

    function clearSmartInput() {
        document.getElementById('smart-scores-input').value = '';
        document.getElementById('parse-status').className = 'parse-status';
        parsedScores = [];
        document.getElementById('preview-container').style.display = 'none';
        document.getElementById('parsed-scores-data').value = '';
    }

    function renderPreviewTable() {
        const tbody = document.getElementById('preview-tbody');
        tbody.innerHTML = '';

        parsedScores.forEach((score, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td class="subject-cell">${escapeHtml(score.subject)}</td>
                <td class="grade-cell">${score.grade}</td>
                <td>
                    <button type="button" class="btn-icon-sm edit" onclick="editScoreRow(${score._id})" title="Edit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button type="button" class="btn-icon-sm delete" onclick="deleteScoreRow(${score._id})" title="Hapus">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('score-count').textContent = `${parsedScores.length} mata pelajaran`;
    }

    function editScoreRow(id) {
        const score = parsedScores.find(s => s._id === id);
        if (!score) return;

        const tbody = document.getElementById('preview-tbody');
        const tr = tbody.querySelector(`tr:nth-child(${parsedScores.indexOf(score) + 1})`);
        if (!tr) return;

        tr.classList.add('editing');
        tr.innerHTML = `
            <td>${parsedScores.indexOf(score) + 1}</td>
            <td>
                <div class="preview-input-cell">
                    <input type="text" class="subject-input" value="${escapeHtml(score.subject)}">
                </div>
            </td>
            <td>
                <div class="preview-input-cell">
                    <input type="number" class="grade-input" value="${score.grade}" min="0" max="100">
                </div>
            </td>
            <td>
                <button type="button" class="btn-icon-sm edit" onclick="saveScoreRow(${score._id})" title="Simpan">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
                <button type="button" class="btn-icon-sm delete" onclick="cancelEditRow(${score._id})" title="Batal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </td>
        `;
    }

    function saveScoreRow(id) {
        const score = parsedScores.find(s => s._id === id);
        if (!score) return;

        const tbody = document.getElementById('preview-tbody');
        const tr = tbody.querySelector(`tr:nth-child(${parsedScores.indexOf(score) + 1})`);
        if (!tr) return;

        const newSubject = tr.querySelector('.subject-input').value.trim();
        const newGrade = parseInt(tr.querySelector('.grade-input').value);

        if (!newSubject || isNaN(newGrade) || newGrade < 0 || newGrade > 100) {
            alert('Nilai harus antara 0-100 dan mata pelajaran tidak boleh kosong');
            return;
        }

        score.subject = newSubject;
        score.grade = newGrade;
        renderPreviewTable();
        document.getElementById('parsed-scores-data').value = JSON.stringify(parsedScores);
    }

    function cancelEditRow(id) {
        renderPreviewTable();
    }

    function deleteScoreRow(id) {
        if (confirm('Hapus mata pelajaran ini?')) {
            parsedScores = parsedScores.filter(s => s._id !== id);
            renderPreviewTable();
            document.getElementById('parsed-scores-data').value = JSON.stringify(parsedScores);

            if (parsedScores.length === 0) {
                document.getElementById('preview-container').style.display = 'none';
            }
        }
    }

    function editFromPreview() {
        // Switch ke manual mode untuk edit lebih lanjut jika perlu
        const textarea = document.getElementById('smart-scores-input');
        if (parsedScores.length > 0) {
            // Generate text dari parsed scores untuk edit di textarea
            const text = parsedScores.map(s => `${s.subject}\t${s.grade}`).join('\n');
            textarea.value = text;
            textarea.focus();
            textarea.select();
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Prepare hidden inputs untuk form submission
    function prepareFormSubmission() {
        // Jika menggunakan smart mode dan ada parsed scores, tambahkan ke form
        if (currentInputMode === 'smart' && parsedScores.length > 0) {
            // Remove existing hidden inputs
            document.querySelectorAll('input[name="academic_scores[subject][]"]').forEach(el => el.remove());
            document.querySelectorAll('input[name="academic_scores[grade][]"]').forEach(el => el.remove());

            // Add hidden inputs dari parsed scores
            parsedScores.forEach(score => {
                const subjectInput = document.createElement('input');
                subjectInput.type = 'hidden';
                subjectInput.name = 'academic_scores[subject][]';
                subjectInput.value = score.subject;
                document.getElementById('academic-form').appendChild(subjectInput);

                const gradeInput = document.createElement('input');
                gradeInput.type = 'hidden';
                gradeInput.name = 'academic_scores[grade][]';
                gradeInput.value = score.grade;
                document.getElementById('academic-form').appendChild(gradeInput);
            });
        }
    }

    function addScoreRow(subject = '', grade = '') {
        const container = document.getElementById('academic-scores-container');
        const entry = document.createElement('div');
        entry.className = 'score-entry';
        entry.innerHTML = `
        <div class="score-input-group">
            <input type="text" name="academic_scores[subject][]" placeholder="Mata Pelajaran" class="subject-input" value="${subject}">
            <input type="number" name="academic_scores[grade][]" placeholder="Nilai" min="0" max="100" class="grade-input" value="${grade}">
            <button type="button" class="btn-remove-score" onclick="removeScoreRow(this)">×</button>
        </div>
    `;
        container.appendChild(entry);
    }

    function removeScoreRow(button) {
        const container = document.getElementById('academic-scores-container');
        if (container.children.length > 1) {
            button.closest('.score-entry').remove();
        } else {
            alert('Minimal harus ada satu mata pelajaran');
        }
    }

    // Form submission
    document.getElementById('academic-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Prepare hidden inputs dari parsed scores jika menggunakan smart mode
        prepareFormSubmission();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action || window.location.href, {
                method: 'POST',
                body: formData
            });

            if (response.redirected) {
                window.location.href = response.url;
            } else if (response.ok) {
                window.location.href = '/profile/academic';
            } else {
                const error = await response.text();
                alert('Gagal menyimpan: ' + error);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
</script>