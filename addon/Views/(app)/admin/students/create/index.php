<?php

/**
 * @var array $school
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Tambah Siswa</h1>
            <p class="page-description">Tambahkan siswa baru ke sekolah Anda</p>
        </div>
    </div>

    <!-- Create Form -->
    <div class="card">
        <div class="card-body">
            <form data-spa action="/admin/students" method="POST" class="form">
                <div class="form-section-title">Informasi Akun</div>

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        required
                        placeholder="Masukkan nama lengkap siswa" />
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        required
                        placeholder="email@contoh.com" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        required
                        minlength="8"
                        placeholder="Minimal 8 karakter" />
                </div>

                <div class="form-section-title">Informasi Siswa</div>

                <!-- Student ID -->
                <div class="form-group">
                    <label for="student_id" class="form-label">NIS/NISN</label>
                    <input
                        type="text"
                        id="student_id"
                        name="student_id"
                        class="form-input"
                        required
                        placeholder="Nomor Induk Siswa" />
                </div>

                <!-- Grade Level -->
                <div class="form-group">
                    <label for="grade_level" class="form-label">Kelas</label>
                    <select id="grade_level" name="grade_level" class="form-input" required>
                        <option value="">Pilih Kelas</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
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
                        placeholder="08xxxxxxxxxx" />
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input"
                        rows="3"
                        placeholder="Alamat lengkap siswa"></textarea>
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
                        required
                        placeholder="Nama lengkap orang tua/wali" />
                </div>

                <!-- Parent Phone -->
                <div class="form-group">
                    <label for="parent_phone" class="form-label">No. Telepon Orang Tua/Wali</label>
                    <input
                        type="tel"
                        id="parent_phone"
                        name="parent_phone"
                        class="form-input"
                        required
                        placeholder="08xxxxxxxxxx" />
                </div>

                <!-- Parent Email -->
                <div class="form-group">
                    <label for="parent_email" class="form-label">Email Orang Tua/Wali</label>
                    <input
                        type="email"
                        id="parent_email"
                        name="parent_email"
                        class="form-input"
                        placeholder="email@contoh.com" />
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">💾</span>
                        Simpan Siswa
                    </button>
                    <a data-spa href="/admin/students" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>