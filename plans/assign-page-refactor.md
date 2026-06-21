# Assign Page Refactor - UI/UX Sederhana

## Masalah Design Saat Ini

1. **2-panel layout terlalu kompleks** - User harus search di kiri, pilih, klik "Tambahkan", lalu manage di kanan
2. **Terlalu banyak klik** - Search → Checkbox → Add → Set Default → Save
3. **State management rumit** - `selectedSchools`, `assignedSchoolIds`, `currentSearchResults`
4. **Confusing flow** - Sekolah yang sudah di-assign masih muncul di search results

## New Design Philosophy

**Single Panel dengan Smart Search**

- Satu list yang menampilkan SEMUA sekolah yang sudah di-assign di TOP
- Search box untuk mencari dan LANGSUNG assign/unassign
- Toggle checkbox = instant assign/unassign (no "Add" button)
- Set default dengan radio button atau dropdown

---

## Wireframe Baru

```
┌─────────────────────────────────────────────────────────────┐
│ ← Kembali                                                   │
│ Assign Konfigurasi ke Sekolah                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ 📋 Tes RIASEC (riasec)                                      │
│    Dimensi: R, I, A, S, E, C                                │
│    📊 5 sekolah diassign                                    │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ 🔍 Cari sekolah...                              [Set Default ▼] │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ SEKOLAH DIASSIGN (5)                                        │
├─────────────────────────────────────────────────────────────┤
│ ☑️ SMA Negeri 1 Bandung         ⭐ Default    [Lepas]      │
│ ☑️ SMA Negeri 2 Bandung         [Jadikan Default] [Lepas]  │
│ ☑️ SMA Negeri 3 Bandung         [Jadikan Default] [Lepas]  │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ HASIL PENCARIAN                                             │
├─────────────────────────────────────────────────────────────┤
│ ☐ SMA Negeri 4 Bandung  •  Kota: Bandung   [Assign]        │
│ ☐ SMA Negeri 5 Bandung  •  Kota: Bogor     [Assign]        │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                        [Batal] [💾 Simpan]                  │
└─────────────────────────────────────────────────────────────┘
```

---

## Flow Interaksi Baru

### 1. Halaman Load

- Tampilkan config info card
- Tampilkan search box (empty)
- Tampilkan list "Sekolah Diassign" dengan semua sekolah yang sudah di-assign
- Tampilkan "Hasil Pencarian" (empty, placeholder)

### 2. User Search

- Ketik di search box → hasil muncul di "Hasil Pencarian"
- Sekolah yang sudah di-assign **TIDAK muncul** di hasil pencarian
- Setiap hasil punya checkbox + button "Assign"

### 3. Assign Sekolah (NEW - Instant)

- User klik checkbox ATAU button "Assign"
- **LANGSUNG pindah** ke list "Sekolah Diassign" (no "Add" button)
- Checkbox di hasil pencarian jadi checked
- Counter update

### 4. Unassign Sekolah

- User klik button "Lepas" di list "Sekolah Diassign"
- **LANGSUNG pindah** ke hasil pencarian (jika masih di-search)
- Checkbox di hasil pencarian jadi unchecked

### 5. Set Default

- Option A: Radio button di setiap sekolah di list "Sekolah Diassign"
- Option B: Dropdown "Set Default" di header yang menampilkan hanya sekolah yang di-assign
- Pilih → update hidden input

### 6. Save

- Klik "Simpan" → submit semua school IDs + default_school

---

## Data Structure

```javascript
// State
let allSchools = [...];  // Semua sekolah dari server
let assignedSchoolIds = [...];  // IDs sekolah yang sudah di-assign
let defaultSchoolId = null;  // ID sekolah default
let searchQuery = '';  // Current search query

// Computed
let filteredSchools = allSchools.filter(s =>
    !assignedSchoolIds.includes(s.id) &&
    matchesSearch(s, searchQuery)
);
```

---

## Controller Changes

```php
public function assignToSchools(Request $request, Response $response): View | RedirectResponse
{
    $configId = $request->param('id');
    $config = $this->configModel->find($configId);

    // Get assigned schools with full data
    $assignedMappings = $this->schoolConfigModel->getByConfigId($configId);
    $assignedSchoolIds = array_column($assignedMappings, 'school_id');

    // Get default school
    $defaultMapping = array_filter($assignedMappings, fn($m) => $m['is_default']);
    $defaultSchoolId = !empty($defaultMapping) ? reset($defaultMapping)['school_id'] : null;

    // Get ALL schools (for search)
    $allSchools = $this->schoolModel->all();

    $props = [
        'config' => $config,
        'allSchools' => $allSchools,
        'assignedSchoolIds' => $assignedSchoolIds,  // Just IDs, not full data
        'defaultSchoolId' => $defaultSchoolId
    ];

    return $response->renderPage($props, ['meta' => ['title' => 'Assign Sekolah']]);
}
```

---

## View Structure (Simplified)

```php
<!-- Config Info Card -->
<div class="config-info-card">...</div>

<!-- Search + Default Selector -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Cari sekolah..." />
    <select id="defaultSelector">
        <option value="">Set Default School</option>
        <!-- Populated by JS -->
    </select>
</div>

<!-- Assigned Schools List -->
<div class="assigned-section">
    <h3>Sekolah Diassign (<span id="assignedCount">0</span>)</h3>
    <div id="assignedList">
        <!-- Rendered by JS -->
    </div>
</div>

<!-- Search Results -->
<div class="search-results-section">
    <h3>Hasil Pencarian</h3>
    <div id="searchResults">
        <!-- Rendered by JS -->
    </div>
</div>

<!-- Form -->
<form method="POST" id="assignForm">
    <?= csrf_field() ?>
    <input type="hidden" name="default_school" id="defaultSchoolInput" />
    <div id="schoolIdsContainer"></div>
    <button type="submit">Simpan</button>
</form>
```

---

## JavaScript Functions (Simplified)

```javascript
// Core functions
function handleSearch(query)
function renderAssignedList()
function renderSearchResults()
function toggleSchool(schoolId)  // Assign/unassign in one function
function setDefaultSchool(schoolId)
function handleFormSubmit()

// No more:
// - addSelectedSchools()
// - toggleSchoolSelection()
// - updateSelectionActions()
// - selectedSchools array
```

---

## CSS Changes

- Remove 2-panel grid layout
- Single column layout
- Search bar dengan inline default selector
- Assigned section dengan list items
- Search results section dengan list items
- Button "Assign" dan "Lepas" dengan different styles

---

## Benefits

1. **Lebih intuitif** - Checkbox = assign, unchecked = unassign
2. **Lebih sedikit klik** - No intermediate "Add" step
3. **Clearer state** - Sekolah di atas = assigned, di bawah = available
4. **Simpler JS** - No complex state management
5. **Instant feedback** - Langsung lihat hasil assign/unassign

---

## Implementation Checklist

- [ ] Update controller untuk pass `assignedSchoolIds` (just IDs)
- [ ] Create new view dengan single-panel layout
- [ ] Implement search dengan instant assign
- [ ] Implement default school selector (dropdown)
- [ ] Implement form submit dengan school IDs
- [ ] Test flow lengkap
