<?php

/**
 * Student Edit Form View
 * 
 * @var array $student
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">✏️ Edit Siswa</h1>
            <p class="page-description">Perbarui informasi siswa</p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="card edit-form-card">
        <div class="card-body">
            <form action="/admin/students/<?= $student['user_id'] ?>" method="POST" class="edit-form">
                <?= csrf_field() ?>
                <!-- Section 1: Student Info -->
                <div class="form-section">
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label for="student_id" class="form-label">
                                <span class="label-icon">🔢</span>
                                NIS/NISN
                                <span class="required-indicator">*</span>
                            </label>
                            <input
                                type="text"
                                id="student_id"
                                name="student_id"
                                class="form-input"
                                required
                                value="<?= e($student['student_id']) ?>"
                                placeholder="Contoh: 0012345678"
                                pattern="[0-9]{8,10}"
                                title="NIS/NISN harus 8-10 digit angka" />
                            <span class="form-hint">Nomor Induk Siswa (8-10 digit)</span>
                        </div>
                        <div class="form-group">
                            <label for="grade_level" class="form-label">
                                <span class="label-icon">📚</span>
                                Kelas
                                <span class="required-indicator">*</span>
                            </label>
                            <select id="grade_level" name="grade_level" class="form-input" required>
                                <option value="">Pilih Kelas</option>
                                <option value="10" <?= $student['grade_level'] === '10' ? 'selected' : '' ?>>Kelas 10</option>
                                <option value="11" <?= $student['grade_level'] === '11' ? 'selected' : '' ?>>Kelas 11</option>
                                <option value="12" <?= $student['grade_level'] === '12' ? 'selected' : '' ?>>Kelas 12</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="major" class="form-label">
                                <span class="label-icon">🎯</span>
                                Jurusan
                            </label>
                            <input
                                type="text"
                                id="major"
                                name="major"
                                class="form-input"
                                value="<?= e($student['major']) ?>"
                                placeholder="Contoh: IPA, IPS, RPL, TKJ" />
                            <span class="form-hint">Kosongkan jika tidak ada jurusan</span>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <span class="label-icon">📱</span>
                                No. Telepon Siswa
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-input"
                                value="<?= e($student['phone']) ?>"
                                placeholder="08xxxxxxxxxx"
                                pattern="08[0-9]{8,12}"
                                title="Format: 08xxxxxxxxxx" />
                        </div>
                        <div class="form-group full-width">
                            <label for="address" class="form-label">
                                <span class="label-icon">📍</span>
                                Alamat Lengkap
                            </label>
                            <textarea
                                id="address"
                                name="address"
                                class="form-input"
                                rows="3"
                                placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kode Pos"><?= e($student['address']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Parent Info -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">👨‍👩‍👦</span>
                        <div class="section-info">
                            <h3 class="section-title">Informasi Orang Tua/Wali</h3>
                            <p class="section-description">Data kontak orang tua atau wali</p>
                        </div>
                    </div>
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label for="parent_name" class="form-label">
                                <span class="label-icon">👤</span>
                                Nama Orang Tua/Wali
                                <span class="required-indicator">*</span>
                            </label>
                            <input
                                type="text"
                                id="parent_name"
                                name="parent_name"
                                class="form-input"
                                required
                                value="<?= e($student['parent_name']) ?>"
                                placeholder="Nama lengkap orang tua/wali"
                                autocomplete="name" />
                        </div>
                        <div class="form-group">
                            <label for="parent_phone" class="form-label">
                                <span class="label-icon">📞</span>
                                No. Telepon Orang Tua/Wali
                                <span class="required-indicator">*</span>
                            </label>
                            <input
                                type="tel"
                                id="parent_phone"
                                name="parent_phone"
                                class="form-input"
                                required
                                value="<?= e($student['parent_phone']) ?>"
                                placeholder="08xxxxxxxxxx"
                                pattern="08[0-9]{8,12}"
                                title="Format: 08xxxxxxxxxx" />
                        </div>
                        <div class="form-group">
                            <label for="parent_email" class="form-label">
                                <span class="label-icon">📧</span>
                                Email Orang Tua/Wali
                            </label>
                            <input
                                type="email"
                                id="parent_email"
                                name="parent_email"
                                class="form-input"
                                value="<?= e($student['parent_email']) ?>"
                                placeholder="email@contoh.com"
                                autocomplete="email" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">💾</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>