<?php

/**
 * @var array $school
 * Form tambah guru untuk sekolah tertentu
 */
?>

<div class="teacher-form-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers" class="back-link">
                ← Kembali ke Daftar Guru
            </a>
            <h1>Tambah Guru BK Baru</h1>
            <p class="page-description">Untuk sekolah <?= htmlspecialchars($school['name']) ?></p>
        </div>
    </div>

    <div class="form-container">
        <form data-spa method="POST" action="/admin/schools/<?= $school['id'] ?>/teachers" class="teacher-form">
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
                            placeholder="Contoh: budi.santoso@school.sch.id"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="required">*</span></label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            minlength="8"
                            placeholder="Minimal 8 karakter"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-input"
                            placeholder="Contoh: 08123456789">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Informasi Guru</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="teacher_id" class="form-label">ID Guru / NIP <span class="required">*</span></label>
                        <input
                            type="text"
                            id="teacher_id"
                            name="teacher_id"
                            class="form-input"
                            placeholder="Contoh: 198501012010011001"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="subject_specialty" class="form-label">Mata Pelajaran <span class="required">*</span></label>
                        <input
                            type="text"
                            id="subject_specialty"
                            name="subject_specialty"
                            class="form-input"
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
                        placeholder="Contoh: Sertifikasi Pendidik Tahun 2020">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input form-textarea"
                        rows="3"
                        placeholder="Masukkan alamat lengkap"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a data-spa href="/admin/schools/<?= $school['id'] ?>/teachers" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span> Simpan Guru
                </button>
            </div>
        </form>
    </div>
</div>