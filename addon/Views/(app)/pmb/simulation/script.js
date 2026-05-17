/**
 * Save current step data to database
 * @param {number} stepId - Current step ID (1-5)
 * @param {FormData} formData - Form data to save
 * @returns {Promise<boolean>} - Success status
 */
async function saveDraft(stepId, formData) {
    try {
        const response = await fetch('/pmb/simulation/step', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || getCsrfToken(),
            },
            body: JSON.stringify({
                step_id: stepId,
                data: Object.fromEntries(formData),
            }),
        });

        const result = await response.json();

        if (result.success) {
            showToast('Draft berhasil disimpan!', 'success');
            return true;
        } else {
            showToast(result.message || 'Gagal menyimpan draft', 'error');
            return false;
        }
    } catch (error) {
        console.error('Save draft error:', error);
        showToast('Terjadi kesalahan saat menyimpan', 'error');
        return false;
    }
}

/**
 * Get CSRF token from meta tag or cookie
 * @returns {string|null} - CSRF token
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || null;
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Toast type (success, error, warning)
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 24px;
        border-radius: 8px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Previous step navigation
function previousStep(stepId) {
    window.location.href = '/pmb/simulation?step=' + stepId;
}

// Copy bank account number
function copyAccount(accountNumber) {
    navigator.clipboard.writeText(accountNumber);
    showToast('Nomor rekening berhasil dicopy!', 'success');
}

// Copy registration number
function copyRegNumber() {
    const regNumber = document.querySelector('.reg-number')?.textContent;
    if (regNumber) {
        navigator.clipboard.writeText(regNumber);
        showToast('Nomor pendaftaran berhasil dicopy!', 'success');
    }
}

// Convert to real application
function convertToReal() {
    if (confirm('Apakah kamu yakin ingin convert simulasi ini ke pendaftaran sebenarnya?')) {
        window.location.href = '/pmb/simulation/convert';
    }
}

// Form submission handlers for each step
document.querySelectorAll('.simulation-form').forEach(form => {
    const stepMatch = form.id?.match(/step-form-(\d+)/);
    const stepId = stepMatch ? parseInt(stepMatch[1]) : null;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!stepId) {
            showToast('Step ID tidak valid', 'error');
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.innerHTML;

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menyimpan...';
        }

        try {
            const response = await fetch('/pmb/simulation/step', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({
                    step_id: stepId,
                    data: Object.fromEntries(formData),
                }),
            });

            const result = await response.json();

            if (result.success) {
                // If last step, redirect to complete
                if (stepId === 3) {
                    window.location.href = '/pmb/simulation/complete';
                } else {
                    // Navigate to next step
                    const nextStep = result.next_step || stepId + 1;
                    window.location.href = '/pmb/simulation?step=' + nextStep;
                }
            } else {
                // Show validation errors
                if (result.errors) {
                    Object.entries(result.errors).forEach(([field, message]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('error');
                            const errorDiv = input.parentElement.querySelector('.error-message');
                            if (!errorDiv) {
                                const errorEl = document.createElement('div');
                                errorEl.className = 'error-message';
                                errorEl.style.cssText = 'color: #ef4444; font-size: 12px; margin-top: 4px;';
                                errorEl.textContent = message;
                                input.parentElement.appendChild(errorEl);
                            } else {
                                errorEl.textContent = message;
                            }
                        }
                    });
                }
                showToast(result.message || 'Gagal menyimpan data', 'error');
            }
        } catch (error) {
            console.error('Form submission error:', error);
            showToast('Terjadi kesalahan saat menyimpan data', 'error');
        } finally {
            // Restore button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    });
});

// Save draft button handlers
document.querySelectorAll('button[onclick="saveDraft()"]').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        // Find the form within the current step
        const form = this.closest('form.simulation-form');
        if (!form) {
            console.error('Form not found');
            return;
        }

        const stepMatch = form.id?.match(/step-form-(\d+)/);
        const stepId = stepMatch ? parseInt(stepMatch[1]) : null;

        if (stepId) {
            const formData = new FormData(form);
            await saveDraft(stepId, formData);
        }
    });
});

// Add CSS animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }
    input.error, select.error, textarea.error {
        border-color: #ef4444 !important;
    }
`;
document.head.appendChild(style);