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

<link rel="stylesheet" href="<?= asset('addon/Views/(app)/pmb/simulation.css') ?>">

<div class="pmb-simulation-container">
    <!-- Header -->
    <div class="simulation-header">
        <div class="header-content">
            <h1>📝 Simulasi Pendaftaran Mahasiswa Baru</h1>
            <p class="header-subtitle">Universitas Univeral</p>
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
            <div class="progress-bar-container-simulation">
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
                <p>Mulai simulasi pendaftaran untuk merasakan pengalaman mendaftar di Universitas Univeral.</p>
                <a data-spa href="/pmb/simulation?start=1" class="btn btn-primary">Mulai Simulasi →</a>
            </div>
        </div>
    <?php endif; ?>
</div>

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