# Migration Plan: Remove psychological_tests Column from student_profiles Table

## Tanggal

2026-05-14

## Deskripsi

Menghapus kolom `psychological_tests` dari tabel `student_profiles` karena sudah digantikan oleh tabel `test_results` yang berfungsi sebagai single source of truth untuk semua hasil tes psikologi, termasuk RIASEC.

## Latar Belakang

### Masalah dengan psychological_tests JSON Column:

1. **Data Redundancy**: Data tes disimpan di dua tempat (student_profiles dan test_results)
2. **No Referential Integrity**: JSON field tidak memiliki foreign key constraints
3. **Hard to Query**: Sulit melakukan query kompleks pada data JSON
4. **Denormalized Structure**: Tidak sesuai dengan prinsip database normalization
5. **Limited Scalability**: Sulit menambahkan metadata tes baru atau history

### Solusi: TestResultModel

Tabel `test_results` sudah ada dan menyediakan:

- Normalized relational structure
- Foreign key constraints untuk data integrity
- Easy querying dengan SQL standar
- Support untuk multiple test types (RIASEC, IQ, dll)
- History tracking (multiple sessions)

## Files yang Terpengaruh

### Models (Already Updated):

- ✅ `addon/Models/StudentProfileModel.php` - Field dihapus dari schema, seed, methods
- ✅ `addon/Models/TestResultModel.php` - Single source of truth

### Controllers (Already Updated):

- ✅ `addon/Controllers/ProfileController.php` - Menggunakan TestResultModel
- ✅ `addon/Controllers/PmbController.php` - Menggunakan TestResultModel
- ✅ `addon/Controllers/DashboardController.php` - Menggunakan TestResultModel

### Services (Already Updated):

- ✅ `addon/Services/GeminiService.php` - Prompt menggunakan riasec_result

### Views (Already Updated):

- ✅ `addon/Views/(app)/profile/results.php` - Tidak menggunakan psychological_tests
- ✅ `addon/Views/(app)/profile/students.php` - Menggunakan has_riasec_test

## Migration Steps

### Step 1: Backup Data (CRITICAL)

```sql
-- Backup semua data psychological_tests sebelum dihapus
CREATE TABLE student_profiles_psychological_tests_backup AS
SELECT id, profile_id, psychological_tests, created_at, updated_at
FROM student_profiles
WHERE psychological_tests IS NOT NULL AND psychological_tests != '[]';
```

### Step 2: Verifikasi Data Migration

```sql
-- Cek apakah semua data penting sudah ada di test_results
SELECT
    sp.id as student_profile_id,
    sp.profile_id,
    COUNT(tr.id) as riasec_test_count
FROM student_profiles sp
LEFT JOIN test_results tr ON sp.id = tr.student_profile_id AND tr.test_type = 'riasec'
WHERE sp.psychological_tests IS NOT NULL AND sp.psychological_tests != '[]'
GROUP BY sp.id, sp.profile_id
HAVING riasec_test_count = 0;
```

**Catatan**: Jika ada hasil query, berarti ada data psychological_tests yang belum termigrasi ke test_results. Perlu dicek manual.

### Step 3: Drop Column

```sql
-- Hapus kolom psychological_tests dari tabel student_profiles
ALTER TABLE student_profiles DROP COLUMN psychological_tests;
```

### Step 4: Verifikasi

```sql
-- Pastikan kolom sudah dihapus
DESCRIBE student_profiles;

-- Pastikan data di test_results masih utuh
SELECT COUNT(*) FROM test_results WHERE test_type = 'riasec';
```

## Rollback Script (Jika Diperlukan)

```sql
-- Kembalikan kolom psychological_tests (jika diperlukan)
ALTER TABLE student_profiles ADD COLUMN psychological_tests JSON NULL;

-- Restore data dari backup
UPDATE student_profiles sp
JOIN student_profiles_psychological_tests_backup b ON sp.id = b.id
SET sp.psychological_tests = b.psychological_tests;
```

## Testing Checklist

- [ ] Backup database sebelum migration
- [ ] Jalankan backup query untuk psychological_tests
- [ ] Verifikasi tidak ada data yang hilang
- [ ] Jalankan DROP COLUMN
- [ ] Test aplikasi:
  - [ ] ProfileController::generateAiAnalysis() - Generate analisis AI
  - [ ] ProfileController::results() - Lihat hasil RIASEC
  - [ ] PmbController::journey() - PMB journey page
  - [ ] DashboardController::index() - Dashboard stats
  - [ ] ProfileController::listStudents() - Daftar siswa bimbingan
- [ ] Verifikasi tidak ada error Intelephense/PHP
- [ ] Test RIASEC test submission flow
- [ ] Test data hash calculation untuk AI analysis

## Benefits

1. **Single Source of Truth**: TestResultModel adalah satu-satunya sumber data tes
2. **Data Integrity**: Foreign key constraints menjamin referential integrity
3. **Better Query Performance**: SQL query lebih efisien daripada JSON parsing
4. **Scalability**: Mudah menambahkan test type baru atau metadata
5. **History Tracking**: Bisa menyimpan multiple test sessions per siswa
6. **Cleaner Code**: Tidak ada lagi duplikasi logic untuk psychological_tests

## Related Files

- `addon/Models/StudentProfileModel.php`
- `addon/Models/TestResultModel.php`
- `addon/Controllers/ProfileController.php`
- `addon/Controllers/PmbController.php`
- `addon/Controllers/DashboardController.php`
- `addon/Services/GeminiService.php`
- `addon/Views/(app)/profile/results.php`
- `addon/Views/(app)/profile/students.php`

## Notes

- Migration ini **TIDAK REVERSIBLE** secara mudah. Pastikan backup sudah dilakukan.
- Semua code sudah diupdate untuk menggunakan TestResultModel sebelum migration database.
- Setelah migration, aplikasi akan tetap berfungsi karena code sudah tidak menggunakan field ini.
- Field `psychological_tests` sudah dihapus dari model schema, jadi aplikasi tidak akan mencoba mengaksesnya lagi.

## Pre-Migration Check

Sebelum menjalankan migration:

```bash
# Pastikan tidak ada error PHP/Intelephense
# Test semua endpoint yang menggunakan TestResultModel:
# - /profile/results
# - /pmb/journey
# - /dashboard
# - /profile/students
```
