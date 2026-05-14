<?php

/**
 * RIASEC Test Results View
 * 
 * @var array $profile Profile data
 * @var array|null $studentProfile Student profile data
 * @var array|null $riasecResult Latest RIASEC test result from TestResultModel
 */

// Decode JSON data
$aiAnalysis = !empty($studentProfile['ai_analysis']) ? json_decode($studentProfile['ai_analysis'], true) : [];

// Extract RIASEC result data
$riasecScores = [];
$riasecCategories = [];
$riasecHollandCode = '';
$riasecDescription = '';

if ($riasecResult) {
    $riasecScoresRaw = json_decode($riasecResult['scores'] ?? '[]', true);
    $riasecCategoriesRaw = json_decode($riasecResult['categories'] ?? '[]', true);

    // Ensure arrays
    $riasecScores = is_array($riasecScoresRaw) ? $riasecScoresRaw : [];
    $riasecCategories = is_array($riasecCategoriesRaw) ? $riasecCategoriesRaw : [];
    $riasecHollandCode = $riasecResult['holland_code'] ?? '';
    $riasecDescription = $riasecResult['holland_description'] ?? '';
}

// Calculate data hash for AI update check (use RIASEC result instead of psychological_tests)
$academic = $studentProfile['academic_scores'] ?? '';
$riasecData = $riasecResult ? json_encode($riasecResult) : '';
$achievements = $studentProfile['achievements'] ?? '';
$currentHash = md5($academic . $riasecData . $achievements);
$lastHash = $aiAnalysis['last_data_hash'] ?? null;

// Check data completeness
$hasRiasec = !empty($riasecResult);
$hasAcademic = !empty($studentProfile['academic_scores']);
$hasAchievements = !empty($studentProfile['achievements']);
$dataCompleteness = $aiAnalysis['data_completeness'] ?? [
    'has_riasec' => $hasRiasec,
    'has_academic' => $hasAcademic,
    'has_achievements' => $hasAchievements
];

// Determine missing data for AI accuracy
$missingData = [];
if (!$dataCompleteness['has_academic']) $missingData[] = 'nilai akademik';
if (!$dataCompleteness['has_achievements']) $missingData[] = 'prestasi';
?>

<div class="results-container">
    <div class="results-header">
        <div class="breadcrumb">
            <a data-spa href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Hasil Tes RIASEC</span>
        </div>
        <h1>Hasil Tes RIASEC</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['warning'])): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($_GET['warning']) ?>
        </div>
    <?php endif; ?>

    <!-- Holland Code Summary -->
    <?php if (!empty($riasecHollandCode)): ?>
        <div class="holland-summary-card">
            <div class="holland-header">
                <span class="holland-icon">🎯</span>
                <div>
                    <h2>Kode Holland: <span class="holland-code"><?= htmlspecialchars($riasecHollandCode) ?></span></h2>
                    <p class="holland-subtitle">Tipe kepribadian karir Anda</p>
                </div>
            </div>
            <?php if (!empty($riasecDescription)): ?>
                <p class="holland-description"><?= htmlspecialchars($riasecDescription) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- AI Analysis Summary -->
    <?php if (!empty($aiAnalysis)): ?>
        <div class="ai-analysis-card">
            <div class="ai-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="ai-icon">🤖</span>
                    <h2>Analisis AI</h2>
                </div>
                <?php if ($currentHash !== $lastHash): ?>
                    <form data-spa method="POST" action="/profile/results/generate" class="ai-generate-form" onsubmit="this.querySelector('button').textContent='Memproses...';">
                        <button type="submit" class="btn btn-primary btn-sm">✨ Update Analisis AI</button>
                    </form>
                <?php else: ?>
                    <span class="badge badge-success" style="font-size: 13px; padding: 4px 10px; border-radius: 12px; background: rgba(255,255,255,0.2);">✅ Mutakhir</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($aiAnalysis['summary'])): ?>
                <div class="ai-section">
                    <h3>Ringkasan</h3>
                    <p class="ai-summary"><?= htmlspecialchars($aiAnalysis['summary']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($aiAnalysis['potential'])): ?>
                <div class="ai-section">
                    <h3>Potensi Utama</h3>
                    <div class="potential-tags">
                        <?php foreach ($aiAnalysis['potential'] as $potential): ?>
                            <span class="tag tag-potential"><?= htmlspecialchars($potential) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($aiAnalysis['interests'])): ?>
                <div class="ai-section">
                    <h3>Minat</h3>
                    <div class="interests-list">
                        <?php foreach ($aiAnalysis['interests'] as $interest): ?>
                            <div class="interest-item">
                                <span class="interest-name"><?= htmlspecialchars($interest['name'] ?? $interest) ?></span>
                                <?php if (isset($interest['level'])): ?>
                                    <div class="interest-bar">
                                        <div class="interest-fill" style="width: <?= $interest['level'] ?>%"></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($aiAnalysis['talents'])): ?>
                <div class="ai-section">
                    <h3>Bakat</h3>
                    <div class="talents-grid">
                        <?php foreach ($aiAnalysis['talents'] as $talent): ?>
                            <div class="talent-card">
                                <span class="talent-icon"><?= $talent['icon'] ?? '⭐' ?></span>
                                <span class="talent-name"><?= htmlspecialchars($talent['name'] ?? $talent) ?></span>
                                <?php if (isset($talent['score'])): ?>
                                    <span class="talent-score"><?= $talent['score'] ?>%</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($aiAnalysis['recommendations'])): ?>
                <div class="ai-section">
                    <h3>Rekomendasi</h3>
                    <ul class="recommendations-list">
                        <?php foreach ($aiAnalysis['recommendations'] as $recommendation): ?>
                            <li><?= htmlspecialchars($recommendation) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($aiAnalysis['career_suggestions'])): ?>
                <div class="ai-section">
                    <h3>Saran Karir</h3>
                    <div class="career-tags">
                        <?php foreach ($aiAnalysis['career_suggestions'] as $career): ?>
                            <span class="tag tag-career"><?= htmlspecialchars($career) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($aiAnalysis['generated_at'])): ?>
                <div class="ai-footer">
                    <small>Analisis dihasilkan pada <?= date('d F Y H:i', strtotime($aiAnalysis['generated_at'])) ?></small>
                </div>
            <?php endif; ?>

            <!-- Data Completeness Info -->
            <div class="data-completeness-info">
                <div class="completeness-header">
                    <span class="icon">📊</span>
                    <strong>Status Data Analisis</strong>
                </div>
                <div class="completeness-items">
                    <div class="completeness-item <?= $dataCompleteness['has_riasec'] ? 'complete' : 'incomplete' ?>">
                        <span class="check-icon"><?= $dataCompleteness['has_riasec'] ? '✅' : '⏳' ?></span>
                        <span>Tes RIASEC</span>
                    </div>
                    <div class="completeness-item <?= $dataCompleteness['has_academic'] ? 'complete' : 'incomplete' ?>">
                        <span class="check-icon"><?= $dataCompleteness['has_academic'] ? '✅' : '⏳' ?></span>
                        <span>Nilai Akademik</span>
                    </div>
                    <div class="completeness-item <?= $dataCompleteness['has_achievements'] ? 'complete' : 'incomplete' ?>">
                        <span class="check-icon"><?= $dataCompleteness['has_achievements'] ? '✅' : '⏳' ?></span>
                        <span>Prestasi</span>
                    </div>
                </div>
                <?php if (!empty($missingData)): ?>
                    <div class="completeness-warning">
                        <span class="warning-icon">⚠️</span>
                        <p><strong>Untuk hasil analisis yang lebih akurat,</strong> lengkapi data berikut:</p>
                        <ul>
                            <?php foreach ($missingData as $item): ?>
                                <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="no-analysis-card">
            <div class="no-analysis-icon">📊</div>
            <h3>Belum Ada Analisis AI</h3>
            <p style="margin-bottom: 20px;">Sistem AI belum menganalisis potensi dan bakatmu berdasarkan data akademik, psikologi, dan prestasimu.</p>
            <?php if ($hasRiasec): ?>
                <form data-spa method="POST" action="/profile/results/generate" class="ai-generate-form" onsubmit="this.querySelector('button').textContent='Memproses...';">
                    <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 24px;">✨ Generate Analisis Pertamamu</button>
                </form>
            <?php else: ?>
                <a data-spa href="/tests/riasec" class="btn btn-primary" style="font-size: 16px; padding: 12px 24px;">🎯 Ikuti Tes RIASEC</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- RIASEC Scores Detail -->
    <?php if (!empty($riasecScores) && is_array($riasecScores)): ?>
        <div class="riasec-scores-section">
            <h2>Skor RIASEC</h2>
            <div class="scores-grid">
                <?php
                $dimensionLabels = [
                    'R' => 'Realistic',
                    'I' => 'Investigative',
                    'A' => 'Artistic',
                    'S' => 'Social',
                    'E' => 'Enterprising',
                    'C' => 'Conventional'
                ];
                $dimensionIcons = [
                    'R' => '🔧',
                    'I' => '🔬',
                    'A' => '🎨',
                    'S' => '🤝',
                    'E' => '💼',
                    'C' => '📊'
                ];

                foreach ($riasecScores as $dimension => $score):
                    $label = $dimensionLabels[$dimension] ?? $dimension;
                    $icon = $dimensionIcons[$dimension] ?? '📌';
                    $category = $riasecCategories[$dimension] ?? 'Unknown';
                    $badgeClass = match ($category) {
                        'Sangat Tinggi' => 'badge-high',
                        'Tinggi' => 'badge-medium',
                        'Cukup' => 'badge-low',
                        default => 'badge-default'
                    };
                ?>
                    <div class="score-card">
                        <div class="score-header">
                            <span class="score-icon"><?= $icon ?></span>
                            <div>
                                <h3><?= htmlspecialchars($label) ?></h3>
                                <span class="score-badge <?= $badgeClass ?>"><?= htmlspecialchars($category) ?></span>
                            </div>
                        </div>
                        <div class="score-value">
                            <span class="score-number"><?= is_numeric($score) ? round($score) : 0 ?></span>
                            <span class="score-label">poin</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .results-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    .results-header {
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

    .results-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    /* Holland Summary Card */
    .holland-summary-card {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }

    .holland-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }

    .holland-icon {
        font-size: 40px;
    }

    .holland-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .holland-code {
        font-size: 32px;
        font-weight: 700;
        letter-spacing: 4px;
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 16px;
        border-radius: 8px;
    }

    .holland-subtitle {
        margin: 4px 0 0 0;
        font-size: 14px;
        opacity: 0.9;
    }

    .holland-description {
        margin: 0;
        font-size: 15px;
        line-height: 1.6;
        opacity: 0.95;
    }

    /* AI Analysis Card */
    .ai-analysis-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .ai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ai-icon {
        font-size: 32px;
    }

    .ai-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
    }

    .ai-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .ai-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .ai-section h3 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 600;
        opacity: 0.9;
    }

    .ai-summary {
        font-size: 15px;
        line-height: 1.6;
        opacity: 0.95;
    }

    /* Tags */
    .potential-tags,
    .career-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }

    .tag-potential {
        background: rgba(255, 255, 255, 0.25);
        color: white;
    }

    .tag-career {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* Interests */
    .interests-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .interest-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .interest-name {
        min-width: 120px;
        font-size: 14px;
    }

    .interest-bar {
        flex: 1;
        height: 8px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
        overflow: hidden;
    }

    .interest-fill {
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    /* Talents */
    .talents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }

    .talent-card {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 8px;
    }

    .talent-icon {
        font-size: 24px;
    }

    .talent-name {
        font-size: 13px;
        font-weight: 500;
    }

    .talent-score {
        font-size: 18px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.3);
        padding: 4px 12px;
        border-radius: 12px;
    }

    /* Recommendations */
    .recommendations-list {
        margin: 0;
        padding-left: 20px;
    }

    .recommendations-list li {
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 8px;
        opacity: 0.95;
    }

    .ai-footer {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        opacity: 0.7;
    }

    /* Data Completeness Info */
    .data-completeness-info {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .completeness-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .completeness-header .icon {
        font-size: 18px;
    }

    .completeness-items {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 16px;
    }

    .completeness-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
    }

    .completeness-item.complete {
        background: rgba(76, 175, 80, 0.2);
        color: #a5d6a7;
    }

    .completeness-item.incomplete {
        background: rgba(255, 193, 7, 0.2);
        color: #ffe082;
    }

    .completeness-item .check-icon {
        font-size: 16px;
    }

    .completeness-warning {
        background: rgba(255, 193, 7, 0.15);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 12px;
        padding: 16px;
        margin-top: 12px;
    }

    .completeness-warning .warning-icon {
        font-size: 20px;
        margin-right: 8px;
    }

    .completeness-warning p {
        margin: 8px 0;
        font-size: 13px;
        color: #ffe082;
    }

    .completeness-warning ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
        font-size: 13px;
        color: #fff9c4;
    }

    .completeness-warning li {
        margin-bottom: 4px;
    }

    /* No Analysis Card */
    .no-analysis-card {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 16px;
        padding: 48px 24px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .no-analysis-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }

    .no-analysis-card h3 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .no-analysis-card p {
        margin: 0;
        font-size: 15px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    /* RIASEC Scores Section */
    .riasec-scores-section {
        margin-top: 32px;
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .riasec-scores-section h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .scores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
    }

    .score-card {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.2s;
    }

    .score-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .score-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .score-icon {
        font-size: 32px;
    }

    .score-header h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .score-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
    }

    .badge-high {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .badge-medium {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-low {
        background: #fff8e1;
        color: #fbc02d;
    }

    .badge-default {
        background: #f5f5f5;
        color: #666;
    }

    .score-value {
        text-align: center;
    }

    .score-number {
        display: block;
        font-size: 32px;
        font-weight: 700;
        color: var(--md-sys-color-primary, #0066cc);
    }

    .score-label {
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    /* Alerts */
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

    .alert-error {
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-error, #d32f2f);
        border: 1px solid var(--md-sys-color-error, #f44336);
    }

    .alert-warning {
        background: var(--md-sys-color-warning-container, #fff3e0);
        color: var(--md-sys-color-warning, #f57c00);
        border: 1px solid var(--md-sys-color-warning, #ff9800);
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: 0.2s;
    }

    .btn-primary {
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
    }

    .btn-primary:hover {
        background: var(--md-sys-color-on-primary, #0052a3);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .talents-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .scores-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .holland-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .ai-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>