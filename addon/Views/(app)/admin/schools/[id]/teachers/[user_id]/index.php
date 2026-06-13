<?php

/**
 * @var array $school
 * @var array $teacher
 * Halaman detail profil guru BK
 */

// Helper untuk format data
$teacherName = $teacher['user_name'] ?? 'N/A';
$teacherEmail = $teacher['email'] ?? 'N/A';
$teacherPhone = $teacher['phone'] ?? '-';
$teacherAddress = $teacher['address'] ?? '-';
$gender = $teacher['gender'] ?? '';
$genderLabel = $gender === 'male' ? 'Laki-laki' : ($gender === 'female' ? 'Perempuan' : '-');
$teacherId = $teacher['teacher_id'] ?? '-';
$subjectSpecialty = $teacher['subject_specialty'] ?? '-';
$certification = $teacher['certification'] ?? '-';
$managedStudentsCount = 0;
if (!empty($teacher['managed_students'])) {
    $students = json_decode($teacher['managed_students'], true);
    $managedStudentsCount = is_array($students) ? count($students) : 0;
}
?>

<div class="teacher-detail-page">
    <!-- Header -->
    <div class="teacher-header">
        <div class="teacher-header-left">
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <h1><?= htmlspecialchars($teacherName) ?></h1>
            <p class="teacher-subtitle">Guru Bimbingan Konseling - <?= htmlspecialchars($school['name']) ?></p>
        </div>
        <div class="teacher-header-actions">
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/<?= $teacher['user_id'] ?>/edit" class="btn btn-edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Edit Profil
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="teacher-content-grid">
        <!-- Main Profile Card -->
        <aside class="teacher-sidebar">
            <div class="profile-card">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar-placeholder">
                        <?= strtoupper(substr($teacherName, 0, 1)) ?>
                    </div>
                </div>
                <h2 class="profile-name"><?= htmlspecialchars($teacherName) ?></h2>
                <div class="profile-role-badge badge-admin">Guru BK</div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?= $managedStudentsCount ?></span>
                        <span class="stat-label">Siswa Bimbingan</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= !empty($certification) ? '✓' : '-' ?></span>
                        <span class="stat-label">Sertifikasi</span>
                    </div>
                </div>

                <div class="profile-divider"></div>

                <!-- Quick Contact -->
                <div class="quick-contact">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </div>
                        <div class="contact-info">
                            <span class="contact-label">Email</span>
                            <span class="contact-value"><?= htmlspecialchars($teacherEmail) ?></span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>
                        <div class="contact-info">
                            <span class="contact-label">Telepon</span>
                            <span class="contact-value"><?= htmlspecialchars($teacherPhone) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="teacher-main">
            <!-- Personal Information -->
            <section class="content-card">
                <div class="card-header">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21a8 8 0 1 0-16 0" />
                            <circle cx="12" cy="8" r="5" />
                        </svg>
                        Informasi Pribadi
                    </h2>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value"><?= htmlspecialchars($teacherName) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Kelamin</span>
                        <span class="info-value"><?= htmlspecialchars($genderLabel) ?></span>
                    </div>
                    <div class="info-item full-width">
                        <span class="info-label">Alamat</span>
                        <span class="info-value"><?= htmlspecialchars($teacherAddress) ?></span>
                    </div>
                </div>
            </section>

            <!-- Professional Information -->
            <section class="content-card">
                <div class="card-header">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 20h20" />
                            <path d="M17 20v-9a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v9" />
                            <path d="M12 3v5" />
                            <path d="M8 7h8" />
                        </svg>
                        Informasi Profesional
                    </h2>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">NIP</span>
                        <span class="info-value"><?= htmlspecialchars($teacherId) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sekolah</span>
                        <span class="info-value"><?= htmlspecialchars($school['name']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mata Pelajaran / Spesialisasi</span>
                        <span class="info-value"><?= htmlspecialchars($subjectSpecialty) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sertifikasi</span>
                        <span class="info-value"><?= htmlspecialchars($certification) ?></span>
                    </div>
                </div>
            </section>

            <!-- Students Bimbingan -->
            <section class="content-card">
                <div class="card-header">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Siswa Bimbingan
                    </h2>
                </div>
                <?php if ($managedStudentsCount > 0): ?>
                    <div class="students-count-card">
                        <div class="count-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <div class="count-info">
                            <span class="count-value"><?= $managedStudentsCount ?></span>
                            <span class="count-label">Siswa sedang dibimbing</span>
                        </div>
                    </div>
                    <div class="card-footer-note">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                        Daftar siswa bimbingan dapat dilihat di halaman manajemen siswa
                    </div>
                <?php else: ?>
                    <div class="empty-state-mini">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <h4>Belum ada siswa bimbingan</h4>
                        <p>Guru ini belum ditugaskan untuk membimbing siswa manapun.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</div>