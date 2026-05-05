<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
?>
<div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="/logo_app/mazu-icon.svg" alt="Mazu Logo" class="sidebar-logo">
            <span class="sidebar-app-name">Mazu App</span>
        </div>
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
            <a data-spa href="/profile" class="sidebar-link <?= in_array($currentPath, ['/profile', '/profile/academic', '/profile/achievements', '/profile/results', '/profile/schedule', '/profile/permissions', '/profile/students', '/profile/edit']) ? 'active' : '' ?>">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span class="sidebar-link-text">Profile</span>
            </a>
            <?php if (($_SESSION['auth.user_role'] ?? '') === 'user'): ?>
                <!-- PMB Journey Menu (hanya untuk siswa) -->
                <?php
                // Cek apakah sedang di halaman PMB
                $isPmbPage = in_array($currentPath, ['/pmb/journey', '/pmb/simulation', '/pmb/scholarship']);
                ?>
                <div class="sidebar-nav-group">
                    <div class="sidebar-nav-group-header <?= $isPmbPage ? 'active' : '' ?>">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <span class="sidebar-link-text">PMB Journey</span>
                    </div>
                    <div class="sidebar-nav-group-content">
                        <a data-spa href="/pmb/journey" class="sidebar-link sidebar-link-sub <?= $currentPath === '/pmb/journey' ? 'active' : '' ?>">
                            <span class="sidebar-link-text">🎯 Journey</span>
                        </a>
                        <a data-spa href="/pmb/simulation" class="sidebar-link sidebar-link-sub <?= $currentPath === '/pmb/simulation' ? 'active' : '' ?>">
                            <span class="sidebar-link-text">📝 Simulasi PMB</span>
                        </a>
                        <a data-spa href="/pmb/scholarship" class="sidebar-link sidebar-link-sub <?= $currentPath === '/pmb/scholarship' ? 'active' : '' ?>">
                            <span class="sidebar-link-text">💰 Beasiswa</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <a data-spa href="/settings" class="sidebar-link <?= $currentPath === '/settings' ? 'active' : '' ?>">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span class="sidebar-link-text">Settings</span>
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
            <div class="header-title">
                <h1><?= $meta->title ?? 'Dashboard'; ?></h1>
            </div>
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

<script src="/addon/Views/(app)/script.js"></script>