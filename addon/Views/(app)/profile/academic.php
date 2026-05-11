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
    <!-- Header & Breadcrumb -->
    <div class="academic-header">
        <nav class="breadcrumb">
            <a href="/profile" data-spa>Profil</a>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i></span>
            <span class="current">Data Akademik</span>
        </nav>
        <h1>Data Akademik</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form dta-spa action="<?= getBaseUrl("/profile/academic") ?>" class="academic-form" method="POST">
        <!-- Section 1: Informasi Sekolah -->
        <section class="edit-section">
            <h2><i class="fas fa-school"></i> Informasi Sekolah</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="student_id">NIS/NISN <span class="required">*</span></label>
                    <input type="text" id="student_id" name="student_id"
                        value="<?= htmlspecialchars($studentProfile['student_id'] ?? '') ?>"
                        required placeholder="Masukkan NIS/NISN">
                </div>

                <div class="form-group">
                    <label for="grade_level">Kelas <span class="required">*</span></label>
                    <select id="grade_level" name="grade_level" required>
                        <option value="">Pilih Kelas</option>
                        <option value="10" <?= ($studentProfile['grade_level'] ?? '') === '10' ? 'selected' : '' ?>>10</option>
                        <option value="11" <?= ($studentProfile['grade_level'] ?? '') === '11' ? 'selected' : '' ?>>11</option>
                        <option value="12" <?= ($studentProfile['grade_level'] ?? '') === '12' ? 'selected' : '' ?>>12</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="major">Jurusan</label>
                    <input type="text" id="major" name="major"
                        value="<?= htmlspecialchars($studentProfile['major'] ?? '') ?>"
                        placeholder="Contoh: IPA, IPS, RPL, TKJ">
                    <small class="form-text"><i class="fas fa-info-circle"></i> Kosongkan jika tidak ada jurusan (SD/SMP)</small>
                </div>
            </div>
        </section>

        <!-- Section 2: Informasi Orang Tua/Wali -->
        <section class="edit-section">
            <h2><i class="fas fa-users"></i> Informasi Orang Tua/Wali</h2>

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
        </section>

        <!-- Section 3: Nilai Akademik (Multi-Semester) -->
        <section class="edit-section">
            <h2><i class="fas fa-book-open"></i> Nilai Akademik</h2>
            <p class="section-description">
                Masukkan nilai rapor berdasarkan semester. Anda dapat menambahkan beberapa semester sekaligus.
            </p>

            <div id="semesters-container" class="semesters-container">
                <!-- Semester blocks will be injected here by JS -->
            </div>

            <div class="semester-actions">
                <button type="button" class="btn-add-semester" onclick="addSemester()">
                    <i class="fas fa-plus"></i> Tambah Semester Baru
                </button>
            </div>

            <input type="hidden" id="academic_scores_json" name="academic_scores_json" value="">
        </section>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="/profile" class="btn-cancel" data-spa>Batal</a>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Simpan Data Akademik
            </button>
        </div>
    </form>
</div>

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
        } catch (e) {
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
            subjects: [{
                id: 'sub_' + Date.now(),
                name: '',
                final_score: ''
            }]
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
        if (sem) {
            sem.semester = newName;
            updateHiddenInput();
        }
    }

    function addSubject(semId) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            sem.subjects.push({
                id: 'sub_' + Date.now() + '_' + Math.random(),
                name: '',
                final_score: ''
            });
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
                updateHiddenInput(); // Update hidden input immediately
            }
        }
    }

    function renderSemesters() {
        const container = document.getElementById('semesters-container');
        container.innerHTML = '';

        if (academicData.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 20px; color: #666;">Belum ada data semester.</p>';
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
                    <button type="button" class="btn-add-semester" onclick="addSubject('${sem.id}')" style="margin-top: 12px; font-size: 13px; padding: 8px 12px;">
                        <i class="fas fa-plus"></i> Tambah Mata Pelajaran
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

    // Set hidden input value setiap kali ada perubahan
    function updateHiddenInput() {
        const cleanData = academicData.map(sem => {
            return {
                semester: sem.semester,
                subjects: sem.subjects.filter(sub => sub.name.trim() !== '').map(sub => {
                    const cleanSub = {
                        name: sub.name.trim()
                    };
                    if (sub.final_score !== '') {
                        cleanSub.final_score = Number(sub.final_score);
                    }
                    return cleanSub;
                })
            };
        }).filter(sem => sem.subjects.length > 0);

        const jsonValue = JSON.stringify(cleanData);
        document.getElementById('academic_scores_json').value = jsonValue;
    }

    // Update hidden input setiap kali renderSemesters dipanggil
    const originalRenderSemesters = renderSemesters;
    renderSemesters = function() {
        originalRenderSemesters();
        updateHiddenInput();
    };

    // Initial update
    updateHiddenInput();
</script>