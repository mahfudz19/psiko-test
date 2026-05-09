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
            <h2>Nilai Akademik (Multi-Semester)</h2>
            <p class="section-description">
                Masukkan nilai rapor berdasarkan semester. Anda dapat menambahkan beberapa semester sekaligus.
            </p>

            <div id="semesters-container" class="semesters-container">
                <!-- Semester blocks will be injected here by JS -->
            </div>

            <div class="semester-actions">
                <button type="button" class="btn btn-secondary btn-sm" onclick="addSemester()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Semester Baru
                </button>
            </div>
            
            <input type="hidden" id="academic_scores_json" name="academic_scores_json" value="">
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

    /* Semester Builder Styles */
    .semesters-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 20px;
    }

    .semester-block {
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 12px;
        overflow: hidden;
        background: var(--md-sys-color-surface, #ffffff);
        transition: box-shadow 0.2s;
    }

    .semester-block:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .semester-header {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
    }

    .semester-title-input {
        font-size: 16px;
        font-weight: 600;
        border: none;
        background: transparent;
        padding: 4px 8px;
        width: 250px;
        border-radius: 4px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .semester-title-input:focus {
        background: white;
        outline: 1px solid var(--md-sys-color-primary, #0066cc);
    }

    .btn-icon {
        background: transparent;
        border: none;
        color: var(--md-sys-color-on-surface-variant, #666);
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        background: var(--md-sys-color-surface-container-highest, #e0e0e0);
        color: var(--md-sys-color-error, #dc3545);
    }

    .semester-body {
        padding: 20px;
    }

    .subject-row {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
    }

    .subject-input {
        flex: 2;
        padding: 10px 14px;
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 6px;
        font-size: 14px;
    }

    .score-input {
        flex: 1;
        max-width: 120px;
        padding: 10px 14px;
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 6px;
        font-size: 14px;
    }

    .subject-input:focus, .score-input:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .btn-remove-subject {
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-error, #dc3545);
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 6px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-remove-subject:hover {
        background: var(--md-sys-color-error, #dc3545);
        color: white;
    }

    .semester-actions {
        padding-top: 8px;
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
    // Global state untuk menyimpan data multi-semester
    let academicData = [];

    // Initialize with existing scores if any
    <?php if (!empty($studentProfile['academic_scores'])): ?>
        try {
            const parsed = <?= $studentProfile['academic_scores'] ?>;
            if (Array.isArray(parsed)) {
                academicData = parsed.map(sem => ({
                    id: 'sem_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                    semester: sem.semester || 'Semester Baru',
                    subjects: (sem.subjects || []).map(sub => ({
                        id: 'sub_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                        name: sub.name || '',
                        final_score: sub.final_score !== undefined ? sub.final_score : ''
                    }))
                }));
            }
        } catch(e) {
            console.error("Error parsing initial academic scores", e);
        }
    <?php endif; ?>

    // Default if empty
    if (academicData.length === 0) {
        addSemester();
    } else {
        renderSemesters();
    }

    function addSemester() {
        academicData.push({
            id: 'sem_' + Date.now(),
            semester: 'Semester ' + (academicData.length + 1),
            subjects: [
                { id: 'sub_' + Date.now(), name: '', final_score: '' }
            ]
        });
        renderSemesters();
    }

    function removeSemester(semId) {
        if (confirm('Hapus semester ini beserta semua nilainya?')) {
            academicData = academicData.filter(s => s.id !== semId);
            renderSemesters();
        }
    }

    function updateSemesterName(semId, newName) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) sem.semester = newName;
    }

    function addSubject(semId) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            sem.subjects.push({ id: 'sub_' + Date.now() + '_' + Math.random(), name: '', final_score: '' });
            renderSemesters();
        }
    }

    function removeSubject(semId, subId) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            sem.subjects = sem.subjects.filter(sub => sub.id !== subId);
            renderSemesters();
        }
    }

    function updateSubject(semId, subId, field, value) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            const sub = sem.subjects.find(s => s.id === subId);
            if (sub) {
                sub[field] = value;
            }
        }
    }

    function renderSemesters() {
        const container = document.getElementById('semesters-container');
        container.innerHTML = '';

        if (academicData.length === 0) {
            container.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;">Belum ada data semester.</p>';
            return;
        }

        academicData.forEach((sem, index) => {
            const block = document.createElement('div');
            block.className = 'semester-block';
            
            let subjectsHtml = '';
            sem.subjects.forEach(sub => {
                subjectsHtml += `
                    <div class="subject-row">
                        <input type="text" class="subject-input" placeholder="Mata Pelajaran" value="${escapeHtml(sub.name)}" 
                            onchange="updateSubject('${sem.id}', '${sub.id}', 'name', this.value)">
                        <input type="number" class="score-input" placeholder="Nilai (0-100)" min="0" max="100" value="${sub.final_score}" 
                            onchange="updateSubject('${sem.id}', '${sub.id}', 'final_score', this.value)">
                        <button type="button" class="btn-remove-subject" onclick="removeSubject('${sem.id}', '${sub.id}')" title="Hapus Mapel">×</button>
                    </div>
                `;
            });

            block.innerHTML = `
                <div class="semester-header">
                    <input type="text" class="semester-title-input" value="${escapeHtml(sem.semester)}" 
                        onchange="updateSemesterName('${sem.id}', this.value)" placeholder="Nama Semester (Contoh: Semester 1 Kelas 10)">
                    <button type="button" class="btn-icon" onclick="removeSemester('${sem.id}')" title="Hapus Semester">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
                <div class="semester-body">
                    <div class="subjects-container">
                        ${subjectsHtml}
                    </div>
                    <button type="button" class="btn btn-outline btn-sm mt-2" onclick="addSubject('${sem.id}')">
                        + Tambah Mata Pelajaran
                    </button>
                </div>
            `;
            container.appendChild(block);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Form submission
    document.getElementById('academic-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Prepare JSON payload
        // Bersihkan id sementara dan pastikan format sesuai
        const cleanData = academicData.map(sem => {
            return {
                semester: sem.semester,
                subjects: sem.subjects.filter(sub => sub.name.trim() !== '').map(sub => {
                    const cleanSub = { name: sub.name.trim() };
                    if (sub.final_score !== '') {
                        cleanSub.final_score = sub.final_score;
                    }
                    return cleanSub;
                })
            };
        }).filter(sem => sem.subjects.length > 0); // Buang semester kosong

        document.getElementById('academic_scores_json').value = JSON.stringify(cleanData);

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
                const errorText = await response.text();
                // Ekstrak pesan dari HTML Exception Mazu jika memungkinkan, atau tampilkan raw
                alert('Gagal menyimpan: Cek kembali format data Anda.');
                console.error(errorText);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            alert('Terjadi kesalahan koneksi.');
            console.error(error);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
</script>