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
    // Initialize with existing scores
    <?php if (!empty($studentProfile['academic_scores'])): ?>
        const existingScores = <?= $studentProfile['academic_scores'] ?>;
        existingScores.forEach(score => {
            addScoreRow(score.subject, score.grade);
        });
    <?php endif; ?>

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