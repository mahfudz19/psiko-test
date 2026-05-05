<?php

/**
 * Dashboard Siswa - Main View
 * Menampilkan overview profil, match score, quick actions, dan next steps
 * 
 * @var \App\Core\View\PageMeta $meta
 */

// Dummy data untuk presentasi
$studentName = $_SESSION['auth.user_name'] ?? 'Siswa';
$profileProgress = 85;
$pmbStatus = 'draft'; // draft, submitted, accepted, rejected
$matchScore = 92;
$topMajor = 'Teknik Informatika';
$scholarshipPotential = 50;
$completedTasks = 3;
$totalTasks = 5;

// Next steps dengan dummy data
$nextSteps = [
  [
    'title' => 'Lengkapi dokumen akademik',
    'description' => 'Upload transkrip nilai semester terakhir',
    'icon' => '📄',
    'priority' => 'high',
    'completed' => false,
    'link' => '/profile/academic'
  ],
  [
    'title' => 'Upload sertifikat prestasi',
    'description' => 'Tambahkan sertifikat lomba atau pencapaian',
    'icon' => '🏆',
    'priority' => 'medium',
    'completed' => false,
    'link' => '/profile/achievements'
  ],
  [
    'title' => 'Ikuti simulasi tes potensi',
    'description' => 'Tes untuk mengukur potensi akademik',
    'icon' => '📝',
    'priority' => 'medium',
    'completed' => true,
    'link' => '/pmb/simulation'
  ],
  [
    'title' => 'Review hasil analisis AI',
    'description' => 'Lihat rekomendasi jurusan dari AI',
    'icon' => '🤖',
    'priority' => 'low',
    'completed' => true,
    'link' => '/profile/results'
  ],
  [
    'title' => 'Lengkapi data diri',
    'description' => 'Pastikan semua informasi pribadi akurat',
    'icon' => '👤',
    'priority' => 'high',
    'completed' => true,
    'link' => '/profile/edit'
  ]
];

// AI Recommendations
$aiRecommendations = [
  [
    'area' => 'Logical Reasoning',
    'current' => 75,
    'target' => 90,
    'improvement' => '+15%',
    'description' => 'Latihan soal logika dan penalaran analitis'
  ],
  [
    'area' => 'Mathematical Ability',
    'current' => 82,
    'target' => 90,
    'improvement' => '+8%',
    'description' => 'Perbanyak latihan matematika dasar dan aljabar'
  ],
  [
    'area' => 'Verbal Comprehension',
    'current' => 88,
    'target' => 90,
    'improvement' => '+2%',
    'description' => 'Baca artikel teknis dan buat ringkasan'
  ]
];

// Match score breakdown
$matchScoreBreakdown = [
  'logic' => ['label' => 'Logical Thinking', 'score' => 88],
  'interest' => ['label' => 'Minat & Passion', 'score' => 95],
  'skills' => ['label' => 'Skills & Kompetensi', 'score' => 90],
  'potential' => ['label' => 'Potensi Akademik', 'score' => 85]
];
?>

<main class="dashboard-main">
  <!-- Welcome Section -->
  <section class="dashboard-welcome">
    <div class="welcome-header">
      <div class="welcome-text">
        <h1 class="welcome-title">Selamat Datang, <?= htmlspecialchars($studentName) ?>! 👋</h1>
        <p class="welcome-subtitle">Profil kamu <strong><?= $profileProgress ?>% lengkap</strong>. Ayo lengkapi untuk hasil analisis yang lebih akurat!</p>
      </div>
      <div class="welcome-avatar">
        <img src="<?= $_SESSION['auth.user_avatar'] ?? $_SESSION['auth.user_avatar_url'] ?? '/logo_app/mazu-icon.svg'; ?>" alt="Avatar" class="avatar-image">
      </div>
    </div>
  </section>

  <!-- Match Score Hero Card -->
  <?php if ($pmbStatus !== 'accepted'): ?>
    <section class="match-score-section">
      <div class="match-score-card">
        <div class="match-score-content">
          <div class="match-score-header">
            <div class="match-score-badge">
              <div class="circular-progress">
                <svg viewBox="0 0 36 36" class="circular-chart">
                  <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                  <path class="circle" stroke-dasharray="<?= $matchScore ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <span class="score-number"><?= $matchScore ?>%</span>
              </div>
            </div>
            <div class="match-score-info">
              <h2 class="match-score-title">Match Score dengan <?= htmlspecialchars($topMajor) ?></h2>
              <p class="match-score-desc">Berdasarkan analisis potensi, minat, dan kompetensimu</p>
              <div class="match-score-actions">
                <a data-spa href="/pmb/journey" class="btn btn-primary btn-sm">
                  <span class="btn-icon">🎯</span> Lihat Detail
                </a>
                <a data-spa href="/pmb/simulation" class="btn btn-secondary btn-sm">
                  <span class="btn-icon">📝</span> Simulasi PMB
                </a>
              </div>
            </div>
          </div>
          <div class="match-score-breakdown">
            <?php foreach ($matchScoreBreakdown as $key => $item): ?>
              <div class="breakdown-item">
                <div class="breakdown-label"><?= htmlspecialchars($item['label']) ?></div>
                <div class="breakdown-bar">
                  <div class="breakdown-fill" style="width: <?= $item['score'] ?>%"></div>
                </div>
                <div class="breakdown-value"><?= $item['score'] ?>%</div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- Stats Cards -->
  <section class="stats-grid">
    <div class="stat-card stat-card--primary">
      <div class="stat-icon">📊</div>
      <div class="stat-content">
        <div class="stat-label">Progress Profil</div>
        <div class="stat-value"><?= $profileProgress ?>%</div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: <?= $profileProgress ?>%"></div>
      </div>
      <a data-spa href="/profile" class="stat-link">Update →</a>
    </div>

    <div class="stat-card stat-card--success">
      <div class="stat-icon">💰</div>
      <div class="stat-content">
        <div class="stat-label">Beasiswa Potensial</div>
        <div class="stat-value"><?= $scholarshipPotential ?>%</div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: <?= $scholarshipPotential ?>%"></div>
      </div>
      <a data-spa href="/pmb/scholarship" class="stat-link">Cek Detail →</a>
    </div>

    <div class="stat-card stat-card--warning">
      <div class="stat-icon">📋</div>
      <div class="stat-content">
        <div class="stat-label">Tugas Selesai</div>
        <div class="stat-value"><?= $completedTasks ?>/<?= $totalTasks ?></div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: <?= ($completedTasks / $totalTasks) * 100 ?>%"></div>
      </div>
      <a data-spa href="#next-steps" class="stat-link">Lihat Tugas →</a>
    </div>

    <div class="stat-card stat-card--info">
      <div class="stat-icon">🎓</div>
      <div class="stat-content">
        <div class="stat-label">Status PMB</div>
        <div class="stat-value"><?= ucfirst($pmbStatus) ?></div>
      </div>
      <div class="stat-status stat-status--<?= $pmbStatus ?>">
        <span class="status-dot"></span>
        <span class="status-text"><?= $pmbStatus === 'draft' ? 'Belum dikirim' : $pmbStatus ?></span>
      </div>
      <a data-spa href="/pmb/simulation" class="stat-link">
        <?= $pmbStatus === 'draft' ? 'Lanjutkan' : 'Lihat' ?> →
      </a>
    </div>
  </section>

  <!-- Quick Actions -->
  <section class="quick-actions-section">
    <h2 class="section-title">⚡ Quick Actions</h2>
    <div class="quick-actions-grid">
      <a data-spa href="/pmb/simulation" class="quick-action-card">
        <div class="quick-action-icon">📝</div>
        <div class="quick-action-label">Simulasi PMB</div>
        <div class="quick-action-desc">Ikuti simulasi pendaftaran</div>
      </a>
      <a data-spa href="/profile/edit" class="quick-action-card">
        <div class="quick-action-icon">✏️</div>
        <div class="quick-action-label">Edit Profil</div>
        <div class="quick-action-desc">Update data diri</div>
      </a>
      <a data-spa href="/profile/academic" class="quick-action-card">
        <div class="quick-action-icon">📚</div>
        <div class="quick-action-label">Data Akademik</div>
        <div class="quick-action-desc">Kelola nilai & prestasi</div>
      </a>
      <a data-spa href="/pmb/scholarship" class="quick-action-card">
        <div class="quick-action-icon">💰</div>
        <div class="quick-action-label">Beasiswa</div>
        <div class="quick-action-desc">Cek eligibility & apply</div>
      </a>
    </div>
  </section>

  <!-- Next Steps & AI Recommendations -->
  <section class="dashboard-split-section">
    <!-- Next Steps -->
    <div class="split-card next-steps-card" id="next-steps">
      <h2 class="card-title">📋 Next Steps</h2>
      <div class="task-list">
        <?php foreach ($nextSteps as $index => $task): ?>
          <div class="task-item <?= $task['completed'] ? 'task-completed' : '' ?> task-priority-<?= $task['priority'] ?>">
            <div class="task-checkbox">
              <input type="checkbox" id="task-<?= $index ?>" <?= $task['completed'] ? 'checked' : '' ?>>
              <label for="task-<?= $index ?>"></label>
            </div>
            <div class="task-icon"><?= $task['icon'] ?></div>
            <div class="task-content">
              <h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
              <p class="task-desc"><?= htmlspecialchars($task['description']) ?></p>
            </div>
            <a data-spa href="<?= $task['link'] ?>" class="task-action">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- AI Recommendations -->
    <div class="split-card ai-recommendations-card">
      <h2 class="card-title">🤖 AI Recommendations</h2>
      <p class="card-subtitle">Area yang perlu ditingkatkan untuk optimalisasi match score</p>
      <div class="recommendations-list">
        <?php foreach ($aiRecommendations as $rec): ?>
          <div class="recommendation-item">
            <div class="rec-header">
              <div class="rec-info">
                <h3 class="rec-area"><?= htmlspecialchars($rec['area']) ?></h3>
                <p class="rec-desc"><?= htmlspecialchars($rec['description']) ?></p>
              </div>
              <div class="rec-badge"><?= $rec['improvement'] ?></div>
            </div>
            <div class="rec-progress">
              <div class="rec-progress-bar">
                <div class="rec-progress-fill" style="width: <?= $rec['current'] ?>%"></div>
              </div>
              <div class="rec-progress-labels">
                <span>Current: <?= $rec['current'] ?>%</span>
                <span>Target: <?= $rec['target'] ?>%</span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <a data-spa href="/profile/results" class="card-cta">
        <span class="cta-icon">📊</span>
        <span>Lihat Hasil Analisis Lengkap</span>
      </a>
    </div>
  </section>

  <!-- Partner Companies Preview -->
  <section class="partners-section">
    <h2 class="section-title">🏢 Partner Perusahaan untuk Magang & Karir</h2>
    <div class="partners-grid">
      <div class="partner-logo">
        <div class="partner-placeholder">🏢</div>
        <span>Google</span>
      </div>
      <div class="partner-logo">
        <div class="partner-placeholder">💻</div>
        <span>Microsoft</span>
      </div>
      <div class="partner-logo">
        <div class="partner-placeholder">🚀</div>
        <span>GoTo</span>
      </div>
      <div class="partner-logo">
        <div class="partner-placeholder">🦄</div>
        <span>Traveloka</span>
      </div>
      <div class="partner-logo">
        <div class="partner-placeholder">🛍️</div>
        <span>Shopee</span>
      </div>
      <div class="partner-logo">
        <div class="partner-placeholder">📱</div>
        <span>Telkomsel</span>
      </div>
    </div>
  </section>
</main>

<script>
  // Toggle task completion (dummy interaction)
  document.querySelectorAll('.task-checkbox input').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const taskItem = this.closest('.task-item');
      if (this.checked) {
        taskItem.classList.add('task-completed');
      } else {
        taskItem.classList.remove('task-completed');
      }
    });
  });
</script>