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
            <a data-spa href="/profile" data-spa>Profil</a>
            <span class="separator">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
            <span class="current">Data Akademik</span>
        </nav>
        <h1>Data Akademik</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form data-spa action="<?= getBaseUrl("/profile/academic") ?>" class="academic-form" method="POST">
        <!-- Section 1: Informasi Sekolah -->
        <section class="edit-section">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"></path>
                    <path d="M5 21V7l8-4v18"></path>
                    <path d="M19 10V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6"></path>
                    <path d="M13 21v-9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v9"></path>
                </svg>
                Informasi Sekolah
            </h2>

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
                    <small class="form-text">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        Kosongkan jika tidak ada jurusan (SD/SMP)
                    </small>
                </div>
            </div>
        </section>

        <!-- Section 2: Informasi Orang Tua/Wali -->
        <section class="edit-section">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Informasi Orang Tua/Wali
            </h2>

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
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Nilai Akademik
            </h2>
            <p class="section-description">
                Masukkan nilai rapor berdasarkan semester. Anda dapat menambahkan beberapa semester sekaligus.
            </p>

            <div id="semesters-container" class="semesters-container">
                <!-- Semester blocks will be injected here by JS -->
            </div>

            <div class="semester-actions">
                <button type="button" class="btn-add-semester" onclick="addSemester()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Semester Baru
                </button>
            </div>

            <input type="hidden" id="academic_scores_json" name="academic_scores_json" value="">
        </section>

        <!-- Form Actions -->
        <div class="form-actions">
            <a data-spa href="/profile" class="btn-cancel" data-spa>Batal</a>
            <button type="submit" class="btn-save">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Data Akademik
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
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Mata Pelajaran
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