/**
 * Sidebar Toggle Controller
 * Mengatur perilaku sidebar untuk desktop (minimize/maximize) dan mobile (show/hide)
 */
(function() {
  'use strict';

  // DOM Elements
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebar-overlay');
  const sidebarToggle = document.getElementById('sidebar-toggle');
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

  // Event Listeners
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', handleToggle);
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeMobileSidebar);
  }

  window.addEventListener('resize', handleResize);

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
    });
    
    // Tambahkan active class ke link yang sesuai
    document.querySelectorAll('.sidebar-link[data-spa]').forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPath) {
        link.classList.add('active');
      }
    });
    
    // Handle PMB Journey group - tambahkan active ke group header jika salah satu sub-link active
    const pmbPages = ['/pmb/journey', '/pmb/simulation', '/pmb/scholarship'];
    if (pmbPages.includes(currentPath)) {
      const pmbGroupHeader = document.querySelector('.sidebar-nav-group-header');
      if (pmbGroupHeader) {
        pmbGroupHeader.classList.add('active');
      }
    }
  }

  // Listen ke SPA navigation event untuk update active state
  window.addEventListener('spa:navigated', function() {
    updateActiveState();
    
    // Close mobile sidebar setelah navigasi
    if (isMobile) {
      closeMobileSidebar();
    }
  });

})();
