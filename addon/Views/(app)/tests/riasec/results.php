<?php

/**
 * View: Tes RIASEC - Halaman Hasil
 * 
 * @var array $result Hasil tes dengan detail
 * @var array $session Session tes
 */

// Decode JSON fields
$scores = isset($result['scores']) ? json_decode($result['scores'], true) : [];
$categories = isset($result['categories']) ? json_decode($result['categories'], true) : [];

$hollandCode = $result['holland_code'] ?? '---';
$hollandDescription = $result['holland_description'] ?? '';

// Dimension colors
$dimensionColors = [
    'R' => '#3B6D11',
    'I' => '#1E5F74',
    'A' => '#8B5CF6',
    'S' => '#F59E0B',
    'E' => '#DC2626',
    'C' => '#6B7280'
];

$dimensionLabels = [
    'R' => 'Realistic',
    'I' => 'Investigative',
    'A' => 'Artistic',
    'S' => 'Social',
    'E' => 'Enterprising',
    'C' => 'Conventional'
];
?>

<div class="riasec-results-page">
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <span><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span><?= htmlspecialchars($_GET['warning']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <span><?= htmlspecialchars($_GET['success']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header dengan Holland Code -->
    <div class="riasec-results-header">
        <div class="holland-code-display">
            <span class="holland-label">Kode Holland Anda</span>
            <div class="holland-code">
                <?php foreach (str_split($hollandCode) as $letter): ?>
                    <span class="holland-letter"><?= $letter ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <p class="results-date">Dikerjakan pada <?= date('d M Y, H:i', strtotime($result['calculated_at'] ?? $session['started_at'] ?? '')) ?></p>
    </div>

    <!-- Holland Description -->
    <div class="riasec-results-description">
        <h2>Profil Anda</h2>
        <p><?= htmlspecialchars($hollandDescription) ?></p>
    </div>

    <!-- Skor per Dimensi -->
    <div class="riasec-scores-section">
        <h2>Skor per Dimensi</h2>
        <div class="scores-grid">
            <?php foreach ($dimensionLabels as $code => $label):
                $score = $scores[$code] ?? 0;
                $category = $categories[$code] ?? '-';
                $maxScore = 28; // 7 statements * 4 max value
                $percentage = ($score / $maxScore) * 100;
                $color = $dimensionColors[$code];

                // Category badge color
                $badgeColor = match ($category) {
                    'Sangat Tinggi' => '#10B981',
                    'Tinggi' => '#3B6D11',
                    'Sedang' => '#F59E0B',
                    'Rendah' => '#DC2626',
                    default => '#6B7280'
                };
            ?>
                <div class="score-card" style="--card-color: <?= $color ?>">
                    <div class="score-card-header">
                        <span class="dimension-code" style="background: <?= $color ?>"><?= $code ?></span>
                        <span class="dimension-name"><?= $label ?></span>
                    </div>
                    <div class="score-card-body">
                        <div class="score-value"><?= $score ?></div>
                        <div class="score-bar">
                            <div class="score-bar-fill" style="width: <?= $percentage ?>%; background: <?= $color ?>"></div>
                        </div>
                        <span class="category-badge" style="background: <?= $badgeColor ?>"><?= $category ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Visualisasi Radar Chart (Simple CSS) -->
    <div class="riasec-visualization">
        <h2>Visualisasi Profil</h2>
        <div class="radar-chart-placeholder">
            <div class="radar-chart">
                <?php
                // Simple hexagon visualization
                $angles = [
                    'R' => 90,
                    'I' => 30,
                    'A' => 330,
                    'S' => 270,
                    'E' => 210,
                    'C' => 150
                ];

                foreach ($angles as $code => $angle):
                    $score = $scores[$code] ?? 0;
                    $percentage = ($score / $maxScore) * 100;
                    $radius = 80 * ($percentage / 100);
                    $rad = deg2rad($angle - 90);
                    $x = 100 + $radius * cos($rad);
                    $y = 100 + $radius * sin($rad);
                ?>
                    <div class="radar-point" style="left: <?= $x ?>px; top: <?= $y ?>px; background: <?= $dimensionColors[$code] ?>">
                        <span><?= $code ?></span>
                    </div>
                <?php endforeach; ?>

                <!-- Connection lines (simplified) -->
                <svg class="radar-lines" viewBox="0 0 200 200">
                    <polygon points="100,20 170,60 170,140 100,180 30,140 30,60" fill="none" stroke="#e5e5e5" stroke-width="1" />
                    <polygon points="100,40 150,70 150,130 100,160 50,130 50,70" fill="none" stroke="#e5e5e5" stroke-width="1" />
                    <polygon points="100,60 130,80 130,120 100,140 70,120 70,80" fill="none" stroke="#e5e5e5" stroke-width="1" />
                    <line x1="100" y1="20" x2="100" y2="180" stroke="#e5e5e5" stroke-width="1" />
                    <line x1="30" y1="60" x2="170" y2="140" stroke="#e5e5e5" stroke-width="1" />
                    <line x1="170" y1="60" x2="30" y2="140" stroke="#e5e5e5" stroke-width="1" />
                </svg>
            </div>
            <p class="radar-note">Garis luar menunjukkan profil minat Anda</p>
        </div>
    </div>


    <!-- Category Legend -->
    <div class="riasec-legend">
        <h3>Kategori Skor</h3>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-badge" style="background: #10B981"></span>
                <span>Sangat Tinggi (25-28)</span>
            </div>
            <div class="legend-item">
                <span class="legend-badge" style="background: #3B6D11"></span>
                <span>Tinggi (19-24)</span>
            </div>
            <div class="legend-item">
                <span class="legend-badge" style="background: #F59E0B"></span>
                <span>Sedang (13-18)</span>
            </div>
            <div class="legend-item">
                <span class="legend-badge" style="background: #DC2626"></span>
                <span>Rendah (7-12)</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="riasec-actions">
        <a data-spa href="/profile/results" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6" />
            </svg>
            Kembali ke Profil
        </a>
        <a data-spa href="/tests/riasec" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
            </svg>
            Kerjakan Ulang
        </a>
    </div>

    <!-- Clear cache setelah submit berhasil -->
    <script>
        (function() {
            // Clear sessionStorage cache untuk sesi ini
            const sessionId = <?= $session['id'] ?? 'null' ?>;
            if (sessionId) {
                const storageKey = 'riasec_answers_' + sessionId;
                sessionStorage.removeItem(storageKey);
                console.log('Cleared cache for session:', sessionId);
            }

            // Cleanup expired entries
            const STORAGE_PREFIX = 'riasec_answers_';
            const STORAGE_TTL = 2 * 60 * 60 * 1000; // 2 jam
            const now = Date.now();

            for (let i = 0; i < sessionStorage.length; i++) {
                const key = sessionStorage.key(i);
                if (key && key.startsWith(STORAGE_PREFIX)) {
                    try {
                        const cached = sessionStorage.getItem(key);
                        if (cached) {
                            const data = JSON.parse(cached);
                            if (data.timestamp && (now - data.timestamp > STORAGE_TTL)) {
                                sessionStorage.removeItem(key);
                            }
                        }
                    } catch (e) {
                        sessionStorage.removeItem(key);
                    }
                }
            }
        })();
    </script>
</div>

<style>
    .riasec-results-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Header */
    .riasec-results-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2.5rem;
        text-align: center;
        color: #fff;
        margin-bottom: 2rem;
    }

    .holland-label {
        display: block;
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .holland-code {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .holland-code div {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
    }

    .results-date {
        font-size: 0.9rem;
        opacity: 0.8;
        margin: 0;
    }

    /* Description */
    .riasec-results-description {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .riasec-results-description h2 {
        font-size: 1.3rem;
        color: #1a1a1a;
        margin: 0 0 1rem 0;
    }

    .riasec-results-description p {
        color: #4a4a4a;
        line-height: 1.7;
        margin: 0;
    }

    /* Scores Section */
    .riasec-scores-section {
        margin-bottom: 2rem;
    }

    .riasec-scores-section h2 {
        font-size: 1.3rem;
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .scores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .score-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-top: 4px solid var(--card-color);
    }

    .score-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .dimension-code {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .dimension-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .score-card-body {
        text-align: center;
    }

    .score-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--card-color);
        margin-bottom: 0.5rem;
    }

    .score-bar {
        height: 8px;
        background: #e5e5e5;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.75rem;
    }

    .score-bar-fill {
        height: 100%;
        transition: width 0.5s ease;
    }

    .category-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
    }

    /* Visualization */
    .riasec-visualization {
        margin-bottom: 2rem;
    }

    .riasec-visualization h2 {
        font-size: 1.3rem;
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .radar-chart-placeholder {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .radar-chart {
        position: relative;
        width: 200px;
        height: 200px;
        margin-bottom: 1rem;
    }

    .radar-point {
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .radar-lines {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .radar-note {
        font-size: 0.85rem;
        color: #666;
        margin: 0;
    }

    .recommendation-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #4a4a4a;
    }

    .recommendation-item svg {
        color: #10B981;
        flex-shrink: 0;
    }

    /* Legend */
    .riasec-legend {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .riasec-legend h3 {
        font-size: 1rem;
        color: #1a1a1a;
        margin: 0 0 1rem 0;
    }

    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #4a4a4a;
    }

    .legend-badge {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Actions */
    .riasec-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .riasec-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .riasec-results-page {
            padding: 1rem;
        }

        .riasec-results-header {
            padding: 1.5rem;
        }

        .holland-code div {
            width: 45px;
            height: 45px;
            font-size: 1.5rem;
        }

        .scores-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .riasec-actions {
            flex-direction: column;
        }

        .riasec-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Alert Messages */
    .alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin: 1rem 1rem 1.5rem;
        font-size: 0.95rem;
    }

    .alert svg {
        flex-shrink: 0;
    }

    .alert-error {
        background-color: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }

    .alert-warning {
        background-color: #FFFBEB;
        border: 1px solid #FEE685;
        color: #92400E;
    }

    .alert-success {
        background-color: #F0FDF4;
        border: 1px solid #BBF7D0;
        color: #166534;
    }
</style>