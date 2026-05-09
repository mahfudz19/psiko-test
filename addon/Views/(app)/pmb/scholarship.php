<?php

/**
 * PMB Scholarship View - Scholarship Calculator
 *
 * @var array $scholarships Data dari AI dengan struktur:
 *   - eligible_scholarships: array beasiswa yang eligible
 *   - not_eligible_scholarships: array beasiswa yang tidak eligible
 *   - average_score: rata-rata nilai siswa
 *   - has_national_achievement: bool apakah punya prestasi nasional
 *   - technology_interest_level: string (low/medium/high)
 *
 * @var array|null $student_profile Data profil siswa
 * @var string|null $ai_error_message Error message jika AI gagal
 */
$scholarshipData = $scholarships ?? null;
$studentProfile = $student_profile ?? null;
$aiErrorMessage = $ai_error_message ?? null;
?>

<div class="pmb-scholarship-container">
    <!-- Header -->
    <div class="scholarship-header">
        <div class="header-content">
            <h1>💰 Kalkulator Beasiswa</h1>
            <p class="header-subtitle">Universitas Univeral</p>
            <p class="header-note">Analisis AI berdasarkan profil akademik dan prestasi kamu</p>
        </div>
    </div>

    <?php if ($aiErrorMessage): ?>
        <!-- AI Error Warning -->
        <div class="alert alert-warning">
            <strong>⚠️ AI Sedang Gangguan:</strong> <?= htmlspecialchars($aiErrorMessage) ?>
            <br>
            <small>Menampilkan data cached terakhir. Update profil untuk refresh.</small>
        </div>
    <?php endif; ?>

    <?php if ($scholarshipData && !empty($scholarshipData['eligible_scholarships'])): ?>
        <!-- User Eligibility (AI Generated) -->
        <div class="eligibility-section">
            <div class="section-header">
                <h2>🎯 Beasiswa yang Kamu Dapatkan</h2>
                <p>Berdasarkan analisis profil akademik dan prestasi kamu</p>
            </div>

            <div class="eligibility-cards">
                <?php foreach ($scholarshipData['eligible_scholarships'] as $scholarship): ?>
                    <div class="eligibility-card eligible">
                        <div class="eligibility-header">
                            <h4><?= htmlspecialchars($scholarship['name']) ?></h4>
                            <span class="status-badge status-eligible">
                                ✅ <?= $scholarship['discount'] ?>% OFF
                            </span>
                        </div>
                        <p class="eligibility-reason"><?= htmlspecialchars($scholarship['reason']) ?></p>
                        <p class="scholarship-description"><?= htmlspecialchars($scholarship['description'] ?? '') ?></p>
                        <div class="scholarship-meta">
                            <span class="meta-item">📚 Tipe: <?= htmlspecialchars($scholarship['type']) ?></span>
                            <span class="meta-item">📊 Kuota: <?= $scholarship['quota'] ?? '-' ?> slot</span>
                            <span class="meta-item">📅 Periode: <?= date('d M Y', strtotime($scholarship['start_date'] ?? 'today')) ?> - <?= date('d M Y', strtotime($scholarship['end_date'] ?? 'today')) ?></span>
                        </div>
                        <a href="<?= htmlspecialchars($scholarship['url'] ?? '/pmb/simulation') ?>"
                            class="btn btn-primary btn-sm"
                            target="_blank"
                            rel="noopener noreferrer">
                            Ajukan Beasiswa ↗
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Not Eligible Scholarships -->
        <?php if (!empty($scholarshipData['not_eligible_scholarships'])): ?>
            <div class="not-eligible-section">
                <div class="section-header">
                    <h2>📋 Beasiswa yang Belum Cocok</h2>
                    <p>Tingkatkan prestasimu untuk mendapatkan beasiswa ini</p>
                </div>

                <div class="not-eligible-cards">
                    <?php foreach ($scholarshipData['not_eligible_scholarships'] as $scholarship): ?>
                        <div class="eligibility-card not-eligible">
                            <div class="eligibility-header">
                                <h4><?= htmlspecialchars($scholarship['name']) ?></h4>
                                <span class="status-badge status-not-eligible">
                                    ⚪ Belum Eligible
                                </span>
                            </div>
                            <p class="eligibility-reason"><?= htmlspecialchars($scholarship['reason']) ?></p>
                            <p class="scholarship-description"><?= htmlspecialchars($scholarship['description'] ?? '') ?></p>
                            <div class="scholarship-meta">
                                <span class="meta-item">📚 Tipe: <?= htmlspecialchars($scholarship['type']) ?></span>
                                <span class="meta-item">📊 Kuota: <?= $scholarship['quota'] ?? '-' ?> slot</span>
                                <span class="meta-item">📅 Periode: <?= date('d M Y', strtotime($scholarship['start_date'] ?? 'today')) ?> - <?= date('d M Y', strtotime($scholarship['end_date'] ?? 'today')) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Student Profile Summary -->
        <div class="profile-summary-section">
            <div class="section-header">
                <h2>📊 Ringkasan Profil Kamu</h2>
                <p>Statistik akademik dan prestasi yang digunakan untuk analisis</p>
            </div>

            <div class="profile-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-value"><?= number_format($scholarshipData['average_score'] ?? 0, 1) ?></div>
                    <div class="stat-label">Rata-rata Nilai</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-value"><?= $scholarshipData['has_national_achievement'] ? '✅' : '❌' ?></div>
                    <div class="stat-label">Prestasi Nasional</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💻</div>
                    <div class="stat-value">
                        <?php
                        $interestLevel = $scholarshipData['technology_interest_level'] ?? 'unknown';
                        $icons = ['low' => '📱', 'medium' => '💻', 'high' => '🚀', 'unknown' => '❓'];
                        echo $icons[$interestLevel] ?? '❓';
                        ?>
                    </div>
                    <div class="stat-label">Minat Teknologi</div>
                    <div class="stat-sub"><?= ucfirst($interestLevel) ?></div>
                </div>
            </div>

            <?php if ($studentProfile): ?>
                <div class="profile-actions">
                    <a href="/profile/academic" class="btn btn-secondary">
                        📝 Update Nilai Akademik
                    </a>
                    <a href="/profile/achievements" class="btn btn-secondary">
                        🏆 Update Prestasi
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cost Estimation -->
        <div class="cost-estimation-section">
            <div class="section-header">
                <h2>💰 Estimasi Penghematan</h2>
                <p>Total diskon beasiswa yang bisa kamu dapatkan</p>
            </div>

            <div class="cost-card">
                <div class="cost-breakdown">
                    <?php
                    $maxDiscount = 0;
                    $bestScholarship = null;
                    foreach ($scholarshipData['eligible_scholarships'] as $s) {
                        if ($s['discount'] > $maxDiscount) {
                            $maxDiscount = $s['discount'];
                            $bestScholarship = $s;
                        }
                    }
                    ?>
                    <div class="cost-row">
                        <span class="cost-label">Beasiswa Terbaik</span>
                        <span class="cost-value"><?= htmlspecialchars($bestScholarship['name'] ?? '-') ?></span>
                    </div>
                    <div class="cost-row total">
                        <span class="cost-label">Diskon Maksimal</span>
                        <span class="cost-value highlight"><?= $maxDiscount ?>% OFF</span>
                    </div>
                </div>
                <div class="cost-savings">
                    <div class="savings-badge">
                        💰 Hemat hingga Rp <?= number_format(15000000 * $maxDiscount / 100, 0, ',', '.') ?>
                    </div>
                    <small>*Dari biaya kuliah normal Rp 15.000.000</small>
                </div>
            </div>

            <div class="cost-actions">
                <a href="/pmb/simulation" class="btn btn-primary btn-lg">
                    Lanjut ke Pendaftaran →
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- No Data -->
        <div class="no-data-section">
            <div class="no-data-content">
                <h2>📊 Belum Ada Analisis Beasiswa</h2>
                <p>Lengkapi profil akademik dan prestasi kamu untuk mendapatkan rekomendasi beasiswa yang personalize.</p>

                <?php if ($studentProfile): ?>
                    <button class="btn btn-primary" onclick="window.location.reload()">
                        🔄 Refresh Analisis
                    </button>
                <?php else: ?>
                    <a href="/profile/academic" class="btn btn-primary">Lengkapi Profil Akademik →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .scholarship-description {
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 8px 0 12px 0;
        line-height: 1.5;
        font-style: italic;
    }

    .scholarship-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 16px;
        padding-top: 8px;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .meta-item {
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant, #666);
    }

    .eligibility-card .btn {
        text-decoration: none;
        text-align: center;
    }

    /* Container */
    .pmb-scholarship-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    /* Header */
    .scholarship-header {
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc) 0%, var(--md-sys-color-primary-container, #e6f0ff) 100%);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        color: white;
    }

    .scholarship-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-subtitle {
        font-size: 18px;
        opacity: 0.9;
        margin: 0 0 16px 0;
    }

    .header-note {
        font-size: 14px;
        opacity: 0.85;
        margin: 0;
    }

    /* Alert Warning */
    .alert {
        background: var(--md-sys-color-tertiary-container, #fff3cd);
        border-left: 4px solid var(--md-sys-color-warning, #ff9800);
        border-radius: 8px;
        padding: 16px 20px;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .alert strong {
        color: var(--md-sys-color-warning, #cc7a00);
    }

    .alert small {
        color: var(--md-sys-color-on-surface-variant, #666);
        font-size: 13px;
    }

    /* Section Headers */
    .section-header {
        margin-bottom: 24px;
    }

    .section-header h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--md-sys-color-on-surface, #1a1a1a);
        margin: 0 0 8px 0;
    }

    .section-header p {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0;
    }

    /* Eligibility Section */
    .eligibility-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .eligibility-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 16px;
    }

    .eligibility-card {
        background: var(--md-sys-color-surface-container-low, #f5f5f5);
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid var(--md-sys-color-outline, #e0e0e0);
        transition: all 0.2s;
    }

    .eligibility-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .eligibility-card.eligible {
        border-left-color: var(--md-sys-color-secondary, #4caf50);
        background: var(--md-sys-color-secondary-container, #e8f5e9);
    }

    .eligibility-card.not-eligible {
        border-left-color: var(--md-sys-color-outline, #e0e0e0);
        background: var(--md-sys-color-surface-container-lowest, #fafafa);
        opacity: 0.8;
    }

    .eligibility-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .eligibility-header h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .status-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 12px;
    }

    .status-eligible {
        background: var(--md-sys-color-secondary, #4caf50);
        color: white;
    }

    .status-not-eligible {
        background: var(--md-sys-color-outline, #e0e0e0);
        color: var(--md-sys-color-on-surface, #1a1a1a);
    }

    .eligibility-reason {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin: 0 0 12px 0;
        line-height: 1.5;
    }


    /* Not Eligible Section */
    .not-eligible-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .not-eligible-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 12px;
    }

    /* Profile Summary Section */
    .profile-summary-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--md-sys-color-surface-container, #f5f5f5);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.2s;
    }

    .stat-card:hover {
        background: var(--md-sys-color-surface-container-high, #eeeeee);
        transform: translateY(-2px);
    }

    .stat-icon {
        font-size: 36px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--md-sys-color-primary, #0066cc);
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 13px;
        color: var(--md-sys-color-on-surface-variant, #666);
        margin-bottom: 4px;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--md-sys-color-primary, #0066cc);
        font-weight: 600;
    }

    .profile-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    /* Cost Estimation */
    .cost-estimation-section {
        background: var(--md-sys-color-surface, #ffffff);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cost-card {
        background: linear-gradient(135deg, var(--md-sys-color-primary, #0066cc), var(--md-sys-color-primary-container, #e6f0ff));
        border-radius: 12px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
    }

    .cost-breakdown {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .cost-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .cost-row:last-child {
        border-bottom: none;
    }

    .cost-row.total {
        font-size: 18px;
        font-weight: 700;
        padding-top: 16px;
        border-bottom: none;
    }

    .cost-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .cost-value {
        font-size: 16px;
        font-weight: 600;
    }

    .cost-value.highlight {
        font-size: 28px;
        color: #fff;
    }

    .cost-savings {
        text-align: center;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .savings-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .cost-savings small {
        display: block;
        font-size: 12px;
        opacity: 0.8;
    }

    .cost-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
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
        .pmb-scholarship-container {
            padding: 16px;
        }

        .scholarship-header {
            padding: 24px;
        }

        .scholarship-header h1 {
            font-size: 24px;
        }

        .eligibility-cards,
        .not-eligible-cards,
        .profile-stats-grid {
            grid-template-columns: 1fr;
        }

        .cost-actions {
            flex-direction: column;
        }

        .cost-actions .btn {
            width: 100%;
        }

        .profile-actions {
            flex-direction: column;
        }

        .profile-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    /**
     * Apply scholarship
     * @param {string} scholarshipName - Nama beasiswa yang akan diajukan
     */
    function applyScholarship(scholarshipName) {
        if (confirm('Apakah kamu yakin ingin mengajukan beasiswa "' + scholarshipName + '"?\n\nKamu akan diarahkan ke halaman pendaftaran untuk melengkapi dokumen.')) {
            // TODO: Implement actual application
            // Redirect to simulation/registration page
            window.location.href = '/pmb/simulation?scholarship=' + encodeURIComponent(scholarshipName);
        }
    }

    /**
     * Refresh analysis - reload page to get fresh data from AI
     */
    function refreshAnalysis() {
        // Show loading state
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '🔄 Memuat ulang...';

        // Reload page
        window.location.reload();
    }
</script>