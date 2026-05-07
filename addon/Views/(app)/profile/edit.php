<?php

/**
 * Profile Edit View
 * 
 * @var array $profile Profile data
 * @var string $role User role
 */
?>

<div class="profile-edit-container">
    <div class="profile-edit-header">
        <div class="breadcrumb">
            <a href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Edit Profile</span>
        </div>
        <h1>Edit Profile</h1>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form id="profile-edit-form" action="<?= getBaseUrl("/profile/update") ?>" class="profile-edit-form" method="POST">
        <!-- Personal Information Section -->
        <div class="edit-section">
            <h2>Informasi Pribadi</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($profile['user_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" disabled class="disabled-input">
                    <small class="form-text">Email tidak dapat diubah</small>
                </div>

                <div class="form-group">
                    <label for="phone">No. Telepon</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
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
                    <textarea id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="social_media">Social Media (Opsional)</label>
                    <div class="social-media-inputs">
                        <div class="social-input">
                            <span class="social-prefix">Instagram</span>
                            <input type="url" name="social_media[instagram]" value="<?= htmlspecialchars($profile['social_media']['instagram'] ?? '') ?>" placeholder="https://instagram.com/username">
                        </div>
                        <div class="social-input">
                            <span class="social-prefix">LinkedIn</span>
                            <input type="url" name="social_media[linkedin]" value="<?= htmlspecialchars($profile['social_media']['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="social-input">
                            <span class="social-prefix">Twitter</span>
                            <input type="url" name="social_media[twitter]" value="<?= htmlspecialchars($profile['social_media']['twitter'] ?? '') ?>" placeholder="https://twitter.com/username">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-Specific Fields -->
        <?php if ($role === 'user'): ?>
            <!-- Student Specific Fields -->
            <div class="edit-section">
                <h2>Informasi Siswa</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="student_id">NIS/NISN</label>
                        <input type="text" id="student_id" name="student_id" value="<?= htmlspecialchars($profile['role_data']['student_id'] ?? '') ?>" placeholder="Masukkan NIS/NISN">
                    </div>

                    <div class="form-group">
                        <label for="grade_level">Jenjang</label>
                        <select id="grade_level" name="grade_level">
                            <option value="">Pilih Jenjang</option>
                            <option value="sd" <?= ($profile['role_data']['grade_level'] ?? '') === 'sd' ? 'selected' : '' ?>>SD</option>
                            <option value="smp" <?= ($profile['role_data']['grade_level'] ?? '') === 'smp' ? 'selected' : '' ?>>SMP</option>
                            <option value="sma" <?= ($profile['role_data']['grade_level'] ?? '') === 'sma' ? 'selected' : '' ?>>SMA</option>
                            <option value="smk" <?= ($profile['role_data']['grade_level'] ?? '') === 'smk' ? 'selected' : '' ?>>SMK</option>
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
            </div>

        <?php elseif ($role === 'admin'): ?>
            <!-- Teacher Specific Fields -->
            <div class="edit-section">
                <h2>Informasi Guru BK</h2>

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
            </div>

        <?php elseif ($role === 'super-admin'): ?>
            <!-- Staff Specific Fields -->
            <div class="edit-section">
                <h2>Informasi Staff</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="employee_id">NIP</label>
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
            </div>
        <?php endif; ?>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="/profile" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<style>
    .profile-edit-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 24px;
    }

    .profile-edit-header {
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

    .profile-edit-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .edit-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .edit-section h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
        padding-bottom: 16px;
        border-bottom: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
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
    .form-group select,
    .form-group textarea {
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
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: var(--md-sys-color-on-surface-variant, #999);
    }

    .form-group .disabled-input {
        background: var(--md-sys-color-surface-container-highest, #f5f5f5);
        color: var(--md-sys-color-on-surface-variant, #666);
        cursor: not-allowed;
    }

    .form-text {
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .social-media-inputs {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .social-input {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .social-prefix {
        min-width: 100px;
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .social-input input {
        flex: 1;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
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

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-on-error-container, #c62828);
        border: 1px solid var(--md-sys-color-error, #dc3545);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .social-input {
            flex-direction: column;
            align-items: flex-start;
        }

        .social-prefix {
            min-width: auto;
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
    // Form submission handler
    document.getElementById('profile-edit-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        // Disable submit button
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
                window.location.href = '/profile';
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