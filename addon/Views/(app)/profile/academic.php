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

    <form data-spa action="<?= getBaseUrl("/profile/academic") ?>" class="academic-form" method="POST" id="academicForm">
        <!-- Validation Error Container -->
        <div id="validation-error-container" class="validation-error-container" style="display: none; background: #fef2f2; border: 1px solid #ef4444; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <div class="validation-error-header" style="display: flex; align-items: center; gap: 8px; color: #dc2626; font-weight: 600; margin-bottom: 12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Validasi Gagal</span>
            </div>
            <ul id="validation-error-list" class="validation-error-list" style="list-style: disc; padding-left: 24px; color: #dc2626; margin: 0;"></ul>
        </div>

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
            <!-- Data attribute untuk default value - menggunakan htmlspecialchars untuk keamanan -->
            <div id="academic_scores_default"
                data-default="<?= !empty($studentProfile['academic_scores']) ? htmlspecialchars($studentProfile['academic_scores'], ENT_QUOTES, 'UTF-8') : '' ?>"
                style="display: none;"></div>
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

    /**
     * Initialize academic data from encoded JSON
     * Data dari PHP sudah berupa JSON string di database, jadi langsung parse saja
     * @param {string} encodedData - JSON string dari data attribute
     * @returns {Array} Array of semester objects with unique IDs
     */
    function initAcademicData(encodedData) {
        console.log('[Academic] Raw encodedData:', encodedData);

        // Cek jika data kosong
        if (!encodedData || encodedData.trim() === '') {
            console.log('[Academic] No data, returning empty array');
            return [];
        }

        try {
            const parsed = JSON.parse(encodedData);
            console.log('[Academic] Parsed data:', parsed);

            if (!Array.isArray(parsed) || parsed.length === 0) {
                console.log('[Academic] Parsed data is not array or empty');
                return [];
            }

            // Transform existing data dengan menambahkan unique IDs
            const result = parsed.map((sem, semIndex) => ({
                id: 'sem_' + Date.now() + '_' + semIndex + '_' + Math.random().toString(36).substr(2, 9),
                semester: sem.semester || 'Semester ' + (semIndex + 1),
                subjects: (sem.subjects || []).map((sub, subIndex) => ({
                    id: 'sub_' + Date.now() + '_' + semIndex + '_' + subIndex + '_' + Math.random().toString(36).substr(2, 9),
                    name: sub.name || '',
                    final_score: sub.final_score !== undefined && sub.final_score !== null ? sub.final_score : ''
                }))
            }));

            console.log('[Academic] Transformed result:', result);
            return result;
        } catch (e) {
            console.error("[Academic] Error parsing initial academic scores:", e);
            console.error("[Academic] Failed encodedData:", encodedData);
            return [];
        }
    }

    /**
     * Escape HTML special characters
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Set hidden input value setiap kali ada perubahan
     */
    function updateHiddenInput() {
        const cleanData = academicData.map(sem => {
            return {
                semester: sem.semester,
                subjects: sem.subjects.filter(sub => sub.name.trim() !== '').map(sub => {
                    const cleanSub = {
                        name: sub.name.trim()
                    };
                    if (sub.final_score !== '' && sub.final_score !== null) {
                        cleanSub.final_score = Number(sub.final_score);
                    }
                    return cleanSub;
                })
            };
        }).filter(sem => sem.subjects.length > 0);

        const jsonValue = JSON.stringify(cleanData);
        const hiddenInput = document.getElementById('academic_scores_json');
        if (hiddenInput) {
            hiddenInput.value = jsonValue;
        }
    }

    /**
     * Render semester blocks with proper event binding
     */
    function renderSemesters() {
        const container = document.getElementById('semesters-container');
        if (!container) return;

        container.innerHTML = '';

        if (academicData.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 20px; color: #666;">Belum ada data semester. Klik "Tambah Semester Baru" untuk memulai.</p>';
            updateHiddenInput();
            return;
        }

        academicData.forEach((sem, index) => {
            const block = document.createElement('div');
            block.className = 'semester-block';

            let subjectsHtml = '';
            sem.subjects.forEach(sub => {
                subjectsHtml += `
                    <div class="subject-row">
                        <input type="text" class="subject-input" required placeholder="Mata Pelajaran" value="${escapeHtml(sub.name)}">
                        <input type="number" class="score-input" required placeholder="Nilai (0-100)" min="0" max="100" value="${sub.final_score}">
                        <button type="button" class="btn-remove-subject" title="Hapus Mapel">×</button>
                    </div>
                `;
            });

            block.innerHTML = `
                <div class="semester-header">
                    <input type="text" class="semester-title-input" value="${escapeHtml(sem.semester)}" placeholder="Nama Semester (Contoh: Semester 1 Kelas 10)">
                    <button type="button" class="btn-icon" title="Hapus Semester">
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
                    <button type="button" class="btn-add-subject" style="margin-top: 12px; font-size: 13px; padding: 8px 12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Mata Pelajaran
                    </button>
                </div>
            `;

            // Bind event listeners
            bindSemesterEvents(block, sem);
            container.appendChild(block);
        });

        updateHiddenInput();
    }

    /**
     * Bind event listeners to semester block elements
     * @param {HTMLElement} block - Semester block element
     * @param {Object} sem - Semester data object
     */
    function bindSemesterEvents(block, sem) {
        // Semester title input
        const titleInput = block.querySelector('.semester-title-input');
        if (titleInput) {
            titleInput.addEventListener('input', function() {
                updateSemesterName(sem.id, this.value);
            });
        }

        // Remove semester button
        const removeSemBtn = block.querySelector('.btn-icon');
        if (removeSemBtn) {
            removeSemBtn.addEventListener('click', function() {
                removeSemester(sem.id);
            });
        }

        // Subject inputs
        const subjectRows = block.querySelectorAll('.subject-row');
        subjectRows.forEach((row, idx) => {
            const sub = sem.subjects[idx];
            const nameInput = row.querySelector('.subject-input');
            const scoreInput = row.querySelector('.score-input');
            const removeBtn = row.querySelector('.btn-remove-subject');

            if (nameInput && sub) {
                nameInput.addEventListener('input', function() {
                    updateSubject(sem.id, sub.id, 'name', this.value);
                });
            }

            if (scoreInput && sub) {
                scoreInput.addEventListener('input', function() {
                    updateSubject(sem.id, sub.id, 'final_score', this.value);
                });
            }

            if (removeBtn && sub) {
                removeBtn.addEventListener('click', function() {
                    removeSubject(sem.id, sub.id);
                });
            }
        });

        // Add subject button
        const addSubjectBtn = block.querySelector('.btn-add-subject');
        if (addSubjectBtn) {
            addSubjectBtn.addEventListener('click', function() {
                addSubject(sem.id);
            });
        }
    }

    /**
     * Add new semester block
     */
    function addSemester() {
        academicData.push({
            id: 'sem_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            semester: 'Semester ' + (academicData.length + 1),
            subjects: [{
                id: 'sub_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                name: '',
                final_score: ''
            }]
        });
        renderSemesters();
    }

    /**
     * Remove semester block
     * @param {string} semId - Semester ID to remove
     */
    function removeSemester(semId) {
        if (confirm('Hapus semester ini beserta semua nilainya?')) {
            academicData = academicData.filter(s => s.id !== semId);
            renderSemesters();
        }
    }

    /**
     * Update semester name
     * @param {string} semId - Semester ID
     * @param {string} newName - New semester name
     */
    function updateSemesterName(semId, newName) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            sem.semester = newName;
            updateHiddenInput();
        }
    }

    /**
     * Add subject to semester
     * @param {string} semId - Semester ID
     */
    function addSubject(semId) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            sem.subjects.push({
                id: 'sub_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                name: '',
                final_score: ''
            });
            renderSemesters();
        }
    }

    /**
     * Remove subject from semester
     * @param {string} semId - Semester ID
     * @param {string} subId - Subject ID
     */
    function removeSubject(semId, subId) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            sem.subjects = sem.subjects.filter(sub => sub.id !== subId);
            renderSemesters();
        }
    }

    /**
     * Update subject data
     * @param {string} semId - Semester ID
     * @param {string} subId - Subject ID
     * @param {string} field - Field to update (name or final_score)
     * @param {string} value - New value
     */
    function updateSubject(semId, subId, field, value) {
        const sem = academicData.find(s => s.id === semId);
        if (sem) {
            const sub = sem.subjects.find(s => s.id === subId);
            if (sub) {
                sub[field] = value;
                updateHiddenInput();
            }
        }
    }

    /**
     * Validate form before submission
     * @returns {Object} { valid: boolean, errors: Array }
     */
    function validateForm() {
        const errors = [];

        // 1. Validate Section 1: Informasi Sekolah
        const studentId = document.getElementById('student_id')?.value?.trim();
        if (!studentId) {
            errors.push('NIS/NISN wajib diisi');
        }

        const gradeLevel = document.getElementById('grade_level')?.value?.trim();
        if (!gradeLevel) {
            errors.push('Kelas wajib dipilih');
        }

        // 2. Validate Section 2: Informasi Orang Tua/Wali
        const parentName = document.getElementById('parent_name')?.value?.trim();
        if (!parentName) {
            errors.push('Nama lengkap orang tua/wali wajib diisi');
        }

        const parentPhone = document.getElementById('parent_phone')?.value?.trim();
        if (!parentPhone) {
            errors.push('No. telepon orang tua/wali wajib diisi');
        } else {
            // Validate phone number format (Indonesian format)
            const phoneRegex = /^08[0-9]{8,11}$/;
            if (!phoneRegex.test(parentPhone.replace(/[\s\-]/g, ''))) {
                errors.push('Format no. telepon tidak valid. Gunakan format: 08xxxxxxxxxx');
            }
        }

        const parentEmail = document.getElementById('parent_email')?.value?.trim();
        if (parentEmail) {
            // Validate email format if provided
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(parentEmail)) {
                errors.push('Format email tidak valid');
            }
        }

        // 3. Validate Section 3: Nilai Akademik
        // Pastikan ada minimal 1 semester dengan minimal 1 mata pelajaran yang diisi
        if (academicData.length === 0) {
            errors.push('Minimal tambahkan 1 semester dengan nilai akademik');
        } else {
            // Cek setiap semester
            let hasValidSubject = false;
            const emptySemesters = [];
            const emptySubjects = [];

            academicData.forEach((sem, semIndex) => {
                if (!sem.semester || sem.semester.trim() === '') {
                    emptySemesters.push(`Semester ${semIndex + 1}`);
                }

                // Cek subjects di semester ini
                const validSubjects = sem.subjects.filter(sub => sub.name && sub.name.trim() !== '');

                if (validSubjects.length === 0) {
                    emptySubjects.push(`Semester ${semIndex + 1}`);
                } else {
                    hasValidSubject = true;

                    // Cek apakah ada subject dengan nilai kosong (warning saja, tidak error)
                    validSubjects.forEach(sub => {
                        if (sub.final_score === '' || sub.final_score === null) {
                            // Ini hanya warning, tidak menghalangi submit
                            console.warn(`[Validation] Subject "${sub.name}" tidak memiliki nilai`);
                        } else {
                            // Validate score range
                            const score = Number(sub.final_score);
                            if (isNaN(score) || score < 0 || score > 100) {
                                errors.push(`Nilai untuk mata pelajaran "${sub.name}" harus antara 0-100`);
                            }
                        }
                    });
                }
            });

            if (!hasValidSubject) {
                errors.push('Minimal isi 1 mata pelajaran dengan nama di salah satu semester');
            }

            if (emptySemesters.length > 0 && emptySemesters.length === academicData.length) {
                errors.push('Semua semester belum memiliki nama');
            }
        }

        return {
            valid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Show validation errors to user
     * @param {Array} errors - Array of error messages
     */
    function showValidationErrors(errors) {
        const container = document.getElementById('validation-error-container');
        const list = document.getElementById('validation-error-list');

        if (!container || !list) return;

        // Clear previous errors
        list.innerHTML = '';

        // Add new errors
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            list.appendChild(li);
        });

        // Show container
        container.style.display = 'block';

        // Scroll to error container
        container.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Auto hide after 10 seconds
        setTimeout(() => {
            container.style.display = 'none';
        }, 10000);
    }

    /**
     * Hide validation errors
     */
    function hideValidationErrors() {
        const container = document.getElementById('validation-error-container');
        if (container) {
            container.style.display = 'none';
        }
    }

    /**
     * Handle form submission with validation
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        console.log('[Validation] Starting form validation...');

        // Hide previous errors
        hideValidationErrors();

        // Run validation
        const validation = validateForm();

        if (!validation.valid) {
            console.error('[Validation] Form validation failed:', validation.errors);
            showValidationErrors(validation.errors);
            return false;
        }

        console.log('[Validation] Form validation passed');

        // Update hidden input with final data
        updateHiddenInput();

        // If validation passes, trigger SPA navigation manually
        const form = document.getElementById('academicForm');
        const action = form.getAttribute('action') || window.location.href;
        const formData = new FormData(form);

        // Use SPA navigation
        if (window.navigateTo) {
            window.navigateTo(action, {
                method: 'POST',
                body: formData
            });
        } else {
            // Fallback to regular form submission
            form.submit();
        }
    }

    /**
     * Initialize on DOM ready
     */
    function init() {
        const defaultEl = document.getElementById('academic_scores_default');
        const encodedData = defaultEl ? defaultEl.dataset.default : '';

        console.log('[Academic] init() called, encodedData:', encodedData);

        academicData = initAcademicData(encodedData);

        if (academicData.length === 0) {
            console.log('[Academic] No existing data, adding empty semester');
            addSemester();
        } else {
            console.log('[Academic] Rendering existing data');
            renderSemesters();
        }
    }

    /**
     * Attach form submit handler
     */
    function attachFormHandler() {
        const form = document.getElementById('academicForm');
        if (form) {
            // Remove existing handler if any
            form.removeEventListener('submit', handleFormSubmit);
            // Add new handler
            form.addEventListener('submit', handleFormSubmit);
            console.log('[Validation] Form handler attached');
        }
    }

    // Initialize on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            init();
            attachFormHandler();
        });
    } else {
        init();
        attachFormHandler();
    }

    // Listen for SPA navigation events to re-initialize
    // Ini memastikan default value di-render ulang saat soft navigation SPA
    window.addEventListener('spa:navigated', function() {
        console.log('[Academic] spa:navigated event fired');
        // Beri delay kecil untuk memastikan DOM sudah siap
        setTimeout(function() {
            init();
            attachFormHandler();
        }, 50);
    });
</script>