<?php

/**
 * Student Create Form View
 * 
 * @var array $school
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="header-breadcrumb">
                <a data-spa href="/admin/students" class="breadcrumb-link">
                    <span class="breadcrumb-icon">←</span>
                    Daftar Siswa
                </a>
            </div>
            <h1 class="page-title">➕ Tambah Siswa</h1>
            <p class="page-description">Tambahkan siswa baru ke sekolah Anda</p>
        </div>
    </div>

    <!-- Create Form Card -->
    <div class="card create-form-card">
        <div class="card-header">
            <div class="card-header-content">
                <h2 class="card-title">📝 Formulir Pendaftaran</h2>
                <p class="card-subtitle">Lengkapi data di bawah untuk menambahkan siswa baru</p>
            </div>
        </div>
        <div class="card-body">
            <form data-spa action="/admin/students" method="POST" class="create-form">
                <!-- Section 1: Account Info -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">👤</span>
                        <div class="section-info">
                            <h3 class="section-title">Informasi Akun</h3>
                            <p class="section-description">Data untuk login ke sistem</p>
                        </div>
                    </div>
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <span class="label-icon">📛</span>
                                Nama Lengkap
                                <span class="required-indicator">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-input"
                                required
                                placeholder="Contoh: Ahmad Rizky Pratama"
                                autocomplete="name" />
                            <span class="form-hint">Nama lengkap sesuai KTP/Akte</span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <span class="label-icon">📧</span>
                                Email
                                <span class="required-indicator">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                required
                                placeholder="siswa@contoh.com"
                                autocomplete="email" />
                            <span class="form-hint">Email aktif untuk notifikasi</span>
                        </div>
                        <div class="form-group full-width">
                            <label for="password" class="form-label">
                                <span class="label-icon">🔒</span>
                                Password
                                <span class="required-indicator">*</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                required
                                minlength="8"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password" />
                            <span class="form-hint">Gunakan kombinasi huruf, angka, dan simbol</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Student Info -->
                <div class="form-section">
                    <div class="section-header">
                        <span class="section-icon">🎓</span>
                        <div class="section-info">
                            <h3 class="section-title">Informasi Siswa</h3>
                            <p class="section-description">Data akademik siswa</p>
                        </div>
                    </div>
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
                                <option value="10">Kelas 10</option>
                                <option value="11">Kelas 11</option>
                                <option value="12">Kelas 12</option>
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
                                placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kode Pos"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Parent Info -->
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
                                placeholder="email@contoh.com"
                                autocomplete="email" />
                        </div>
                        <div class="form-group">
                            <label for="parent_occupation" class="form-label">
                                <span class="label-icon">💼</span>
                                Pekerjaan Orang Tua/Wali
                            </label>
                            <input
                                type="text"
                                id="parent_occupation"
                                name="parent_occupation"
                                class="form-input"
                                placeholder="Contoh: Wiraswasta, PNS, Pegawai Swasta" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">💾</span>
                        Simpan Siswa
                    </button>
                    <a data-spa href="/admin/students" class="btn btn-secondary">
                        <span class="btn-icon">✕</span>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>