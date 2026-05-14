<?php

/**
 * View: Tes RIASEC - Halaman Pengerjaan (dengan Stepper)
 * 
 * @var array $session Session tes
 * @var array $statements Daftar pernyataan
 * @var array $answers Jawaban yang sudah diisi
 * @var float $progress Progress pengerjaan (0-100)
 * @var int $answeredCount Jumlah yang sudah dijawab
 * @var int $totalCount Total jumlah pernyataan
 */

$dimensions = ['R' => 'Realistic', 'I' => 'Investigative', 'A' => 'Artistic', 'S' => 'Social', 'E' => 'Enterprising', 'C' => 'Conventional'];
$dimensionColors = ['R' => 'dimension-r', 'I' => 'dimension-i', 'A' => 'dimension-a', 'S' => 'dimension-s', 'E' => 'dimension-e', 'C' => 'dimension-c'];
$dimensionFullNames = ['R' => 'Realistic', 'I' => 'Investigative', 'A' => 'Artistic', 'S' => 'Social', 'E' => 'Enterprising', 'C' => 'Conventional'];
$optionData = [
    ['value' => '1', 'label' => 'STS', 'desc' => 'Sangat Tidak Sesuai'],
    ['value' => '2', 'label' => 'TS', 'desc' => 'Tidak Sesuai'],
    ['value' => '3', 'label' => 'S', 'desc' => 'Sesuai'],
    ['value' => '4', 'label' => 'SS', 'desc' => 'Sangat Sesuai']
];

// Hitung jumlah pernyataan per kategori
$dimensionCounts = [];
foreach ($dimensions as $dimCode => $dimName) {
    $dimStatements = array_filter($statements, fn($s) => $s['dimension'] === $dimCode);
    $dimensionCounts[$dimCode] = count($dimStatements);
}
?>

<div class="riasec-take-page">
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <span><?= e($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span><?= e($_GET['warning']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header dengan Stepper -->
    <div class="riasec-take-header">
        <div class="riasec-take-info">
            <h1 class="riasec-take-title">Tes RIASEC</h1>
            <p class="riasec-take-counter">
                <span id="answered-count"><?= e((string) $answeredCount) ?></span> / <?= e((string) $totalCount) ?> terjawab
            </p>
        </div>

        <!-- Stepper Component -->
        <div class="riasec-stepper" id="riasec-stepper">
            <?php
            $stepIndex = 0;
            foreach ($dimensions as $dimCode => $dimName):
                $isActive = ($stepIndex === 0);
                $stepClass = $isActive ? 'active' : 'pending';
            ?>
                <div class="stepper-item <?= $stepClass ?>" data-step="<?= e($dimCode) ?>" data-step-index="<?= $stepIndex ?>">
                    <span class="stepper-icon"><?= $stepIndex + 1 ?></span>
                    <span class="stepper-label"><?= e($dimName) ?></span>
                </div>
            <?php
                $stepIndex++;
            endforeach;
            ?>
        </div>
    </div>

    <!-- Form Pengerjaan -->
    <form id="riasec-form" action="/tests/riasec/submit" method="POST">
        <input type="hidden" name="session_id" value="<?= e((string) $session['id']) ?>">
        <?= csrf_field() ?>

        <!-- Dimension Sections -->
        <?php
        $sectionIndex = 0;
        foreach ($dimensions as $dimCode => $dimName):
            $dimStatements = array_filter($statements, fn($s) => $s['dimension'] === $dimCode);
            if (empty($dimStatements)) continue;

            $isActive = ($sectionIndex === 0);
            $totalInDimension = count($dimStatements);
        ?>
            <div class="riasec-dimension-section <?= $isActive ? 'active' : 'hidden' ?>" data-dimension="<?= e($dimCode) ?>" data-step-index="<?= $sectionIndex ?>">
                <div class="riasec-dimension-header <?= e($dimensionColors[$dimCode]) ?>">
                    <span class="dimension-badge"><?= e($dimCode) ?></span>
                    <h2 class="dimension-title"><?= e($dimName) ?></h2>

                    <!-- Progress Bar per Kategori -->
                    <div class="dimension-progress">
                        <div class="dimension-progress-bar">
                            <div class="dimension-progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="dimension-progress-text">0/<?= $totalInDimension ?></span>
                    </div>
                </div>

                <div class="riasec-statements">
                    <?php foreach ($dimStatements as $statement):
                        $userAnswer = $answers[$statement['id']] ?? null;
                    ?>
                        <div class="riasec-statement-item <?= $userAnswer ? 'answered' : '' ?>" data-statement-id="<?= e((string) $statement['id']) ?>">
                            <p class="statement-text"><?= e($statement['statement_text']) ?></p>

                            <div class="statement-options">
                                <?php foreach ($optionData as $opt): ?>
                                    <label class="option <?= $userAnswer === $opt['value'] ? 'selected' : '' ?>" data-value="<?= e($opt['value']) ?>">
                                        <input type="radio" name="answers[<?= e((string) $statement['id']) ?>]" value="<?= e($opt['value']) ?>" <?= $userAnswer === $opt['value'] ? 'checked' : '' ?> hidden>
                                        <span class="option-text"><?= e($opt['label']) ?></span>
                                        <span class="option-desc"><?= e($opt['desc']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Buttons -->
                <div class="dimension-navigation">
                    <?php if ($sectionIndex > 0): ?>
                        <button type="button" class="btn btn-secondary btn-prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                            Sebelumnya
                        </button>
                    <?php else: ?>
                        <div></div> <!-- Spacer -->
                    <?php endif; ?>

                    <?php if ($sectionIndex < count($dimensions) - 1): ?>
                        <button type="button" class="btn btn-primary btn-next">
                            Lanjut
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary" id="submit-from-section">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            Kirim Jawaban
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php
            $sectionIndex++;
        endforeach;
        ?>
    </form>

    <!-- Confirmation Modal -->
    <dialog class="modal" id="confirm-modal">
        <div class="modal-content">
            <h3>Konfirmasi Pengiriman</h3>
            <p>Apakah Anda yakin sudah menjawab semua pertanyaan dan ingin mengirim jawaban?</p>
            <p class="modal-note">Jawaban yang sudah dikirim tidak dapat diubah.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancel-btn">Lanjutkan Mengerjakan</button>
                <button type="button" class="btn btn-primary" id="confirm-submit">Ya, Kirim Jawaban</button>
            </div>
        </div>
    </dialog>
</div>