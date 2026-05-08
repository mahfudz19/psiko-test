<?php

/**
 * @var array $student
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Edit Siswa</h1>
            <p class="page-description">Perbarui informasi siswa</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card">
        <div class="card-body">
            <form data-spa action="/admin/students/<?= $student['id'] ?>" method="POST" class="form">
                <div class="form-section-title">Informasi Siswa</div>

                <!-- Student ID -->
                <div class="form-group">
                    <label for="student_id" class="form-label">NIS/NISN</label>
                    <input
                        type="text"
                        id="student_id"
                        name="student_id"
                        class="form-input"
                        value="<?= e($student['student_id']) ?>"
                        required />
                </div>

                <!-- Grade Level -->
                <div class="form-group">
                    <label for="grade_level" class="form-label">Kelas</label>
                    <select id="grade_level" name="grade_level" class="form-input" required>
                        <option value="10" <?= $student['grade_level'] === '10' ? 'selected' : '' ?>>Kelas 10</option>
                        <option value="11" <?= $student['grade_level'] === '11' ? 'selected' : '' ?>>Kelas 11</option>
                        <option value="12" <?= $student['grade_level'] === '12' ? 'selected' : '' ?>>Kelas 12</option>
                    </select>
                </div>

                <!-- Major -->
                <div class="form-group">
                    <label for="major" class="form-label">Jurusan</label>
                    <input
                        type="text"
                        id="major"
                        name="major"
                        class="form-input"
                        value="<?= e($student['major']) ?>"
                        placeholder="Contoh: IPA, IPS, Bahasa" />
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone" class="form-label">No. Telepon Siswa</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input"
                        value="<?= e($student['phone']) ?>"
                        placeholder="08xxxxxxxxxx" />
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input"
                        rows="3"><?= e($student['address']) ?></textarea>
                </div>

                <div class="form-section-title">Informasi Orang Tua/Wali</div>

                <!-- Parent Name -->
                <div class="form-group">
                    <label for="parent_name" class="form-label">Nama Orang Tua/Wali</label>
                    <input
                        type="text"
                        id="parent_name"
                        name="parent_name"
                        class="form-input"
                        value="<?= e($student['parent_name']) ?>"
                        required />
                </div>

                <!-- Parent Phone -->
                <div class="form-group">
                    <label for="parent_phone" class="form-label">No. Telepon Orang Tua/Wali</label>
                    <input
                        type="tel"
                        id="parent_phone"
                        name="parent_phone"
                        class="form-input"
                        value="<?= e($student['parent_phone']) ?>"
                        required />
                </div>

                <!-- Parent Email -->
                <div class="form-group">
                    <label for="parent_email" class="form-label">Email Orang Tua/Wali</label>
                    <input
                        type="email"
                        id="parent_email"
                        name="parent_email"
                        class="form-input"
                        value="<?= e($student['parent_email']) ?>"
                        placeholder="email@contoh.com" />
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">💾</span>
                        Simpan Perubahan
                    </button>
                    <a data-spa href="/admin/students/<?= $student['id'] ?>" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>