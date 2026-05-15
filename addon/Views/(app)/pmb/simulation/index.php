<?php

/**
 * PMB Simulation View - Multi-step Wizard
 * 
 * @var array $simulation Simulation data with steps and progress
 */
$sim = $simulation ?? null;
$currentStep = $sim['current_step'] ?? 1;
$totalSteps = $sim['total_steps'] ?? 3;
$progressPercentage = $sim['progress_percentage'] ?? 0;
?>

<link rel="stylesheet" href="<?= asset('addon/Views/(app)/pmb/simulation.css') ?>">
<script src="<?= asset('addon/Views/(app)/pmb/simulation/script.js') ?>" defer></script>

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
                <?php include_once 'step1.php'; ?>
            <?php elseif ($currentStep === 2): ?>
                <!-- Step 2: Upload Dokumen -->
                <?php include_once 'step2.php'; ?>

            <?php elseif ($currentStep === 3): ?>
                <!-- Step 3: Pembayaran -->
                <?php include_once 'step3.php'; ?>

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
                            <label>Progress</label>
                            <p><?= $sim['completed_steps'] ?? 3 ?>/<?= $sim['total_steps'] ?? 3 ?> steps</p>
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

        <!-- Converted Success Message -->
        <?php if (isset($_GET['converted']) && $_GET['converted'] === '1'): ?>
            <div class="success-section">
                <div class="success-header">
                    <div class="success-icon">✅</div>
                    <h2>Pendaftaran Berhasil Dibuat!</h2>
                    <p>Simulasi kamu telah berhasil dikonversi menjadi pendaftaran sebenarnya.</p>
                </div>

                <div class="registration-number-card">
                    <h3>Nomor Pendaftaran Kamu</h3>
                    <div class="reg-number"><?= htmlspecialchars($_GET['reg_number'] ?? 'PMB-XXXXXXXX') ?></div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="copyRegNumber()">
                        📋 Copy Nomor
                    </button>
                </div>

                <div class="next-steps">
                    <h3>Langkah Selanjutnya</h3>
                    <ol>
                        <li>Simpan nomor pendaftaran kamu</li>
                        <li>Lakukan pembayaran sesuai instruksi</li>
                        <li>Upload bukti pembayaran di halaman dashboard</li>
                        <li>Tunggu konfirmasi dari panitia</li>
                    </ol>
                </div>

                <div class="success-actions">
                    <a data-spa href="/dashboard" class="btn btn-primary">Ke Dashboard</a>
                    <a data-spa href="/pmb/journey" class="btn btn-secondary">Lihat Journey PMB</a>
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