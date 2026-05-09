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
        <div class="alert alert-warning" style="margin: 20px; border-radius: 8px; padding: 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">⚠️</span>
            <div>
                <strong>AI sedang sibuk!</strong> Menampilkan data rekomendasi Anda sebelumnya.<br>
                <small style="opacity: 0.8;">Detail error: <?= htmlspecialchars($ai_error_message) ?></small>
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
            <!-- No Match Score Data -->
            <div class="no-data-section">
                <div class="no-data-content">
                    <h2>📊 Data Analisis Belum Tersedia</h2>
                    <p>Sebelum melihat match score, kamu perlu menyelesaikan analisis potensi dan minat terlebih dahulu.</p>
                    <a href="/profile/results" class="btn btn-primary">Lengkapi Analisis AI →</a>
                </div>
            </div>
        <?php endif; ?>
        </div>

        <style>
            /* Container */
            .pmb-journey-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 24px;
                display: flex;
                flex-direction: column;
                gap: 40px;
            }

            /* Hero Section */
            .journey-hero {
                background: linear-gradient(135deg, var(--primary-main, #0066cc) 0%, var(--primary-light, #e6f0ff) 100%);
                border-radius: 16px;
                padding: 40px;
                color: white;
                text-align: center;
            }

            .journey-hero h1 {
                font-size: 32px;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .hero-subtitle {
                font-size: 18px;
                opacity: 0.9;
                margin-bottom: 16px;
            }

            .hero-greeting {
                font-size: 16px;
                opacity: 0.85;
            }

            .hero-greeting strong {
                color: #fff;
            }

            /* Section Headers */
            .section-header {
                margin-bottom: 24px;
            }

            .section-header h2 {
                font-size: 24px;
                font-weight: 600;
                color: var(--text-primary, #1a1a1a);
                margin: 0 0 8px 0;
            }

            .section-description {
                font-size: 14px;
                color: var(--text-secondary, #666);
                margin: 0;
            }

            /* Match Score Section */
            .match-score-section {
                background: var(--bg-paper, #ffffff);
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            /* Top Match Card */
            .top-match-card {
                background: var(--bg-paper, #ffffff);
                border: 2px solid var(--primary-main, #0066cc);
                border-radius: 12px;
                padding: 24px;
                margin-bottom: 24px;
            }

            /* No Top Match */
            .no-top-match {
                text-align: center;
                padding: 32px;
                background: var(--bg-subtle, #f8f9fa);
                border-radius: 12px;
                border: 1px dashed var(--border-default, #e0e0e0);
            }

            .no-top-match p {
                font-size: 14px;
                color: var(--text-secondary, #666);
                margin: 0;
            }

            .match-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
            }

            .program-info h3 {
                font-size: 22px;
                font-weight: 600;
                margin: 0 0 8px 0;
                color: var(--text-primary, #1a1a1a);
            }

            .program-meta {
                display: flex;
                gap: 8px;
            }

            .badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 16px;
                font-size: 12px;
                font-weight: 500;
            }

            .badge-degree {
                background: var(--primary-light, #e6f0ff);
                color: var(--primary-dark, #004c99);
            }

            .badge-accreditation {
                background: var(--success-light, #e8f5e9);
                color: var(--success-dark, #2e7d32);
            }

            /* Score Circle */
            .match-score-circle {
                width: 100px;
                height: 100px;
                position: relative;
            }

            .circular-chart {
                width: 100%;
                height: 100%;
                transform: rotate(-90deg);
            }

            .circle-bg {
                fill: none;
                stroke: var(--border-default, #e0e0e0);
                stroke-width: 3.5;
            }

            .circle {
                fill: none;
                stroke: var(--primary-main, #0066cc);
                stroke-width: 3.5;
                stroke-linecap: round;
                transition: stroke-dasharray 0.5s ease;
            }

            .score-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .score-number {
                font-size: 24px;
                font-weight: 700;
                color: var(--primary-main, #0066cc);
            }

            .score-label {
                font-size: 11px;
                color: var(--text-secondary, #666);
                text-transform: uppercase;
            }

            /* Skills Breakdown */
            .skills-breakdown {
                margin-bottom: 24px;
            }

            .skills-breakdown h4 {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 16px;
                color: var(--text-primary, #1a1a1a);
            }

            .skill-bar {
                margin-bottom: 12px;
            }

            .skill-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 4px;
            }

            .skill-name {
                font-size: 14px;
                color: var(--text-primary, #1a1a1a);
            }

            .skill-score {
                font-size: 14px;
                font-weight: 600;
                color: var(--primary-main, #0066cc);
            }

            .skill-progress {
                height: 8px;
                background: var(--border-default, #e0e0e0);
                border-radius: 4px;
                overflow: hidden;
            }

            .skill-progress-bar {
                height: 100%;
                background: linear-gradient(90deg, var(--primary-main, #0066cc), var(--primary-light, #e6f0ff));
                border-radius: 4px;
                transition: width 0.5s ease;
            }

            /* Why This Program */
            .why-this-program h4 {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 12px;
                color: var(--text-primary, #1a1a1a);
            }

            .reasons-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 12px;
            }

            .reason-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px;
                background: var(--bg-subtle, #f5f5f5);
                border-radius: 8px;
                font-size: 14px;
                color: var(--text-primary, #1a1a1a);
            }

            .reason-icon {
                font-size: 18px;
            }

            /* Other Matches */
            .other-matches-section h4 {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 16px;
                color: var(--text-primary, #1a1a1a);
            }

            .other-matches-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 16px;
            }

            .other-match-card {
                background: var(--bg-paper, #ffffff);
                border: 1px solid var(--border-default, #e0e0e0);
                border-radius: 8px;
                padding: 16px;
            }

            .match-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .match-info h5 {
                font-size: 14px;
                font-weight: 600;
                margin: 0;
                color: var(--text-primary, #1a1a1a);
            }

            .match-percentage {
                font-size: 12px;
                font-weight: 600;
                color: var(--primary-main, #0066cc);
            }

            .match-bar {
                height: 6px;
                background: var(--border-default, #e0e0e0);
                border-radius: 3px;
                overflow: hidden;
            }

            .match-progress {
                height: 100%;
                background: var(--primary-main, #0066cc);
                border-radius: 3px;
                transition: width 0.5s ease;
            }

            /* Career Path Timeline */
            .career-path-section {
                background: var(--bg-paper, #ffffff);
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .timeline-container {
                display: flex;
                flex-direction: column;
                gap: 0;
            }

            .timeline-item {
                display: flex;
                gap: 16px;
                position: relative;
            }

            .timeline-marker {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: var(--primary-light, #e6f0ff);
                border-radius: 50%;
                flex-shrink: 0;
                z-index: 1;
            }

            .marker-icon {
                font-size: 20px;
            }

            .timeline-content {
                flex: 1;
                background: var(--bg-subtle, #f5f5f5);
                border-radius: 12px;
                padding: 16px;
            }

            .timeline-header {
                display: flex;
                gap: 12px;
                align-items: center;
                margin-bottom: 8px;
            }

            .timeline-semester {
                font-size: 12px;
                font-weight: 600;
                color: var(--primary-main, #0066cc);
                background: white;
                padding: 4px 8px;
                border-radius: 4px;
            }

            .timeline-title {
                font-size: 16px;
                font-weight: 600;
                color: var(--text-primary, #1a1a1a);
            }

            .timeline-description {
                font-size: 14px;
                color: var(--text-secondary, #666);
                margin: 0;
            }

            .timeline-connector {
                position: absolute;
                left: 19px;
                top: 40px;
                width: 2px;
                height: calc(100% - 40px);
                background: var(--border-default, #e0e0e0);
            }

            /* Partner Section */
            .partner-section {
                background: var(--bg-paper, #ffffff);
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .partners-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
            }

            .partner-card {
                background: var(--bg-subtle, #f5f5f5);
                border-radius: 12px;
                padding: 20px;
                text-align: center;
            }

            .partner-card h4 {
                font-size: 16px;
                font-weight: 600;
                margin: 0 0 4px 0;
                color: var(--text-primary, #1a1a1a);
            }

            .partner-card p {
                font-size: 12px;
                color: var(--text-secondary, #666);
                margin: 0;
            }

            /* Scholarship Section */
            .scholarship-section {
                background: var(--bg-paper, #ffffff);
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .scholarships-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 16px;
                margin-bottom: 24px;
            }

            .scholarship-card {
                background: var(--bg-subtle, #f5f5f5);
                border-radius: 12px;
                padding: 20px;
                border-left: 4px solid var(--border-default, #e0e0e0);
            }

            .scholarship-card.eligible {
                border-left-color: var(--success-main, #4caf50);
                background: var(--success-light, #e8f5e9);
            }

            .scholarship-card.check_eligibility {
                border-left-color: var(--primary-main, #0066cc);
            }

            .scholarship-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .scholarship-header h4 {
                font-size: 16px;
                font-weight: 600;
                margin: 0;
                color: var(--text-primary, #1a1a1a);
            }

            .scholarship-badge {
                background: var(--primary-main, #0066cc);
                color: white;
                padding: 4px 12px;
                border-radius: 16px;
                font-size: 12px;
                font-weight: 600;
            }

            .scholarship-requirement {
                font-size: 14px;
                color: var(--text-secondary, #666);
                margin: 0 0 12px 0;
            }

            .status-badge {
                font-size: 12px;
                font-weight: 500;
            }

            .status-eligible {
                color: var(--success-main, #4caf50);
            }

            .status-check {
                color: var(--primary-main, #0066cc);
            }

            .scholarship-cta {
                text-align: center;
            }

            /* Alumni Section */
            .alumni-section {
                background: var(--bg-paper, #ffffff);
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .testimonials-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
                margin-bottom: 24px;
            }

            .testimonial-card {
                background: var(--bg-subtle, #f5f5f5);
                border-radius: 12px;
                padding: 20px;
            }

            .testimonial-header {
                display: flex;
                gap: 12px;
                align-items: center;
                margin-bottom: 16px;
            }

            .testimonial-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: var(--md-sys-color-primary, #0066cc);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                font-weight: 600;
                flex-shrink: 0;
            }

            .testimonial-info h4 {
                font-size: 16px;
                font-weight: 600;
                margin: 0 0 4px 0;
                color: var(--text-primary, #1a1a1a);
            }

            .testimonial-school,
            .testimonial-similarity {
                font-size: 12px;
                color: var(--text-secondary, #666);
                margin: 0;
            }

            .testimonial-content {
                font-size: 14px;
                color: var(--text-primary, #1a1a1a);
                margin: 0 0 12px 0;
                font-style: italic;
                line-height: 1.6;
            }

            .testimonial-status {
                font-size: 13px;
                font-weight: 600;
                color: var(--primary-main, #0066cc);
            }

            .fomo-stat {
                text-align: center;
                padding: 16px;
                background: var(--primary-light, #e6f0ff);
                border-radius: 8px;
            }

            .fomo-stat p {
                margin: 0;
                font-size: 14px;
                color: var(--primary-dark, #004c99);
            }

            /* Simulation CTA Section */
            .simulation-cta-section {
                background: linear-gradient(135deg, var(--primary-main, #0066cc) 0%, var(--primary-dark, #004c99) 100%);
                border-radius: 16px;
                padding: 32px;
                color: white;
            }

            .simulation-cta-card {
                max-width: 600px;
                margin: 0 auto;
            }

            .cta-header {
                text-align: center;
                margin-bottom: 24px;
            }

            .cta-header h2 {
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 8px 0;
            }

            .cta-header p {
                font-size: 14px;
                opacity: 0.9;
                margin: 0;
            }

            .simulation-progress-bar {
                height: 12px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 6px;
                overflow: hidden;
                margin-bottom: 24px;
            }

            .progress-fill {
                height: 100%;
                background: white;
                border-radius: 6px;
                transition: width 0.5s ease;
            }

            .steps-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 24px;
            }

            .step-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                font-size: 14px;
            }

            .step-item.completed .step-icon {
                color: #4caf50;
            }

            .step-item.pending .step-icon {
                opacity: 0.7;
            }

            .step-name {
                flex: 1;
            }

            .cta-actions {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 16px;
            }

            .special-offer {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 20px;
                background: rgba(255, 255, 255, 0.15);
                border-radius: 8px;
                font-size: 14px;
            }

            .offer-icon {
                font-size: 18px;
            }

            /* No Data Section */
            .no-data-section {
                background: var(--bg-paper, #ffffff);
                border-radius: 16px;
                padding: 48px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .no-data-content h2 {
                font-size: 24px;
                font-weight: 600;
                margin: 0 0 16px 0;
                color: var(--text-primary, #1a1a1a);
            }

            .no-data-content p {
                font-size: 16px;
                color: var(--text-secondary, #666);
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
                background: var(--primary-main, #0066cc);
                color: white;
            }

            .btn-primary:hover {
                background: var(--primary-dark, #0052a3);
            }

            .btn-secondary {
                background: var(--primary-light, #e6f0ff);
                color: var(--primary-dark, #004c99);
            }

            .btn-secondary:hover {
                background: var(--primary-main, #0066cc);
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
                .pmb-journey-container {
                    padding: 16px;
                    gap: 24px;
                }

                .journey-hero {
                    padding: 24px;
                }

                .journey-hero h1 {
                    font-size: 24px;
                }

                .match-header {
                    flex-direction: column;
                    gap: 20px;
                    align-items: flex-start;
                }

                .reasons-grid,
                .other-matches-grid,
                .scholarships-grid,
                .testimonials-grid,
                .partners-grid {
                    grid-template-columns: 1fr;
                }

                .timeline-item {
                    flex-direction: column;
                }

                .timeline-connector {
                    left: 19px;
                    top: 40px;
                }

                .simulation-cta-section {
                    padding: 24px;
                }
            }
        </style>