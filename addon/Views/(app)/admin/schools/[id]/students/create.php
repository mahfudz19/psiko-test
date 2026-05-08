<?php

/**
 * @var array $school
 * Form tambah siswa untuk sekolah tertentu
 */
?>

<div class="student-form-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/schools/<?= $school['id'] ?>/students" class="back-link">
                ← Kembali ke Daftar Siswa
            </a>
            <h1>Tambah Siswa Baru</h1>
            <p class="page-description">Untuk sekolah <?= htmlspecialchars($school['name']) ?></p>
        </div>
    </div>

    <div class="form-container">
        <form data-spa method="POST" action="/admin/schools/<?= $school['id'] ?>/students" class="student-form">
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
                            placeholder="Contoh: Ahmad Rizki"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="Contoh: ahmad.rizki@student.sch.id"
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
                        <label for="phone" class="form-label">Nomor Telepon Siswa</label>
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
                <h2>Informasi Siswa</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="student_id" class="form-label">ID Siswa / NISN <span class="required">*</span></label>
                        <input
                            type="text"
                            id="student_id"
                            name="student_id"
                            class="form-input"
                            placeholder="Contoh: 0012345678"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="grade_level" class="form-label">Kelas <span class="required">*</span></label>
                        <select id="grade_level" name="grade_level" class="form-input" required>
                            <option value="">Pilih Kelas</option>
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="major" class="form-label">Jurusan</label>
                        <select id="major" name="major" class="form-input">
                            <option value="">Pilih Jurusan (Opsional)</option>
                            <option value="IPA">IPA (Ilmu Pengetahuan Alam)</option>
                            <option value="IPS">IPS (Ilmu Pengetahuan Sosial)</option>
                            <option value="Bahasa">Bahasa</option>
                            <option value="SMK - RPL">SMK - Rekayasa Perangkat Lunak</option>
                            <option value="SMK - TKJ">SMK - Teknik Komputer Jaringan</option>
                            <option value="SMK - Akuntansi">SMK - Akuntansi</option>
                            <option value="SMK - Multimedia">SMK - Multimedia</option>
                        </select>
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
            </div>

            <div class="form-section">
                <h2>Informasi Orang Tua</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_name" class="form-label">Nama Orang Tua / Wali <span class="required">*</span></label>
                        <input
                            type="text"
                            id="parent_name"
                            name="parent_name"
                            class="form-input"
                            placeholder="Contoh: Budi Santoso"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="parent_phone" class="form-label">Nomor Telepon Orang Tua <span class="required">*</span></label>
                        <input
                            type="tel"
                            id="parent_phone"
                            name="parent_phone"
                            class="form-input"
                            placeholder="Contoh: 08123456789"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="parent_email" class="form-label">Email Orang Tua</label>
                    <input
                        type="email"
                        id="parent_email"
                        name="parent_email"
                        class="form-input"
                        placeholder="Contoh: budi.santoso@email.com">
                </div>
            </div>

            <div class="form-actions">
                <a data-spa href="/admin/schools/<?= $school['id'] ?>/students" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span> Simpan Siswa
                </button>
            </div>
        </form>
    </div>
</div>