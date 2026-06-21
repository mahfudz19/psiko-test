# Assign Page Redesign - Arsitektur Baru (Left-Right Layout)

## Konsep UI/UX

### Layout Halaman (Side-by-Side)

```
┌──────────────────────────────────────────────────────────────────────┐
│  ← Kembali ke Daftar Konfigurasi                                     │
│                                                                       │
│  Assign Konfigurasi ke Sekolah                                        │
│  RIASEC Standar 42 Butir                                              │
├──────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌───────────────────────────┐    ┌──────────────────────────────┐  │
│  │  🔍 CARI SEKOLAH          │    │  ✅ SEKOLAH DIASSIGN (3)     │  │
│  │                           │    │                              │  │
│  │  ┌─────────────────────┐  │    │  ┌────────────────────────┐  │  │
│  │  │ 🔍 Search...        │  │    │  │ ⭐ SMA N 1 Jakarta     │  │  │
│  │  └─────────────────────┘  │    │  │    Default             │  │  │
│  │                           │    │  │ [Lepas]                │  │  │
│  │  Hasil Pencarian:         │    │  ├────────────────────────┤  │  │
│  │  ┌─────────────────────┐  │    │  │   SMA N 2 Bandung      │  │  │
│  │  │ ☐ SMA N 3 Jakarta   │  │    │  │ [Set Default] [Lepas] │  │  │
│  │  │ ☐ SMA N 4 Bandung   │  │    │  ├────────────────────────┤  │  │
│  │  │ ☐ SMK N 2 Jakarta   │  │    │  │   SMK N 1 Surabaya     │  │  │
│  │  │ ☐ SMA N 5 Surabaya  │  │    │  │ [Set Default] [Lepas] │  │  │
│  │  │ ☐ ...               │  │    │  └────────────────────────┘  │  │
│  │  └─────────────────────┘  │    │                              │  │
│  │                           │    │                              │  │
│  │  Selected: 2              │    │  ─────────────────────────   │  │
│  │  [→ Tambahkan]            │    │  Total: 3 sekolah            │  │
│  └───────────────────────────┘    └──────────────────────────────┘  │
│                                                                       │
├──────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  [Batal]  [💾 Simpan Assignment]                                      │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

## Komponen UI

### Panel Kiri: Cari Sekolah

- **Search Input** - Real-time search (nama/NPSN/kota)
- **Results List** - Checkbox cards dengan:
  - Nama sekolah
  - Kota
  - NPSN
  - Checkbox untuk select
- **Selection Counter** - Jumlah yang dipilih
- **Add Button** - "Tambahkan" untuk move ke panel kanan

### Panel Kanan: Sekolah Diassign

- **Header** - Counter jumlah sekolah assigned
- **Assigned List** - Vertical cards:
  - Nama sekolah
  - Badge "Default" (⭐) jika default
  - Action buttons:
    - **Set Default** - Jadikan default
    - **Lepas** - Remove dari assigned
- **Total Counter** - Summary di bawah

## Flow Interaksi

### Menambah Sekolah (Kiri → Kanan)

1. User ketik di search box (panel kiri)
2. System filter sekolah (live search)
3. User centang sekolah yang diinginkan
4. Counter update "Selected: X"
5. User klik "Tambahkan"
6. Sekolah pindah ke panel kanan (assigned list)
7. Checkbox di kiri reset

### Menghapus Sekolah (Kanan)

1. User klik "Lepas" di sekolah
2. Sekolah langsung dihapus dari assigned list
3. Sekolah bisa dicari lagi di panel kiri

### Set Default

1. User klik "Set Default" di sekolah
2. Badge default pindah ke sekolah tersebut
3. Sekolah lain kehilangan badge default

### Simpan Assignment

1. User klik "Simpan Assignment"
2. Form submit dengan data:
   - Array school IDs (dari panel kanan)
   - Default school ID
3. Redirect ke /admin/tests dengan success message

## Data Structure

### Controller Output

```php
$props = [
    'config' => $config,                    // Config detail
    'assignedSchools' => $assignedSchools,  // Full school data assigned
    'defaultSchoolId' => $defaultSchoolId,  // ID default school
    'allSchools' => $allSchools,            // All schools untuk search
];
```

### Frontend State

```javascript
{
    assignedSchools: [
        { id: 1, name: 'SMA N 1', npsn: '...', city: '...', isDefault: true }
    ],
    allSchools: [...],        // All schools untuk search
    searchQuery: '',
    searchResults: [],
    selectedSchools: []       // IDs yang dipilih di kiri
}
```

## Responsive Behavior

### Desktop (≥768px)

- 2 kolom side-by-side
- Kiri: 40-50% width
- Kanan: 50-60% width

### Mobile (<768px)

- Stack vertical
- Panel kiri atas
- Panel kanan bawah
- Full width

## Visual Hierarchy

### Panel Kanan (Priority Higher)

- Border lebih prominent
- Background highlight
- Karena ini data yang sudah tersimpan

### Panel Kiri (Priority Lower)

- Standard card styling
- Untuk input/add baru

## Next Steps

1. Update controller untuk pass `allSchools` dan `assignedSchools`
2. Buat view dengan 2-panel layout
3. CSS untuk side-by-side layout
4. JavaScript untuk:
   - Live search (filter allSchools)
   - Checkbox selection
   - Move left → right (add)
   - Remove right (lepas)
   - Set default
   - Form submission
