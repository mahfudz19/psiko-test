<?php

/**
 * @var array $school
 * Form edit sekolah
 */
?>

<div class="school-form-page">
    <div class="page-header">
        <div>
            <h1>Edit Sekolah</h1>
            <p class="page-description">Update informasi sekolah <?= htmlspecialchars($school['name']) ?></p>
        </div>
    </div>

    <div class="form-container">
        <form data-spa method="POST" action="/admin/schools/<?= $school['id'] ?>/update" class="school-form">
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
                        value="<?= htmlspecialchars($school['name']) ?>"
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
                            value="<?= htmlspecialchars($school['npsn']) ?>"
                            pattern="[0-9]{8}"
                            title="NPSN harus 8 digit angka"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="accreditation" class="form-label">Akreditasi <span class="required">*</span></label>
                        <select id="accreditation" name="accreditation" class="form-input" required>
                            <option value="">Pilih Akreditasi</option>
                            <option value="A" <?= $school['accreditation'] === 'A' ? 'selected' : '' ?>>A - Unggul</option>
                            <option value="B" <?= $school['accreditation'] === 'B' ? 'selected' : '' ?>>B - Baik</option>
                            <option value="C" <?= $school['accreditation'] === 'C' ? 'selected' : '' ?>>C - Cukup</option>
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
                        required><?= htmlspecialchars($school['address']) ?></textarea>
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
                            value="<?= htmlspecialchars($school['contact']) ?>"
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
                            value="<?= htmlspecialchars($school['principal_name']) ?>"
                            placeholder="Contoh: Dr. Budi Santoso, M.Pd"
                            required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a data-spa href="/admin/schools" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾</span> Update Sekolah
                </button>
            </div>
        </form>
    </div>
</div>