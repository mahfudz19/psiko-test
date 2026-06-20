<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
?>
<div class="app-layout">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <a data-spa href="/" class="sidebar-header">
      <img src="/logo_app/mazu-icon.svg" alt="<?= env('APP_NAME') ?> Logo" class="sidebar-logo">
      <span class="sidebar-app-name"><?= env('APP_NAME') ?></span>
    </a>
    <nav class="sidebar-nav">
      <?php
      // Deteksi current path untuk active state
      $currentPath = $_SERVER['REQUEST_URI'] ?? '/dashboard';
      if (strpos($currentPath, '?') !== false) {
        $currentPath = substr($currentPath, 0, strpos($currentPath, '?'));
      }
      ?>
      <a data-spa href="/dashboard" class="sidebar-link <?= $currentPath === '/dashboard' ? 'active' : '' ?>">
        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="7" height="9" x="3" y="3" rx="1" />
          <rect width="7" height="5" x="14" y="3" rx="1" />
          <rect width="7" height="9" x="14" y="12" rx="1" />
          <rect width="7" height="5" x="3" y="16" rx="1" />
        </svg>
        <span class="sidebar-link-text">Dashboard</span>
      </a>
      <?php
      // Profile menu dengan submenu untuk student
      $isProfilePage = in_array($currentPath, ['/profile', '/profile/academic', '/profile/achievements', '/profile/results', '/profile/edit']);
      $isStudentProfile = in_array($currentPath, ['/profile/academic', '/profile/achievements', '/profile/results']);
      ?>
      <div class="sidebar-nav-group">
        <a data-spa href="/profile" class="sidebar-nav-group-header <?= $currentPath === '/profile' ? 'active' : ($isProfilePage ? 'active-group' : '') ?>">
          <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
          <span class="sidebar-link-text">Profile</span>
          <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-collapse-toggle>
            <path d="m6 9 6 6 6-6" />
          </svg>
        </a>
        <?php if (($_SESSION['auth.user_role'] ?? '') === 'user'): ?>
          <!-- Student Profile Submenu -->
          <div class="sidebar-nav-group-content">
            <a data-spa href="/profile/academic" class="sidebar-link sidebar-link-sub <?= $currentPath === '/profile/academic' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" />
                <path d="M3 9h18" />
                <path d="m9 21 3-6 3 6" />
              </svg>
              <span class="sidebar-link-text">Nilai Akademik</span>
            </a>
            <a data-spa href="/profile/achievements" class="sidebar-link sidebar-link-sub <?= $currentPath === '/profile/achievements' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
                <path d="M4 22h16" />
                <path d="M10 14.66V18c0 .55-.47.98-.97 1.21C7.85 19.75 5.96 19.5 4 18.5" />
                <path d="M14 14.66V18c0 .55.47.98.97 1.21C16.15 19.75 18.04 19.5 20 18.5" />
                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />
              </svg>
              <span class="sidebar-link-text">Prestasi</span>
            </a>
            <a data-spa href="/profile/results" class="sidebar-link sidebar-link-sub <?= $currentPath === '/profile/results' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4Z" />
                <path d="M12 12v8" />
                <path d="m8 16 4 4 4-4" />
                <path d="M12 2v2" />
                <path d="M12 20v2" />
                <path d="m4.93 4.93 1.41 1.41" />
                <path d="m17.66 17.66 1.41 1.41" />
                <path d="M2 12h2" />
                <path d="M20 12h2" />
              </svg>
              <span class="sidebar-link-text">Analisis AI</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
      <?php if (($_SESSION['auth.user_role'] ?? '') === 'user'): ?>
        <!-- PMB Journey Menu (hanya untuk siswa) -->
        <?php
        // Cek apakah sedang di halaman PMB
        $isPmbPage = in_array($currentPath, ['/pmb/journey', '/pmb/simulation', '/pmb/scholarship']);
        ?>
        <div class="sidebar-nav-group">
          <a data-spa href="/pmb/journey" class="sidebar-nav-group-header <?= $currentPath === '/pmb/journey' ? 'active' : ($isPmbPage ? 'active-group' : '') ?>">
            <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <span class="sidebar-link-text">PMB Journey</span>
            <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-collapse-toggle>
              <path d="m6 9 6 6 6-6" />
            </svg>
          </a>
          <div class="sidebar-nav-group-content">
            <a data-spa href="/pmb/journey" class="sidebar-link sidebar-link-sub <?= $currentPath === '/pmb/journey' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M16.24 7.76 12 11.99 7.76 7.76" />
                <path d="m12 12-2.5 5.5" />
              </svg>
              <span class="sidebar-link-text">Journey</span>
            </a>
            <a data-spa href="/pmb/simulation" class="sidebar-link sidebar-link-sub <?= $currentPath === '/pmb/simulation' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                <path d="M12 12v9" />
                <path d="m8 17 4 4 4-4" />
              </svg>
              <span class="sidebar-link-text">Simulasi PMB</span>
            </a>
            <a data-spa href="/pmb/scholarship" class="sidebar-link sidebar-link-sub <?= $currentPath === '/pmb/scholarship' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20" />
                <path d="m17 5-5-3-5 3" />
                <path d="m17 19-5 3-5-3" />
                <path d="M4 7h16" />
                <path d="M4 17h16" />
              </svg>
              <span class="sidebar-link-text">Beasiswa</span>
            </a>
          </div>
        </div>

        <!-- Chat Consultation Menu (untuk siswa) -->
        <?php
        $isChatPage = str_starts_with($currentPath, '/chat');
        ?>
        <a data-spa href="/chat" class="sidebar-link <?= $currentPath === '/chat' ? 'active' : '' ?>">
          <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
          </svg>
          <span class="sidebar-link-text">Konsultasi AI</span>
        </a>

        <!-- Test Psychology Menu (untuk siswa) -->
        <?php
        $isTestPage = in_array($currentPath, ['/tests/riasec', '/tests/riasec/take', '/tests/riasec/results', '/tests/iq']);
        ?>
        <div class="sidebar-nav-group">
          <a data-spa href="/tests/riasec" class="sidebar-nav-group-header <?= $currentPath === '/tests/riasec' ? 'active' : ($isTestPage ? 'active-group' : '') ?>">
            <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
              <polyline points="14 2 14 8 20 8" />
              <path d="m9 15 2 2 4-4" />
            </svg>
            <span class="sidebar-link-text">Tes Psikologi</span>
            <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-collapse-toggle>
              <path d="m6 9 6 6 6-6" />
            </svg>
          </a>
          <div class="sidebar-nav-group-content">
            <a data-spa href="/tests/riasec" class="sidebar-link sidebar-link-sub <?= $currentPath === '/tests/riasec' || $currentPath === '/tests/riasec/take' || $currentPath === '/tests/riasec/results' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
              </svg>
              <span class="sidebar-link-text">RIASEC</span>
            </a>
            <a data-spa href="/tests/iq" class="sidebar-link sidebar-link-sub <?= $currentPath === '/tests/iq' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4Z" />
                <path d="M12 12v8" />
                <path d="m8 16 4 4 4-4" />
              </svg>
              <span class="sidebar-link-text">IQ Test</span>
            </a>
          </div>
        </div>
      <?php endif; ?>
      <?php if (($_SESSION['auth.user_role'] ?? '') === 'admin'): ?>
        <!-- School Admin Menu (untuk Guru BK) -->
        <?php
        // Cek apakah sedang di halaman School Admin
        $isSchoolAdminPage = str_starts_with($currentPath, '/admin/schools/my') || str_starts_with($currentPath, '/admin/students');
        ?>
        <div class="sidebar-nav-group">
          <div class="sidebar-nav-group-header <?= $isSchoolAdminPage ? 'active-group' : '' ?>">
            <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              <path d="m9 12 2 2 4-4" />
            </svg>
            <span class="sidebar-link-text">Admin Sekolah</span>
            <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-collapse-toggle>
              <path d="m6 9 6 6 6-6" />
            </svg>
          </div>
          <div class="sidebar-nav-group-content">
            <a data-spa href="/admin/schools/my" class="sidebar-link sidebar-link-sub <?= $currentPath === '/admin/schools/my' || $currentPath === '/admin/schools/my/edit' ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21c0-1.7.9-3.3 2.3-4.2" />
                <path d="M18.7 16.8c1.4.9 2.3 2.5 2.3 4.2" />
                <path d="M12 2a7 7 0 0 0-7 7v2H3v2h2v2H3v2h2v2a7 7 0 0 0 14 0v-2h2v-2h-2v-2h2V9h-2V9a7 7 0 0 0-7-7Z" />
                <path d="M12 2v4" />
                <path d="M12 18v4" />
              </svg>
              <span class="sidebar-link-text">Sekolah Saya</span>
            </a>
            <a data-spa href="/admin/students" class="sidebar-link sidebar-link-sub <?= str_starts_with($currentPath, '/admin/students') ? 'active' : '' ?>">
              <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
              <span class="sidebar-link-text">Kelola Siswa</span>
            </a>
          </div>
        </div>
      <?php endif; ?>
      <?php if (($_SESSION['auth.user_role'] ?? '') === 'super-admin'): ?>
        <!-- Super Admin Menu -->
        <?php
        // Cek apakah sedang di halaman Admin
        $isAdminPage = str_starts_with($currentPath, '/admin');
        ?>
        <a data-spa href="/admin/schools" class="sidebar-link <?= str_starts_with($currentPath, '/admin/schools') ? 'active' : '' ?>">
          <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21c0-1.7.9-3.3 2.3-4.2" />
            <path d="M18.7 16.8c1.4.9 2.3 2.5 2.3 4.2" />
            <path d="M12 2a7 7 0 0 0-7 7v2H3v2h2v2H3v2h2v2a7 7 0 0 0 14 0v-2h2v-2h-2v-2h2V9h-2V9a7 7 0 0 0-7-7Z" />
          </svg>
          <span class="sidebar-link-text">Sekolah</span>
        </a>
      <?php endif; ?>
      <a data-spa href="/settings" class="sidebar-link <?= $currentPath === '/settings' ? 'active' : '' ?>">
        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
          <circle cx="12" cy="12" r="3" />
        </svg>
        <span class="sidebar-link-text">Pengaturan</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <form data-spa action="/logout" method="POST" class="sidebar-logout-form">
        <button type="submit" class="sidebar-link">
          <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" x2="9" y1="12" y2="12" />
          </svg>
          <span class="sidebar-link-text">Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Overlay for mobile -->
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  <!-- Main Content -->
  <div class="main-wrapper">
    <!-- Header -->
    <header class="app-header">
      <button class="header-toggle" id="sidebar-toggle" aria-label="Toggle Sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" x2="21" y1="6" y2="6" />
          <line x1="3" x2="21" y1="12" y2="12" />
          <line x1="3" x2="21" y1="18" y2="18" />
        </svg>
      </button>
      <div class="header-actions">
        <!-- Avatar Dropdown using native <details> element - auto closes without JS -->
        <details class="avatar-dropdown">
          <summary class="avatar-trigger">
            <div class="avatar">
              <img src=<?= $_SESSION['auth.user_avatar'] ?? $_SESSION['auth.user_avatar_url'] ?? "/logo_app/mazu-icon.svg"; ?> alt="User Avatar" class="avatar-image">
            </div>
            <svg class="avatar-chevron" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m6 9 6 6 6-6" />
            </svg>
          </summary>
          <div class="avatar-menu">
            <div class="avatar-menu-header">
              <div class="avatar-menu-info">
                <span class="avatar-menu-name"><?= $_SESSION['auth.user_name'] ?? 'User Name'; ?></span>
                <span class="avatar-menu-email"><?= $_SESSION['auth.user_email'] ?? 'user@example.com'; ?></span>
              </div>
            </div>
            <div class="avatar-menu-divider"></div>
            <a data-spa href="/profile" class="avatar-menu-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
              <span>Profile</span>
            </a>
            <a data-spa href="/settings" class="avatar-menu-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              <span>Settings</span>
            </a>
            <div class="avatar-menu-divider"></div>
            <form data-spa action="/logout" method="POST" class="avatar-logout-form">
              <button type="submit" class="avatar-menu-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                  <polyline points="16 17 21 12 16 7" />
                  <line x1="21" x2="9" y1="12" y2="12" />
                </svg>
                <span>Logout</span>
              </button>
            </form>
          </div>
        </details>
      </div>
    </header>

    <!-- Page Content -->
    <main class="app-content" data-layout="(app)/layout.php">
      <?= $children; ?>
    </main>
  </div>
</div>