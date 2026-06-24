<?php

/**
 * @var array $config - Konfigurasi tes yang akan diassign
 * @var array $allSchools - Daftar semua sekolah
 * @var array $assignedSchoolIds - IDs sekolah yang sudah diassign
 * @var array $defaultSchoolIds - Array of school_id yang config ini jadi default-nya
 */
?>

<div class="assign-config-page">
    <div class="page-header">
        <div>
            <a data-spa href="/admin/tests" class="back-link">
                ← Kembali ke Daftar Konfigurasi
            </a>
            <h1>Assign Konfigurasi ke Sekolah</h1>
            <p class="page-description">Kelola sekolah yang menggunakan konfigurasi tes ini</p>
        </div>
    </div>

    <!-- Config Info Card -->
    <div class="config-info-card">
        <div class="config-info-content">
            <div class="config-icon">📋</div>
            <div class="config-info">
                <h2><?= htmlspecialchars($config['name']) ?></h2>
                <div class="config-meta">
                    <span class="badge badge-type badge-<?= htmlspecialchars($config['test_type']) ?>">
                        <?= htmlspecialchars($config['test_type']) ?>
                    </span>
                    <?php
                    $dimensions = json_decode($config['dimensions'], true) ?? [];
                    $dimensionKeys = array_keys($dimensions);
                    ?>
                    <span class="dimension-info">
                        <span class="dimension-label">Dimensi:</span>
                        <?php foreach ($dimensionKeys as $i => $key): ?>
                            <span class="dimension-tag"><?= $key ?></span><?= $i < count($dimensionKeys) - 1 ? ',' : '' ?>
                        <?php endforeach; ?>
                    </span>
                </div>
            </div>
            <div class="config-stats">
                <div class="stat-item">
                    <span class="stat-value" id="assignedCount"><?= count($assignedSchoolIds) ?></span>
                    <span class="stat-label">Sekolah Diassign</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar-container">
        <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                id="searchInput"
                class="form-input"
                placeholder="Cari nama sekolah atau NPSN..."
                autocomplete="off" />
            <button type="button" id="clearSearch" class="btn-clear-search" style="display: none;">
                ✕
            </button>
        </div>
    </div>
    <p class="default-hint-text">
        💡 Centang <strong>"Default"</strong> pada sekolah untuk menjadikan konfigurasi ini
        sebagai config default untuk test type tersebut di sekolah itu.
        Siswa tetap bisa tes meski sekolahnya tidak dicentang (fallback otomatis).
    </p>

    <!-- Two Column Layout: Search Results (Left) | Assigned Schools (Right) -->
    <div class="section-container">
        <!-- Search Results Section (Left) -->
        <div class="section" id="searchResultsSection">
            <div class="section-header">
                <h3 class="section-title">
                    <span class="section-icon">📋</span>
                    Hasil Pencarian
                </h3>
            </div>
            <div class="section-body" id="searchResults">
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <p>Ketik untuk mencari sekolah</p>
                    <p class="empty-hint">Cari berdasarkan nama sekolah, NPSN, atau kota</p>
                </div>
            </div>
        </div>

        <!-- Assigned Schools Section (Right) -->
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">
                    <span class="section-icon">✅</span>
                    Sekolah Diassign
                    <span class="section-badge" id="assignedBadge"><?= count($assignedSchoolIds) ?></span>
                </h3>
            </div>
            <div class="section-body" id="assignedList">
                <?php if (empty($assignedSchoolIds)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🏫</div>
                        <p>Belum ada sekolah yang diassign</p>
                        <p class="empty-hint">Cari sekolah dan klik "Assign" untuk menambahkan</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($assignedSchoolIds as $schoolId): ?>
                        <?php
                        $school = array_filter($allSchools, fn($s) => $s['id'] == $schoolId);
                        $school = reset($school);
                        $isDefault = in_array((int)$schoolId, array_map('intval', $defaultSchoolIds), true);
                        ?>
                        <div class="list-item <?= $isDefault ? 'is-default' : '' ?>"
                            data-school-id="<?= $schoolId ?>">
                            <div class="item-content">
                                <span class="item-icon">🏫</span>
                                <div class="item-details">
                                    <span class="item-name"><?= htmlspecialchars($school['name']) ?></span>
                                    <span class="item-meta">
                                        <?= htmlspecialchars($school['city'] ?? '-') ?>
                                        <span class="separator">•</span>
                                        NPSN: <?= htmlspecialchars($school['npsn']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="item-actions">
                                <label class="default-toggle" title="Jadikan config default untuk test type ini di sekolah tersebut">
                                    <input type="checkbox"
                                        class="default-checkbox"
                                        data-school-id="<?= $schoolId ?>"
                                        <?= $isDefault ? 'checked' : '' ?> />
                                    <span class="default-toggle-label">Default</span>
                                </label>
                                <button type="button" class="btn-icon btn-unassign"
                                    data-school-id="<?= $schoolId ?>"
                                    data-school-name="<?= htmlspecialchars($school['name']) ?>"
                                    title="Lepas">
                                    Lepas
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <form method="POST" action="/admin/tests/<?= $config['id'] ?>/assign" id="assignForm">
        <?= csrf_field() ?>
        <div id="defaultSchoolsContainer"></div>
        <div id="schoolIdsContainer"></div>

        <div class="form-actions">
            <a data-spa href="/admin/tests" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="saveBtn">
                <span>💾</span> Simpan Assignment
            </button>
        </div>
    </form>
</div>

<script>
    // Store all schools data - global scope for access in init function
    const allSchools = <?= json_encode($allSchools) ?>;
    // Convert to string array for consistent comparison (PHP returns integers)
    let assignedSchoolIds = <?= json_encode($assignedSchoolIds) ?>.map(id => String(id));
    // Array of school_id (as string) yang config ini jadi default-nya
    let defaultSchoolIds = <?= json_encode($defaultSchoolIds ?? []) ?>.map(id => String(id));

    // Flag to prevent duplicate initialization
    let isInitialized = false;

    // Initialize function for SPA navigation compatibility
    function initAssignPage() {
        // Prevent duplicate initialization
        if (isInitialized) return;

        // DOM Elements - must be queried inside init function for SPA
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const searchResultsEl = document.getElementById('searchResults');
        const searchResultsSection = document.getElementById('searchResultsSection');
        const assignedListEl = document.getElementById('assignedList');
        const assignedBadgeEl = document.getElementById('assignedBadge');
        const assignedCountEl = document.getElementById('assignedCount');
        const defaultSchoolsContainer = document.getElementById('defaultSchoolsContainer');
        const schoolIdsContainer = document.getElementById('schoolIdsContainer');
        const assignForm = document.getElementById('assignForm');

        // Skip if elements don't exist (SPA may call this on wrong page)
        if (!searchInput || !assignForm) return;

        // Mark as initialized
        isInitialized = true;

        // Helper: Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper: Update assigned count
        function updateAssignedCount() {
            const count = assignedSchoolIds.length;
            assignedBadgeEl.textContent = count;
            assignedCountEl.textContent = count;
        }

        // Helper: Render search results
        function renderSearchResults(schools) {
            if (schools.length === 0) {
                searchResultsEl.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">🔍</div>
                        <p>Tidak ada sekolah ditemukan</p>
                        <p class="empty-hint">Coba kata kunci lain</p>
                    </div>
                `;
                return;
            }

            const html = schools.map(school => `
                <div class="list-item" data-school-id="${school.id}">
                    <div class="item-content">
                        <span class="item-icon">🏫</span>
                        <div class="item-details">
                            <span class="item-name">${escapeHtml(school.name)}</span>
                            <span class="item-meta">
                                ${escapeHtml(school.city || '-')}
                                <span class="separator">•</span>
                                NPSN: ${escapeHtml(school.npsn)}
                            </span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <button type="button" class="btn-icon btn-assign"
                            data-school-id="${school.id}"
                            data-school-name="${escapeHtml(school.name)}">
                            Assign
                        </button>
                    </div>
                </div>
            `).join('');

            searchResultsEl.innerHTML = html;
        }

        // Search Handler
        function handleSearch(e) {
            const query = e.target.value.trim().toLowerCase();

            // Show/hide clear button
            clearSearchBtn.style.display = query ? 'flex' : 'none';

            if (!query) {
                // Reset to default empty state when search is cleared
                searchResultsEl.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">🔍</div>
                        <p>Ketik untuk mencari sekolah</p>
                        <p class="empty-hint">Cari berdasarkan nama sekolah, NPSN, atau kota</p>
                    </div>
                `;
                return;
            }

            // Filter schools (exclude already assigned)
            const filtered = allSchools.filter(school => {
                const isAssigned = assignedSchoolIds.includes(String(school.id));
                if (isAssigned) return false;

                const name = school.name.toLowerCase();
                const npsn = String(school.npsn).toLowerCase();
                const city = (school.city || '').toLowerCase();
                return name.includes(query) || npsn.includes(query) || city.includes(query);
            });

            renderSearchResults(filtered);
        }

        // Clear Search
        function clearSearch() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            searchResultsSection.style.display = 'none';
        }

        // Helper: toggle default status untuk sebuah sekolah
        function toggleDefault(schoolId, isChecked) {
            const idStr = String(schoolId);
            const item = document.querySelector(`#assignedList .list-item[data-school-id="${idStr}"]`);
            if (isChecked) {
                if (!defaultSchoolIds.includes(idStr)) {
                    defaultSchoolIds.push(idStr);
                }
                if (item) item.classList.add('is-default');
            } else {
                const idx = defaultSchoolIds.indexOf(idStr);
                if (idx > -1) defaultSchoolIds.splice(idx, 1);
                if (item) item.classList.remove('is-default');
            }
        }

        // Assign School
        function assignSchool(schoolId, schoolName) {
            const school = allSchools.find(s => String(s.id) === String(schoolId));
            if (!school) return;

            // Add to assigned list (store as string for consistent comparison)
            assignedSchoolIds.push(String(schoolId));

            // Remove from search results
            const searchItem = document.querySelector(`#searchResults .list-item[data-school-id="${schoolId}"]`);
            if (searchItem) searchItem.remove();

            // Remove empty state if exists
            const emptyState = assignedListEl.querySelector('.empty-state');
            if (emptyState) emptyState.remove();

            // Create assigned item dengan toggle default
            const itemHtml = `
                <div class="list-item" data-school-id="${schoolId}">
                    <div class="item-content">
                        <span class="item-icon">🏫</span>
                        <div class="item-details">
                            <span class="item-name">${escapeHtml(school.name)}</span>
                            <span class="item-meta">
                                ${escapeHtml(school.city || '-')}
                                <span class="separator">•</span>
                                NPSN: ${escapeHtml(school.npsn)}
                            </span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <label class="default-toggle" title="Jadikan config default untuk test type ini di sekolah tersebut">
                            <input type="checkbox" class="default-checkbox" data-school-id="${schoolId}" />
                            <span class="default-toggle-label">Default</span>
                        </label>
                        <button type="button" class="btn-icon btn-unassign"
                            data-school-id="${schoolId}"
                            data-school-name="${escapeHtml(school.name)}">
                            Lepas
                        </button>
                    </div>
                </div>
            `;

            assignedListEl.insertAdjacentHTML('beforeend', itemHtml);

            // Update counts
            updateAssignedCount();

            // Hide search if empty
            if (searchResultsEl.children.length === 0) {
                searchResultsSection.style.display = 'none';
            }
        }

        // Unassign School
        function unassignSchool(schoolId, schoolName) {
            const idStr = String(schoolId);

            // Remove from assigned list
            const item = document.querySelector(`#assignedList .list-item[data-school-id="${idStr}"]`);
            if (item) item.remove();

            // Remove from assignedSchoolIds
            const index = assignedSchoolIds.indexOf(idStr);
            if (index > -1) {
                assignedSchoolIds.splice(index, 1);
            }

            // Remove from defaultSchoolIds
            const defIndex = defaultSchoolIds.indexOf(idStr);
            if (defIndex > -1) {
                defaultSchoolIds.splice(defIndex, 1);
            }

            // Check if empty
            const remaining = assignedListEl.querySelectorAll('.list-item');
            if (remaining.length === 0) {
                assignedListEl.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">🏫</div>
                        <p>Belum ada sekolah yang diassign</p>
                        <p class="empty-hint">Cari sekolah dan klik "Assign" untuk menambahkan</p>
                    </div>
                `;
            }

            // Update counts
            updateAssignedCount();
        }

        // Handle List Actions (Assign/Unassign/Default toggle) - use event delegation on document
        function handleListActions(e) {
            const target = e.target;

            // Default checkbox toggle
            if (target.classList.contains('default-checkbox')) {
                toggleDefault(target.dataset.schoolId, target.checked);
                e.stopPropagation();
                return;
            }

            // Assign button
            if (target.classList.contains('btn-assign')) {
                const schoolId = target.dataset.schoolId;
                const schoolName = target.dataset.schoolName;
                assignSchool(schoolId, schoolName);
                e.stopPropagation();
            }

            // Unassign button
            if (target.classList.contains('btn-unassign')) {
                const schoolId = target.dataset.schoolId;
                const schoolName = target.dataset.schoolName;
                unassignSchool(schoolId, schoolName);
                e.stopPropagation();
            }
        }

        // Form Submit Handler
        function handleFormSubmit(e) {
            // Clear previous hidden inputs (important for SPA)
            schoolIdsContainer.innerHTML = '';
            defaultSchoolsContainer.innerHTML = '';

            // Collect school IDs (allow empty to unassign all)
            assignedSchoolIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'schools[]';
                input.value = id;
                schoolIdsContainer.appendChild(input);
            });

            // Collect default school IDs
            defaultSchoolIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'default_schools[]';
                input.value = id;
                defaultSchoolsContainer.appendChild(input);
            });
        }

        // Attach event listeners
        searchInput.addEventListener('input', handleSearch);
        clearSearchBtn.addEventListener('click', clearSearch);
        document.addEventListener('click', handleListActions);
        assignForm.addEventListener('submit', handleFormSubmit);
    }

    // Initialize on initial page load
    document.addEventListener('DOMContentLoaded', initAssignPage);

    // Initialize on SPA navigation (Mazu custom event)
    window.addEventListener('spa:navigated', initAssignPage);
</script>