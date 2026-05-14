# Migration: Add Unique Constraint pada test_responses

## Tanggal

2026-05-14

## Deskripsi

Menambahkan unique constraint pada tabel `test_responses` untuk mendukung `ON DUPLICATE KEY UPDATE` dalam bulk insert operations.

## Latar Belakang

Method `TestResponseModel::saveMany()` menggunakan `ON DUPLICATE KEY UPDATE` untuk handle insert or update (upsert). Namun, fitur ini memerlukan unique constraint atau primary key pada kolom yang di-check untuk duplicate.

Tanpa unique constraint, `ON DUPLICATE KEY UPDATE` tidak akan berfungsi karena MySQL tidak memiliki cara untuk mendeteksi duplicate records.

## Perubahan Schema

### Tabel: `test_responses`

**Unique Constraint Baru:**

```sql
ALTER TABLE `test_responses`
ADD UNIQUE KEY `uk_session_statement` (`session_id`, `statement_id`);
```

**Alasan:**

- `session_id` + `statement_id` harus unik karena satu siswa hanya boleh menjawab satu pernyataan sekali dalam satu sesi tes
- Constraint ini memungkinkan `ON DUPLICATE KEY UPDATE` untuk mengupdate jawaban jika user mengubah jawaban mereka
- Mencegah duplicate entries untuk kombinasi session_id dan statement_id yang sama

## SQL Migration Script

```sql
-- Migration: Add unique constraint pada test_responses
-- Untuk mendukung bulk insert with upsert (ON DUPLICATE KEY UPDATE)

-- Cek apakah constraint sudah ada
SELECT CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND TABLE_NAME = 'test_responses'
  AND CONSTRAINT_NAME = 'uk_session_statement';

-- Tambahkan unique constraint
ALTER TABLE `test_responses`
ADD UNIQUE KEY `uk_session_statement` (`session_id`, `statement_id`);

-- Verifikasi
SHOW INDEX FROM `test_responses` WHERE Key_name = 'uk_session_statement';
```

## Impact Analysis

### Files yang Terpengaruh:

1. **`addon/Models/TestResponseModel.php`**
   - Method `saveMany()` sekarang akan berfungsi dengan benar untuk upsert
   - Method `saveResponse()` sudah menggunakan `ON DUPLICATE KEY UPDATE`

2. **`addon/Controllers/TestController.php`**
   - Method `submitTest()` menggunakan `saveMany()` untuk bulk insert
   - Validasi jawaban lebih ketat sebelum insert

### Benefits:

- ✅ **Performance**: Bulk insert lebih efisien daripada insert satu per satu
- ✅ **Data Integrity**: Mencegah duplicate jawaban untuk statement yang sama
- ✅ **Upsert Support**: User bisa mengubah jawaban tanpa error duplicate
- ✅ **Atomicity**: Semua jawaban disimpan dalam satu query

## Rollback Script

Jika perlu rollback:

```sql
-- Hapus unique constraint
ALTER TABLE `test_responses`
DROP INDEX `uk_session_statement`;
```

## Testing Checklist

- [ ] Jalankan migration SQL di database development
- [ ] Verifikasi unique constraint sudah ada dengan `SHOW INDEX FROM test_responses`
- [ ] Test submit jawaban baru (harus INSERT)
- [ ] Test ubah jawaban (harus UPDATE, bukan INSERT baru)
- [ ] Test bulk insert dengan `saveMany()`
- [ ] Test validasi jawaban duplicate manual di database

## Related Files

- [`addon/Models/TestResponseModel.php`](../addon/Models/TestResponseModel.php:54) - Method `saveMany()`
- [`addon/Controllers/TestController.php`](../addon/Controllers/TestController.php:198) - Method `submitTest()`

## Notes

Migration ini **NON-BREAKING** dan aman untuk dijalankan di production karena:

- Hanya menambahkan constraint, tidak mengubah data existing
- Data existing seharusnya sudah unik secara natural (satu siswa satu jawaban per statement)
- Jika ada duplicate data existing, migration akan gagal dan perlu cleanup manual terlebih dahulu

## Pre-Migration Check

Sebelum menjalankan migration, cek apakah ada duplicate data:

```sql
-- Cek duplicate records
SELECT session_id, statement_id, COUNT(*) as count
FROM test_responses
GROUP BY session_id, statement_id
HAVING COUNT(*) > 1;

-- Jika ada duplicate, hapus yang lama (keep yang terbaru)
DELETE tr1 FROM test_responses tr1
INNER JOIN test_responses tr2
WHERE tr1.session_id = tr2.session_id
  AND tr1.statement_id = tr2.statement_id
  AND tr1.answered_at < tr2.answered_at;
```
