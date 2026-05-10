<?php

/**
 * Dashboard Siswa - Main View
 * Menampilkan overview profil, match score, quick actions, dan next steps
 * 
 * @var mixed $userName
 * @var mixed $role
 * @var int $profileProgress
 * @var mixed $matchScore
 * @var mixed $topMajor
 * @var array|null $aiRecommendations
 * @var mixed $pmbStatus
 * @var int $eligibleScholarshipsCount
 * @var array|null $studentProfile
 * @var array|null $profile
 */
?>
<div class="student-dashboard-container">
  <main class="dashboard-main">
    <!-- Welcome Section -->
    <section class="dashboard-welcome">
      <div class="welcome-content">
        <div class="welcome-text">
          <h1 class="welcome-title">Halo, <?= htmlspecialchars($userName) ?>! 👋</h1>
          <p class="welcome-subtitle">
            <?php if ($profileProgress < 100): ?>
              Profil kamu baru <strong><?= $profileProgress ?>%</strong> lengkap. Yuk, lengkapi data akademikmu untuk analisis yang lebih akurat!
            <?php else: ?>
              Profil kamu sudah lengkap! Kamu siap untuk mengeksplorasi rekomendasi kampus terbaik.
            <?php endif; ?>
          </p>
        </div>
        <div class="welcome-avatar">
          <img src="<?= $profile['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=6366f1&color=fff' ?>" alt="Avatar" class="avatar-image">
        </div>
      </div>
    </section>

    <!-- Match Score Section (Hanya jika sudah ada analisis) -->
    <?php if ($matchScore > 0): ?>
      <section class="match-score-section">
        <div class="match-score-card">
          <div class="match-score-content">
            <div class="match-score-header">
              <span class="match-score-badge">Top Match</span>
              <div class="circular-progress">
                <svg viewBox="0 0 36 36" class="circular-chart">
                  <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                  <path class="circle" stroke-dasharray="<?= $matchScore ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                  <text x="18" y="20.35" class="score-number"><?= $matchScore ?>%</text>
                </svg>
              </div>
            </div>
            <div class="match-score-info">
              <h2 class="match-score-title"><?= htmlspecialchars($topMajor) ?></h2>
              <p class="match-score-desc">Berdasarkan minat, bakat, dan nilai akademikmu, jurusan ini memiliki kecocokan tertinggi.</p>
              <div class="match-score-actions">
                <a href="/pmb/journey" class="btn btn-primary btn-sm">Lihat Detail Analisis</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- Stats Grid -->
    <section class="stats-grid">
      <!-- Profile Progress -->
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Kelengkapan Profil</span>
          <div class="stat-value"><?= $profileProgress ?>%</div>
          <div class="stat-progress">
            <div class="stat-progress-bar" style="width: <?= $profileProgress ?>%; background: #6366f1;"></div>
          </div>
          <a href="/profile/edit" class="stat-link">Lengkapi Profil &rarr;</a>
        </div>
      </div>

      <!-- PMB Journey Status -->
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 11 12 14 22 4"></polyline>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Status PMB</span>
          <div class="stat-value">
            <?php
            $statusLabels = [
              'not_started' => 'Belum Mulai',
              'in_progress' => 'Sedang Berjalan',
              'completed' => 'Selesai Simulasi',
              'converted' => 'Terdaftar'
            ];
            echo $statusLabels[$pmbStatus] ?? 'Unknown';
            ?>
          </div>
          <div class="stat-status">
            <span class="status-dot" style="background: <?= $pmbStatus === 'not_started' ? '#94a3b8' : '#10b981' ?>;"></span>
            <span><?= $pmbStatus === 'not_started' ? 'Siap untuk simulasi' : 'Terus pantau progresmu' ?></span>
          </div>
          <a href="/pmb/journey" class="stat-link">Lihat Journey &rarr;</a>
        </div>
      </div>

      <!-- Scholarship Eligibility -->
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
            <path d="M2 17l10 5 10-5"></path>
            <path d="M2 12l10 5 10-5"></path>
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Peluang Beasiswa</span>
          <div class="stat-value"><?= $eligibleScholarshipsCount ?> Program</div>
          <p class="stat-desc" style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">Kamu memenuhi syarat untuk <?= $eligibleScholarshipsCount ?> beasiswa.</p>
          <a href="/pmb/scholarship" class="stat-link">Cek Beasiswa &rarr;</a>
        </div>
      </div>
    </section>

    <!-- Quick Actions -->
    <section class="quick-actions-section">
      <h3 class="section-title">Akses Cepat</h3>
      <div class="quick-actions-grid">
        <a href="/profile/results" class="quick-action-card">
          <div class="quick-action-icon" style="background: #eef2ff; color: #6366f1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
              <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
          </div>
          <div class="quick-action-info">
            <span class="quick-action-label">Hasil Tes</span>
            <span class="quick-action-desc">Lihat analisis psikologi</span>
          </div>
        </a>
        <a href="/chat" class="quick-action-card">
          <div class="quick-action-icon" style="background: #ecfdf5; color: #10b981;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
          </div>
          <div class="quick-action-info">
            <span class="quick-action-label">Konsultasi AI</span>
            <span class="quick-action-desc">Tanya jawab karir</span>
          </div>
        </a>
        <a href="/pmb/simulation" class="quick-action-card">
          <div class="quick-action-icon" style="background: #fff7ed; color: #f59e0b;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
              <line x1="8" y1="21" x2="16" y2="21"></line>
              <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
          </div>
          <div class="quick-action-info">
            <span class="quick-action-label">Simulasi PMB</span>
            <span class="quick-action-desc">Coba daftar kampus</span>
          </div>
        </a>
      </div>
    </section>

    <div class="dashboard-split-section">
      <!-- Next Steps / Tasks -->
      <section class="split-card next-steps-card">
        <div class="card-header">
          <h3 class="card-title">Langkah Selanjutnya</h3>
          <p class="card-subtitle">Selesaikan tugas ini untuk progres maksimal</p>
        </div>
        <div class="task-list">
          <div class="task-item <?= !empty($profile['phone']) ? 'task-completed' : '' ?>">
            <div class="task-checkbox">
              <input type="checkbox" <?= !empty($profile['phone']) ? 'checked' : '' ?> disabled>
            </div>
            <div class="task-content">
              <span class="task-title">Lengkapi Data Diri</span>
              <span class="task-desc">Nomor HP, Alamat, dan Tanggal Lahir</span>
            </div>
            <?php if (empty($profile['phone'])): ?>
              <a href="/profile/edit" class="task-action">Lengkapi</a>
            <?php endif; ?>
          </div>
          <div class="task-item <?= !empty($studentProfile['academic_scores']) ? 'task-completed' : '' ?>">
            <div class="task-checkbox">
              <input type="checkbox" <?= !empty($studentProfile['academic_scores']) ? 'checked' : '' ?> disabled>
            </div>
            <div class="task-content">
              <span class="task-title">Input Nilai Rapor</span>
              <span class="task-desc">Minimal 3 semester terakhir</span>
            </div>
            <?php if (empty($studentProfile['academic_scores'])): ?>
              <a href="/profile/academic" class="task-action">Input</a>
            <?php endif; ?>
          </div>
          <div class="task-item <?= !empty($studentProfile['ai_analysis']) ? 'task-completed' : '' ?>">
            <div class="task-checkbox">
              <input type="checkbox" <?= !empty($studentProfile['ai_analysis']) ? 'checked' : '' ?> disabled>
            </div>
            <div class="task-content">
              <span class="task-title">Generate Analisis AI</span>
              <span class="task-desc">Dapatkan rekomendasi jurusan</span>
            </div>
            <?php if (empty($studentProfile['ai_analysis'])): ?>
              <a href="/profile/results" class="task-action">Generate</a>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- AI Recommendations -->
      <section class="split-card ai-recs-card">
        <div class="card-header">
          <h3 class="card-title">Rekomendasi AI</h3>
          <p class="card-subtitle">Berdasarkan profil unik kamu</p>
        </div>
        <div class="recommendations-list">
          <?php if (!empty($aiRecommendations)): ?>
            <?php foreach ($aiRecommendations as $rec): ?>
              <div class="recommendation-item">
                <div class="rec-header">
                  <span class="rec-area"><?= htmlspecialchars($rec['field'] ?? 'Rekomendasi') ?></span>
                  <span class="rec-badge">AI Suggestion</span>
                </div>
                <p class="rec-desc"><?= htmlspecialchars($rec['reason'] ?? $rec) ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <p>Belum ada rekomendasi. Lengkapi data rapor dan hasil tes kamu!</p>
              <a href="/profile/results" class="btn btn-secondary btn-sm">Mulai Analisis</a>
            </div>
          <?php endif; ?>
        </div>
        <?php if (!empty($aiRecommendations)): ?>
          <a href="/profile/results" class="card-cta">
            <span>Lihat Analisis Lengkap</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cta-icon">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
        <?php endif; ?>
      </section>
    </div>

    <!-- Partners / Universities -->
    <section class="partners-section">
      <h3 class="section-title">Kampus Rekomendasi</h3>
      <div class="partners-grid">
        <div class="partner-logo">
          <div class="partner-placeholder"><span>UI</span></div>
        </div>
        <div class="partner-logo">
          <div class="partner-placeholder"><span>ITB</span></div>
        </div>
        <div class="partner-logo">
          <div class="partner-placeholder"><span>UGM</span></div>
        </div>
        <div class="partner-logo">
          <div class="partner-placeholder"><span>ITS</span></div>
        </div>
        <div class="partner-logo">
          <div class="partner-placeholder"><span>UNAIR</span></div>
        </div>
        <div class="partner-logo">
          <div class="partner-placeholder"><span>IPB</span></div>
        </div>
      </div>
    </section>
  </main>
</div>