<?php

/**
 * @var array $school
 */
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Edit Sekolah</h1>
            <p class="page-description">Perbarui informasi sekolah Anda</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card">
        <div class="card-body">
            <form data-spa action="/admin/schools/my" method="POST" class="form">
                <!-- School Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Nama Sekolah</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="<?= e($school['name']) ?>"
                        required />
                </div>

                <!-- NPSN -->
                <div class="form-group">
                    <label for="npsn" class="form-label">NPSN</label>
                    <input
                        type="text"
                        id="npsn"
                        name="npsn"
                        class="form-input"
                        value="<?= e($school['npsn']) ?>"
                        required />
                </div>

                <!-- Accreditation -->
                <div class="form-group">
                    <label for="accreditation" class="form-label">Akreditasi</label>
                    <select id="accreditation" name="accreditation" class="form-input" required>
                        <option value="A" <?= $school['accreditation'] === 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= $school['accreditation'] === 'B' ? 'selected' : '' ?>>B</option>
                        <option value="C" <?= $school['accreditation'] === 'C' ? 'selected' : '' ?>>C</option>
                        <option value="Belum" <?= $school['accreditation'] === 'Belum' ? 'selected' : '' ?>>Belum Akreditasi</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-input"
                        rows="3"
                        required><?= e($school['address']) ?></textarea>
                </div>

                <!-- Principal Name -->
                <div class="form-group">
                    <label for="principal_name" class="form-label">Nama Kepala Sekolah</label>
                    <input
                        type="text"
                        id="principal_name"
                        name="principal_name"
                        class="form-input"
                        value="<?= e($school['principal_name']) ?>"
                        required />
                </div>

                <!-- Contact -->
                <div class="form-group">
                    <label for="contact" class="form-label">Kontak (Telepon/Email)</label>
                    <input
                        type="text"
                        id="contact"
                        name="contact"
                        class="form-input"
                        value="<?= e($school['contact']) ?>"
                        required />
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">💾</span>
                        Simpan Perubahan
                    </button>
                    <a data-spa href="/admin/schools/my" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>