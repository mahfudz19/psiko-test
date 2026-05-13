<?php

/**
 * Profile Edit View - Radical Redesign
 * 
 * @var array $profile Profile data
 * @var string $role User role
 */
?>

<div class="profile-edit-container">
    <!-- Header & Breadcrumb -->
    <div class="profile-edit-header">
        <nav class="breadcrumb">
            <a data-spa href="/profile" data-spa>Profil</a>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i></span>
            <span class="current">Edit Profil</span>
        </nav>
        <h1>Edit Profil</h1>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form data-spa action="<?= getBaseUrl("/profile/update") ?>" class="profile-edit-form" method="POST">
        <!-- Personal Information Section -->
        <section class="edit-section">
            <h2><i class="fas fa-user-edit"></i> Informasi Pribadi</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($profile['user_name'] ?? '') ?>" required placeholder="Masukkan nama lengkap">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" disabled class="disabled-input">
                    <small class="form-text"><i class="fas fa-info-circle"></i> Email tidak dapat diubah untuk keamanan akun</small>
                </div>

                <div class="form-group">
                    <label for="phone">No. Telepon</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="Contoh: 081234567890">
                </div>

                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select id="gender" name="gender">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" <?= ($profile['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="female" <?= ($profile['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birth_place">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" value="<?= htmlspecialchars($profile['birth_place'] ?? '') ?>" placeholder="Contoh: Jakarta">
                </div>

                <div class="form-group">
                    <label for="birth_date">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($profile['birth_date'] ?? '') ?>">
                </div>

                <div class="form-group full-width">
                    <label for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap tempat tinggal saat ini"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Media Sosial (Opsional)</label>
                    <div class="social-media-inputs">
                        <div class="social-input">
                            <div class="social-icon-box"><i class="fab fa-instagram"></i></div>
                            <input type="url" name="social_media[instagram]" value="<?= htmlspecialchars($profile['social_media']['instagram'] ?? '') ?>" placeholder="https://instagram.com/username">
                        </div>
                        <div class="social-input">
                            <div class="social-icon-box"><i class="fab fa-linkedin"></i></div>
                            <input type="url" name="social_media[linkedin]" value="<?= htmlspecialchars($profile['social_media']['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="social-input">
                            <div class="social-icon-box"><i class="fab fa-twitter"></i></div>
                            <input type="url" name="social_media[twitter]" value="<?= htmlspecialchars($profile['social_media']['twitter'] ?? '') ?>" placeholder="https://twitter.com/username">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Role-Specific Fields -->
        <?php if ($role === 'user'): ?>
            <section class="edit-section">
                <h2><i class="fas fa-graduation-cap"></i> Informasi Siswa</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="student_id">NIS/NISN</label>
                        <input type="text" id="student_id" name="student_id" value="<?= htmlspecialchars($profile['role_data']['student_id'] ?? '') ?>" placeholder="Masukkan NIS/NISN">
                    </div>

                    <div class="form-group">
                        <label for="grade_level">Kelas</label>
                        <select id="grade_level" name="grade_level">
                            <option value="">Pilih Kelas</option>
                            <option value="10" <?= ($profile['role_data']['grade_level'] ?? '') === '10' ? 'selected' : '' ?>>10</option>
                            <option value="11" <?= ($profile['role_data']['grade_level'] ?? '') === '11' ? 'selected' : '' ?>>11</option>
                            <option value="12" <?= ($profile['role_data']['grade_level'] ?? '') === '12' ? 'selected' : '' ?>>12</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="major">Jurusan (Khusus SMA/SMK)</label>
                        <input type="text" id="major" name="major" value="<?= htmlspecialchars($profile['role_data']['major'] ?? '') ?>" placeholder="Contoh: IPA, IPS, RPL">
                    </div>

                    <div class="form-group">
                        <label for="parent_name">Nama Orang Tua/Wali</label>
                        <input type="text" id="parent_name" name="parent_name" value="<?= htmlspecialchars($profile['role_data']['parent_name'] ?? '') ?>" placeholder="Nama lengkap orang tua/wali">
                    </div>

                    <div class="form-group">
                        <label for="parent_phone">No. Telepon Orang Tua</label>
                        <input type="tel" id="parent_phone" name="parent_phone" value="<?= htmlspecialchars($profile['role_data']['parent_phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label for="parent_email">Email Orang Tua</label>
                        <input type="email" id="parent_email" name="parent_email" value="<?= htmlspecialchars($profile['role_data']['parent_email'] ?? '') ?>" placeholder="email@example.com">
                    </div>
                </div>
            </section>

        <?php elseif ($role === 'admin'): ?>
            <section class="edit-section">
                <h2><i class="fas fa-chalkboard-teacher"></i> Informasi Guru BK</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="teacher_id">NIP</label>
                        <input type="text" id="teacher_id" name="teacher_id" value="<?= htmlspecialchars($profile['role_data']['teacher_id'] ?? '') ?>" placeholder="Masukkan NIP">
                    </div>

                    <div class="form-group">
                        <label for="subject_specialty">Mata Pelajaran / Spesialisasi</label>
                        <input type="text" id="subject_specialty" name="subject_specialty" value="<?= htmlspecialchars($profile['role_data']['subject_specialty'] ?? '') ?>" placeholder="Contoh: Bimbingan Konseling">
                    </div>

                    <div class="form-group">
                        <label for="certification">Sertifikasi</label>
                        <input type="text" id="certification" name="certification" value="<?= htmlspecialchars($profile['role_data']['certification'] ?? '') ?>" placeholder="Contoh: Guru BK Bersertifikat">
                    </div>
                </div>
            </section>

        <?php elseif ($role === 'super-admin'): ?>
            <section class="edit-section">
                <h2><i class="fas fa-user-shield"></i> Informasi Staff</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="employee_id">NIP / ID Pegawai</label>
                        <input type="text" id="employee_id" name="employee_id" value="<?= htmlspecialchars($profile['role_data']['employee_id'] ?? '') ?>" placeholder="Masukkan NIP">
                    </div>

                    <div class="form-group">
                        <label for="department">Departemen</label>
                        <input type="text" id="department" name="department" value="<?= htmlspecialchars($profile['role_data']['department'] ?? '') ?>" placeholder="Contoh: IT, HRD, Akademik">
                    </div>

                    <div class="form-group">
                        <label for="position">Jabatan</label>
                        <input type="text" id="position" name="position" value="<?= htmlspecialchars($profile['role_data']['position'] ?? '') ?>" placeholder="Contoh: Administrator">
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Form Actions -->
        <div class="form-actions">
            <a data-spa href="/profile" class="btn-cancel" data-spa>Batal</a>
            <button type="submit" class="btn-save" id="submit-btn">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>