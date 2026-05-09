<?php

/**
 * @var \App\Core\View\PageMeta $meta
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
      'title' => 'Lupa Password? 🔑',
      'description' => 'Jangan khawatir, kami akan membantu Anda memulihkan akses ke akun Psyco-Test Anda.',
      'features' => [
        ['icon' => '📧', 'title' => 'Reset via Email', 'description' => 'Kami akan mengirimkan link aman untuk mengatur ulang password Anda.'],
        ['icon' => '🛡️', 'title' => 'Keamanan Akun', 'description' => 'Pastikan Anda menggunakan password yang kuat dan unik.'],
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
    <h1 class="auth-title">Lupa Password</h1>
    <p class="auth-subtitle">Masukkan email Anda untuk menerima link reset password.</p>

    <?php if (isset($message)): ?>
      <div class="auth-success">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div class="auth-error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form data-spa method="POST" action="/password/forgot">
      <div class="auth-form-group">
        <label for="email" class="auth-label">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          class="auth-input"
          placeholder="Masukkan email Anda"
          required>
      </div>

      <button type="submit" class="auth-button">
        Kirim Link Reset
      </button>
    </form>

    <div class="auth-links">
      <a data-spa href="/login" class="auth-link">Kembali ke Login</a>
    </div>
  </div>
</div>