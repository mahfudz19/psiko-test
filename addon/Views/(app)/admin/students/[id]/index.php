<?php

/**
 * Student Detail View
 *
 * @var array $student
 * @var array $is_super_admin
 */

$url_edit_page = $is_super_admin ? "/admin/schools/{$student['school_id']}/students/{$student['user_id']}/edit" : "/admin/students/{$student['user_id']}/edit";
$url_delete_action = $is_super_admin ? "/admin/schools/{$student['school_id']}/students/{$student['user_id']}/delete" : "/admin/students/{$student['user_id']}/delete";

// Decode JSON fields
$academicScores = json_decode($student['academic_scores'] ?? 'null', true) ?? [];
$achievements = json_decode($student['achievements'] ?? 'null', true) ?? [];
$aiAnalysis = json_decode($student['ai_analysis'] ?? 'null', true) ?? [];
?>

<div class="student-detail-page">
  <!-- Page Header -->
  <header class="student-detail-header">
    <div class="student-detail-header-content">
      <h1 class="student-detail-title">🎓 Detail Siswa</h1>
      <p class="student-detail-description">Informasi lengkap siswa</p>
    </div>
    <div class="student-detail-actions">
      <a data-spa href=<?= $url_edit_page ?> class="student-detail-btn student-detail-btn-primary">
        <span class="student-detail-btn-icon">✏️</span>
        Edit Data
      </a>
    </div>
  </header>

  <!-- Student Profile Card -->
  <section class="student-detail-profile-card">
    <div class="student-detail-profile-header">
      <div class="student-detail-avatar">
        <?= strtoupper(substr($student['user_name'], 0, 2)) ?>
      </div>
      <div class="student-detail-profile-info">
        <h2 class="student-detail-profile-name"><?= e($student['user_name']) ?></h2>
        <p class="student-detail-profile-email"><?= e($student['email']) ?></p>
        <div class="student-detail-profile-badges">
          <span class="student-detail-badge student-detail-badge-nis">
            <span class="student-detail-badge-icon">🔢</span>
            NIS/NISN: <?= e($student['student_id']) ?>
          </span>
          <span class="student-detail-badge student-detail-badge-class">
            <span class="student-detail-badge-icon">📚</span>
            Kelas <?= e($student['grade_level']) ?>
          </span>
        </div>
      </div>
    </div>
  </section>

  <!-- Info Cards Grid -->
  <div class="student-detail-info-grid">
    <!-- Student Info Card -->
    <article class="student-detail-card student-detail-info-card">
      <header class="student-detail-card-header">
        <span class="student-detail-card-icon">👨‍🎓</span>
        <div class="student-detail-card-title-wrapper">
          <h3 class="student-detail-card-title">Informasi Siswa</h3>
          <p class="student-detail-card-subtitle">Data akademik dan kontak</p>
        </div>
      </header>
      <div class="student-detail-card-body">
        <dl class="student-detail-info-list">
          <div class="student-detail-info-item">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">📚</span>
              Kelas
            </dt>
            <dd class="student-detail-info-value">Kelas <?= e($student['grade_level']) ?></dd>
          </div>
          <div class="student-detail-info-item">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">🎯</span>
              Jurusan
            </dt>
            <dd class="student-detail-info-value"><?= e($student['major']) ?: '-' ?></dd>
          </div>
          <div class="student-detail-info-item">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">📱</span>
              No. Telepon
            </dt>
            <dd class="student-detail-info-value"><?= e($student['phone']) ?: '-' ?></dd>
          </div>
          <div class="student-detail-info-item student-detail-info-item-full">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">📍</span>
              Alamat
            </dt>
            <dd class="student-detail-info-value"><?= nl2br(e($student['address'])) ?: '-' ?></dd>
          </div>
        </dl>
      </div>
    </article>

    <!-- Parent Info Card -->
    <article class="student-detail-card student-detail-info-card">
      <header class="student-detail-card-header">
        <span class="student-detail-card-icon">👨‍👩‍👧</span>
        <div class="student-detail-card-title-wrapper">
          <h3 class="student-detail-card-title">Informasi Orang Tua/Wali</h3>
          <p class="student-detail-card-subtitle">Data kontak wali siswa</p>
        </div>
      </header>
      <div class="student-detail-card-body">
        <dl class="student-detail-info-list">
          <div class="student-detail-info-item">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">👤</span>
              Nama Lengkap
            </dt>
            <dd class="student-detail-info-value"><?= e($student['parent_name']) ?></dd>
          </div>
          <div class="student-detail-info-item">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">📞</span>
              No. Telepon
            </dt>
            <dd class="student-detail-info-value"><?= e($student['parent_phone']) ?></dd>
          </div>
          <div class="student-detail-info-item">
            <dt class="student-detail-info-label">
              <span class="student-detail-info-icon">📧</span>
              Email
            </dt>
            <dd class="student-detail-info-value"><?= e($student['parent_email']) ?: '-' ?></dd>
          </div>
        </dl>
      </div>
    </article>
  </div>

  <!-- Data Tabs Section -->
  <section class="student-detail-tabs-section">
    <!-- Tab Navigation -->
    <div class="student-detail-tab-nav">
      <button class="student-detail-tab-btn student-detail-tab-btn-active" data-tab="student-detail-tab-academic">
        <span class="student-detail-tab-icon">📊</span>
        Nilai Akademik
      </button>
      <button class="student-detail-tab-btn" data-tab="student-detail-tab-achievements">
        <span class="student-detail-tab-icon">🏆</span>
        Prestasi
      </button>
      <button class="student-detail-tab-btn" data-tab="student-detail-tab-ai-analysis">
        <span class="student-detail-tab-icon">🤖</span>
        Analisis AI
      </button>
    </div>

    <!-- Tab Content: Academic Scores -->
    <div class="student-detail-tab-content student-detail-tab-content-active" id="student-detail-tab-academic">
      <div class="student-detail-tab-header">
        <h3 class="student-detail-tab-title">📊 Nilai Akademik</h3>
        <p class="student-detail-tab-subtitle">Riwayat nilai per semester</p>
      </div>
      <?php if (empty($academicScores)): ?>
        <div class="student-detail-empty-state">
          <span class="student-detail-empty-icon">📚</span>
          <p class="student-detail-empty-text">Belum ada data nilai akademik</p>
        </div>
      <?php else: ?>
        <div class="student-detail-semester-list">
          <?php foreach ($academicScores as $semesterData): ?>
            <article class="student-detail-semester-card">
              <header class="student-detail-semester-header">
                <h4 class="student-detail-semester-title"><?= e($semesterData['semester'] ?? 'Semester Tidak Diketahui') ?></h4>
              </header>
              <div class="student-detail-subjects-table-wrapper">
                <table class="student-detail-subjects-table">
                  <thead>
                    <tr>
                      <th class="student-detail-th">Mata Pelajaran</th>
                      <th class="student-detail-th">Pengetahuan</th>
                      <th class="student-detail-th">Keterampilan</th>
                      <th class="student-detail-th student-detail-th-final">Nilai Akhir</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($semesterData['subjects'] ?? [] as $subject): ?>
                      <tr class="student-detail-tr">
                        <td class="student-detail-td student-detail-td-subject"><?= e($subject['name'] ?? '-') ?></td>
                        <td class="student-detail-td"><?= e($subject['sub_scores']['pengetahuan'] ?? '-') ?></td>
                        <td class="student-detail-td"><?= e($subject['sub_scores']['keterampilan'] ?? '-') ?></td>
                        <td class="student-detail-td student-detail-td-final">
                          <span class="student-detail-final-score"><?= e($subject['final_score'] ?? '-') ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Tab Content: Achievements -->
    <div class="student-detail-tab-content" id="student-detail-tab-achievements">
      <div class="student-detail-tab-header">
        <h3 class="student-detail-tab-title">🏆 Prestasi</h3>
        <p class="student-detail-tab-subtitle">Daftar pencapaian dan prestasi siswa</p>
      </div>
      <?php if (empty($achievements)): ?>
        <div class="student-detail-empty-state">
          <span class="student-detail-empty-icon">🏅</span>
          <p class="student-detail-empty-text">Belum ada data prestasi</p>
        </div>
      <?php else: ?>
        <div class="student-detail-achievements-grid">
          <?php foreach ($achievements as $achievement): ?>
            <article class="student-detail-achievement-card">
              <header class="student-detail-achievement-header">
                <h4 class="student-detail-achievement-name"><?= e($achievement['name'] ?? 'Prestasi') ?></h4>
                <?php if (!empty($achievement['rank'])): ?>
                  <span class="student-detail-achievement-rank"><?= e($achievement['rank']) ?></span>
                <?php endif; ?>
              </header>
              <div class="student-detail-achievement-body">
                <p class="student-detail-achievement-title"><?= e($achievement['title'] ?? '-') ?></p>
                <div class="student-detail-achievement-meta">
                  <?php if (!empty($achievement['level'])): ?>
                    <span class="student-detail-achievement-badge"><?= e($achievement['level']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($achievement['year'])): ?>
                    <span class="student-detail-achievement-year">📅 <?= e($achievement['year']) ?></span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($achievement['organizer'])): ?>
                  <p class="student-detail-achievement-organizer">🏛️ <?= e($achievement['organizer']) ?></p>
                <?php endif; ?>
                <?php if (!empty($achievement['description'])): ?>
                  <p class="student-detail-achievement-description"><?= nl2br(e($achievement['description'])) ?></p>
                <?php endif; ?>
              </div>
              <?php if (!empty($achievement['certificate_url'])): ?>
                <footer class="student-detail-achievement-footer">
                  <a href="<?= e($achievement['certificate_url']) ?>" target="_blank" class="student-detail-achievement-cert-link">
                    <span class="student-detail-icon">📜</span> Lihat Sertifikat
                  </a>
                </footer>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Tab Content: AI Analysis -->
    <div class="student-detail-tab-content" id="student-detail-tab-ai-analysis">
      <div class="student-detail-tab-header">
        <h3 class="student-detail-tab-title">🤖 Analisis AI</h3>
        <p class="student-detail-tab-subtitle">Hasil analisis kecerdasan buatan</p>
      </div>
      <?php if (empty($aiAnalysis)): ?>
        <div class="student-detail-empty-state">
          <span class="student-detail-empty-icon">🧠</span>
          <p class="student-detail-empty-text">Belum ada data analisis AI</p>
        </div>
      <?php else: ?>
        <div class="student-detail-ai-analysis">
          <!-- Holland Code Badge -->
          <?php if (!empty($aiAnalysis['holland_code'])): ?>
            <div class="student-detail-holland-section">
              <div class="student-detail-holland-badge">
                <span class="student-detail-holland-code"><?= e(strtoupper($aiAnalysis['holland_code'])) ?></span>
                <span class="student-detail-holland-label">Kode Holland</span>
              </div>
            </div>
          <?php endif; ?>

          <!-- RIASEC Scores -->
          <?php if (!empty($aiAnalysis['riasec_scores'])): ?>
            <div class="student-detail-riasec-section">
              <h4 class="student-detail-section-title">📊 Skor RIASEC</h4>
              <div class="student-detail-riasec-bars">
                <?php
                $riasecLabels = [
                  'R' => 'Realistic',
                  'I' => 'Investigative',
                  'A' => 'Artistic',
                  'S' => 'Social',
                  'E' => 'Enterprising',
                  'C' => 'Conventional'
                ];
                foreach ($riasecLabels as $code => $label):
                  $score = $aiAnalysis['riasec_scores'][$code] ?? 0;
                  $percentage = min(100, max(0, $score));
                ?>
                  <div class="student-detail-riasec-item">
                    <div class="student-detail-riasec-header">
                      <span class="student-detail-riasec-code"><?= e($code) ?></span>
                      <span class="student-detail-riasec-label"><?= e($label) ?></span>
                      <span class="student-detail-riasec-score"><?= e($score) ?>%</span>
                    </div>
                    <div class="student-detail-riasec-bar">
                      <div class="student-detail-riasec-fill" style="width: <?= e($percentage) ?>%"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Summary -->
          <?php if (!empty($aiAnalysis['summary'])): ?>
            <div class="student-detail-summary-section">
              <h4 class="student-detail-section-title">📝 Ringkasan</h4>
              <p class="student-detail-summary-text"><?= nl2br(e($aiAnalysis['summary'])) ?></p>
            </div>
          <?php endif; ?>

          <!-- Potential, Interests, Talents -->
          <div class="student-detail-trait-grid">
            <?php if (!empty($aiAnalysis['potential'])): ?>
              <div class="student-detail-trait-card">
                <h4 class="student-detail-trait-title">💡 Potensi</h4>
                <ul class="student-detail-trait-list">
                  <?php foreach ($aiAnalysis['potential'] as $item): ?>
                    <li><?= e($item) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <?php if (!empty($aiAnalysis['interests'])): ?>
              <div class="student-detail-trait-card">
                <h4 class="student-detail-trait-title">❤️ Minat</h4>
                <ul class="student-detail-trait-list">
                  <?php foreach ($aiAnalysis['interests'] as $item): ?>
                    <li><?= e($item) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <?php if (!empty($aiAnalysis['talents'])): ?>
              <div class="student-detail-trait-card">
                <h4 class="student-detail-trait-title">🎯 Bakat</h4>
                <ul class="student-detail-trait-list">
                  <?php foreach ($aiAnalysis['talents'] as $item): ?>
                    <li><?= e($item) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>

          <!-- Recommendations -->
          <?php if (!empty($aiAnalysis['recommendations'])): ?>
            <div class="student-detail-recommendations-section">
              <h4 class="student-detail-section-title">💬 Rekomendasi</h4>
              <ul class="student-detail-recommendations-list">
                <?php foreach ($aiAnalysis['recommendations'] as $rec): ?>
                  <li class="student-detail-recommendation-item">
                    <span class="student-detail-recommendation-icon">✓</span>
                    <?= e($rec) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- Career Suggestions -->
          <?php if (!empty($aiAnalysis['career_suggestions'])): ?>
            <div class="student-detail-career-section">
              <h4 class="student-detail-section-title">💼 Saran Karir</h4>
              <div class="student-detail-career-tags">
                <?php foreach ($aiAnalysis['career_suggestions'] as $career): ?>
                  <span class="student-detail-career-tag"><?= e($career) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Delete Section -->
  <section class="student-detail-card student-detail-danger-card">
    <header class="student-detail-card-header">
      <span class="student-detail-card-icon student-detail-card-icon-danger">⚠️</span>
      <div class="student-detail-card-title-wrapper">
        <h3 class="student-detail-card-title student-detail-card-title-danger">Zona Bahaya</h3>
        <p class="student-detail-card-subtitle student-detail-card-subtitle-danger">Tindakan ini tidak dapat dibatalkan</p>
      </div>
    </header>
    <div class="student-detail-card-body">
      <p class="student-detail-danger-description">
        Menghapus siswa akan menghapus semua data terkait termasuk nilai, pencapaian, dan riwayat konseling.
        Pastikan Anda telah melakukan backup data sebelum melanjutkan.
      </p>
      <form data-spa action="<?= $url_delete_action ?>" method="POST" class="student-detail-danger-form">
        <button type="submit" class="student-detail-btn student-detail-btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Tindakan ini tidak dapat dibatalkan.')">
          <span class="student-detail-btn-icon">🗑️</span>
          Hapus Siswa
        </button>
      </form>
    </div>
  </section>

  <!-- Tab Switching Script -->
  <script>
    (function() {
      const tabButtons = document.querySelectorAll('.student-detail-tab-btn');
      const tabContents = document.querySelectorAll('.student-detail-tab-content');

      tabButtons.forEach(button => {
        button.addEventListener('click', function() {
          const targetTabId = this.getAttribute('data-tab');

          // Remove active class from all buttons and contents
          tabButtons.forEach(btn => btn.classList.remove('student-detail-tab-btn-active'));
          tabContents.forEach(content => content.classList.remove('student-detail-tab-content-active'));

          // Add active class to clicked button and target content
          this.classList.add('student-detail-tab-btn-active');
          const targetContent = document.getElementById(targetTabId);
          if (targetContent) {
            targetContent.classList.add('student-detail-tab-content-active');
          }
        });
      });
    })();
  </script>
</div>