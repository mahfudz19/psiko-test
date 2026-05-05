<?php

/**
 * PMB Simulation View - Multi-step Wizard
 * 
 * @var array $simulation Simulation data with steps and progress
 */
$sim = $simulation ?? null;
$currentStep = $sim['current_step'] ?? 1;
$totalSteps = $sim['total_steps'] ?? 5;
$progressPercentage = $sim['progress_percentage'] ?? 0;
?>

<div class="pmb-simulation-container">
    <!-- Header -->
    <div class="simulation-header">
        <div class="header-content">
            <h1>📝 Simulasi Pendaftaran Mahasiswa Baru</h1>
            <p class="header-subtitle">Universitas Universal</p>
            <p class="header-note">Lengkapi data kamu step by step. Kamu bisa save dan lanjut nanti!</p>
        </div>
    </div>

    <?php if ($sim): ?>
        <!-- Progress Bar -->
        <div class="progress-section">
            <div class="progress-info">
                <span class="progress-label">Progress: <strong><?= $progressPercentage ?>%</strong></span>
                <span class="progress-steps">Step <?= $currentStep ?>/<?= $totalSteps ?></span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?= $progressPercentage ?>%"></div>
            </div>
            <div class="steps-indicator">
                <?php foreach ($sim['steps'] as $step): ?>
                    <div class="step-indicator <?= $step['is_completed'] ? 'completed' : ($step['id'] === $currentStep ? 'active' : 'pending') ?>">
                        <span class="step-number"><?= $step['id'] ?></span>
                        <span class="step-name"><?= htmlspecialchars($step['name']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step Content -->
        <div class="step-content-wrapper">
            <?php
            $currentStepData = null;
            foreach ($sim['steps'] as $step) {
                if ($step['id'] === $currentStep) {
                    $currentStepData = $step;
                    break;
                }
            }
            ?>

            <?php if ($currentStep === 1): ?>
                <!-- Step 1: Data Pribadi -->
                <div class="step-form-section">
                    <div class="step-header">
                        <h2>Data Pribadi</h2>
                        <p>Lengkapi informasi pribadi kamu dengan benar</p>
                    </div>

                    <form class="simulation-form" id="step-form-1">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="full_name">Nama Lengkap *</label>
                                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($currentStepData['data']['full_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentStepData['data']['email'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">No. Telepon *</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($currentStepData['data']['phone'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="birth_place">Tempat Lahir *</label>
                                <input type="text" id="birth_place" name="birth_place" value="<?= htmlspecialchars($currentStepData['data']['birth_place'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="birth_date">Tanggal Lahir *</label>
                                <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($currentStepData['data']['birth_date'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Jenis Kelamin *</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Pilih</option>
                                    <option value="male" <?= ($currentStepData['data']['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="female" <?= ($currentStepData['data']['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label for="address">Alamat Lengkap *</label>
                                <textarea id="address" name="address" rows="3" required><?= htmlspecialchars($currentStepData['data']['address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
                            <button type="submit" class="btn btn-primary">Lanjut <span class="icon">→</span></button>
                        </div>
                    </form>
                </div>

            <?php elseif ($currentStep === 2): ?>
                <!-- Step 2: Nilai Akademik -->
                <div class="step-form-section">
                    <div class="step-header">
                        <h2>Nilai Akademik</h2>
                        <p>Masukkan informasi akademik dan nilai kamu</p>
                    </div>

                    <form class="simulation-form" id="step-form-2">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="school_name">Nama Sekolah *</label>
                                <input type="text" id="school_name" name="school_name" value="<?= htmlspecialchars($currentStepData['data']['school_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="major">Jurusan *</label>
                                <select id="major" name="major" required>
                                    <option value="">Pilih</option>
                                    <option value="IPA" <?= ($currentStepData['data']['major'] ?? '') === 'IPA' ? 'selected' : '' ?>>IPA</option>
                                    <option value="IPS" <?= ($currentStepData['data']['major'] ?? '') === 'IPS' ? 'selected' : '' ?>>IPS</option>
                                    <option value="Bahasa" <?= ($currentStepData['data']['major'] ?? '') === 'Bahasa' ? 'selected' : '' ?>>Bahasa</option>
                                    <option value="SMK" <?= ($currentStepData['data']['major'] ?? '') === 'SMK' ? 'selected' : '' ?>>SMK</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="graduation_year">Tahun Lulus *</label>
                                <input type="number" id="graduation_year" name="graduation_year" value="<?= htmlspecialchars($currentStepData['data']['graduation_year'] ?? '') ?>" min="2020" max="2025" required>
                            </div>
                            <div class="form-group">
                                <label for="average_grade">Rata-rata Nilai *</label>
                                <input type="number" id="average_grade" name="average_grade" value="<?= htmlspecialchars($currentStepData['data']['average_grade'] ?? '') ?>" step="0.01" min="0" max="100" required>
                            </div>
                        </div>

                        <div class="subjects-section">
                            <h3>Nilai Mata Pelajaran</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="math">Matematika</label>
                                    <input type="number" id="math" name="subjects[math]" value="<?= htmlspecialchars($currentStepData['data']['subjects']['math'] ?? '') ?>" min="0" max="100">
                                </div>
                                <div class="form-group">
                                    <label for="indonesian">Bahasa Indonesia</label>
                                    <input type="number" id="indonesian" name="subjects[indonesian]" value="<?= htmlspecialchars($currentStepData['data']['subjects']['indonesian'] ?? '') ?>" min="0" max="100">
                                </div>
                                <div class="form-group">
                                    <label for="english">Bahasa Inggris</label>
                                    <input type="number" id="english" name="subjects[english]" value="<?= htmlspecialchars($currentStepData['data']['subjects']['english'] ?? '') ?>" min="0" max="100">
                                </div>
                                <div class="form-group">
                                    <label for="physics">Fisika</label>
                                    <input type="number" id="physics" name="subjects[physics]" value="<?= htmlspecialchars($currentStepData['data']['subjects']['physics'] ?? '') ?>" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="previousStep(1)">← Kembali</button>
                            <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
                            <button type="submit" class="btn btn-primary">Lanjut <span class="icon">→</span></button>
                        </div>
                    </form>
                </div>

            <?php elseif ($currentStep === 3): ?>
                <!-- Step 3: Hasil Analisis AI -->
                <div class="step-form-section">
                    <div class="step-header">
                        <h2>Hasil Analisis AI</h2>
                        <p>Review hasil analisis potensi dan minat kamu</p>
                    </div>

                    <div class="ai-analysis-review">
                        <div class="analysis-card">
                            <h3>🧠 Potensi Kamu</h3>
                            <div class="analysis-tags">
                                <?php foreach ($currentStepData['data']['potentials'] ?? [] as $potential): ?>
                                    <span class="tag tag-primary"><?= htmlspecialchars($potential) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="analysis-card">
                            <h3>💝 Minat Kamu</h3>
                            <div class="analysis-tags">
                                <?php foreach ($currentStepData['data']['interests'] ?? [] as $interest): ?>
                                    <span class="tag tag-secondary"><?= htmlspecialchars($interest) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="analysis-card">
                            <h3>🌟 Bakat Kamu</h3>
                            <div class="analysis-tags">
                                <?php foreach ($currentStepData['data']['talents'] ?? [] as $talent): ?>
                                    <span class="tag tag-success"><?= htmlspecialchars($talent) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="analysis-card recommended">
                            <h3>🎯 Jurusan Rekomendasi</h3>
                            <p class="recommended-major"><?= htmlspecialchars($currentStepData['data']['recommended_major'] ?? '-') ?></p>
                            <p class="recommendation-note">Berdasarkan analisis potensi, minat, dan bakat kamu</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="previousStep(2)">← Kembali</button>
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
                        <button type="submit" class="btn btn-primary" onclick="nextStep(4)">Lanjut <span class="icon">→</span></button>
                    </div>
                </div>

            <?php elseif ($currentStep === 4): ?>
                <!-- Step 4: Upload Dokumen -->
                <div class="step-form-section">
                    <div class="step-header">
                        <h2>Upload Dokumen</h2>
                        <p>Upload dokumen yang diperlukan dalam format PDF atau JPG (max 2MB)</p>
                    </div>

                    <form class="simulation-form" id="step-form-4">
                        <div class="documents-list">
                            <?php foreach ($currentStepData['documents'] ?? [] as $index => $doc): ?>
                                <div class="document-item">
                                    <div class="document-info">
                                        <span class="document-name">
                                            <?= $doc['is_uploaded'] ? '✅' : '⏳' ?>
                                            <?= htmlspecialchars($doc['name']) ?>
                                            <?php if ($doc['required']): ?>
                                                <span class="required-badge">*</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="document-status <?= $doc['is_uploaded'] ? 'uploaded' : 'pending' ?>">
                                            <?= $doc['is_uploaded'] ? 'Sudah diupload' : 'Belum upload' ?>
                                        </span>
                                    </div>
                                    <div class="document-upload">
                                        <input type="file" name="documents[<?= $index ?>]" accept=".pdf,.jpg,.jpeg,.png" class="file-input" <?= $doc['required'] && !$doc['is_uploaded'] ? 'required' : '' ?>>
                                        <button type="button" class="btn btn-secondary btn-sm">Upload</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="upload-tips">
                            <h4>💡 Tips Upload</h4>
                            <ul>
                                <li>Pastikan dokumen jelas dan terbaca</li>
                                <li>Format: PDF atau JPG</li>
                                <li>Ukuran maksimal: 2MB</li>
                                <li>Kamu bisa lanjut nanti jika belum selesai</li>
                            </ul>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="previousStep(3)">← Kembali</button>
                            <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
                            <button type="submit" class="btn btn-primary">Lanjut <span class="icon">→</span></button>
                        </div>
                    </form>
                </div>

            <?php elseif ($currentStep === 5): ?>
                <!-- Step 5: Pembayaran -->
                <div class="step-form-section">
                    <div class="step-header">
                        <h2>Pembayaran</h2>
                        <p>Selesaikan pembayaran untuk menyelesaikan pendaftaran</p>
                    </div>

                    <div class="payment-section">
                        <div class="payment-summary">
                            <h3>Ringkasan Biaya</h3>
                            <div class="summary-row">
                                <span>Biaya Pendaftaran</span>
                                <span>Rp <?= number_format($currentStepData['payment_info']['registration_fee'] ?? 500000, 0, ',', '.') ?></span>
                            </div>
                            <div class="summary-row discount">
                                <span>Diskon</span>
                                <span>- Rp <?= number_format($currentStepData['payment_info']['discount'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span>Rp <?= number_format($currentStepData['payment_info']['total'] ?? 500000, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="payment-methods">
                            <h3>Transfer ke:</h3>
                            <div class="bank-accounts">
                                <?php foreach ($currentStepData['payment_info']['bank_accounts'] ?? [] as $bank): ?>
                                    <div class="bank-card">
                                        <div class="bank-header">
                                            <span class="bank-name"><?= htmlspecialchars($bank['bank']) ?></span>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="copyAccount('<?= htmlspecialchars($bank['account']) ?>')">Copy</button>
                                        </div>
                                        <div class="bank-account-number" id="account-<?= htmlspecialchars($bank['account']) ?>">
                                            <?= htmlspecialchars($bank['account']) ?>
                                        </div>
                                        <div class="bank-account-name"><?= htmlspecialchars($bank['name']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="payment-confirmation">
                            <h3>Konfirmasi Pembayaran</h3>
                            <form class="simulation-form" id="step-form-5">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="sender_name">Nama Pengirim *</label>
                                        <input type="text" id="sender_name" name="sender_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="transfer_date">Tanggal Transfer *</label>
                                        <input type="date" id="transfer_date" name="transfer_date" required>
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="proof_upload">Bukti Transfer *</label>
                                        <input type="file" id="proof_upload" name="proof_upload" accept="image/*" required>
                                    </div>
                                </div>

                                <div class="checkbox-group">
                                    <label>
                                        <input type="checkbox" name="terms_accepted" required>
                                        Saya menyetujui syarat dan ketentuan pendaftaran
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="previousStep(4)">← Kembali</button>
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">Simpan Draft</button>
                        <button type="submit" class="btn btn-success" form="step-form-5">Konfirmasi Pembayaran</button>
                    </div>
                </div>

            <?php endif; ?>
        </div>

        <!-- Review & Submit (setelah semua step selesai) -->
        <?php if (isset($_GET['completed']) && $_GET['completed'] === '1'): ?>
            <div class="review-section">
                <div class="review-header">
                    <h2>🎉 Simulasi Selesai!</h2>
                    <p>Selamat! Kamu telah menyelesaikan simulasi pendaftaran.</p>
                </div>

                <div class="review-summary">
                    <h3>Ringkasan Pendaftaran</h3>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <label>Program Studi</label>
                            <p><?= htmlspecialchars($sim['selected_program']['name'] ?? 'Teknik Informatika') ?></p>
                        </div>
                        <div class="summary-item">
                            <label>Progress</label>
                            <p><?= $sim['completed_steps'] ?? 3 ?>/<?= $sim['total_steps'] ?? 5 ?> steps</p>
                        </div>
                    </div>
                </div>

                <div class="review-actions">
                    <button type="button" class="btn btn-primary btn-lg" onclick="convertToReal()">
                        🚀 Daftar Sekarang (Convert ke Pendaftaran Sebenarnya)
                    </button>
                    <p class="review-note">Kamu akan mendapatkan nomor pendaftaran dan instruksi pembayaran</p>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- No Data -->
        <div class="no-data-section">
            <div class="no-data-content">
                <h2>📝 Simulasi Belum Dimulai</h2>
                <p>Mulai simulasi pendaftaran untuk merasakan pengalaman mendaftar di Universitas Universal.</p>
                <a href="/pmb/simulation?start=1" class="btn btn-primary">Mulai Simulasi →</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Container */
    .pmb-simulation-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* Header */
    .simulation-header {
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc) 0%, var(--md-sys-color-primary-container, #e6f0ff) 100%);
        border-radius: 16px;
        padding: 32px;
        text-align: center;
        color: white;
    }

    .simulation-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin: 0 0 16px 0;
    }

    .header-note {
        font-size: 14px;
        opacity: 0.85;
        margin: 0;
    }

    /* Progress Section */
    .progress-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .progress-label {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .progress-label strong {
        color: var(--md-sys-color-primary, #0066cc);
        font-size: 18px;
    }

    .progress-steps {
        font-size: 14px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .progress-bar-container {
        height: 12px;
        background: var(--md-sys-color-surface-container-high, #e0e0e0);
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--md-sys-color-primary, #0066cc), var(--md-sys-color-primary-container, #e6f0ff));
        border-radius: 6px;
        transition: width 0.5s ease;
    }

    .steps-indicator {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .step-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.2s;
    }

    .step-indicator.completed {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
    }

    .step-indicator.active {
        background: var(--md-sys-color-primary-container, #e6f0ff);
        border: 1px solid var(--md-sys-color-primary, #0066cc);
    }

    .step-number {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--md-sys-color-outline, #e0e0e0);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .step-indicator.completed .step-number {
        background: var(--md-sys-color-secondary, #4caf50);
        color: white;
    }

    .step-indicator.active .step-number {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .step-name {
        white-space: nowrap;
    }

    /* Step Content */
    .step-content-wrapper {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .step-form-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .step-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--md-sys-color-outline, #e0e0e0);
    }

    .step-header h2 {
        font-size: 22px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .step-header p {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0;
    }

    /* Form */
    .simulation-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 12px;
        border: 1px solid var(--md-sys-color-outline, #e0e0e0);
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--md-sys-color-primary, #0066cc);
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 20px;
        border-top: 1px solid var(--md-sys-color-outline, #e0e0e0);
    }

    .form-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .form-actions .btn .icon {
        font-size: 18px;
    }

    /* AI Analysis Review */
    .ai-analysis-review {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .analysis-card {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 12px;
        padding: 20px;
    }

    .analysis-card h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 12px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .analysis-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag {
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 13px;
        font-weight: 500;
    }

    .tag-primary {
        background: var(--md-sys-color-primary-container, #e6f0ff);
        color: var(--md-sys-color-on-primary-container, #004c99);
    }

    .tag-secondary {
        background: var(--md-sys-color-secondary-container, #e8f5e9);
        color: var(--md-sys-color-on-secondary-container, #2e7d32);
    }

    .tag-success {
        background: var(--md-sys-color-tertiary-container, #fff3e0);
        color: var(--md-sys-color-on-tertiary-container, #e65100);
    }

    .analysis-card.recommended {
        grid-column: 1 / -1;
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc), var(--md-sys-color-primary-container, #e6f0ff));
        color: white;
    }

    .analysis-card.recommended h3 {
        color: white;
    }

    .recommended-major {
        font-size: 20px;
        font-weight: 700;
        margin: 8px 0;
    }

    .recommendation-note {
        font-size: 13px;
        opacity: 0.9;
        margin: 0;
    }

    /* Documents */
    .documents-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .document-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 8px;
    }

    .document-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .document-name {
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .required-badge {
        color: var(--md-sys-color-error, #f44336);
        font-weight: 700;
    }

    .document-status {
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .document-status.uploaded {
        color: var(--md-sys-color-secondary, #4caf50);
    }

    .document-upload {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .file-input {
        max-width: 200px;
    }

    .upload-tips {
        background: var(--md-sys-color-primary-container, #e6f0ff);
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
    }

    .upload-tips h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: var(--md-sys-color-on-primary-container, #004c99);
    }

    .upload-tips ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
        color: var(--md-sys-color-on-primary-container, #004c99);
    }

    .upload-tips li {
        margin-bottom: 4px;
    }

    /* Payment Section */
    .payment-section {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .payment-summary {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border: 2px solid var(--md-sys-color-primary, #0066cc);
        border-radius: 12px;
        padding: 20px;
    }

    .payment-summary h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 16px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }

    .summary-row.total {
        border-top: 2px solid var(--md-sys-color-outline, #e0e0e0);
        padding-top: 12px;
        margin-top: 8px;
        font-weight: 700;
        font-size: 16px;
    }

    .summary-row.discount {
        color: var(--md-sys-color-secondary, #4caf50);
    }

    .bank-accounts {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }

    .bank-card {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 12px;
        padding: 16px;
    }

    .bank-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .bank-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .bank-account-number {
        font-size: 18px;
        font-weight: 700;
        color: var(--md-sys-color-primary, #0066cc);
        font-family: monospace;
        margin-bottom: 4px;
    }

    .bank-account-name {
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .payment-confirmation {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 12px;
        padding: 20px;
    }

    .payment-confirmation h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 16px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .checkbox-group {
        margin-top: 16px;
    }

    .checkbox-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    /* Review Section */
    .review-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .review-header h2 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .review-header p {
        font-size: 16px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0 0 32px 0;
    }

    .review-summary {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 32px;
    }

    .review-summary h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 16px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }

    .summary-item {
        text-align: left;
    }

    .summary-item label {
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant, #666);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-item p {
        font-size: 16px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
        margin: 4px 0 0 0;
    }

    .review-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .review-note {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0;
    }

    /* No Data Section */
    .no-data-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 48px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .no-data-content h2 {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 16px 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .no-data-content p {
        font-size: 16px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0 0 24px 0;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
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

    .btn-success {
        background: var(--md-sys-color-secondary, #4caf50);
        color: white;
    }

    .btn-success:hover {
        background: #43a047;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-lg {
        padding: 16px 32px;
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .pmb-simulation-container {
            padding: 16px;
        }

        .simulation-header {
            padding: 24px;
        }

        .simulation-header h1 {
            font-size: 20px;
        }

        .step-form-section {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .steps-indicator {
            overflow-x: auto;
            flex-wrap: nowrap;
        }

        .step-name {
            display: none;
        }

        .bank-accounts,
        .ai-analysis-review,
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Save draft function
    function saveDraft() {
        alert('Draft berhasil disimpan! Kamu bisa lanjut nanti.');
    }

    // Previous step
    function previousStep(stepId) {
        window.location.href = '/pmb/simulation?step=' + stepId;
    }

    // Next step
    function nextStep(stepId) {
        window.location.href = '/pmb/simulation?step=' + stepId;
    }

    // Copy bank account number
    function copyAccount(accountNumber) {
        navigator.clipboard.writeText(accountNumber);
        alert('Nomor rekening berhasil dicopy: ' + accountNumber);
    }

    // Convert to real application
    function convertToReal() {
        if (confirm('Apakah kamu yakin ingin convert simulasi ini ke pendaftaran sebenarnya?')) {
            // TODO: Implement actual conversion
            alert('Pendaftaran berhasil dibuat! Nomor pendaftaran kamu: PMB-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-001');
            window.location.href = '/pmb/simulation?completed=1';
        }
    }

    // Form submission handlers
    document.querySelectorAll('.simulation-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // TODO: Implement actual form submission
            alert('Data berhasil disimpan!');
        });
    });
</script>