<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?= App\Core\View\View::renderMeta($meta) ?>

  <!-- Link ke file CSS yang sudah di-generate oleh Tailwind CLI -->
  <!-- Google Fonts - Pindahkan ke sini -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

  <!-- Auto-Injected Styles -->
  <?= App\Core\View\View::renderStyles() ?>

</head>

<body>
  <!-- Global Loading Progress Bar -->
  <div id="global-progress-bar" class="progress-bar-container">
    <div id="global-progress-bar-inner" class="progress-bar-fill"></div>
  </div>

  <!-- Content Injection Point -->
  <main id="app-content" data-layout="layout.php">
    <?= $children; ?>
  </main>

  <!-- SPA Script -->
  <?= App\Core\View\View::renderScripts() ?>
</body>

</html>