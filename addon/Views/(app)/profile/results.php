<?php

/**
 * Student Psychotest Results View
 * 
 * @var array $profile Profile data
 * @var array|null $studentProfile Student profile data
 */

// Decode JSON data
$psychologicalTests = !empty($studentProfile['psychological_tests']) ? json_decode($studentProfile['psychological_tests'], true) : [];
$aiAnalysis = !empty($studentProfile['ai_analysis']) ? json_decode($studentProfile['ai_analysis'], true) : [];

// Calculate data hash for AI update check
$academic = $studentProfile['academic_scores'] ?? '';
$psycho = $studentProfile['psychological_tests'] ?? '';
$achievements = $studentProfile['achievements'] ?? '';
$currentHash = md5($academic . $psycho . $achievements);
$lastHash = $aiAnalysis['last_data_hash'] ?? null;
?>

<div class="results-container">
    <div class="results-header">
        <div class="breadcrumb">
            <a href="/profile">Profile</a>
            <span class="separator">/</span>
            <span class="current">Hasil Psykotest</span>
        </div>
        <h1>Hasil Psykotest</h1>
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
        </div>
    <?php else: ?>
        <div class="no-analysis-card">
            <div class="no-analysis-icon">📊</div>
            <h3>Belum Ada Analisis AI</h3>
            <p style="margin-bottom: 20px;">Sistem AI belum menganalisis potensi dan bakatmu berdasarkan data akademik, psikologi, dan prestasimu.</p>
            <form data-spa method="POST" action="/profile/results/generate" class="ai-generate-form" onsubmit="this.querySelector('button').textContent='Memproses...';">
                <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 24px;">✨ Generate Analisis Pertamamu</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Psychological Test Results -->
    <div class="test-results-section">
        <h2>Riwayat Test Psikologi</h2>

        <?php if (!empty($psychologicalTests)): ?>
            <div class="tests-list">
                <?php foreach ($psychologicalTests as $index => $test): ?>
                    <div class="test-card <?= $index === count($psychologicalTests) - 1 ? 'latest' : '' ?>">
                        <?php if ($index === count($psychologicalTests) - 1): ?>
                            <span class="latest-badge">Test Terbaru</span>
                        <?php endif; ?>

                        <div class="test-header">
                            <h3><?= htmlspecialchars($test['test_name'] ?? 'Test Psikologi') ?></h3>
                            <span class="test-date"><?= date('d M Y', strtotime($test['date'] ?? 'now')) ?></span>
                        </div>

                        <div class="test-body">
                            <?php if (!empty($test['metrics'])): ?>
                                <div class="test-categories">
                                    <?php foreach ($test['metrics'] as $metricName => $score): ?>
                                        <div class="category-item">
                                            <span class="category-name"><?= htmlspecialchars($metricName) ?></span>
                                            <div class="category-bar">
                                                <div class="category-fill" style="width: <?= is_numeric($score) ? min(100, max(0, $score)) : 100 ?>%"></div>
                                            </div>
                                            <span class="category-score"><?= is_numeric($score) ? $score . '%' : htmlspecialchars($score) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($test['description'])): ?>
                                <p class="test-description"><?= htmlspecialchars($test['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-tests-card">
                <div class="no-tests-icon">📝</div>
                <h3>Belum Ada Test</h3>
                <p>Kamu belum mengikuti test psikologi. Silakan hubungi guru BK untuk menjadwalkan test.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .results-container {
        max-width: 1000px;
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

    /* No Analysis Card */
    .no-analysis-card,
    .no-tests-card {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 16px;
        padding: 48px 24px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .no-analysis-icon,
    .no-tests-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }

    .no-analysis-card h3,
    .no-tests-card h3 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .no-analysis-card p,
    .no-tests-card p {
        margin: 0;
        font-size: 15px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    /* Test Results Section */
    .test-results-section {
        margin-top: 32px;
    }

    .test-results-section h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .tests-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .test-card {
        background: var(--md-sys-color-surface-container-lowest, #ffffff);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .test-card.latest {
        border: 2px solid var(--md-sys-color-primary, #0066cc);
    }

    .latest-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--md-sys-color-primary, #0066cc);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .test-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .test-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .test-date {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .test-categories {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 16px;
    }

    .category-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .category-name {
        min-width: 150px;
        font-size: 14px;
        font-weight: 500;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .category-bar {
        flex: 1;
        height: 10px;
        background: var(--md-sys-color-surface-container-highest, #f0f0f0);
        border-radius: 5px;
        overflow: hidden;
    }

    .category-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--md-sys-color-primary, #0066cc), var(--md-sys-color-primary-light, #4d94ff));
        border-radius: 5px;
        transition: width 0.5s ease;
    }

    .category-score {
        min-width: 45px;
        text-align: right;
        font-size: 14px;
        font-weight: 600;
        color: var(--md-sys-color-primary, #0066cc);
    }

    .test-description {
        margin: 0;
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        line-height: 1.6;
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

    .alert-error {
        background: var(--md-sys-color-error-container, #ffebee);
        color: var(--md-sys-color-error, #d32f2f);
        border: 1px solid var(--md-sys-color-error, #f44336);
    }

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

        .test-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .category-item {
            flex-wrap: wrap;
        }

        .category-name {
            min-width: 100%;
            margin-bottom: 4px;
        }

        .category-score {
            min-width: auto;
        }
    }
</style>