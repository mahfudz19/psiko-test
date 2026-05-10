/**
 * Sidebar Toggle Controller
 * Mengatur perilaku sidebar untuk desktop (minimize/maximize) dan mobile (show/hide)
 */
(function() {
  'use strict';

  // DOM Elements
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebar-overlay');
  const mainWrapper = document.querySelector('.main-wrapper');

  // State
  let isMobile = window.innerWidth <= 768;

  /**
   * Cek apakah tampilan mobile
   * @returns {boolean} true jika mobile
   */
  function checkMobile() {
    return window.innerWidth <= 768;
  }

  /**
   * Toggle sidebar untuk desktop (minimize/maximize)
   */
  function toggleDesktop() {
    sidebar.classList.toggle('minimized');
    mainWrapper.classList.toggle('minimized');
    
    // Simpan preferensi user ke localStorage
    const isMinimized = sidebar.classList.contains('minimized');
    localStorage.setItem('sidebar-minimized', isMinimized);
  }

  /**
   * Toggle sidebar untuk mobile (show/hide)
   */
  function toggleMobile() {
    sidebar.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
    
    // Prevent body scroll ketika sidebar terbuka
    if (sidebar.classList.contains('active')) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }

  /**
   * Close sidebar untuk mobile
   */
  function closeMobileSidebar() {
    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  /**
   * Handle toggle button click
   */
  function handleToggle() {
    if (isMobile) {
      toggleMobile();
    } else {
      toggleDesktop();
    }
  }

  /**
   * Handle window resize
   */
  function handleResize() {
    const wasMobile = isMobile;
    isMobile = checkMobile();

    // Jika berpindah dari mobile ke desktop
    if (wasMobile && !isMobile) {
      closeMobileSidebar();
      // Restore desktop state dari localStorage
      const isMinimized = localStorage.getItem('sidebar-minimized') === 'true';
      if (isMinimized) {
        sidebar.classList.add('minimized');
        mainWrapper.classList.add('minimized');
      } else {
        sidebar.classList.remove('minimized');
        mainWrapper.classList.remove('minimized');
      }
    } 
    // Jika berpindah dari desktop ke mobile
    else if (!wasMobile && isMobile) {
      sidebar.classList.remove('minimized');
      mainWrapper.classList.remove('minimized');
      closeMobileSidebar();
    }
  }

  /**
   * Initialize sidebar toggle listener
   * Perlu dipanggil ulang setelah navigasi SPA karena header mungkin di-render ulang
   */
  function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    if (sidebarToggle) {
      // Hapus listener lama jika ada (dengan clone) untuk mencegah double listener
      const newToggle = sidebarToggle.cloneNode(true);
      sidebarToggle.parentNode.replaceChild(newToggle, sidebarToggle);
      newToggle.addEventListener('click', handleToggle);
    }
  }

  // Event Listeners
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeMobileSidebar);
  }

  window.addEventListener('resize', handleResize);

  // Note: Collapse toggle handlers are now attached directly to each header
  // in the initCollapseState() function to ensure they work after SPA navigation

  // Initialize state saat page load
  document.addEventListener('DOMContentLoaded', function() {
    isMobile = checkMobile();
    
    // Jika desktop, restore state dari localStorage
    if (!isMobile) {
      const isMinimized = localStorage.getItem('sidebar-minimized') === 'true';
      if (isMinimized) {
        sidebar.classList.add('minimized');
        mainWrapper.classList.add('minimized');
      }
    }
    
    // Initialize sidebar toggle
    initSidebarToggle();
    
    // Initialize collapse state - semua group tertutup kecuali yang active
    initCollapseState();
  });

  /**
   * Update active state untuk sidebar navigation links
   * Dipanggil setelah SPA navigation selesai
   */
  function updateActiveState() {
    const currentPath = window.location.pathname;
    
    // Hapus semua active class
    document.querySelectorAll('.sidebar-link, .sidebar-nav-group-header').forEach(el => {
      el.classList.remove('active');
      el.classList.remove('active-group');
    });
    
    // Tambahkan active class ke link yang sesuai (termasuk header yang berfungsi sebagai link)
    document.querySelectorAll('.sidebar-link[data-spa], .sidebar-nav-group-header[data-spa]').forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPath) {
        link.classList.add('active');
      }
    });
    
    // Handle navigation groups - tambahkan active-group ke group header jika salah satu sub-link active
    const studentProfilePages = ['/profile/academic', '/profile/achievements', '/profile/results'];
    const pmbPages = ['/pmb/journey', '/pmb/simulation', '/pmb/scholarship'];
    const chatPages = ['/chat', '/chat/create'];
    const schoolAdminPages = ['/admin/schools/my', '/admin/schools/my/edit', '/admin/students'];
    const superAdminPages = ['/admin', '/admin/schools'];
    
    // Helper function untuk check dan set active group
    function setActiveGroup(headers, pages) {
      if (pages.includes(currentPath)) {
        headers.forEach(header => {
          if (header) header.classList.add('active-group');
        });
      }
    }
    
    // Update active-group state based on sub-pages
    document.querySelectorAll('.sidebar-nav-group').forEach(group => {
      const header = group.querySelector('.sidebar-nav-group-header');
      const subLinks = group.querySelectorAll('.sidebar-link-sub');
      
      let isSubPageActive = false;
      subLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
          isSubPageActive = true;
        }
      });
      
      if (isSubPageActive) {
        header.classList.add('active-group');
      }
    });
  }

  /**
   * Toggle collapse untuk navigation group
   * @param {HTMLElement} header - Header element yang diklik
   */
  function toggleCollapse(header) {
    const content = header.nextElementSibling;
    if (!content || !content.classList.contains('sidebar-nav-group-content')) {
      return;
    }
    
    const isCollapsed = content.classList.contains('collapsed');
    
    content.classList.toggle('collapsed');
    header.classList.toggle('collapsed');
    
    // Simpan state ke localStorage dengan key unik berdasarkan index group
    const allGroups = Array.from(header.closest('.sidebar-nav').querySelectorAll('.sidebar-nav-group'));
    const groupIndex = allGroups.indexOf(header.closest('.sidebar-nav-group'));
    localStorage.setItem(`sidebar-group-${groupIndex}-collapsed`, !isCollapsed);
  }

  /**
   * Initialize collapse state - semua group tertutup kecuali yang active
   */
  function initCollapseState() {
    const currentPath = window.location.pathname;
    const navGroups = document.querySelectorAll('.sidebar-nav-group');
    
    // Define pages untuk setiap group
    const groupPages = [
      ['/profile/academic', '/profile/achievements', '/profile/results'], // Profile group (index 0)
      ['/pmb/journey', '/pmb/simulation', '/pmb/scholarship'], // PMB group (index 1)
      ['/chat', '/chat/create'], // Chat group (index 2)
      ['/admin/schools/my', '/admin/schools/my/edit', '/admin/students'], // School Admin group (index 3)
      ['/admin', '/admin/schools'] // Super Admin group (index 4)
    ];
    
    navGroups.forEach((group, index) => {
      const header = group.querySelector('.sidebar-nav-group-header');
      const content = group.querySelector('.sidebar-nav-group-content');
      
      if (!header || !content) return;
      
      // Cek apakah group ini memiliki sub-menu yang active
      const pages = groupPages[index] || [];
      const isActiveGroup = pages.includes(currentPath);
      
      // Default behavior: collapse semua kecuali yang active
      // User preference akan di-save saat user manual toggle
      if (isActiveGroup) {
        content.classList.remove('collapsed');
        header.classList.remove('collapsed');
      } else {
        content.classList.add('collapsed');
        header.classList.add('collapsed');
      }
      
      // Setup click handler langsung ke header untuk memastikan berfungsi setelah SPA
      // Hapus listener lama jika ada (dengan clone and replace)
      const newHeader = header.cloneNode(true);
      header.parentNode.replaceChild(newHeader, header);
      
      // Add click handler baru
      newHeader.addEventListener('click', function(event) {
        // Cek apakah yang diklik adalah chevron icon (collapse toggle)
        const collapseToggle = event.target.closest('[data-collapse-toggle]');
        if (collapseToggle) {
          // Prevent navigasi SPA jika header adalah link
          event.preventDefault();
          event.stopPropagation();
          
          const content = this.nextElementSibling;
          if (!content || !content.classList.contains('sidebar-nav-group-content')) {
            return;
          }
          
          const isCollapsed = content.classList.contains('collapsed');
          content.classList.toggle('collapsed');
          this.classList.toggle('collapsed');
          
          // Simpan state ke localStorage
          const allGroups = Array.from(this.closest('.sidebar-nav').querySelectorAll('.sidebar-nav-group'));
          const groupIndex = allGroups.indexOf(this.closest('.sidebar-nav-group'));
          localStorage.setItem(`sidebar-group-${groupIndex}-collapsed`, !isCollapsed);
          return;
        }
        
        // Jika header adalah link <a>, biarkan navigasi SPA berjalan normal
        // Jika header adalah <div> (Admin Sekolah, Super Admin), toggle collapse
        if (this.tagName === 'DIV') {
          event.preventDefault();
          event.stopPropagation();
          
          const content = this.nextElementSibling;
          if (!content || !content.classList.contains('sidebar-nav-group-content')) {
            return;
          }
          
          const isCollapsed = content.classList.contains('collapsed');
          content.classList.toggle('collapsed');
          this.classList.toggle('collapsed');
          
          // Simpan state ke localStorage
          const allGroups = Array.from(this.closest('.sidebar-nav').querySelectorAll('.sidebar-nav-group'));
          const groupIndex = allGroups.indexOf(this.closest('.sidebar-nav-group'));
          localStorage.setItem(`sidebar-group-${groupIndex}-collapsed`, !isCollapsed);
        }
        // Untuk <a> link, biarkan SPA navigation proceed tanpa preventDefault
      });
    });
  }

  /**
   * Restore minimize state dari localStorage
   */
  function restoreMinimizeState() {
    const isMinimized = localStorage.getItem('sidebar-minimized') === 'true';
    
    if (isMinimized && !isMobile) {
      sidebar.classList.add('minimized');
      mainWrapper.classList.add('minimized');
    } else {
      sidebar.classList.remove('minimized');
      mainWrapper.classList.remove('minimized');
    }
  }

  // Listen ke SPA navigation event untuk update active state
  window.addEventListener('spa:navigated', function() {
    updateActiveState();
    restoreMinimizeState();
    initSidebarToggle();
    
    // Re-initialize collapse state setelah navigasi SPA
    // Ini akan membuka group yang active dan menutup yang lain
    initCollapseState();
    
    // Close mobile sidebar setelah navigasi
    if (isMobile) {
      closeMobileSidebar();
    }
    
    // Close avatar dropdown setelah navigasi
    const avatarDropdown = document.querySelector('.avatar-dropdown');
    if (avatarDropdown) {
      avatarDropdown.removeAttribute('open');
    }
  });

  // Avatar Dropdown - Close saat klik away
  document.addEventListener('click', function(event) {
    const avatarDropdown = document.querySelector('.avatar-dropdown');
    if (avatarDropdown && avatarDropdown.hasAttribute('open')) {
      const isClickInsideDropdown = avatarDropdown.contains(event.target);
      const isClickOnSummary = event.target.closest('.avatar-trigger');
      
      // Close jika klik di luar dropdown atau klik menu item
      if (!isClickInsideDropdown || (isClickInsideDropdown && !isClickOnSummary)) {
        // Cek apakah yang diklik adalah menu item (bukan summary)
        const isMenuItem = event.target.closest('.avatar-menu-item');
        if (!isClickInsideDropdown || isMenuItem) {
          avatarDropdown.removeAttribute('open');
        }
      }
    }
  });

})();
