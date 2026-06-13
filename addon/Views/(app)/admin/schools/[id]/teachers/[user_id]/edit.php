<?php

/**
 * @var array $school
 * @var array $teacher
 * Form edit guru untuk sekolah tertentu
 */

// Helper untuk format data
$teacherName = $teacher['user_name'] ?? '';
$teacherEmail = $teacher['email'] ?? '';
$teacherPhone = $teacher['phone'] ?? '';
$teacherAddress = $teacher['address'] ?? '';
$gender = $teacher['gender'] ?? '';
$birthPlace = $teacher['birth_place'] ?? '';
$birthDate = $teacher['birth_date'] ?? '';
$teacherId = $teacher['teacher_id'] ?? '';
$subjectSpecialty = $teacher['subject_specialty'] ?? '';
$certification = $teacher['certification'] ?? '';
?>

<div class="teacher-form-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/<?= $teacher['user_id'] ?>" class="back-link">
                ← Kembali ke Detail Guru
            </a>
            <h1>Edit Profil Guru BK</h1>
            <p class="page-description">Untuk sekolah <?= htmlspecialchars($school['name']) ?></p>
        </div>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form data-spa method="POST" action="/admin/schools/<?= $school['id'] ?>/teachers/<?= $teacher['user_id'] ?>/update" class="teacher-form">
            <div class="form-section">
                <h2>Informasi Akun</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            value="<?= htmlspecialchars($teacherName) ?>"
                            placeholder="Contoh: Budi Santoso, S.Pd"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            value="<?= htmlspecialchars($teacherEmail) ?>"
                            placeholder="Contoh: budi.santoso@school.sch.id"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru <span class="optional">(opsional)</span></label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            minlength="8"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        <small class="form-hint">Kosongkan jika tidak ingin mengubah password</small>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-input"
                            value="<?= htmlspecialchars($teacherPhone) ?>"
                            placeholder="Contoh: 08123456789">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Informasi Pribadi</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="form-input">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="birth_place" class="form-label">Tempat Lahir</label>
                        <input
                            type="text"
                            id="birth_place"
                            name="birth_place"
                            class="form-input"
                            value="<?= htmlspecialchars($birthPlace) ?>"
                            placeholder="Contoh: Jakarta">
                    </div>
                </div>

                <div class="form-group">
                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                    <input
                        type="date"
                        id="birth_date"
                        name="birth_date"
                        class="form-input"
                        value="<?= htmlspecialchars($birthDate) ?>">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input form-textarea"
                        rows="3"
                        placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($teacherAddress) ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Informasi Profesional</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="teacher_id" class="form-label">ID Guru / NIP <span class="required">*</span></label>
                        <input
                            type="text"
                            id="teacher_id"
                            name="teacher_id"
                            class="form-input"
                            value="<?= htmlspecialchars($teacherId) ?>"
                            placeholder="Contoh: 198501012010011001"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="subject_specialty" class="form-label">Mata Pelajaran / Spesialisasi <span class="required">*</span></label>
                        <input
                            type="text"
                            id="subject_specialty"
                            name="subject_specialty"
                            class="form-input"
                            value="<?= htmlspecialchars($subjectSpecialty) ?>"
                            placeholder="Contoh: Matematika"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="certification" class="form-label">Sertifikasi</label>
                    <input
                        type="text"
                        id="certification"
                        name="certification"
                        class="form-input"
                        value="<?= htmlspecialchars($certification) ?>"
                        placeholder="Contoh: Sertifikasi Pendidik Tahun 2020">
                </div>
            </div>

            <div class="form-actions">
                <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers/<?= $teacher['user_id'] ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Update Profil
                </button>
            </div>
        </form>
    </div>
</div>