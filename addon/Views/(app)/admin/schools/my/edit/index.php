<?php

/**
 * Edit School Form View
 * 
 * @var array $school
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="header-breadcrumb">
                <a data-spa href="/admin/schools/my" class="breadcrumb-link">
                    <span class="breadcrumb-icon">←</span>
                    Dashboard
                </a>
            </div>
            <h1 class="page-title">Edit Sekolah</h1>
            <p class="page-description">Perbarui informasi sekolah Anda</p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="card edit-form-card">
        <div class="card-header">
            <div class="card-header-content">
                <h2 class="card-title">📝 Informasi Sekolah</h2>
                <p class="card-subtitle">Lengkapi formulir di bawah untuk memperbarui data sekolah</p>
            </div>
        </div>
        <div class="card-body">
            <form data-spa action="/admin/schools/my" method="POST" class="edit-form">
                <!-- Form Grid -->
                <div class="form-grid">
                    <!-- School Name -->
                    <div class="form-group form-group-full">
                        <label for="name" class="form-label">
                            <span class="label-icon">🏫</span>
                            Nama Sekolah
                            <span class="required-indicator">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            value="<?= e($school['name']) ?>"
                            placeholder="Contoh: SMA Negeri 1 Jakarta"
                            required />
                        <span class="form-hint">Masukkan nama lengkap sekolah Anda</span>
                    </div>

                    <!-- NPSN -->
                    <div class="form-group">
                        <label for="npsn" class="form-label">
                            <span class="label-icon">🔢</span>
                            NPSN
                            <span class="required-indicator">*</span>
                        </label>
                        <input
                            type="text"
                            id="npsn"
                            name="npsn"
                            class="form-input"
                            value="<?= e($school['npsn']) ?>"
                            placeholder="Contoh: 12345678"
                            pattern="[0-9]{8}"
                            title="NPSN harus 8 digit angka"
                            required />
                        <span class="form-hint">8 digit angka</span>
                    </div>

                    <!-- Accreditation -->
                    <div class="form-group">
                        <label for="accreditation" class="form-label">
                            <span class="label-icon">📊</span>
                            Akreditasi
                            <span class="required-indicator">*</span>
                        </label>
                        <select id="accreditation" name="accreditation" class="form-input" required>
                            <option value="">Pilih Akreditasi</option>
                            <option value="A" <?= $school['accreditation'] === 'A' ? 'selected' : '' ?>>A - Unggul</option>
                            <option value="B" <?= $school['accreditation'] === 'B' ? 'selected' : '' ?>>B - Baik</option>
                            <option value="C" <?= $school['accreditation'] === 'C' ? 'selected' : '' ?>>C - Cukup</option>
                            <option value="Belum" <?= $school['accreditation'] === 'Belum' ? 'selected' : '' ?>>Belum Akreditasi</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="form-group form-group-full">
                        <label for="address" class="form-label">
                            <span class="label-icon">📍</span>
                            Alamat Lengkap
                            <span class="required-indicator">*</span>
                        </label>
                        <textarea
                            id="address"
                            name="address"
                            class="form-input"
                            rows="4"
                            placeholder="Masukkan alamat lengkap sekolah termasuk jalan, kelurahan, kecamatan, dan kode pos"
                            required><?= e($school['address']) ?></textarea>
                    </div>

                    <!-- Principal Name -->
                    <div class="form-group">
                        <label for="principal_name" class="form-label">
                            <span class="label-icon">👔</span>
                            Nama Kepala Sekolah
                            <span class="required-indicator">*</span>
                        </label>
                        <input
                            type="text"
                            id="principal_name"
                            name="principal_name"
                            class="form-input"
                            value="<?= e($school['principal_name']) ?>"
                            placeholder="Contoh: Dr. Budi Santoso, M.Pd"
                            required />
                    </div>

                    <!-- Contact -->
                    <div class="form-group">
                        <label for="contact" class="form-label">
                            <span class="label-icon">📞</span>
                            Kontak (Telepon/Email)
                            <span class="required-indicator">*</span>
                        </label>
                        <input
                            type="text"
                            id="contact"
                            name="contact"
                            class="form-input"
                            value="<?= e($school['contact']) ?>"
                            placeholder="Contoh: (021) 1234567 atau info@smansa.sch.id"
                            required />
                        <span class="form-hint">Nomor telepon atau email sekolah</span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">💾</span>
                        Simpan Perubahan
                    </button>
                    <a data-spa href="/admin/schools/my" class="btn btn-secondary">
                        <span class="btn-icon">✕</span>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>