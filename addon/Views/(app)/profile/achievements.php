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

<div class="profile-container">
    <div class="breadcrumb">
        <a href="<?= getBaseUrl('/profile') ?>">Profile</a>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
        <span class="current">Prestasi & Ekstrakurikuler</span>
    </div>

    <h1 class="page-title">Prestasi & Ekstrakurikuler</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <form data-spa action="<?= getBaseUrl('/profile/achievements') ?>" class="achievements-form" method="POST">
        <!-- Extracurricular Section -->
        <div class="edit-section">
            <div class="section-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <h2>Ekstrakurikuler</h2>
            </div>
            <p class="section-description">
                Daftarkan kegiatan ekstrakurikuler yang kamu ikuti. <strong>Opsional</strong> - kosongkan jika tidak mengikuti kegiatan ekstrakurikuler.
            </p>

            <div id="extracurricular-container" class="form-grid">
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
                                    <input type="text" name="extracurricular[name][]" value="<?= htmlspecialchars($item['name'] ?? '') ?>" placeholder="Contoh: Pramuka, PMR, Basket">
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
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <p>Belum ada kegiatan ekstrakurikuler yang terdaftar</p>
                        <small>Klik tombol di bawah untuk menambahkan kegiatan ekstrakurikuler</small>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn-add" onclick="addEntry('extracurricular')">
                + Tambah Ekstrakurikuler
            </button>
        </div>

        <!-- Achievements Section -->
        <div class="edit-section">
            <div class="section-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                <h2>Prestasi</h2>
            </div>
            <p class="section-description">
                Daftarkan prestasi yang pernah kamu raih. <strong>Opsional</strong> - kosongkan jika belum ada prestasi.
            </p>

            <div id="achievements-container" class="form-grid">
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
                                    <input type="text" name="achievements[name][]" value="<?= htmlspecialchars($item['name'] ?? '') ?>" placeholder="Contoh: Juara 1 Olimpiade Matematika">
                                </div>
                                <div class="form-group">
                                    <label>Juara Ke-</label>
                                    <select name="achievements[rank][]">
                                        <option value="">Pilih Juara</option>
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
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                        <p>Belum ada prestasi yang terdaftar</p>
                        <small>Klik tombol di bawah untuk menambahkan prestasi</small>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn-add" onclick="addEntry('achievements')">
                + Tambah Prestasi
            </button>
        </div>

        <div class="form-actions">
            <a href="<?= getBaseUrl('/profile') ?>" class="btn-cancel">Kembali</a>
            <button type="submit" class="btn-save">Simpan Data</button>
        </div>
    </form>
</div>

<style>
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 24px;
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

    .breadcrumb svg {
        color: var(--md-sys-color-on-surface-variant, #999);
    }

    .breadcrumb .current {
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .page-title {
        margin: 0 0 24px 0;
        font-size: 28px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .edit-section {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .section-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .section-header svg {
        color: var(--md-sys-color-primary, #0066cc);
    }

    .section-description {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 20px;
    }

    .section-description strong {
        color: var(--md-sys-color-primary, #0066cc);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .entry-card {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 8px;
        padding: 16px;
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

    .form-group select:disabled {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        cursor: not-allowed;
    }

    .form-text {
        font-size: 11px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .empty-state svg {
        color: var(--md-sys-color-on-surface-variant, #999);
        margin-bottom: 16px;
    }

    .empty-state p {
        font-size: 16px;
        font-weight: 500;
        margin: 0 0 8px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .empty-state small {
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        margin-top: 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
    }

    .btn-cancel,
    .btn-save {
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

    .btn-cancel {
        background: var(--md-sys-color-secondary-container, #e6f0ff);
        color: var(--md-sys-color-on-secondary-container, #004c99);
    }

    .btn-cancel:hover {
        background: var(--md-sys-color-secondary, #0066cc);
        color: white;
    }

    .btn-save {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-save:hover {
        background: var(--md-sys-color-on-primary, #0052a3);
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

        .form-actions .btn-cancel,
        .form-actions .btn-save {
            width: 100%;
        }
    }
</style>

<script>
    function addEntry(type) {
        const container = document.getElementById(type + '-container');
        const count = container.children.length + 1;

        // Remove empty state if exists
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

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
                    <input type="text" name="extracurricular[name][]" placeholder="Contoh: Pramuka, PMR, Basket">
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
                    <input type="text" name="achievements[name][]" placeholder="Contoh: Juara 1 Olimpiade Matematika">
                </div>
                <div class="form-group">
                    <label>Juara Ke-</label>
                    <select name="achievements[rank][]">
                        <option value="">Pilih Juara</option>
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
        const card = button.closest('.entry-card');

        // Remove the card
        card.remove();

        // Update entry numbers
        updateEntryNumbers();

        // If no entries left, show empty state
        if (container.children.length === 0) {
            showEmptyState(container, type);
        }
    }

    function showEmptyState(container, type) {
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';

        if (type === 'extracurricular') {
            emptyState.innerHTML = `
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <p>Belum ada kegiatan ekstrakurikuler yang terdaftar</p>
                <small>Klik tombol di bawah untuk menambahkan kegiatan ekstrakurikuler</small>
            `;
        } else {
            emptyState.innerHTML = `
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                <p>Belum ada prestasi yang terdaftar</p>
                <small>Klik tombol di bawah untuk menambahkan prestasi</small>
            `;
        }

        container.appendChild(emptyState);
    }

    function updateEntryNumbers() {
        document.querySelectorAll('#extracurricular-container .entry-card').forEach((card, index) => {
            const titleEl = card.querySelector('.entry-title');
            if (titleEl) {
                titleEl.textContent = 'Ekstrakurikuler ' + (index + 1);
            }
        });
        document.querySelectorAll('#achievements-container .entry-card').forEach((card, index) => {
            const titleEl = card.querySelector('.entry-title');
            if (titleEl) {
                titleEl.textContent = 'Prestasi ' + (index + 1);
            }
        });
    }
</script>