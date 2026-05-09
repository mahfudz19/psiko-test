<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $token
 * @var string $email
 * @var array|null $infoPanel
 */
?>

<div class="auth-info-panel">
  <div class="auth-info-content">
    <div class="auth-logo">
      <img src="/logo_app/mazu-icon.svg" alt="Psyco-Test Logo">
      <span>Psyco-Test</span>
    </div>

    <?php
    $info = $infoPanel ?? [
      'title' => 'Atur Ulang Password 🛠️',
      'description' => 'Silakan buat password baru yang aman untuk akun Anda.',
      'features' => [
        ['icon' => '🔒', 'title' => 'Password Baru', 'description' => 'Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.'],
        ['icon' => '✅', 'title' => 'Konfirmasi', 'description' => 'Pastikan konfirmasi password sama dengan password baru Anda.'],
      ]
    ];
    ?>

    <h1><?= $info['title'] ?></h1>
    <p><?= $info['description'] ?></p>

    <?php if (!empty($info['features'])): ?>
      <div class="auth-features">
        <?php foreach ($info['features'] as $feature): ?>
          <div class="feature-item">
            <span class="feature-icon"><?= $feature['icon'] ?></span>
            <div class="feature-text">
              <h3><?= $feature['title'] ?></h3>
              <p><?= $feature['description'] ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <div class="auth-info-footer">
    &copy; <?= date('Y') ?> Psyco-Test. Powered by Mazu Framework.
  </div>
</div>

<div class="auth-form-panel">
  <div class="auth-form-content">
    <h1 class="auth-title">Reset Password</h1>
    <p class="auth-subtitle">Silakan masukkan password baru Anda.</p>

    <?php if (isset($error)): ?>
      <div class="auth-error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form data-spa method="POST" action="/password/reset">
      <?php if (isset($token)): ?>
        <input type="hidden" name="token" value="<?= $token ?>">
      <?php endif; ?>

      <div class="auth-form-group">
        <label for="email" class="auth-label">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          class="auth-input"
          value="<?= htmlspecialchars($email ?? '') ?>"
          readonly
          required>
      </div>

      <div class="auth-form-group">
        <label for="password" class="auth-label">Password Baru</label>
        <input
          type="password"
          id="password"
          name="password"
          class="auth-input"
          minlength="8"
          required>
      </div>

      <div class="auth-form-group">
        <label for="password_confirmation" class="auth-label">Konfirmasi Password</label>
        <input
          type="password"
          id="password_confirmation"
          name="password_confirmation"
          class="auth-input"
          minlength="8"
          required>
      </div>

      <button type="submit" class="auth-button">
        Reset Password
      </button>
    </form>

    <div class="auth-links">
      <a data-spa href="/login" class="auth-link">Kembali ke Login</a>
    </div>
  </div>
</div>