# School Admin Architecture

## Overview

Dokumen ini merancang arsitektur untuk role `admin` yang dapat mengelola sekolah dan siswa di sekolahnya sendiri.

## Role Hierarchy

| Role          | Akses                                                                                 |
| ------------- | ------------------------------------------------------------------------------------- |
| `super-admin` | Full akses ke SEMUA sekolah (CRUD semua data)                                         |
| `admin`       | Akses terbatas ke SATU sekolah (CRUD sekolah sendiri + CRUD siswa di sekolah sendiri) |
| `user`        | Akses data siswa sendiri saja                                                         |

## Database Structure

### Tabel `teacher_profiles`

```sql
CREATE TABLE teacher_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    profile_id INT NOT NULL,
    school_id INT NOT NULL,
    teacher_id VARCHAR(50) NOT NULL,
    subject_specialty VARCHAR(100),
    certification VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id),
    FOREIGN KEY (school_id) REFERENCES schools(id)
);
```

**Field penting:**

- `school_id` - Mengaitkan guru dengan sekolah tertentu
- Role `admin` akan menggunakan field ini untuk menentukan akses

## Authorization Logic

### 1. Middleware untuk School-Based Authorization

Buat middleware baru: `SchoolAdminMiddleware`

```php
class SchoolAdminMiddleware implements MiddlewareInterface
{
    public function handle($request, Closure $next, array $params = [])
    {
        $session = new SessionService();
        $userRole = $_SESSION['auth.user_role'] ?? '';
        $userProfileId = $_SESSION['auth.user_profile_id'] ?? null;

        // Super admin bisa akses semua
        if ($userRole === 'super-admin') {
            return $next($request);
        }

        // Admin hanya bisa akses sekolah sendiri
        if ($userRole === 'admin') {
            $teacherModel = new TeacherProfileModel();
            $teacherProfile = $teacherModel->findByProfileId($userProfileId);

            if (!$teacherProfile) {
                throw new AuthorizationException('Anda tidak terafiliasi dengan sekolah manapun');
            }

            // Simpan school_id di session untuk akses cepat
            $_SESSION['admin.school_id'] = $teacherProfile['school_id'];

            // Check jika ada parameter school_id di route
            $routeSchoolId = $request->param('id');
            if ($routeSchoolId && $routeSchoolId != $teacherProfile['school_id']) {
                throw new AuthorizationException('Anda hanya bisa mengelola sekolah sendiri');
            }

            return $next($request);
        }

        throw new AuthorizationException('Akses ditolak');
    }
}
```

### 2. Routes untuk School Admin

```php
// School Admin Routes (untuk role admin)
$router->group(['middleware' => ['auth', 'school-admin']], function () use ($router) {
    // Hanya bisa lihat dan edit sekolah sendiri
    $router->get('/admin/schools/my', [SchoolAdminController::class, 'mySchool']);
    $router->get('/admin/schools/my/edit', [SchoolAdminController::class, 'editMySchool']);
    $router->post('/admin/schools/my', [SchoolAdminController::class, 'updateMySchool']);

    // CRUD Students di sekolah sendiri
    $router->get('/admin/students', [SchoolAdminController::class, 'students']);
    $router->get('/admin/students/create', [SchoolAdminController::class, 'createStudent']);
    $router->post('/admin/students', [SchoolAdminController::class, 'storeStudent']);
    $router->get('/admin/students/:id', [SchoolAdminController::class, 'showStudent']);
    $router->get('/admin/students/:id/edit', [SchoolAdminController::class, 'editStudent']);
    $router->post('/admin/students/:id', [SchoolAdminController::class, 'updateStudent']);
    $router->post('/admin/students/:id/delete', [SchoolAdminController::class, 'deleteStudent']);
});
```

### 3. Controller untuk School Admin

Buat controller baru: `SchoolAdminController`

```php
class SchoolAdminController
{
    public function __construct(
        private SchoolModel $schoolModel,
        private StudentProfileModel $studentModel,
        private TeacherProfileModel $teacherModel,
        private ProfileModel $profileModel,
        private UserModel $userModel
    ) {}

    /**
     * Get current admin's school ID from session
     */
    private function getAdminSchoolId(): int
    {
        return $_SESSION['admin.school_id'] ?? 0;
    }

    /**
     * Halaman sekolah sendiri
     */
    public function mySchool(Request $request, Response $response): View | RedirectResponse
    {
        $schoolId = $this->getAdminSchoolId();
        $school = $this->schoolModel->find($schoolId);

        if (!$school) {
            return $response->redirect('/admin/schools/my?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
        }

        $teachers = $this->teacherModel->findBySchoolId($schoolId);
        $students = $this->studentModel->findBySchoolId($schoolId);

        return $response->renderPage([
            'school' => $school,
            'teachers' => $teachers,
            'students' => $students,
        ]);
    }

    /**
     * Form edit sekolah sendiri
     */
    public function editMySchool(Request $request, Response $response): View | RedirectResponse
    {
        $schoolId = $this->getAdminSchoolId();
        $school = $this->schoolModel->find($schoolId);

        if (!$school) {
            return $response->redirect('/admin/schools/my?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
        }

        return $response->renderPage(['school' => $school]);
    }

    /**
     * Update sekolah sendiri
     */
    public function updateMySchool(Request $request, Response $response): View | RedirectResponse
    {
        $schoolId = $this->getAdminSchoolId();
        $school = $this->schoolModel->find($schoolId);

        if (!$school) {
            return $response->redirect('/admin/schools/my?error=404&message=' . urlencode('Sekolah tidak ditemukan'));
        }

        $data = $request->input();
        $this->schoolModel->updateById($schoolId, $data);

        return $response->redirect('/admin/schools/my');
    }

    /**
     * Daftar siswa di sekolah sendiri
     */
    public function students(Request $request, Response $response): View | RedirectResponse
    {
        $schoolId = $this->getAdminSchoolId();
        $students = $this->studentModel->findBySchoolId($schoolId);

        return $response->renderPage(['students' => $students]);
    }

    /**
     * Form tambah siswa
     */
    public function createStudent(Request $request, Response $response): View | RedirectResponse
    {
        return $response->renderPage([]);
    }

    /**
     * Simpan siswa baru
     */
    public function storeStudent(Request $request, Response $response): View | RedirectResponse
    {
        $schoolId = $this->getAdminSchoolId();
        $data = $request->input();

        // Validasi dan simpan dengan school_id admin
        // ... (sama seperti AdminController::storeStudent)
        // Tapi otomatis set school_id = $schoolId
    }

    // ... method CRUD students lainnya
}
```

## UI/UX Changes

### 1. Sidebar Menu untuk Role Admin

```php
<?php if (($_SESSION['auth.user_role'] ?? '') === 'admin'): ?>
    <!-- School Admin Menu -->
    <div class="sidebar-nav-group">
        <div class="sidebar-nav-group-header <?= $isAdminPage ? 'active' : '' ?>">
            <svg>🏫</svg>
            <span>Sekolah Saya</span>
        </div>
        <div class="sidebar-nav-group-content">
            <a data-spa href="/admin/schools/my" class="sidebar-link sidebar-link-sub">
                <span class="sidebar-link-text">🏛️ Dashboard Sekolah</span>
            </a>
            <a data-spa href="/admin/students" class="sidebar-link sidebar-link-sub">
                <span class="sidebar-link-text">👨‍🎓 Kelola Siswa</span>
            </a>
        </div>
    </div>
<?php endif; ?>
```

### 2. View Pages yang Dibutuhkan

```
addon/Views/(app)/admin/
├── school-admin/
│   ├── dashboard.php          # Dashboard sekolah sendiri
│   ├── students/
│   │   ├── index.php          # Daftar siswa
│   │   ├── create.php         # Form tambah siswa
│   │   ├── [id].php           # Detail siswa
│   │   └── [id]/edit.php      # Form edit siswa
```

## Implementation Steps

1. **Buat Middleware `SchoolAdminMiddleware`**
   - File: `addon/Middleware/SchoolAdminMiddleware.php`
   - Logic: Cek role admin dan validasi school_id

2. **Daftarkan Middleware di Kernel**
   - Otomatis terdaftar via `Kernel::getRouteMiddleware()`

3. **Buat Controller `SchoolAdminController`**
   - File: `addon/Controllers/SchoolAdminController.php`
   - Method: mySchool, editMySchool, updateMySchool, students, CRUD students

4. **Tambahkan Routes**
   - File: `addon/Router/index.php`
   - Group middleware: `['auth', 'school-admin']`

5. **Buat View Pages**
   - Dashboard sekolah
   - CRUD students

6. **Update Sidebar Menu**
   - File: `addon/Views/(app)/layout.php`
   - Tambahkan menu untuk role admin

## Security Considerations

1. **Always validate school_id** - Setiap akses data harus cek school_id
2. **Session-based authorization** - Simpan school_id di session setelah validasi
3. **Prevent IDOR** - Jangan trust user input untuk school_id
4. **Audit trail** - Log semua perubahan data

## Testing Scenarios

1. Login sebagai admin → Akses `/admin/schools/my` → ✅ Berhasil
2. Login sebagai admin → Akses `/admin/schools/1` (bukan sekolah sendiri) → ❌ Error 403
3. Login sebagai admin → Tambah siswa → ✅ Siswa tersimpan dengan school_id admin
4. Login sebagai super-admin → Akses semua sekolah → ✅ Berhasil
