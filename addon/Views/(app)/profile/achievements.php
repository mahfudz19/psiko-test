<?php

/**
 * Student Achievements & Extracurricular View
 * 
 * @var array $profile Profile data
 * @var array|null $studentProfile Student profile data
 */

// Decode JSON data
$achievements = !empty($studentProfile['achievements']) ? json_decode($studentProfile['achievements'], true) : [];
$extracurricular = !empty($studentProfile['extracurricular']) ? json_decode($studentProfile['extracurricular'], true) : [];
?>

<div class="achievements-container">
    <div class="achievements-header">
        <div class="breadcrumb">
            <a href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Prestasi & Ekstrakurikuler</span>
        </div>
        <h1>Prestasi & Ekstrakurikuler</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <form id="achievements-form" class="achievements-form" method="POST">
        <!-- Extracurricular Section -->
        <div class="form-section">
            <h2>Ekstrakurikuler</h2>
            <p class="section-description">
                Daftarkan kegiatan ekstrakurikuler yang kamu ikuti.
            </p>

            <div id="extracurricular-container">
                <?php if (!empty($extracurricular)): ?>
                    <?php foreach ($extracurricular as $index => $item): ?>
                        <div class="entry-card">
                            <div class="entry-header">
                                <span class="entry-title">Ekstrakurikuler <?= $index + 1 ?></span>
                                <button type="button" class="btn-remove" onclick="removeEntry(this, 'extracurricular')">×</button>
                            </div>
                            <div class="entry-grid">
                                <div class="form-group">
                                    <label>Nama Kegiatan</label>
                                    <input type="text" name="extracurricular[name][]" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required placeholder="Contoh: Pramuka, PMR, Basket">
                                </div>
                                <div class="form-group">
                                    <label>Posisi/Jabatan</label>
                                    <input type="text" name="extracurricular[position][]" value="<?= htmlspecialchars($item['position'] ?? '') ?>" placeholder="Contoh: Anggota, Ketua">
                                </div>
                                <div class="form-group">
                                    <label>Tahun Mulai</label>
                                    <input type="number" name="extracurricular[year_start][]" value="<?= htmlspecialchars($item['year_start'] ?? '') ?>" min="2000" max="2100" placeholder="2023">
                                </div>
                                <div class="form-group">
                                    <label>Tahun Selesai</label>
                                    <input type="number" name="extracurricular[year_end][]" value="<?= htmlspecialchars($item['year_end'] ?? '') ?>" min="2000" max="2100" placeholder="2024">
                                    <small class="form-text">Kosongkan jika masih aktif</small>
                                </div>
                                <div class="form-group full-width">
                                    <label>Deskripsi</label>
                                    <textarea name="extracurricular[description][]" rows="2" placeholder="Deskripsi singkat kegiatan"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="entry-card">
                        <div class="entry-header">
                            <span class="entry-title">Ekstrakurikuler 1</span>
                            <button type="button" class="btn-remove" onclick="removeEntry(this, 'extracurricular')">×</button>
                        </div>
                        <div class="entry-grid">
                            <div class="form-group">
                                <label>Nama Kegiatan</label>
                                <input type="text" name="extracurricular[name][]" required placeholder="Contoh: Pramuka, PMR, Basket">
                            </div>
                            <div class="form-group">
                                <label>Posisi/Jabatan</label>
                                <input type="text" name="extracurricular[position][]" placeholder="Contoh: Anggota, Ketua">
                            </div>
                            <div class="form-group">
                                <label>Tahun Mulai</label>
                                <input type="number" name="extracurricular[year_start][]" min="2000" max="2100" placeholder="2023">
                            </div>
                            <div class="form-group">
                                <label>Tahun Selesai</label>
                                <input type="number" name="extracurricular[year_end][]" min="2000" max="2100" placeholder="2024">
                                <small class="form-text">Kosongkan jika masih aktif</small>
                            </div>
                            <div class="form-group full-width">
                                <label>Deskripsi</label>
                                <textarea name="extracurricular[description][]" rows="2" placeholder="Deskripsi singkat kegiatan"></textarea>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-secondary btn-sm" onclick="addEntry('extracurricular')">
                + Tambah Ekstrakurikuler
            </button>
        </div>

        <!-- Achievements Section -->
        <div class="form-section">
            <h2>Prestasi</h2>
            <p class="section-description">
                Daftarkan prestasi yang pernah kamu raih.
            </p>

            <div id="achievements-container">
                <?php if (!empty($achievements)): ?>
                    <?php foreach ($achievements as $index => $item): ?>
                        <div class="entry-card">
                            <div class="entry-header">
                                <span class="entry-title">Prestasi <?= $index + 1 ?></span>
                                <button type="button" class="btn-remove" onclick="removeEntry(this, 'achievements')">×</button>
                            </div>
                            <div class="entry-grid">
                                <div class="form-group">
                                    <label>Nama Prestasi</label>
                                    <input type="text" name="achievements[name][]" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required placeholder="Contoh: Juara 1 Olimpiade Matematika">
                                </div>
                                <div class="form-group">
                                    <label>Juara Ke-</label>
                                    <select name="achievements[rank][]">
                                        <option value="1" <?= ($item['rank'] ?? '') == '1' ? 'selected' : '' ?>>Juara 1</option>
                                        <option value="2" <?= ($item['rank'] ?? '') == '2' ? 'selected' : '' ?>>Juara 2</option>
                                        <option value="3" <?= ($item['rank'] ?? '') == '3' ? 'selected' : '' ?>>Juara 3</option>
                                        <option value="harapan" <?= ($item['rank'] ?? '') === 'harapan' ? 'selected' : '' ?>>Juara Harapan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tingkat</label>
                                    <select name="achievements[level][]">
                                        <option value="">Pilih Tingkat</option>
                                        <option value="sekolah" <?= ($item['level'] ?? '') === 'sekolah' ? 'selected' : '' ?>>Sekolah</option>
                                        <option value="kecamatan" <?= ($item['level'] ?? '') === 'kecamatan' ? 'selected' : '' ?>>Kecamatan</option>
                                        <option value="kabupaten" <?= ($item['level'] ?? '') === 'kabupaten' ? 'selected' : '' ?>>Kabupaten/Kota</option>
                                        <option value="provinsi" <?= ($item['level'] ?? '') === 'provinsi' ? 'selected' : '' ?>>Provinsi</option>
                                        <option value="nasional" <?= ($item['level'] ?? '') === 'nasional' ? 'selected' : '' ?>>Nasional</option>
                                        <option value="internasional" <?= ($item['level'] ?? '') === 'internasional' ? 'selected' : '' ?>>Internasional</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tahun</label>
                                    <input type="number" name="achievements[year][]" value="<?= htmlspecialchars($item['year'] ?? '') ?>" min="2000" max="2100" placeholder="2024">
                                </div>
                                <div class="form-group full-width">
                                    <label>Penyelenggara</label>
                                    <input type="text" name="achievements[organizer][]" value="<?= htmlspecialchars($item['organizer'] ?? '') ?>" placeholder="Contoh: Dinas Pendidikan">
                                </div>
                                <div class="form-group full-width">
                                    <label>Deskripsi</label>
                                    <textarea name="achievements[description][]" rows="2" placeholder="Deskripsi singkat prestasi"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="entry-card">
                        <div class="entry-header">
                            <span class="entry-title">Prestasi 1</span>
                            <button type="button" class="btn-remove" onclick="removeEntry(this, 'achievements')">×</button>
                        </div>
                        <div class="entry-grid">
                            <div class="form-group">
                                <label>Nama Prestasi</label>
                                <input type="text" name="achievements[name][]" required placeholder="Contoh: Juara 1 Olimpiade Matematika">
                            </div>
                            <div class="form-group">
                                <label>Juara Ke-</label>
                                <select name="achievements[rank][]">
                                    <option value="1">Juara 1</option>
                                    <option value="2">Juara 2</option>
                                    <option value="3">Juara 3</option>
                                    <option value="harapan">Juara Harapan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tingkat</label>
                                <select name="achievements[level][]">
                                    <option value="">Pilih Tingkat</option>
                                    <option value="sekolah">Sekolah</option>
                                    <option value="kecamatan">Kecamatan</option>
                                    <option value="kabupaten">Kabupaten/Kota</option>
                                    <option value="provinsi">Provinsi</option>
                                    <option value="nasional">Nasional</option>
                                    <option value="internasional">Internasional</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tahun</label>
                                <input type="number" name="achievements[year][]" min="2000" max="2100" placeholder="2024">
                            </div>
                            <div class="form-group full-width">
                                <label>Penyelenggara</label>
                                <input type="text" name="achievements[organizer][]" placeholder="Contoh: Dinas Pendidikan">
                            </div>
                            <div class="form-group full-width">
                                <label>Deskripsi</label>
                                <textarea name="achievements[description][]" rows="2" placeholder="Deskripsi singkat prestasi"></textarea>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-secondary btn-sm" onclick="addEntry('achievements')">
                + Tambah Prestasi
            </button>
        </div>

        <div class="form-actions">
            <a href="/profile" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>
</div>

<style>
    .achievements-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 24px;
    }

    .achievements-header {
        margin-bottom: 24px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 8px;
    }

    .breadcrumb a {
        color: var(--md-sys-color-primary, #0066cc);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb .separator {
        color: var(--md-sys-color-on-surface-variant, #999);
    }

    .breadcrumb .current {
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .achievements-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .form-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-section h2 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .section-description {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 20px;
    }

    .entry-card {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .entry-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .entry-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .btn-remove {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-error, #dc3545);
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        background: var(--md-sys-color-error, #dc3545);
        color: white;
    }

    .entry-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px 14px;
        border: 1px solid var(--md-sys-color-outline-variant, #e0e0e0);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        background: var(--md-sys-color-surface, #ffffff);
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .form-text {
        font-size: 11px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-primary:hover {
        background: var(--md-sys-color-on-primary, #0052a3);
    }

    .btn-secondary {
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
    }

    .btn-secondary:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 14px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
        border: 1px solid var(--md-sys-color-secondary, #4caf50);
    }

    @media (max-width: 768px) {
        .entry-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    function addEntry(type) {
        const container = document.getElementById(type + '-container');
        const count = container.children.length + 1;
        const entry = document.createElement('div');
        entry.className = 'entry-card';

        if (type === 'extracurricular') {
            entry.innerHTML = `
            <div class="entry-header">
                <span class="entry-title">Ekstrakurikuler ${count}</span>
                <button type="button" class="btn-remove" onclick="removeEntry(this, 'extracurricular')">×</button>
            </div>
            <div class="entry-grid">
                <div class="form-group">
                    <label>Nama Kegiatan</label>
                    <input type="text" name="extracurricular[name][]" required placeholder="Contoh: Pramuka, PMR, Basket">
                </div>
                <div class="form-group">
                    <label>Posisi/Jabatan</label>
                    <input type="text" name="extracurricular[position][]" placeholder="Contoh: Anggota, Ketua">
                </div>
                <div class="form-group">
                    <label>Tahun Mulai</label>
                    <input type="number" name="extracurricular[year_start][]" min="2000" max="2100" placeholder="2023">
                </div>
                <div class="form-group">
                    <label>Tahun Selesai</label>
                    <input type="number" name="extracurricular[year_end][]" min="2000" max="2100" placeholder="2024">
                    <small class="form-text">Kosongkan jika masih aktif</small>
                </div>
                <div class="form-group full-width">
                    <label>Deskripsi</label>
                    <textarea name="extracurricular[description][]" rows="2" placeholder="Deskripsi singkat kegiatan"></textarea>
                </div>
            </div>
        `;
        } else {
            entry.innerHTML = `
            <div class="entry-header">
                <span class="entry-title">Prestasi ${count}</span>
                <button type="button" class="btn-remove" onclick="removeEntry(this, 'achievements')">×</button>
            </div>
            <div class="entry-grid">
                <div class="form-group">
                    <label>Nama Prestasi</label>
                    <input type="text" name="achievements[name][]" required placeholder="Contoh: Juara 1 Olimpiade Matematika">
                </div>
                <div class="form-group">
                    <label>Juara Ke-</label>
                    <select name="achievements[rank][]">
                        <option value="1">Juara 1</option>
                        <option value="2">Juara 2</option>
                        <option value="3">Juara 3</option>
                        <option value="harapan">Juara Harapan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tingkat</label>
                    <select name="achievements[level][]">
                        <option value="">Pilih Tingkat</option>
                        <option value="sekolah">Sekolah</option>
                        <option value="kecamatan">Kecamatan</option>
                        <option value="kabupaten">Kabupaten/Kota</option>
                        <option value="provinsi">Provinsi</option>
                        <option value="nasional">Nasional</option>
                        <option value="internasional">Internasional</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="achievements[year][]" min="2000" max="2100" placeholder="2024">
                </div>
                <div class="form-group full-width">
                    <label>Penyelenggara</label>
                    <input type="text" name="achievements[organizer][]" placeholder="Contoh: Dinas Pendidikan">
                </div>
                <div class="form-group full-width">
                    <label>Deskripsi</label>
                    <textarea name="achievements[description][]" rows="2" placeholder="Deskripsi singkat prestasi"></textarea>
                </div>
            </div>
        `;
        }

        container.appendChild(entry);
        updateEntryNumbers();
    }

    function removeEntry(button, type) {
        const container = document.getElementById(type + '-container');
        if (container.children.length > 1) {
            button.closest('.entry-card').remove();
            updateEntryNumbers();
        } else {
            alert('Minimal harus ada satu entri');
        }
    }

    function updateEntryNumbers() {
        document.querySelectorAll('#extracurricular-container .entry-card').forEach((card, index) => {
            card.querySelector('.entry-title').textContent = 'Ekstrakurikuler ' + (index + 1);
        });
        document.querySelectorAll('#achievements-container .entry-card').forEach((card, index) => {
            card.querySelector('.entry-title').textContent = 'Prestasi ' + (index + 1);
        });
    }

    // Form submission
    document.getElementById('achievements-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action || window.location.href, {
                method: 'POST',
                body: formData
            });

            if (response.redirected) {
                window.location.href = response.url;
            } else if (response.ok) {
                window.location.href = '/profile/achievements';
            } else {
                const error = await response.text();
                alert('Gagal menyimpan: ' + error);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
</script>