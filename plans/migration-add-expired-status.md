# Migration: Tambahkan Status 'expired' ke Tabel `test_sessions`

## Tanggal

2026-05-13

## Deskripsi

Menambahkan status `'expired'` ke kolom `status` di tabel `test_sessions` untuk menangani session tes yang expired karena timeout.

## Masalah

### Bug 1: Status 'expired' tidak ada di enum

Error yang terjadi:

```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status' at row 1
```

Schema di [`TestSessionModel.php`](addon/Models/TestSessionModel.php:32) hanya memiliki:

```php
'status' => ['type' => 'enum', 'values' => ['in_progress', 'completed', 'abandoned'], ...]
```

Tapi code di [`TestController.php`](addon/Controllers/TestController.php:160) mencoba set status = `'expired'`:

```php
$this->sessionModel->updateById($sessionId, ['status' => 'expired']);
```

### Bug 2: Timeout Validation

Dari debug output:

- Waktu pengerjaan: 4028 detik = **67 menit**
- Timeout: 1800 detik = **30 menit**

Validasi timeout sudah benar, user memang sudah melebihi waktu.

## SQL Migration

### Tambahkan Status 'expired' ke Enum

```sql
-- Tambahkan status 'expired' ke kolom enum status
ALTER TABLE test_sessions
MODIFY COLUMN status ENUM('in_progress', 'completed', 'abandoned', 'expired')
NOT NULL DEFAULT 'in_progress';
```

### Verifikasi

```sql
-- Cek struktur kolom
DESCRIBE test_sessions;

-- Cek session yang expired
SELECT * FROM test_sessions WHERE status = 'expired';

-- Cek session yang masih in_progress tapi sudah lewat timeout
SELECT
    id,
    student_profile_id,
    status,
    started_at,
    TIMESTAMPDIFF(SECOND, started_at, NOW()) as elapsed_seconds
FROM test_sessions
WHERE status = 'in_progress'
HAVING elapsed_seconds > 1800;
```

## Update Model

### TestSessionModel.php

Schema telah diupdate:

```php
'status' => ['type' => 'enum', 'values' => ['in_progress', 'completed', 'abandoned', 'expired'], 'nullable' => false, 'default' => 'in_progress'],
```

## Checklist Eksekusi

- [ ] Backup database
- [ ] Jalankan SQL ALTER TABLE untuk tambah status 'expired'
- [ ] Verifikasi dengan DESCRIBE test_sessions
- [ ] Test timeout functionality
- [ ] Update dokumentasi jika diperlukan

## Rollback (Jika Diperlukan)

```sql
-- Hapus status 'expired' dari enum (hanya jika tidak ada data yang menggunakan)
-- Pertama, update semua 'expired' menjadi 'abandoned'
UPDATE test_sessions SET status = 'abandoned' WHERE status = 'expired';

-- Kemudian modify enum
ALTER TABLE test_sessions
MODIFY COLUMN status ENUM('in_progress', 'completed', 'abandoned')
NOT NULL DEFAULT 'in_progress';
```

## Status Tes RIASEC

| Status        | Deskripsi                                                  |
| ------------- | ---------------------------------------------------------- |
| `in_progress` | Tes sedang dikerjakan                                      |
| `completed`   | Tes selesai dikerjakan dan sudah disubmit                  |
| `abandoned`   | Tes ditinggalkan/dibatalkan sebelum selesai                |
| `expired`     | Tes otomatis selesai karena waktu habis (timeout 30 menit) |
