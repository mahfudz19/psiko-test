/**
 * Student Dashboard Controller
 * Scoped specifically for the student dashboard view
 */
(function() {
  'use strict';

  /**
   * Initialize Dashboard Interactivity
   */
  function initDashboard() {
    const container = document.querySelector('.student-dashboard-container');
    if (!container) return;

    console.log('Mazu Dashboard: Initializing student dashboard...');

    // 1. Task Checkbox Toggle
    const checkboxes = container.querySelectorAll('.task-checkbox input');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const taskItem = this.closest('.task-item');
        if (this.checked) {
          taskItem.classList.add('task-completed');
        } else {
          taskItem.classList.remove('task-completed');
        }
      });
    });

    // 2. Animate Progress Bars on Load
    // We use a small timeout to ensure the transition triggers after DOM insertion
    setTimeout(() => {
      const progressFills = container.querySelectorAll('.breakdown-fill, .stat-progress-bar, .rec-progress-fill');
      progressFills.forEach(fill => {
        const targetWidth = fill.style.width;
        fill.style.width = '0';
        // Force reflow
        fill.offsetHeight;
        fill.style.width = targetWidth;
      });
    }, 100);

    // 3. Quick Action Hover Effects (Optional enhancement)
    const cards = container.querySelectorAll('.quick-action-card, .stat-card');
    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        // Potential for sound effects or haptic feedback triggers
      });
    });
  }

  // Initialize on first load
  document.addEventListener('DOMContentLoaded', initDashboard);

  // Re-initialize after SPA navigation
  window.addEventListener('spa:navigated', initDashboard);

})();
