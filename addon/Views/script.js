/**
 * Toast Notification System
 * Menampilkan toast notification berdasarkan query parameter URL
 * Support untuk query: ?error=CODE&message=TEXT atau ?success=MESSAGE
 */

(function () {
  'use strict';

  /**
   * Parse query string dari URL menjadi object
   * @returns {Object} Object berisi key-value dari query parameter
   */
  function getQueryParams() {
    const params = {};
    const queryString = window.location.search.substring(1);
    if (!queryString) return params;

    const pairs = queryString.split('&');
    for (let pair of pairs) {
      const [key, value] = pair.split('=');
      if (key) {
        params[decodeURIComponent(key)] = decodeURIComponent(value || '').replace(/\+/g, ' ');
      }
    }
    return params;
  }

  /**
   * Membuat elemen toast notification
   * @param {string} type - Tipe toast ('success' atau 'error')
   * @param {string} message - Pesan yang ditampilkan
   * @returns {HTMLElement} Elemen toast
   */
  function createToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');

    // Icon berdasarkan tipe
    const icon = type === 'success'
      ? '<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
      : '<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';

    toast.innerHTML = `
      <div class="toast-icon-wrapper">${icon}</div>
      <div class="toast-content">
        <span class="toast-message">${escapeHtml(message)}</span>
      </div>
      <button class="toast-close" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    `;

    // Event listener untuk tombol close
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => removeToast(toast));

    return toast;
  }

  /**
   * Escape HTML untuk mencegah XSS
   * @param {string} text - Text yang akan di-escape
   * @returns {string} Text yang sudah di-escape
   */
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Menampilkan toast di container
   * @param {string} type - Tipe toast ('success' atau 'error')
   * @param {string} message - Pesan yang ditampilkan
   */
  function showToast(type, message) {
    let container = document.getElementById('toast-container');

    // Buat container jika belum ada
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = createToast(type, message);
    container.appendChild(toast);

    // Trigger reflow untuk animasi
    toast.offsetHeight;
    toast.classList.add('toast-show');

    // Auto remove setelah 5 detik
    const timeoutId = setTimeout(() => removeToast(toast), 5000);

    // Simpan timeout ID untuk cleanup jika ditutup manual
    toast.dataset.timeoutId = timeoutId;
  }

  /**
   * Menghapus toast dari container
   * @param {HTMLElement} toast - Elemen toast yang akan dihapus
   */
  function removeToast(toast) {
    // Clear timeout jika ada
    if (toast.dataset.timeoutId) {
      clearTimeout(parseInt(toast.dataset.timeoutId));
    }

    toast.classList.remove('toast-show');
    toast.classList.add('toast-hide');

    // Hapus elemen setelah animasi selesai
    toast.addEventListener('transitionend', () => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
      // Hapus query parameter setelah toast dihapus
      removeToastQueryParams();
    }, { once: true });
  }

  /**
   * Menghapus query parameter error, message, dan success dari URL
   */
  function removeToastQueryParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const toastParams = ['error', 'message', 'success'];
    let hasRemoved = false;

    for (let param of toastParams) {
      if (urlParams.has(param)) {
        urlParams.delete(param);
        hasRemoved = true;
      }
    }

    // Update URL tanpa reload jika ada parameter yang dihapus
    if (hasRemoved) {
      const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
      window.history.replaceState({}, '', newUrl);
    }
  }

  /**
   * Inisialisasi toast notification dari query parameter URL
   */
  function initToastFromQuery() {
    const params = getQueryParams();

    // Cek untuk error
    if (params.error || params.message) {
      const errorCode = params.error ? `Error ${params.error}: ` : '';
      const message = params.message || 'Terjadi kesalahan';
      showToast('error', errorCode + message);
    }
    // Cek untuk success
    else if (params.success) {
      showToast('success', params.success);
    }
  }

  // Jalankan saat DOM siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initToastFromQuery);
  } else {
    initToastFromQuery();
  }

  // Expose fungsi showToast untuk penggunaan manual
  window.showToast = showToast;
})();
