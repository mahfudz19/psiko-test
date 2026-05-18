<?php

/**
 * Form tambah sekolah
 */
?>

<div class="school-form-page">
    <div class="page-header">
        <div>
            <h1>Tambah Sekolah Baru</h1>
            <p class="page-description">Isi form di bawah untuk menambahkan sekolah baru</p>
        </div>
    </div>

    <div class="form-container">
        <form data-spa method="POST" action="/admin/schools" class="school-form">
            <?= csrf_field() ?>
            <div class="form-section">
                <h2>Informasi Dasar</h2>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Sekolah <span class="required">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        placeholder="Contoh: SMA Negeri 1 Jakarta"
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="npsn" class="form-label">NPSN <span class="required">*</span></label>
                        <input
                            type="text"
                            id="npsn"
                            name="npsn"
                            class="form-input"
                            placeholder="Contoh: 12345678"
                            pattern="[0-9]{8}"
                            title="NPSN harus 8 digit angka"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="accreditation" class="form-label">Akreditasi <span class="required">*</span></label>
                        <select id="accreditation" name="accreditation" class="form-input" required>
                            <option value="">Pilih Akreditasi</option>
                            <option value="A">A - Unggul</option>
                            <option value="B">B - Baik</option>
                            <option value="C">C - Cukup</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat <span class="required">*</span></label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input form-textarea"
                        rows="3"
                        placeholder="Masukkan alamat lengkap sekolah"
                        required></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Informasi Kontak</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact" class="form-label">Nomor Telepon <span class="required">*</span></label>
                        <input
                            type="tel"
                            id="contact"
                            name="contact"
                            class="form-input"
                            placeholder="Contoh: 021-1234567"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="principal_name" class="form-label">Nama Kepala Sekolah <span class="required">*</span></label>
                        <input
                            type="text"
                            id="principal_name"
                            name="principal_name"
                            class="form-input"
                            placeholder="Contoh: Dr. Budi Santoso, M.Pd"
                            required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a data-spa href="/admin/schools" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span> Simpan Sekolah
                </button>
            </div>
        </form>
    </div>
</div>