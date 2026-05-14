/**
 * RIASEC Test Take Page - JavaScript (dengan Stepper + Browser Storage Cache)
 * Handles option selection, stepper navigation, progress tracking, form submission,
 * and sessionStorage caching for answer persistence
 */

(function() {
    'use strict';

    /**
     * Storage key prefix untuk sessionStorage
     */
    const STORAGE_PREFIX = 'riasec_answers_';
    
    /**
     * TTL (Time-To-Live) dalam milidetik (2 jam)
     */
    const STORAGE_TTL = 2 * 60 * 60 * 1000;

    /**
     * Initialize Riasec Take page functionality
     */
    function initRiasecTake() {
        // Get elements
        const form = document.getElementById('riasec-form');
        const submitBtn = document.getElementById('submit-btn');
        const submitFromSection = document.getElementById('submit-from-section');
        const backToTopBtn = document.getElementById('back-to-top-btn');
        const confirmModal = document.getElementById('confirm-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const confirmSubmitBtn = document.getElementById('confirm-submit');
        const answeredCountEl = document.getElementById('answered-count');
        const progressFillEl = document.getElementById('progress-fill');
        const progressTextEl = document.getElementById('progress-text');

        // Stepper state
        const dimensions = ['R', 'I', 'A', 'S', 'E', 'C'];
        let currentStep = 0;

        // Get session ID from form
        const sessionIdInput = form?.querySelector('input[name="session_id"]');
        const sessionId = sessionIdInput ? sessionIdInput.value : null;
        const storageKey = sessionId ? STORAGE_PREFIX + sessionId : null;

        // Get total count
        const totalStatements = document.querySelectorAll('.riasec-statement-item').length;
        
        /**
         * Get cached data dari sessionStorage
         * @returns {Object|null} Cached data atau null jika tidak ada/expired
         */
        function getCachedData() {
            if (!storageKey) return null;
            
            try {
                const cached = sessionStorage.getItem(storageKey);
                if (!cached) return null;
                
                const data = JSON.parse(cached);
                const now = Date.now();
                
                // Cek apakah data sudah expired (TTL)
                if (data.timestamp && (now - data.timestamp > STORAGE_TTL)) {
                    // Data expired, hapus
                    sessionStorage.removeItem(storageKey);
                    return null;
                }
                
                return data;
            } catch (e) {
                console.warn('Failed to parse cached data:', e);
                return null;
            }
        }

        /**
         * Save data ke sessionStorage
         * @param {Object} data Data yang akan disimpan
         */
        function saveToCache(data) {
            if (!storageKey) return;
            
            try {
                const dataWithTimestamp = {
                    ...data,
                    timestamp: Date.now()
                };
                sessionStorage.setItem(storageKey, JSON.stringify(dataWithTimestamp));
            } catch (e) {
                console.warn('Failed to save to cache:', e);
            }
        }

        /**
         * Clear cache dari sessionStorage
         */
        function clearCache() {
            if (!storageKey) return;
            sessionStorage.removeItem(storageKey);
        }

        /**
         * Restore jawaban dari cache
         */
        function restoreAnswers() {
            const cached = getCachedData();
            if (!cached || !cached.answers) return;

            console.log('[restoreAnswers] Restoring from cache:', cached);

            // Restore jawaban per statement
            Object.entries(cached.answers).forEach(([statementId, answerValue]) => {
                const statementItem = document.querySelector(`[data-statement-id="${statementId}"]`);
                if (!statementItem) return;

                // Find and select the option
                const option = statementItem.querySelector(`.option[data-value="${answerValue}"]`);
                const radioInput = statementItem.querySelector(`input[type="radio"][value="${answerValue}"]`);

                if (option && radioInput) {
                    // Remove selected from all options
                    statementItem.querySelectorAll('.option').forEach(opt => {
                        opt.classList.remove('selected');
                    });

                    // Select the correct option
                    option.classList.add('selected');
                    radioInput.checked = true;
                    statementItem.classList.add('answered');
                }
            });

            // Restore current step jika ada
            if (typeof cached.currentStep === 'number') {
                currentStep = cached.currentStep;
            }

            // Update UI
            updateProgress();
            updateAllDimensionProgress();
            updateStepperUI();

            // Scroll ke step yang direstore
            if (cached.currentStep !== undefined && cached.currentStep !== null) {
                console.log('[restoreAnswers] Restoring to step:', cached.currentStep);
                setTimeout(() => {
                    goToStep(cached.currentStep);
                }, 100);
            }
        }

        /**
         * Cleanup expired cache entries
         */
        function cleanupExpiredCache() {
            const now = Date.now();
            const keysToRemove = [];

            for (let i = 0; i < sessionStorage.length; i++) {
                const key = sessionStorage.key(i);
                if (key && key.startsWith(STORAGE_PREFIX)) {
                    try {
                        const cached = sessionStorage.getItem(key);
                        if (cached) {
                            const data = JSON.parse(cached);
                            if (data.timestamp && (now - data.timestamp > STORAGE_TTL)) {
                                keysToRemove.push(key);
                            }
                        }
                    } catch (e) {
                        // Ignore parse errors, mark for removal
                        keysToRemove.push(key);
                    }
                }
            }

            // Remove expired keys
            keysToRemove.forEach(key => sessionStorage.removeItem(key));
        }

        /**
         * Save current state to cache
         */
        function saveCurrentState() {
            const answers = {};
            document.querySelectorAll('.riasec-statement-item.answered').forEach(item => {
                const statementId = item.dataset.statementId;
                const radioInput = item.querySelector('input[type="radio"]:checked');
                if (statementId && radioInput) {
                    answers[statementId] = radioInput.value;
                }
            });

            saveToCache({
                sessionId: sessionId,
                answers: answers,
                currentStep: currentStep
            });
        }

        /**
         * Update global progress counter and bar
         */
        function updateProgress() {
            const answered = document.querySelectorAll('.riasec-statement-item.answered').length;
            const progress = totalStatements > 0 ? (answered / totalStatements) * 100 : 0;

            if (answeredCountEl) answeredCountEl.textContent = answered;
            if (progressFillEl) progressFillEl.style.width = progress + '%';
            if (progressTextEl) progressTextEl.textContent = Math.round(progress) + '%';

            // Toggle submit section visibility
            toggleSubmitSection(answered === totalStatements);
        }

        /**
         * Toggle submit section visibility based on completion
         */
        function toggleSubmitSection(allAnswered) {
            const submitSection = document.querySelector('.riasec-submit-section');
            if (!submitSection) return;

            if (allAnswered) {
                submitSection.classList.add('visible');
                // Scroll to submit section smoothly
                setTimeout(() => {
                    submitSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 300);
            } else {
                submitSection.classList.remove('visible');
            }
        }

        /**
         * Update progress bar untuk dimensi tertentu
         */
        function updateDimensionProgress(dimension) {
            const section = document.querySelector(`[data-dimension="${dimension}"]`);
            if (!section) return;

            const totalInDimension = section.querySelectorAll('.riasec-statement-item').length;
            const answeredInDimension = section.querySelectorAll('.riasec-statement-item.answered').length;
            const progress = totalInDimension > 0 ? (answeredInDimension / totalInDimension) * 100 : 0;

            // Update progress bar
            const progressFill = section.querySelector('.dimension-progress-fill');
            const progressText = section.querySelector('.dimension-progress-text');

            if (progressFill) {
                progressFill.style.width = progress + '%';
            }
            if (progressText) {
                progressText.textContent = `${answeredInDimension}/${totalInDimension}`;
            }

            // Check if dimension is complete
            if (answeredInDimension === totalInDimension && totalInDimension > 0) {
                section.classList.add('completed');
            }
        }

        /**
         * Update semua progress bars
         */
        function updateAllDimensionProgress() {
            dimensions.forEach(dim => updateDimensionProgress(dim));
        }

        /**
         * Update stepper UI
         */
        function updateStepperUI() {
            dimensions.forEach((dim, index) => {
                const stepperItem = document.querySelector(`.stepper-item[data-step="${dim}"]`);
                if (!stepperItem) return;

                // Remove all state classes
                stepperItem.classList.remove('active', 'completed', 'pending');

                if (index < currentStep) {
                    // Completed step
                    stepperItem.classList.add('completed');
                    stepperItem.querySelector('.stepper-icon').textContent = '✓';
                } else if (index === currentStep) {
                    // Active step
                    stepperItem.classList.add('active');
                    stepperItem.querySelector('.stepper-icon').textContent = index + 1;
                } else {
                    // Pending step
                    stepperItem.classList.add('pending');
                    stepperItem.querySelector('.stepper-icon').textContent = index + 1;
                }
            });
        }

        /**
         * Go to specific step
         */
        function goToStep(stepIndex) {
            if (stepIndex < 0 || stepIndex >= dimensions.length) return;

            // Hide all sections
            document.querySelectorAll('.riasec-dimension-section').forEach(section => {
                section.classList.remove('active');
                section.classList.add('hidden');
            });

            // Show target section - use specific selector for dimension sections only
            const targetSection = document.querySelector(`.riasec-dimension-section[data-step-index="${stepIndex}"]`);
            if (targetSection) {
                // Force reflow to ensure class changes are applied
                void targetSection.offsetWidth;
                
                targetSection.classList.remove('hidden');
                targetSection.classList.add('active');

                // Debug logging
                console.log('[goToStep] Step:', stepIndex, 'Section found:', targetSection !== null, 'Classes:', targetSection.className);

                // Update state
                currentStep = stepIndex;

                // Update UI
                updateStepperUI();
                updateDimensionProgress(dimensions[stepIndex]);

                // Save current step to cache
                saveCurrentState();

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Section tidak ada (empty dimension), coba step berikutnya
                // Cari section valid berikutnya
                console.log('[goToStep] Section not found for step:', stepIndex);
                for (let i = stepIndex + 1; i < dimensions.length; i++) {
                    const nextSection = document.querySelector(`.riasec-dimension-section[data-step-index="${i}"]`);
                    if (nextSection) {
                        goToStep(i);
                        return;
                    }
                }
                // Jika tidak ada section valid, kembali ke step sebelumnya
                for (let i = stepIndex - 1; i >= 0; i--) {
                    const prevSection = document.querySelector(`.riasec-dimension-section[data-step-index="${i}"]`);
                    if (prevSection) {
                        goToStep(i);
                        return;
                    }
                }
            }
        }

        /**
         * Validate current step (check all questions answered)
         */
        function validateCurrentStep() {
            const currentDimension = dimensions[currentStep];
            const currentSection = document.querySelector(`[data-dimension="${currentDimension}"].active`);
            
            if (!currentSection) return true;

            const unansweredItems = currentSection.querySelectorAll('.riasec-statement-item:not(.answered)');
            
            if (unansweredItems.length > 0) {
                // Mark as error
                unansweredItems.forEach(item => {
                    item.classList.add('error');
                });

                // Scroll to first error
                const firstError = unansweredItems[0];
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });

                return false;
            }

            return true;
        }

        /**
         * Clear all error states
         */
        function clearErrors() {
            document.querySelectorAll('.riasec-statement-item.error').forEach(item => {
                item.classList.remove('error');
            });
        }

        /**
         * Go to next step
         */
        function nextStep() {
            // Validate current step first
            if (!validateCurrentStep()) {
                return;
            }

            // Clear any errors
            clearErrors();

            // Move to next step
            if (currentStep < dimensions.length - 1) {
                goToStep(currentStep + 1);
            }
        }

        /**
         * Go to previous step
         */
        function prevStep() {
            clearErrors();
            if (currentStep > 0) {
                goToStep(currentStep - 1);
            }
        }

        /**
         * Handle option click
         */
        function handleOptionClick(event, optionEl) {
            // Prevent event from bubbling up
            event.preventDefault();
            event.stopPropagation();

            const statementItem = optionEl.closest('.riasec-statement-item');
            const radioInput = optionEl.querySelector('input[type="radio"]');

            if (!statementItem || !radioInput) return;

            // Remove selected from all options in this statement
            statementItem.querySelectorAll('.option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selected to clicked option
            optionEl.classList.add('selected');

            // Check the radio input
            radioInput.checked = true;

            // Mark statement as answered
            statementItem.classList.add('answered');

            // Clear error state if any
            statementItem.classList.remove('error');

            // Update progress
            updateProgress();
            
            // Update dimension progress
            const dimension = statementItem.closest('.riasec-dimension-section')?.dataset.dimension;
            if (dimension) {
                updateDimensionProgress(dimension);
            }

            // Save to cache
            saveCurrentState();
        }

        /**
         * Initialize option click handlers using event delegation
         */
        function initOptionHandlers() {
            // Use event delegation on the form level
            form.addEventListener('click', function(event) {
                const optionEl = event.target.closest('.option');
                
                if (optionEl) {
                    handleOptionClick(event, optionEl);
                }
            });

            // Handle keyboard accessibility
            form.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    const optionEl = event.target.closest('.option');
                    if (optionEl) {
                        handleOptionClick(event, optionEl);
                    }
                }
            });
        }

        /**
         * Initialize stepper navigation
         */
        function initStepperNavigation() {
            // Click on stepper items (for completed steps - allows editing)
            document.querySelectorAll('.stepper-item').forEach(item => {
                item.addEventListener('click', function() {
                    const stepIndex = parseInt(this.dataset.stepIndex || '0');
                    
                    // Only allow clicking on completed steps (go back to edit)
                    if (stepIndex < currentStep) {
                        goToStep(stepIndex);
                    }
                });
            });

            // Previous buttons
            document.querySelectorAll('.btn-prev').forEach(btn => {
                btn.addEventListener('click', function() {
                    prevStep();
                });
            });

            // Next buttons
            document.querySelectorAll('.btn-next').forEach(btn => {
                btn.addEventListener('click', function() {
                    nextStep();
                });
            });

            // Submit from section (last dimension)
            if (submitFromSection) {
                submitFromSection.addEventListener('click', function() {
                    // Validate current step first
                    if (!validateCurrentStep()) {
                        return;
                    }
                    
                    // Check if all steps are completed
                    const allAnswered = document.querySelectorAll('.riasec-statement-item.answered').length;
                    if (allAnswered < totalStatements) {
                        const remaining = totalStatements - allAnswered;
                        alert('Anda belum menjawab ' + remaining + ' pertanyaan. Pastikan semua terjawab sebelum mengirim!');
                        return;
                    }
                    
                    // Show confirmation modal
                    if (confirmModal) {
                        confirmModal.showModal();
                    }
                });
            }
        }

        /**
         * Initialize form handler
         */
        function initFormHandler() {
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Navigate to first incomplete step if any
                for (let i = 0; i < dimensions.length; i++) {
                    const section = document.querySelector(`[data-step-index="${i}"]`);
                    const unanswered = section?.querySelectorAll('.riasec-statement-item:not(.answered)').length || 0;
                    
                    if (unanswered > 0) {
                        goToStep(i);
                        alert('Silakan selesaikan semua pertanyaan pada kategori ini terlebih dahulu.');
                        return;
                    }
                }
                
                // All steps completed, show confirmation modal
                if (confirmModal) {
                    confirmModal.showModal();
                }
            });
        }

        /**
         * Initialize modal handlers
         */
        function initModalHandlers() {
            // Cancel button
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (confirmModal) confirmModal.close();
                });
            }

            // Confirm submit button (with double-submit prevention)
            if (confirmSubmitBtn) {
                confirmSubmitBtn.addEventListener('click', function() {
                    const answered = document.querySelectorAll('.riasec-statement-item.answered').length;

                    if (answered < totalStatements) {
                        const remaining = totalStatements - answered;
                        alert('Anda belum menjawab ' + remaining + ' pertanyaan. Pastikan semua terjawab sebelum mengirim!');
                        if (confirmModal) confirmModal.close();
                        return;
                    }

                    // Double-submit prevention: disable button
                    confirmSubmitBtn.disabled = true;
                    confirmSubmitBtn.textContent = 'Mengirim...';

                    // Submit form
                    if (form) form.submit();
                });
            }

            // Back to top button
            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // Close modal when clicking backdrop
            if (confirmModal) {
                confirmModal.addEventListener('click', function(event) {
                    const rect = confirmModal.getBoundingClientRect();
                    const isInDialog = (rect.top <= event.clientY && event.clientY <= rect.top + rect.height
                        && rect.left <= event.clientX && event.clientX <= rect.left + rect.width);
                    
                    if (!isInDialog) {
                        confirmModal.close();
                    }
                });
            }
        }

        /**
         * Initialize beforeunload warning
         */
        function initBeforeUnloadWarning() {
            if (!form) return;

            window.addEventListener('beforeunload', function(event) {
                // Check if there are any answered questions
                const answered = document.querySelectorAll('.riasec-statement-item.answered').length;
                
                // Only show warning if user has answered something but not submitted
                if (answered > 0 && answered < totalStatements) {
                    event.preventDefault();
                    event.returnValue = '';
                    return '';
                }
            });
        }

        /**
         * Initialize form submit handler (for clearing cache on success)
         */
        function initFormSubmitHandler() {
            if (!form) return;

            // Listen for form submit success (after form.submit() is called)
            // We'll clear cache when page is about to unload after successful submit
            const originalSubmit = form.submit;
            form.submit = function() {
                // Cache will be cleared on next page load if submit succeeds
                // If submit fails, user can restore from cache
                return originalSubmit.call(this);
            };
        }

        // Initialize everything
        initStepperNavigation();
        initOptionHandlers();
        initFormHandler();
        initModalHandlers();
        initBeforeUnloadWarning();
        initFormSubmitHandler();
        
        // Cleanup expired cache on load
        cleanupExpiredCache();
        
        // Debug: Log all sections on page load
        document.querySelectorAll('.riasec-dimension-section').forEach(section => {
            console.log('[init] Section:', {
                dimension: section.dataset.dimension,
                stepIndex: section.dataset.stepIndex,
                className: section.className
            });
        });
        
        // Restore answers from cache (if any)
        restoreAnswers();
        
        updateStepperUI();
        updateAllDimensionProgress();
        updateProgress();
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRiasecTake);
    } else {
        initRiasecTake();
    }

    // Re-initialize after SPA navigation
    window.addEventListener('spa:navigated', initRiasecTake);

    // Expose for manual calls
    window.initRiasecTake = initRiasecTake;
})();
