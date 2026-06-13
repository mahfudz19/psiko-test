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
            <span class="separator">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 0.75rem; height: 0.75rem;">
                    <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                </svg>
            </span>
            <span class="current">Edit Profil</span>
        </nav>
        <h1>Edit Profil</h1>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
            </svg>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form data-spa action="<?= getBaseUrl("/profile/update") ?>" class="profile-edit-form" method="POST">
        <!-- Personal Information Section -->
        <section class="edit-section">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                    <path d="M12 4a4 4 0 0 1 4 4c0 1.05-.4 2-1.05 2.75L16 11.5c.97-.54 2-1.5 2-3.5a6 6 0 0 0-12 0c0 2 1.03 2.96 2 3.5l1.05-.75A3.96 3.96 0 0 1 8 8a4 4 0 0 1 4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
                Informasi Pribadi
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($profile['user_name'] ?? '') ?>" required placeholder="Masukkan nama lengkap">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" disabled class="disabled-input">
                    <small class="form-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 0.75rem; height: 0.75rem; vertical-align: middle;">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-4h2v2h-2zm0-2h2V7h-2v7z" />
                        </svg>
                        Email tidak dapat diubah untuk keamanan akun
                    </small>
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
                            <div class="social-icon-box">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                    <path d="M16.365 1.7c1.482 0 2.73.49 3.734 1.467C21.102 4.145 21.64 5.47 21.7 7.15v5.364c0 3.325-.73 5.897-2.187 7.715-1.458 1.818-3.452 2.728-5.983 2.728-1.69 0-3.14-.535-4.35-1.605l-.24-.225-.24.225c-1.21 1.07-2.66 1.605-4.35 1.605-2.53 0-4.526-.91-5.983-2.728C-1.09 18.41-1.82 15.84-1.82 12.514V7.15c.06-1.68.598-3.005 1.602-3.983C.785 2.19 2.033 1.7 3.515 1.7c1.69 0 3.14.535 4.35 1.605l.24.225.24-.225C9.555 2.235 11.005 1.7 12.695 1.7h3.67zm-3.67 1.45c-1.21 0-2.26.36-3.15 1.08l-.24.225-.24-.225c-.89-.72-1.94-1.08-3.15-1.08-1.07 0-1.93.33-2.58.99-.65.66-1.02 1.55-1.12 2.67v5.314c0 2.91.61 5.09 1.83 6.55 1.22 1.46 2.84 2.19 4.86 2.19 1.21 0 2.26-.36 3.15-1.08l.24-.225.24.225c.89.72 1.94 1.08 3.15 1.08 2.02 0 3.64-.73 4.86-2.19 1.22-1.46 1.83-3.64 1.83-6.55V7.15c-.1-1.12-.47-2.01-1.12-2.67-.65-.66-1.51-.99-2.58-.99h-3.67v.06zM7.5 6.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm0 1c-.28 0-.5.22-.5.5s.22.5.5.5.5-.22.5-.5-.22-.5-.5-.5zm9 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm0 1c-.28 0-.5.22-.5.5s.22.5.5.5.5-.22.5-.5-.22-.5-.5-.5z" />
                                </svg>
                            </div>
                            <input type="url" name="social_media[instagram]" value="<?= htmlspecialchars($profile['social_media']['instagram'] ?? '') ?>" placeholder="https://instagram.com/username">
                        </div>
                        <div class="social-input">
                            <div class="social-icon-box">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                </svg>
                            </div>
                            <input type="url" name="social_media[linkedin]" value="<?= htmlspecialchars($profile['social_media']['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="social-input">
                            <div class="social-icon-box">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                </svg>
                            </div>
                            <input type="url" name="social_media[twitter]" value="<?= htmlspecialchars($profile['social_media']['twitter'] ?? '') ?>" placeholder="https://twitter.com/username">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Role-Specific Fields -->
        <?php if ($role === 'user'): ?>
            <section class="edit-section">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                        <path d="M5 13.18v.76l.94.53 4 2.28 1.06.6 1.06-.6 4-2.28.94-.53v-.76L12 9 5 13.18zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z" />
                        <path d="M5 13.18v.76l.94.53 4 2.28 1.06.6 1.06-.6 4-2.28.94-.53v-.76L12 9 5 13.18z" opacity=".3" />
                    </svg>
                    Informasi Siswa
                </h2>

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
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                        <path d="M12 3L1 9l4 2.18v6L12 21l11-6V9.18l-11-6.18zM12 5.5l7.5 4.18-.88.48L12 6.5 5.38 10.16 4.5 9.68 12 5.5zm0 13.5l-7-3.82v-5.5l7 3.82 7-3.82v5.5l-7 3.82z" />
                    </svg>
                    Informasi Guru BK
                </h2>

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
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem;">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 6c1.4 0 2.8 1.1 2.8 2.5V11c.6 0 1.2.6 1.2 1.2v3.5c0 .7-.6 1.3-1.2 1.3H9.2c-.6 0-1.2-.6-1.2-1.3v-3.5c0-.6.6-1.2 1.2-1.2V9.5C9.2 8.1 10.6 7 12 7zm0 1c-.8 0-1.5.7-1.5 1.5V11h3V9.5c0-.8-.7-1.5-1.5-1.5z" />
                    </svg>
                    Informasi Staff
                </h2>

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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem; vertical-align: middle; margin-right: 0.5rem;">
                    <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>