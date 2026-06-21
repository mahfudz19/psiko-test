# Test Management - Arsitektur & Implementasi

## Overview

Fitur Test Management memungkinkan Super Admin untuk:

1. Menambah konfigurasi tes baru (RIASEC, IQ, Learning Style, Personality, dll)
2. Mengelola butir soal (statements) untuk setiap konfigurasi
3. Menassign konfigurasi ke sekolah

## Arsitektur yang Ada

### Database Schema

```
test_configurations
├── id (PK)
├── name (string)
├── test_type (enum: 'riasec', 'iq', 'learning_style', 'personality')
├── dimensions (JSON) - definisi dimensi tes
├── scoring_rules (JSON) - aturan scoring
├── is_active (boolean)
└── timestamps

test_statements
├── id (PK)
├── config_id (FK → test_configurations.id, cascade delete)
├── dimension (enum: 'R', 'I', 'A', 'S', 'E', 'C' atau custom)
├── statement_text (text)
├── display_order (int)
├── is_active (boolean)
└── timestamps

school_config_mappings
├── id (PK)
├── school_id (FK → schools.id)
├── config_id (FK → test_configurations.id)
├── is_default (boolean)
├── valid_from (date, nullable)
├── valid_until (date, nullable)
└── timestamps
```

## Desain Implementasi

### 1. Controller: TestManagementController

File: `addon/Controllers/Admin/TestManagementController.php`

```php
namespace Addon\Controllers\Admin;

use Addon\Models\TestConfigurationModel;
use Addon\Models\TestStatementModel;
use Addon\Models\SchoolConfigMappingModel;
use Addon\Models\SchoolModel;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\RedirectResponse;
use App\Core\View\View;

class TestManagementController
{
    public function __construct(
        private TestConfigurationModel $configModel,
        private TestStatementModel $statementModel,
        private SchoolConfigMappingModel $schoolConfigModel,
        private SchoolModel $schoolModel
    ) {}

    // GET /admin/tests - List semua konfigurasi
    public function index(Request $request, Response $response): View
    {
        $configs = $this->configModel->all();
        return $response->renderPage(['configs' => $configs]);
    }

    // GET /admin/tests/create - Form tambah konfigurasi
    public function create(Request $request, Response $response): View
    {
        return $response->renderPage([
            'testTypes' => ['riasec', 'iq', 'learning_style', 'personality']
        ]);
    }

    // POST /admin/tests - Simpan konfigurasi baru
    public function store(Request $request, Response $response): RedirectResponse
    {
        // Validasi & create konfigurasi
    }

    // GET /admin/tests/:id/edit - Form edit konfigurasi
    public function edit(Request $request, Response $response): View
    {
        $id = $request->param('id');
        $config = $this->configModel->find($id);
        return $response->renderPage(['config' => $config]);
    }

    // POST /admin/tests/:id/update - Update konfigurasi
    public function update(Request $request, Response $response): RedirectResponse
    {
        // Validasi & update konfigurasi
    }

    // GET /admin/tests/:id/statements - Kelola butir soal
    public function manageStatements(Request $request, Response $response): View
    {
        $id = $request->param('id');
        $config = $this->configModel->find($id);
        $statements = $this->statementModel->getByConfigId($id);
        return $response->renderPage([
            'config' => $config,
            'statements' => $statements
        ]);
    }

    // POST /admin/tests/:id/statements - Tambah butir soal
    public function addStatement(Request $request, Response $response): RedirectResponse
    {
        // Validasi & create statement
    }

    // GET /admin/tests/:id/assign - Assign ke sekolah
    public function assignToSchools(Request $request, Response $response): View
    {
        $id = $request->param('id');
        $config = $this->configModel->find($id);
        $schools = $this->schoolModel->all();
        $assignedSchools = $this->schoolConfigModel->getByConfigId($id);
        return $response->renderPage([
            'config' => $config,
            'schools' => $schools,
            'assignedSchools' => $assignedSchools
        ]);
    }

    // POST /admin/tests/:id/assign - Simpan assignment
    public function saveAssignment(Request $request, Response $response): RedirectResponse
    {
        // Simpan mapping sekolah-konfigurasi
    }
}
```

### 2. Routes

File: `addon/Router/index.php`

```php
// Test Management (Super Admin only)
$router->group(['middleware' => ['auth', 'role:super-admin', 'csrf']], function () use ($router) {
    // Test Configurations CRUD
    $router->get('/admin/tests', [TestManagementController::class, 'index']);
    $router->get('/admin/tests/create', [TestManagementController::class, 'create']);
    $router->post('/admin/tests', [TestManagementController::class, 'store']);
    $router->get('/admin/tests/:id/edit', [TestManagementController::class, 'edit']);
    $router->post('/admin/tests/:id/update', [TestManagementController::class, 'update']);

    // Statements Management
    $router->get('/admin/tests/:id/statements', [TestManagementController::class, 'manageStatements']);
    $router->post('/admin/tests/:id/statements', [TestManagementController::class, 'addStatement']);
    $router->post('/admin/tests/:id/statements/:statement_id/delete', [TestManagementController::class, 'deleteStatement']);

    // School Assignment
    $router->get('/admin/tests/:id/assign', [TestManagementController::class, 'assignToSchools']);
    $router->post('/admin/tests/:id/assign', [TestManagementController::class, 'saveAssignment']);
});
```

### 3. Views Structure

```
addon/Views/(app)/admin/tests/
├── index.php          # List semua konfigurasi tes
├── create.php         # Form tambah konfigurasi baru
├── edit.php           # Form edit konfigurasi
├── statements.php     # Kelola butir soal
├── assign.php         # Assign konfigurasi ke sekolah
└── [id]/
    └── index.php      # Detail konfigurasi
```

## UI/UX Design

### Halaman Index (/admin/tests)

```
┌─────────────────────────────────────────────────────────────┐
│  Kelola Konfigurasi Tes                              [+ Tambah] │
│  Daftar semua konfigurasi tes yang tersedia                  │
├─────────────────────────────────────────────────────────────┤
│  Filter: [Search...] [Tipe: All ▼] [Status: All ▼] [Filter] │
├─────────────────────────────────────────────────────────────┤
│  Nama Tes              │ Tipe      │ Butir │ Sekolah │ Aksi │
│  ─────────────────────────────────────────────────────────  │
│  RIASEC Standar 42     │ riasec    │ 42    │ 5       │ 👁 ✏️ │
│  IQ Test Standar 30    │ iq        │ 30    │ 2       │ 👁 ✏️ │
│  Learning Style VAK    │ learning  │ 15    │ 3       │ 👁 ✏️ │
└─────────────────────────────────────────────────────────────┘
```

### Form Create/Edit (/admin/tests/create)

```
┌─────────────────────────────────────────────────────────────┐
│  Tambah Konfigurasi Tes Baru                               │
├─────────────────────────────────────────────────────────────┤
│  Informasi Dasar                                           │
│  ─────────────────────────────────────────────────────────  │
│  Nama Tes*: [_________________________________]             │
│  Tipe Tes*: [riasec ▼]                                      │
│         (riasec, iq, learning_style, personality)           │
│                                                             │
│  Dimensi (JSON)*:                                          │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ {                                                      │ │
│  │   "R": {"label": "Realistic", "color": "#3B6D11"},   │ │
│  │   "I": {"label": "Investigative", "color": "#185FA5"}│ │
│  │ }                                                      │ │
│  └───────────────────────────────────────────────────────┘ │
│  💡 Format: {"DIMENSI": {"label": "Nama", "color": "#hex"}}│
│                                                             │
│  Scoring Rules (JSON)*:                                    │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ {                                                      │ │
│  │   "scale": 4,                                          │ │
│  │   "min_value": 1,                                      │ │
│  │   "max_value": 4,                                      │ │
│  │   "categories": [                                      │ │
│  │     {"min": 25, "max": 28, "label": "Sangat Tinggi"}  │ │
│  │   ]                                                    │ │
│  │ }                                                      │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  [Batal] [💾 Simpan Konfigurasi]                           │
└─────────────────────────────────────────────────────────────┘
```

### Halaman Statements (/admin/tests/:id/statements)

```
┌─────────────────────────────────────────────────────────────┐
│  Kelola Butir Soal - RIASEC Standar 42 Butir      [+ Tambah] │
├─────────────────────────────────────────────────────────────┤
│  Info Konfigurasi                                          │
│  Tipe: riasec | Dimensi: R, I, A, S, E, C | Total: 42 butir│
├─────────────────────────────────────────────────────────────┤
│  Filter: [Semua Dimensi ▼]                                  │
├─────────────────────────────────────────────────────────────┤
│  No │ Dimensi │ Pernyataan                  │ Urutan │ Aksi│
│  ────────────────────────────────────────────────────────── │
│  1  │    R    │ Aku suka mengulik...       │   1     │ ✏️ 🗑│
│  2  │    R    │ Aku suka bekerja mandiri...│   2     │ ✏️ 🗑│
│  3  │    I    │ Aku suka mengerjakan...    │   8     │ ✏️ 🗑│
│  ...                                                        │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  Tambah Butir Soal                                         │
│  Dimensi*: [R ▼]  Urutan*: [___]                           │
│  Pernyataan*:                                              │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ [_________________________________________________]   │ │
│  │ [_________________________________________________]   │ │
│  └───────────────────────────────────────────────────────┘ │
│  [Tambah Butir]                                            │
└─────────────────────────────────────────────────────────────┘
```

### Halaman Assign (/admin/tests/:id/assign)

```
┌─────────────────────────────────────────────────────────────┐
│  Assign Konfigurasi ke Sekolah                             │
├─────────────────────────────────────────────────────────────┤
│  Konfigurasi: RIASEC Standar 42 Butir                      │
│  Tipe: riasec                                              │
├─────────────────────────────────────────────────────────────┤
│  Sekolah yang Sudah Diassign:                              │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ ✓ SMA Negeri 1 Jakarta (Default)                      │ │
│  │ ✓ SMA Negeri 2 Bandung                                │ │
│  │ ✓ SMK Negeri 1 Surabaya                               │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  Tambah Sekolah:                                           │
│  [✓ SMA Negeri 3 Jakarta  ] [✓ SMA Negeri 4 Bandung ] ... │
│  [✓ SMK Negeri 2 Surabaya ] [✓ SMA Negeri 5 Medan   ] ... │
│                                                             │
│  [Simpan Assignment]                                       │
└─────────────────────────────────────────────────────────────┘
```

## Flow Menambah IQ Test

### Step 1: Buat Konfigurasi

```
POST /admin/tests
{
  "name": "IQ Test Standar 30 Butir",
  "test_type": "iq",
  "dimensions": {
    "LOG": {"label": "Logika", "color": "#FF0000"},
    "VER": {"label": "Verbal", "color": "#00FF00"},
    "NUM": {"label": "Numerik", "color": "#0000FF"},
    "SPA": {"label": "Spasial", "color": "#FFFF00"}
  },
  "scoring_rules": {
    "scale": 1,
    "min_value": 0,
    "max_value": 1,
    "categories": [
      {"min": 25, "max": 30, "label": "Sangat Tinggi"},
      {"min": 19, "max": 24, "label": "Tinggi"},
      {"min": 13, "max": 18, "label": "Sedang"},
      {"min": 0, "max": 12, "label": "Rendah"}
    ]
  }
}
```

### Step 2: Tambah Butir Soal

```
POST /admin/tests/:id/statements
{
  "dimension": "LOG",
  "statement_text": "Jika semua A adalah B dan sebagian B adalah C, maka...",
  "display_order": 1
}
```

Ulangi untuk 30 butir (7-8 butir per dimensi).

### Step 3: Assign ke Sekolah

```
POST /admin/tests/:id/assign
{
  "schools": [1, 2, 5, 8],
  "default_school": 1
}
```

## Perubahan yang Diperlukan

### 1. Model Updates

**TestStatementModel.php** - Update dimension enum untuk support custom dimensi:

```php
// Opsi 1: Tetap gunakan enum, tambahkan semua kemungkinan
'dimension' => ['type' => 'string', 'length' => 10, 'nullable' => false]

// Opsi 2: Gunakan string bebas (recommended untuk fleksibilitas)
```

### 2. TestController Updates

Update `takeTest()` untuk support dimensi dinamis:

```php
// Saat ini hardcoded ['R', 'I', 'A', 'S', 'E', 'C']
// Perlu diubah menjadi dinamis dari config
$dimensions = array_keys(json_decode($config['dimensions'], true));
foreach ($dimensions as $dimension) {
    // Hitung skor per dimensi
}
```

## Keamanan & Validasi

1. **CSRF Protection** - Semua form POST wajib punya token
2. **Role-based Access** - Hanya super-admin yang bisa akses
3. **JSON Validation** - Validasi format JSON untuk dimensions & scoring_rules
4. **Unique Constraint** - Nama konfigurasi harus unik
5. **Cascade Delete** - Delete config → delete statements

## Testing Checklist

- [ ] Create konfigurasi RIASEC baru
- [ ] Create konfigurasi IQ Test dengan 4 dimensi
- [ ] Tambah 30 butir soal IQ Test
- [ ] Assign konfigurasi ke 3 sekolah
- [ ] Siswa di sekolah tersebut bisa akses IQ Test
- [ ] Scoring berjalan benar untuk dimensi custom
- [ ] Hasil tes ditampilkan dengan benar
