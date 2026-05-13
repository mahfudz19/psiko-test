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

<link rel="stylesheet" href="<?= asset('addon/Views/(app)/pmb/scholarship.css') ?>">

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
                    <a data-spa href="/profile/academic" class="btn btn-secondary">
                        📝 Update Nilai Akademik
                    </a>
                    <a data-spa href="/profile/achievements" class="btn btn-secondary">
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
                <a data-spa href="/pmb/simulation" class="btn btn-primary btn-lg">
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
                    <a data-spa href="/profile/academic" class="btn btn-primary">Lengkapi Profil Akademik →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    /**
     * Apply scholarship
     * @param {string} scholarshipName - Nama beasiswa yang akan diajukan
     */
    function applyScholarship(scholarshipName) {
        if (confirm('Apakah kamu yakin ingin mengajukan beasiswa "' + scholarshipName + '"?\n\nKamu akan diarahkan ke halaman pendaftaran untuk melengkapi dokumen.')) {
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