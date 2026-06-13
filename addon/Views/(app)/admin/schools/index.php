<?php

/**
 * @var array $schools
 * @var array $pagination
 * @var array $filters
 */
?>

<div class="schools-page">
    <div class="page-header">
        <div>
            <h1>Kelola Sekolah</h1>
            <p class="page-description">Daftar semua sekolah yang terdaftar dalam sistem</p>
        </div>
        <a data-spa href="/admin/schools/create" class="btn btn-primary">
            <span>➕</span> Tambah Sekolah
        </a>
    </div>

    <!-- Filters Section -->
    <form method="GET" action="/admin/schools" id="schools-filter-form" class="filters-section">
        <div class="filters-row">
            <!-- Search Input -->
            <div class="filter-group filter-group--search">
                <input
                    type="text"
                    id="search-input"
                    name="search"
                    class="filter-input"
                    placeholder="🔍 Cari nama, NPSN, atau alamat..."
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>

            <!-- Accreditation Filter -->
            <div class="filter-group filter-group--small">
                <select id="accreditation-select" name="accreditation" class="filter-select" title="Akreditasi">
                    <option value="">Semua Akreditasi</option>
                    <option value="A" <?= ($filters['accreditation'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= ($filters['accreditation'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= ($filters['accreditation'] ?? '') === 'C' ? 'selected' : '' ?>>C</option>
                </select>
            </div>

            <!-- Min Students Filter -->
            <div class="filter-group filter-group--small">
                <input
                    type="number"
                    id="min-students-input"
                    name="min_students"
                    class="filter-input"
                    placeholder="Min siswa"
                    min="0"
                    value="<?= htmlspecialchars($filters['min_students'] ?? '') ?>">
            </div>

            <!-- Max Students Filter -->
            <div class="filter-group filter-group--small">
                <input
                    type="number"
                    id="max-students-input"
                    name="max_students"
                    class="filter-input"
                    placeholder="Max siswa"
                    min="0"
                    value="<?= htmlspecialchars($filters['max_students'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filter
            </button>
            <a href="/admin/schools" class="btn btn-secondary btn-reset" title="Reset filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <polyline points="23 20 23 14 17 14"></polyline>
                    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                </svg>
            </a>
        </div>
    </form>

    <!-- Results Info -->
    <div class="results-info">
        <span class="results-count">
            Menampilkan <strong><?= count($schools) ?></strong> dari <strong><?= $pagination['total'] ?? 0 ?></strong> sekolah
        </span>
        <?php if ($pagination['total_pages'] ?? 1 > 1): ?>
            <span class="page-info">Halaman <strong><?= $pagination['page'] ?? 1 ?></strong> dari <strong><?= $pagination['total_pages'] ?? 1 ?></strong></span>
        <?php endif; ?>
    </div>

    <!-- Schools Table -->
    <div class="schools-table-container">
        <table class="schools-table">
            <thead>
                <tr>
                    <th>
                        <a href="#" class="sortable-header" data-sort="name">
                            Nama Sekolah
                            <svg class="sort-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                    </th>
                    <th>
                        <a href="#" class="sortable-header" data-sort="npsn">
                            NPSN
                            <svg class="sort-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                    </th>
                    <th>
                        <a href="#" class="sortable-header" data-sort="accreditation">
                            Akreditasi
                            <svg class="sort-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                    </th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>
                        <a href="#" class="sortable-header" data-sort="student_count">
                            Guru / Siswa
                            <svg class="sort-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                    </th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schools)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-message">
                                <div class="empty-icon">🏫</div>
                                <p>Belum ada sekolah terdaftar</p>
                                <a data-spa href="/admin/schools/create" class="btn btn-primary btn-sm">
                                    <span>➕</span> Tambah Sekolah Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schools as $school): ?>
                        <tr>
                            <td>
                                <div class="school-name">
                                    <strong><?= htmlspecialchars($school['name']) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($school['npsn']) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower(htmlspecialchars($school['accreditation'] ?? 'C')) ?>">
                                    <?= htmlspecialchars($school['accreditation'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-truncate" title="<?= htmlspecialchars($school['address'] ?? '') ?>">
                                <?= htmlspecialchars($school['address'] ?? '-') ?>
                            </td>
                            <td><?= htmlspecialchars($school['contact'] ?? '-') ?></td>
                            <td>
                                <div class="stats-badge">
                                    <span class="stat-item" title="Guru">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                        </svg>
                                        <?= $school['teacher_count'] ?? 0 ?>
                                    </span>
                                    <span class="stat-divider">/</span>
                                    <span class="stat-item" title="Siswa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <?= $school['student_count'] ?? 0 ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        data-spa
                                        href="/admin/schools/<?= $school['id'] ?>"
                                        class="btn btn-sm btn-secondary"
                                        title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                    <a
                                        data-spa
                                        href="/admin/schools/<?= $school['id'] ?>/edit"
                                        class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination">
                <?php
                $currentPage = $pagination['page'] ?? 1;
                $totalPages = $pagination['total_pages'] ?? 1;
                $perPage = $pagination['per_page'] ?? 15;

                // Prev button
                if ($currentPage > 1):
                    $prevParams = http_build_query(array_merge($_GET, ['page' => $currentPage - 1]));
                ?>
                    <a data-spa href="/admin/schools?<?= $prevParams ?>" class="pagination-item pagination-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Prev
                    </a>
                <?php else: ?>
                    <span class="pagination-item pagination-prev disabled">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Prev
                    </span>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                // First page if not in range
                if ($startPage > 1):
                ?>
                    <a data-spa href="/admin/schools?page=1&per_page=<?= $perPage ?>" class="pagination-item">1</a>
                    <?php if ($startPage > 2): ?>
                        <span class="pagination-item pagination-ellipsis">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Page range -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="pagination-item pagination-item--active"><?= $i ?></span>
                    <?php else: ?>
                        <a data-spa href="/admin/schools?page=<?= $i ?>&per_page=<?= $perPage ?>" class="pagination-item"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Last page if not in range -->
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="pagination-item pagination-ellipsis">...</span>
                    <?php endif; ?>
                    <a data-spa href="/admin/schools?page=<?= $totalPages ?>&per_page=<?= $perPage ?>" class="pagination-item"><?= $totalPages ?></a>
                <?php endif; ?>

                <!-- Next button -->
                <?php if ($currentPage < $totalPages):
                    $nextParams = http_build_query(array_merge($_GET, ['page' => $currentPage + 1]));
                ?>
                    <a data-spa href="/admin/schools?<?= $nextParams ?>" class="pagination-item pagination-next">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                <?php else: ?>
                    <span class="pagination-item pagination-next disabled">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Per Page Selector -->
            <div class="per-page-selector">
                <label for="per-page-select">Tampilkan:</label>
                <select id="per-page-select" class="per-page-select" onchange="updatePerPage(this.value)">
                    <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                    <option value="15" <?= $perPage === 15 ? 'selected' : '' ?>>15</option>
                    <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                </select>
                <span>sekolah per halaman</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    /**
     * Update per page parameter and redirect
     */
    function updatePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', '1');
        // Preserve other filter params
        const form = document.getElementById('schools-filter-form');
        if (form) {
            const formData = new FormData(form);
            formData.forEach((val, key) => {
                if (key !== 'per_page' && key !== 'page' && val) {
                    url.searchParams.set(key, val);
                }
            });
        }
        window.location.href = url.href;
    }

    /**
     * Handle sortable headers
     */
    document.querySelectorAll('.sortable-header').forEach(header => {
        header.addEventListener('click', function(e) {
            e.preventDefault();

            const sortBy = this.dataset.sort;
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort_by');
            const currentOrder = url.searchParams.get('sort_order') || 'ASC';

            // Toggle sort order if clicking same column
            if (currentSort === sortBy) {
                url.searchParams.set('sort_order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
            } else {
                url.searchParams.set('sort_by', sortBy);
                url.searchParams.set('sort_order', 'ASC');
            }

            // Preserve filter params
            const form = document.getElementById('schools-filter-form');
            if (form) {
                const formData = new FormData(form);
                formData.forEach((val, key) => {
                    if (val && key !== 'sort_by' && key !== 'sort_order') {
                        url.searchParams.set(key, val);
                    }
                });
            }

            window.location.href = url.href;
        });
    });
</script>