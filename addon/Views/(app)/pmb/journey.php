<?php

/**
 * PMB Journey View - Match Score Dashboard
 * 
 * @var array $match_score Match score data
 * @var array|null $profile User profile
 * @var array|null $student_profile Student profile data
 */

$matchScore = $match_score ?? null;
$userName = $profile['user_name'] ?? 'Siswa';
?>

<link rel="stylesheet" href="<?= asset('addon/Views/(app)/pmb/journey.css') ?>">

<div class="pmb-journey-container">
    <!-- Hero Section -->
    <div class="journey-hero">
        <div class="hero-content">
            <h1>🎯 Journey Kamu ke Univeral</h1>
            <p class="hero-subtitle">"Kenalimu lebih dekat, masa depan lebih jelas"</p>
            <p class="hero-greeting">Halo, <strong><?= htmlspecialchars($userName) ?></strong>! Mari kita lihat potensi masa depanmu.</p>
        </div>
    </div>

    <?php if (!empty($ai_error_message)): ?>
        <div class="alert alert-warning" style="margin: 0; border-radius: 16px; padding: 20px; background: var(--warning-light, #fff3cd); color: var(--warning-dark, #856404); border: 1px solid var(--warning-main, #ffeeba); display: flex; align-items: center; gap: 15px; box-shadow: var(--journey-card-shadow);">
            <span style="font-size: 2rem;">⚠️</span>
            <div>
                <strong style="font-size: 1.1rem; display: block; margin-bottom: 4px;">AI sedang sibuk!</strong>
                <span style="font-size: 0.95rem;">Menampilkan data rekomendasi Anda sebelumnya.</span><br>
                <small style="opacity: 0.8; font-size: 0.8rem;">Detail error: <?= htmlspecialchars($ai_error_message) ?></small>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($matchScore): ?>
        <!-- Main Match Score Card -->
        <div class="match-score-section">
            <div class="section-header">
                <h2>🎓 Kecocokan Program Studi</h2>
                <p class="section-description">Berdasarkan analisis potensi, minat, dan bakat kamu</p>
            </div>

            <!-- Top Match Card -->
            <div class="top-match-card">
                <?php if (!empty($matchScore['top_match'])): ?>
                    <div class="match-header">
                        <div class="program-info">
                            <h3><?= htmlspecialchars($matchScore['top_match']['study_program'] ?? 'Program Studi Belum Ditentukan') ?></h3>
                            <?php if (!empty($matchScore['top_match']['degree_type']) || !empty($matchScore['top_match']['accreditation'])): ?>
                                <div class="program-meta">
                                    <?php if (!empty($matchScore['top_match']['degree_type'])): ?>
                                        <span class="badge badge-degree"><?= htmlspecialchars($matchScore['top_match']['degree_type']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($matchScore['top_match']['accreditation'])): ?>
                                        <span class="badge badge-accreditation">Akreditasi <?= htmlspecialchars($matchScore['top_match']['accreditation']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($matchScore['top_match']['match_percentage'])): ?>
                            <div class="match-score-circle" data-score="<?= $matchScore['top_match']['match_percentage'] ?>">
                                <svg viewBox="0 0 36 36" class="circular-chart">
                                    <path class="circle-bg"
                                        d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="circle"
                                        stroke-dasharray="<?= $matchScore['top_match']['match_percentage'] ?>, 100"
                                        d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div class="score-text">
                                    <span class="score-number"><?= $matchScore['top_match']['match_percentage'] ?>%</span>
                                    <span class="score-label">Match</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Skills Breakdown -->
                    <?php if (!empty($matchScore['top_match']['skills_breakdown'])): ?>
                        <div class="skills-breakdown">
                            <h4>Analisis Kompetensi</h4>
                            <?php foreach ($matchScore['top_match']['skills_breakdown'] as $skill): ?>
                                <div class="skill-bar">
                                    <div class="skill-info">
                                        <span class="skill-name"><?= htmlspecialchars($skill['name'] ?? '') ?></span>
                                        <span class="skill-score"><?= $skill['score'] ?? 0 ?>%</span>
                                    </div>
                                    <div class="skill-progress">
                                        <div class="skill-progress-bar" style="width: <?= $skill['score'] ?? 0 ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Kenapa Cocok? (Alasan dari AI) -->
                    <div class="why-this-program">
                        <h4>Kenapa Program Ini Cocok Untuk Kamu?</h4>
                        <div class="reason-ai-summary">
                            <p><?= htmlspecialchars($matchScore['top_match']['reason'] ?? 'Berdasarkan profil Anda, jurusan ini menawarkan peluang terbaik untuk pengembangan karier dan minat Anda.') ?></p>
                        </div>
                        <div class="reasons-grid">
                            <div class="reason-item">
                                <span class="reason-icon">✅</span>
                                <span>Program ini ada di <?= env('APP_NAME') ?></span>
                            </div>
                            <?php if (!empty($matchScore['top_match']['accreditation'])): ?>
                                <div class="reason-item">
                                    <span class="reason-icon">🏆</span>
                                    <span>Akreditasi <?= htmlspecialchars($matchScore['top_match']['accreditation']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($matchScore['top_match']['degree_type'])): ?>
                                <div class="reason-item">
                                    <span class="reason-icon">🎓</span>
                                    <span>Jenjang <?= htmlspecialchars($matchScore['top_match']['degree_type']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-top-match">
                        <p>⚠️ Data program studi terbaik belum tersedia. Silakan lengkapi profil Anda terlebih dahulu.</p>
                    </div>
                <?php endif; ?>

                <!-- Other Matches -->
                <?php if (!empty($matchScore['other_matches']) && is_array($matchScore['other_matches'])): ?>
                    <div class="other-matches-section">
                        <h4>Program Studi Lain yang Cocok</h4>
                        <div class="other-matches-grid">
                            <?php foreach ($matchScore['other_matches'] as $other): ?>
                                <div class="other-match-card">
                                    <div class="match-info">
                                        <h5><?= htmlspecialchars($other['study_program'] ?? 'Program Studi') ?></h5>
                                        <span class="match-percentage"><?= $other['match_percentage'] ?? 0 ?>% match</span>
                                    </div>
                                    <div class="match-bar">
                                        <div class="match-progress" style="width: <?= $other['match_percentage'] ?? 0 ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Career Path Timeline -->
            <?php if (!empty($matchScore['career_paths']) && is_array($matchScore['career_paths'])): ?>
                <div class="career-path-section">
                    <div class="section-header">
                        <h2>📊 Masa Depanmu di <?= env('APP_NAME') ?></h2>
                        <p class="section-description">Perjalanan akademismu dari semester 1 hingga graduation</p>
                    </div>

                    <div class="timeline-container">
                        <?php foreach ($matchScore['career_paths'] as $index => $path): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <span class="marker-icon">
                                        <?php if ($index === 0): ?>📚
                                        <?php elseif ($index === 1): ?>🎯
                                        <?php elseif ($index === 2): ?>💼
                                        <?php else: ?>🎓<?php endif; ?>
                                    </span>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-semester"><?= htmlspecialchars($path['semester'] ?? '') ?></span>
                                        <span class="timeline-title"><?= htmlspecialchars($path['title'] ?? '') ?></span>
                                    </div>
                                    <p class="timeline-description"><?= htmlspecialchars($path['description'] ?? '') ?></p>
                                </div>
                                <?php if ($index < count($matchScore['career_paths']) - 1): ?>
                                    <div class="timeline-connector"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Partner Companies -->
            <?php if (!empty($matchScore['partner_companies']) && is_array($matchScore['partner_companies'])): ?>
                <div class="partner-section">
                    <div class="section-header">
                        <h2>🏢 Partner Companies untuk Internship</h2>
                    </div>
                    <div class="partners-grid">
                        <?php foreach ($matchScore['partner_companies'] as $company): ?>
                            <div class="partner-card">
                                <h4><?= htmlspecialchars($company['name'] ?? 'Partner') ?></h4>
                                <p><?= htmlspecialchars($company['type'] ?? '') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Scholarship Section -->
            <?php if (!empty($matchScore['scholarships']) && is_array($matchScore['scholarships'])): ?>
                <div class="scholarship-section">
                    <div class="section-header">
                        <h2>💰 Beasiswa yang Kamu Dapatkan</h2>
                        <p class="section-description">Berdasarkan prestasi dan kondisi kamu</p>
                    </div>

                    <div class="scholarships-grid">
                        <?php foreach ($matchScore['scholarships'] as $scholarship): ?>
                            <div class="scholarship-card <?= htmlspecialchars($scholarship['status'] ?? 'check_eligibility') ?>">
                                <div class="scholarship-header">
                                    <h4><?= htmlspecialchars($scholarship['name'] ?? 'Beasiswa') ?></h4>
                                    <span class="scholarship-badge">
                                        <?= $scholarship['discount'] ?? 0 ?>% Discount
                                    </span>
                                </div>
                                <p class="scholarship-requirement"><?= htmlspecialchars($scholarship['requirement'] ?? '') ?></p>
                                <div class="scholarship-status">
                                    <?php if ($scholarship['status'] === 'eligible'): ?>
                                        <span class="status-badge status-eligible">✅ Kamu Lulus</span>
                                    <?php elseif ($scholarship['status'] === 'check_eligibility'): ?>
                                        <span class="status-badge status-check">⏳ Cek Eligibility</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($scholarship['status'] === 'check_eligibility'): ?>
                                    <button class="btn btn-secondary btn-sm">Cek Sekarang</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="scholarship-cta">
                        <a href="/pmb/scholarship" class="btn btn-primary">Hitung Beasiswa Lengkap →</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Simulation CTA -->
            <?php if (!empty($matchScore['simulation_progress']) && is_array($matchScore['simulation_progress'])): ?>
                <div class="simulation-cta-section">
                    <div class="simulation-cta-card">
                        <div class="cta-header">
                            <h2>🚀 Mulai Simulasi Pendaftaran</h2>
                            <p>Progress kamu: <strong><?= ($matchScore['simulation_progress']['completed_steps'] ?? 0) ?>/<?= ($matchScore['simulation_progress']['total_steps'] ?? 0) ?> steps</strong></p>
                        </div>

                        <?php
                        $completed = $matchScore['simulation_progress']['completed_steps'] ?? 0;
                        $total = $matchScore['simulation_progress']['total_steps'] ?? 0;
                        $progressPercent = $total > 0 ? ($completed / $total) * 100 : 0;
                        ?>
                        <div class="simulation-progress-bar">
                            <div class="progress-fill" style="width: <?= $progressPercent ?>%"></div>
                        </div>

                        <?php if (!empty($matchScore['simulation_progress']['steps']) && is_array($matchScore['simulation_progress']['steps'])): ?>
                            <div class="steps-list">
                                <?php foreach ($matchScore['simulation_progress']['steps'] as $step): ?>
                                    <div class="step-item <?= ($step['is_completed'] ?? false) ? 'completed' : 'pending' ?>">
                                        <span class="step-icon"><?= ($step['is_completed'] ?? false) ? '✅' : '❌' ?></span>
                                        <span class="step-name"><?= htmlspecialchars($step['name'] ?? 'Step') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="cta-actions">
                            <a href="/pmb/simulation" class="btn btn-primary btn-lg">Lanjut ke Pendaftaran</a>
                            <div class="special-offer">
                                <span class="offer-icon">🎁</span>
                                <span>Daftar minggu ini, <strong>gratis biaya pendaftaran!</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Data -->
            <div class="no-data-section">
                <div class="no-data-content">
                    <h2>📊 Data Analisis Belum Tersedia</h2>
                    <p>Sebelum melihat match score, kamu perlu menyelesaikan analisis potensi dan minat terlebih dahulu.</p>
                    <a href="/profile/results" class="btn btn-primary">Lengkapi Analisis AI →</a>
                </div>
            </div>
        <?php endif; ?>
        </div>
</div>