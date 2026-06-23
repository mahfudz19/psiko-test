<?php

/**
 * @var array $schools      // Daftar sekolah hasil pencarian
 * @var string $keyword    // Kata kunci pencarian
 * @var bool $searched     // Apakah sudah melakukan pencarian
 */

?>

<div class="ss-container">
    <div class="ss-header">
        <h1>Cari Sekolah Anda</h1>
        <p>Temukan sekolah Anda untuk melanjutkan proses registrasi</p>
    </div>

    <div class="ss-form-wrapper">
        <div class="ss-input-wrapper">
            <input
                type="text"
                id="ss-search-keyword"
                class="ss-input"
                placeholder="Ketik nama sekolah atau NPSN..."
                autocomplete="off" />
            <svg class="ss-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>

        <!-- Autocomplete Dropdown -->
        <div id="ss-autocomplete-dropdown" class="ss-autocomplete-dropdown" style="display: none;"></div>
    </div>

    <!-- Selected School Info -->
    <div id="ss-selected-school-info" class="ss-selected-school-info" style="display: none;">
        <div class="ss-selected-school-card">
            <div class="ss-selected-school-header">
                <h3 id="ss-selected-school-name"></h3>
                <button type="button" id="ss-clear-selection" class="ss-clear-selection-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="ss-selected-school-details">
                <span id="ss-selected-school-npsn"></span>
                <span id="ss-selected-school-address"></span>
            </div>
        </div>
        <form id="ss-submit-form" method="POST" action="/search-school/select">
            <?= csrf_field() ?>
            <input type="hidden" id="ss-selected-school-id" name="school_id" />
            <button type="submit" class="ss-submit-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Lanjutkan dengan Sekolah Ini
            </button>
        </form>
    </div>

    <!-- No Results Message -->
    <div id="ss-no-results-message" class="ss-no-results-message" style="display: none;">
        <div class="ss-no-results">
            <div class="ss-no-results-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <line x1="8" y1="11" x2="14" y2="11"></line>
                </svg>
            </div>
            <h3>Sekolah Tidak Ditemukan</h3>
            <p>Kami tidak menemukan sekolah yang cocok dengan pencarian Anda.</p>
            <div class="ss-admin-contact">
                <p>Untuk menambahkan sekolah Anda, silakan hubungi admin:</p>
                <a href="https://wa.me/<?= env('ADMIN_PHONE', 628114302220) ?>" target="_blank" class="ss-contact-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    Hubungi Admin via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Schools data from server
        const schoolsData = <?= json_encode($schools ?? []) ?>;

        const searchInput = document.getElementById('ss-search-keyword');
        const autocompleteDropdown = document.getElementById('ss-autocomplete-dropdown');
        const selectedSchoolInfo = document.getElementById('ss-selected-school-info');
        const noResultsMessage = document.getElementById('ss-no-results-message');
        const submitForm = document.getElementById('ss-submit-form');

        let selectedSchool = null;

        // Function to filter schools based on search query
        function filterSchools(query) {
            if (!query || query.trim() === '') {
                return [];
            }

            const lowerQuery = query.toLowerCase().trim();

            return schoolsData.filter(school => {
                const nameMatch = school.name.toLowerCase().includes(lowerQuery);
                const npsnMatch = school.npsn.toLowerCase().includes(lowerQuery);
                return nameMatch || npsnMatch;
            });
        }

        // Function to render autocomplete dropdown
        function renderDropdown(schools) {
            if (schools.length === 0) {
                autocompleteDropdown.style.display = 'none';
                noResultsMessage.style.display = 'block';
                selectedSchoolInfo.style.display = 'none';
                return;
            }

            noResultsMessage.style.display = 'none';

            autocompleteDropdown.innerHTML = schools.map(school => `
            <div class="ss-autocomplete-item" data-school-id="${school.id}" data-school='${JSON.stringify(school).replace(/'/g, "'")}'>
                <div class="ss-autocomplete-item-header">
                    <span class="ss-autocomplete-item-name">${escapeHtml(school.name)}</span>
                    <span class="ss-autocomplete-item-accreditation ss-accreditation-${school.accreditation.toLowerCase()}">${school.accreditation}</span>
                </div>
                <div class="ss-autocomplete-item-details">
                    <span>NPSN: ${escapeHtml(school.npsn)}</span>
                    <span>${escapeHtml(school.address)}</span>
                </div>
            </div>
        `).join('');

            autocompleteDropdown.style.display = 'block';

            // Add click event to each item
            autocompleteDropdown.querySelectorAll('.ss-autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    const schoolData = JSON.parse(this.dataset.school);
                    selectSchool(schoolData);
                });
            });
        }

        // Function to select a school
        function selectSchool(school) {
            selectedSchool = school;

            document.getElementById('ss-selected-school-name').textContent = school.name;
            document.getElementById('ss-selected-school-npsn').textContent = `NPSN: ${school.npsn}`;
            document.getElementById('ss-selected-school-address').textContent = school.address;
            document.getElementById('ss-selected-school-id').value = school.id;

            autocompleteDropdown.style.display = 'none';
            noResultsMessage.style.display = 'none';
            selectedSchoolInfo.style.display = 'block';
            searchInput.value = '';
        }

        // Function to clear selection
        function clearSelection() {
            selectedSchool = null;
            selectedSchoolInfo.style.display = 'none';
            searchInput.value = '';
            searchInput.focus();
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event listener for search input
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            if (query === '') {
                autocompleteDropdown.style.display = 'none';
                noResultsMessage.style.display = 'none';
                return;
            }

            const filteredSchools = filterSchools(query);
            renderDropdown(filteredSchools);
        });

        // Event listener for clear selection button
        document.getElementById('ss-clear-selection').addEventListener('click', clearSelection);

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ss-form-wrapper')) {
                autocompleteDropdown.style.display = 'none';
            }
        });

        // Handle form submission
        submitForm.addEventListener('submit', function(e) {
            if (!selectedSchool) {
                e.preventDefault();
                return;
            }
        });
    })();
</script>