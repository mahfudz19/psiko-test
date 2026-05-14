# Migration: Hapus Kolom `school_config_mappings_id` dari Tabel `schools`

## Tanggal

2026-05-13

## Deskripsi

Menghapus kolom `school_config_mappings_id` dari tabel `schools` karena field ini redundan dan menciptakan circular reference. Informasi konfigurasi default sekolah sekarang disimpan di tabel `school_config_mappings` menggunakan flag `is_default = TRUE`.

## Alasan

1. **Circular Reference**: Tabel `schools` tidak boleh memiliki foreign key ke tabel junction `school_config_mappings`
2. **Many-to-Many Relationship**: Tabel `school_config_mappings` adalah junction table untuk relasi many-to-many antara `schools` dan `test_configurations`
3. **Redundansi**: Informasi "default config" sudah disimpan via flag `is_default` di `school_config_mappings`

## SQL Migration

### Drop Kolom

```sql
ALTER TABLE schools DROP COLUMN school_config_mappings_id;
```

### Verifikasi

```sql
-- Cek struktur tabel setelah drop
DESCRIBE schools;

-- Pastikan school_config_mappings memiliki is_default flag
DESCRIBE school_config_mappings;

-- Cek data default config per sekolah
SELECT
    s.id AS school_id,
    s.name AS school_name,
    scm.config_id,
    tc.name AS config_name,
    tc.test_type,
    scm.is_default
FROM schools s
LEFT JOIN school_config_mappings scm ON s.id = scm.school_id AND scm.is_default = TRUE
LEFT JOIN test_configurations tc ON scm.config_id = tc.id
ORDER BY s.id;
```

## Update Model

### SchoolModel.php

Field `school_config_mappings_id` telah dihapus dari schema:

```php
protected array $schema = [
    'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
    'name' => ['type' => 'string', 'nullable' => false],
    'npsn' => ['type' => 'string', 'nullable' => true, 'unique' => true],
    'address' => ['type' => 'text', 'nullable' => true],
    'principal_name' => ['type' => 'string', 'nullable' => true],
    'contact' => ['type' => 'string', 'nullable' => true],
    'accreditation' => ['type' => 'enum', 'values' => ['A', 'B', 'C'], 'nullable' => true]
];
```

### Method Deprecated

Method berikut di `SchoolModel.php` telah di-deprecate:

- `findWithConfig()` - Gunakan `SchoolConfigMappingModel::getDefaultConfig()` instead
- `setDefaultConfig()` - Gunakan `SchoolConfigMappingModel::assignConfig()` dengan `is_default = true`

## Cara Baru Mengakses Default Config

### Sebelum (SALAH)

```php
$school = $schoolModel->findWithConfig($schoolId);
$config = $school['default_config_mapping'];
```

### Sesudah (BENAR)

```php
use SchoolConfigMappingModel;

$schoolConfigModel = new SchoolConfigMappingModel($dbManager);
$config = $schoolConfigModel->getDefaultConfig($schoolId, 'riasec');
```

## Cara Baru Set Default Config

### Sebelum (SALAH)

```php
$schoolModel->setDefaultConfig($schoolId, $mappingId);
```

### Sesudah (BENAR)

```php
$schoolConfigModel->assignConfig($schoolId, $configId, isDefault: true);
```

## Checklist Eksekusi

- [ ] Backup database
- [ ] Jalankan SQL `ALTER TABLE schools DROP COLUMN school_config_mappings_id`
- [ ] Verifikasi struktur tabel dengan `DESCRIBE schools`
- [ ] Test aplikasi untuk memastikan tidak ada error terkait kolom yang dihapus
- [ ] Update dokumentasi jika diperlukan

## Rollback (Jika Diperlukan)

```sql
-- Tambahkan kembali kolom (jika rollback diperlukan)
ALTER TABLE schools ADD COLUMN school_config_mappings_id BIGINT UNSIGNED NULL;

-- Tambahkan foreign key constraint
ALTER TABLE schools
ADD CONSTRAINT fk_school_config_mapping
FOREIGN KEY (school_config_mappings_id)
REFERENCES school_config_mappings(id)
ON DELETE SET NULL;
```

**CATATAN**: Rollback TIDAK DIREKOMENDASIKAN karena akan mengembalikan circular reference.
