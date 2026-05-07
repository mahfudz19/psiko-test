# Super Admin Architecture - School Management System

## Overview

Sistem manajemen untuk Super Admin dalam mengelola:

- **Sekolah** (School)
- **Guru BK** (Teacher/ Admin dengan role "admin")
- **Siswa** (Student/ User dengan role "user")

## Struktur Database

### Tabel yang Terlibat

```
users
├── id (PK)
├── email
├── password
├── name
├── role (super-admin | admin | user)
├── is_active
└── timestamps

profiles
├── id (PK)
├── user_id (FK → users.id)
├── phone
├── address
└── timestamps

schools
├── id (PK)
├── name
├── npsn (unique)
├── address
├── principal_name
├── contact
├── accreditation (A | B | C)
└── timestamps

teacher_profiles
├── id (PK)
├── profile_id (FK → profiles.id)
├── school_id (FK → schools.id)
├── teacher_id (NIP/NIK)
├── subject_specialty
├── certification
├── managed_students (JSON)
└── timestamps

student_profiles
├── id (PK)
├── profile_id (FK → profiles.id)
├── school_id (FK → schools.id)
├── student_id (NIS/NISN)
├── grade_level (10 | 11 | 12)
├── major
├── parent_name
├── parent_phone
├── parent_email
└── timestamps
```

## Workflow

### 1. Menambah Sekolah Baru

```
Super Admin → Schools → Add School
                ↓
        Isi form data sekolah
                ↓
        Simpan ke tabel 'schools'
```

### 2. Menambah Guru BK ke Sekolah

```
Super Admin → Schools → [Detail Sekolah] → Add Teacher
                          ↓
                  Isi form data guru + akun login
                          ↓
                  1. Buat user (role: admin)
                  2. Buat profile
                  3. Buat teacher_profiles (dengan school_id)
```

### 3. Menambah Siswa ke Sekolah

```
Super Admin → Schools → [Detail Sekolah] → Add Student
                          ↓
                  Isi form data siswa + akun login
                          ↓
                  1. Buat user (role: user)
                  2. Buat profile
                  3. Buat student_profiles (dengan school_id)
```

## Routes Structure

```
/admin                        → Dashboard Admin
/admin/schools                → Daftar Sekolah
/admin/schools/create         → Form Tambah Sekolah
/admin/schools/{id}           → Detail Sekolah
/admin/schools/{id}/teachers  → Daftar Guru di Sekolah
/admin/schools/{id}/students  → Daftar Siswa di Sekolah
/admin/teachers               → Daftar Semua Guru
/admin/students               → Daftar Semua Siswa
```

## Controller Methods

### AdminController

```php
class AdminController
{
    // Dashboard
    public function index()                    // Dashboard admin

    // Schools
    public function schools()                  // List semua sekolah
    public function createSchool()             // Form tambah sekolah
    public function storeSchool()              // Proses simpan sekolah
    public function showSchool($id)            // Detail sekolah
    public function editSchool($id)            // Form edit sekolah
    public function updateSchool($id)          // Proses update sekolah
    public function deleteSchool($id)          // Hapus sekolah

    // Teachers (dalam konteks sekolah)
    public function schoolTeachers($schoolId)  // List guru per sekolah
    public function createTeacher($schoolId)   // Form tambah guru
    public function storeTeacher($schoolId)    // Proses simpan guru

    // Students (dalam konteks sekolah)
    public function schoolStudents($schoolId)  // List siswa per sekolah
    public function createStudent($schoolId)   // Form tambah siswa
    public function storeStudent($schoolId)    // Proses simpan siswa
}
```

## UI/UX Design

### 1. Dashboard Admin

- Statistik: Total Sekolah, Total Guru, Total Siswa
- Quick actions: Tambah Sekolah, Tambah Guru, Tambah Siswa
- Recent activities

### 2. Halaman Daftar Sekolah

- Table dengan kolom: Nama, NPSN, Alamat, Akreditasi, Jumlah Guru, Jumlah Siswa, Aksi
- Search bar
- Pagination
- Button "Tambah Sekolah"

### 3. Form Tambah Sekolah

- Input: Nama Sekolah, NPSN, Alamat, Nama Kepala Sekolah, Kontak, Akreditasi
- Validation: NPSN harus unique

### 4. Detail Sekolah

- Info sekolah
- Tab: Guru, Siswa
- Button: Tambah Guru, Tambah Siswa

### 5. Form Tambah Guru

- Data Guru: NIP/NIK, Nama, Email, Password, Spesialisasi, Sertifikasi
- Auto-assign ke sekolah yang sedang dilihat

### 6. Form Tambah Siswa

- Data Siswa: NIS/NISN, Nama, Email, Password, Kelas, Jurusan
- Data Orang Tua: Nama, Telepon, Email
- Auto-assign ke sekolah yang sedang dilihat

## Data Dummy untuk Development

### Sekolah

```php
[
    ['name' => 'SMA Negeri 1 Jakarta', 'npsn' => '10100001', 'accreditation' => 'A'],
    ['name' => 'SMA Negeri 2 Jakarta', 'npsn' => '10100002', 'accreditation' => 'A'],
    ['name' => 'SMA Negeri 1 Bandung', 'npsn' => '10200001', 'accreditation' => 'A'],
]
```

### Guru BK

```php
[
    [
        'email' => 'guru1@school.com',
        'name' => 'Ahmad Fauzi, S.Pd',
        'role' => 'admin',
        'school' => 'SMA Negeri 1 Jakarta'
    ],
    [
        'email' => 'guru2@school.com',
        'name' => 'Siti Nurhaliza, M.Pd',
        'role' => 'admin',
        'school' => 'SMA Negeri 2 Jakarta'
    ],
]
```

### Siswa

```php
[
    [
        'email' => 'siswa1@student.com',
        'name' => 'Budi Santoso',
        'role' => 'user',
        'grade' => '10',
        'major' => 'IPA',
        'school' => 'SMA Negeri 1 Jakarta'
    ],
    [
        'email' => 'siswa2@student.com',
        'name' => 'Dewi Lestari',
        'role' => 'user',
        'grade' => '11',
        'major' => 'IPS',
        'school' => 'SMA Negeri 1 Jakarta'
    ],
]
```

## Security Considerations

1. **Middleware Protection**: Semua route `/admin/*` harus menggunakan middleware `super-admin`
2. **Role Check**: Pastikan user yang mengakses adalah role `super-admin`
3. **Data Isolation**: Guru hanya bisa melihat siswa di sekolahnya sendiri
4. **Audit Trail**: Log semua aksi penting (create, update, delete)

## Next Steps

1. ✅ Buat AdminController
2. [ ] Implementasi method di AdminController
3. [ ] Buat routes untuk admin panel
4. [ ] Buat views (schools, teachers, students)
5. [ ] Tambahkan middleware super-admin
6. [ ] Seed data dummy
