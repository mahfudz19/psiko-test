<?php

/**
 * Student Detail View
 * 
 * @var array $student
 * @var array $is_super_admin
 */

$url_edit_page = $is_super_admin ? "/admin/schools/{$student['school_id']}/students/{$student['user_id']}/edit" : "/admin/students/{$student['user_id']}/edit";
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
      <form data-spa action="/admin/students/<?= $student['user_id'] ?>/delete" method="POST" class="student-detail-danger-form">
        <button type="submit" class="student-detail-btn student-detail-btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Tindakan ini tidak dapat dibatalkan.')">
          <span class="student-detail-btn-icon">🗑️</span>
          Hapus Siswa
        </button>
      </form>
    </div>
  </section>
</div>