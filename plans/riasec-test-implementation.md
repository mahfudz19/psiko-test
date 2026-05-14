# 📋 Rencana Implementasi Fitur Tes RIASEC

## 1. Overview

Dokumen ini berisi rencana implementasi fitur Tes RIASEC untuk siswa (role "user") dalam aplikasi Psiko-Test. Implementasi mencakup menu sidebar, controller, routing, dan tampilan.

---

## 2. Struktur Menu Sidebar

### 2.1 Lokasi File

`addon/Views/(app)/layout.php` - di dalam blok `<?php if (($_SESSION['auth.user_role'] ?? '') === 'user'): ?>`

### 2.2 Posisi Menu

Menu "Tes Psikologi" akan ditempatkan **setelah** menu "Konsultasi AI" dan **sebelum** penutup blok user.

### 2.3 Struktur Menu

```
Tes Psikologi (menu utama dengan collapse)
├── Tes RIASEC (submenu)
└── Tes IQ (submenu - future use)
```

### 2.4 Active State Logic

- Menu utama "Tes Psikologi" aktif jika user berada di halaman `/tests` atau halaman turunannya
- Submenu "Tes RIASEC" aktif jika user di `/tests/riasec` atau `/tests/riasec/*`
- Submenu "Tes IQ" aktif jika user di `/tests/iq` atau `/tests/iq/*`

---

## 3. Controller: TestController.php

### 3.1 Lokasi File

`addon/Controllers/TestController.php`

### 3.2 Dependencies (Inject via Constructor)

- `TestConfigurationModel` - Mengelola konfigurasi tes
- `TestSessionModel` - Mengelola sesi pengerjaan tes
- `TestStatementModel` - Mengambil butir pernyataan
- `TestResponseModel` - Menyimpan jawaban siswa
- `TestResultModel` - Menghitung skor dan hasil
- `StudentProfileModel` - Data profil siswa
- `Request` - HTTP request handler
- `Response` - HTTP response handler
- `View` - View renderer

### 3.3 Method Controller

#### A. `index()` - Dashboard Tes

**Route:** `GET /tests`
**Fungsi:**

- Redirect ke `/profile/results` (karena user memilih menggunakan halaman yang sudah ada)
- Atau tampilkan dashboard dengan daftar tes tersedia

#### B. `riasecIndex()` - Halaman Info Tes RIASEC

**Route:** `GET /tests/riasec`
**Fungsi:**

- Cek apakah siswa sudah pernah tes RIASEC sebelumnya
- Jika sudah: redirect ke hasil tes terakhir
- Jika belum: tampilkan halaman info tes dengan tombol "Mulai Tes"
- Tampilkan info: jumlah butir (42), waktu pengerjaan, instruksi

#### C. `startTest()` - Mulai Tes

**Route:** `POST /tests/riasec/start`
**Fungsi:**

- Validasi: cek apakah sudah ada sesi aktif untuk RIASEC
- Jika ada sesi aktif: redirect ke sesi tersebut
- Jika tidak: buat sesi baru di `test_sessions`
- Redirect ke `/tests/riasec/take/{sessionId}`

#### D. `takeTest()` - Pengerjaan Tes

**Route:** `GET /tests/riasec/take/{sessionId}`
**Fungsi:**

- Validasi sessionId dan kepemilikan sesi
- Ambil konfigurasi tes dari session
- Ambil semua pernyataan (42 butir) dari database
- Tampilkan interface pengerjaan dengan progress bar

#### E. `submitTest()` - Submit Jawaban

**Route:** `POST /tests/riasec/submit`
**Fungsi:**

- Terima semua jawaban dari form (42 butir)
- Validasi: pastikan semua terisi
- Simpan jawaban ke `test_responses`
- Update status session menjadi "completed"
- Hitung skor menggunakan `TestResultModel.calculateScores()`
- Simpan hasil ke `test_results`
- Redirect ke halaman hasil

#### F. `viewResults()` - Lihat Hasil

**Route:** `GET /tests/riasec/results/{sessionId}`
**Fungsi:**

- Ambil hasil tes dari database
- Decode JSON fields (scores, percentages, categories, dll)
- Tampilkan: skor per dimensi, kategori, holland code, rekomendasi

---

## 4. Routing

### 4.1 Lokasi File

`addon/Router/index.php`

### 4.2 Route Definitions

| Method | URI                     | Handler                      | Deskripsi                |
| ------ | ----------------------- | ---------------------------- | ------------------------ |
| GET    | `/tests`                | `TestController@index`       | Dashboard tes (redirect) |
| GET    | `/tests/riasec`         | `TestController@riasecIndex` | Info tes RIASEC          |
| POST   | `/tests/riasec/start`   | `TestController@startTest`   | Mulai tes baru           |
| GET    | `/tests/riasec/take`    | `TestController@takeTest`    | Pengerjaan tes           |
| POST   | `/tests/riasec/submit`  | `TestController@submitTest`  | Submit jawaban           |
| GET    | `/tests/riasec/results` | `TestController@viewResults` | Lihat hasil              |

### 4.3 Middleware

Semua route tes menggunakan middleware:

- `AuthMiddleware` - Wajib login
- `RoleMiddleware` - Hanya role "user" (siswa)

---

## 5. View Files

### 5.1 Folder Structure

```
addon/Views/(app)/tests/
├── index.php (optional - redirect)
└── riasec/
    ├── index.php (info tes)
    ├── take.php (pengerjaan)
    └── results.php (hasil)
```

### 5.2 `tests/index.php` (Optional)

**Fungsi:** Dashboard daftar tes
**Konten:**

- Card "Tes RIASEC" dengan tombol "Mulai"
- Card "Tes IQ" (placeholder untuk future)
- Widget riwayat tes terakhir

### 5.3 `tests/riasec/index.php` - Info Tes

**Layout:** `(app)/layout.php`
**Konten:**

- Header: "Tes Minat Karir RIASEC"
- Deskripsi singkat tentang RIASEC (Holland, 1959)
- Info box:
  - Jumlah butir: 42 pertanyaan
  - Waktu: Tanpa batas (self-paced)
  - Skala: Likert 4 poin (Sangat Tidak Suka - Sangat Suka)
- 6 dimensi dengan icon dan warna:
  - R (Realistic) - Hijau
  - I (Investigative) - Biru
  - A (Artistic) - Oranye
  - S (Social) - Ungu
  - E (Enterprising) - Merah
  - C (Conventional) - Abu-abu
- Tombol "Mulai Tes" (besar, prominent)
- Link ke panduan lengkap (tab/modal)

### 5.4 `tests/riasec/take.php` - Pengerjaan Tes

**Layout:** `(app)/layout.php` atau full-screen layout
**Konten:**

- **Header:**
  - Progress bar: "Butir X dari 42"
  - Indikator progress (persentase)
- **Pernyataan:**
  - Tampilkan 1 butir per halaman ATAU semua butir dalam satu halaman (scrollable)
  - Nomor butir
  - Teks pernyataan
  - 4 radio buttons (1-4) dengan label:
    - 1 = Sangat Tidak Suka
    - 2 = Tidak Suka
    - 3 = Suka
    - 4 = Sangat Suka
- **Navigation:**
  - Tombol "Sebelumnya" (disabled untuk butir pertama)
  - Tombol "Selanjutnya" (ubah jadi "Submit" untuk butir terakhir)
  - Atau: tombol "Submit Semua" jika single-page
- **Validasi:**
  - Highlight butir yang belum diisi
  - Konfirmasi sebelum submit jika ada yang kosong

### 5.5 `tests/riasec/results.php` - Hasil Tes

**Layout:** `(app)/layout.php`
**Konten:**

- **Header:**
  - Judul: "Hasil Tes RIASEC"
  - Tanggal pengerjaan
- **Kode Holland:**
  - Badge besar dengan 3 huruf (contoh: "ISA")
  - Warna sesuai dimensi dominan
- **Grafik Skor:**
  - Bar chart horizontal dengan 6 bar (R, I, A, S, E, C)
  - Sorted descending (tertinggi di atas)
  - Warna berbeda per dimensi
  - Label skor numerik di setiap bar
- **Tabel Detail:**
  | Dimensi | Skor | Persentase | Kategori |
  |---------|------|------------|----------|
  | I | 25 | 89% | Sangat Tinggi |
  | S | 23 | 82% | Tinggi |
  | ... | ... | ... | ... |
- **Deskripsi Dimensi Dominan:**
  - Paragraf penjelasan untuk 3 dimensi teratas
  - Icon dan warna per dimensi
- **Rekomendasi Profesi:**
  - List profesi per dimensi (dari konfigurasi)
- **Rekomendasi Mata Pelajaran:**
  - List mapel lintas minat (dari konfigurasi)
- **Action Buttons:**
  - "Cetak Hasil" (print/download PDF)
  - "Konsultasi dengan Guru BK" (link ke Chat AI)
  - "Kembali ke Profile" (link ke `/profile/results`)

---

## 6. Update profile/results.php

### 6.1 Widget "Tes Tersedia"

**Posisi:** Di atas section "Analisis AI" atau di sidebar kanan

**Konten:**

- Card dengan header "Tes Psikologi"
- List tes tersedia:
  - RIASEC: Status "Belum dikerjakan" / "Sudah dikerjakan"
  - IQ: Status "Coming Soon"
- Tombol "Mulai Tes RIASEC" (jika belum tes)
- Link "Lihat Hasil" (jika sudah tes)

### 6.2 Update Section "Riwayat Test Psikologi"

**Tambahan:**

- Badge "Test Terbaru" untuk hasil terakhir
- Tombol "Lihat Detail" yang link ke `/tests/riasec/results/{sessionId}`

---

## 7. User Flow

### 7.1 First-Time User (Belum Pernah Tes)

```
1. User klik menu "Tes Psikologi" → "Tes RIASEC" di sidebar
2. Tampil halaman info tes RIASEC
3. User klik "Mulai Tes"
4. System buat sesi baru, redirect ke pengerjaan
5. User jawab 42 butir pernyataan
6. User klik "Submit"
7. System validasi, simpan jawaban, hitung skor
8. Redirect ke halaman hasil
9. User bisa lihat detail skor, holland code, rekomendasi
```

### 7.2 Returning User (Sudah Pernah Tes)

```
1. User klik menu "Tes Psikologi" → "Tes RIASEC" di sidebar
2. System deteksi sudah ada hasil tes
3. Redirect langsung ke halaman hasil
4. User bisa lihat hasil terakhir
5. Opsi: Tes ulang (jika diizinkan) atau cetak hasil
```

---

## 8. Business Rules & Validasi

### 8.1 Validasi Sebelum Tes

- User harus sudah melengkapi profil akademik (cek di `student_profiles`)
- User belum memiliki sesi aktif untuk RIASEC
- User belum pernah tes (atau belum ada cooldown period)

### 8.2 Validasi Saat Submit

- Semua 42 butir harus terisi
- Jawaban harus bernilai 1-4
- Session harus dalam status "in_progress"

### 8.3 Skoring Rules

- Skor per dimensi: Σ jawaban 7 butir (min 7, max 28)
- Persentase: (skor / 28) × 100
- Kategori:
  - 25-28 = Sangat Tinggi (89-100%)
  - 19-24 = Tinggi (68-86%)
  - 13-18 = Sedang (46-64%)
  - 7-12 = Rendah (25-43%)
- Holland Code: 3 huruf dari dimensi tertinggi

### 8.4 Tie-Breaking

- Jika ada skor sama, urutkan berdasarkan abjad (R → I → A → S → E → C)
- Flag untuk konseling individual jika ada tie yang signifikan

---

## 9. Database Interaction Summary

### 9.1 Read Operations

| Model                  | Method                                   | Tujuan               |
| ---------------------- | ---------------------------------------- | -------------------- |
| TestConfigurationModel | `getActiveConfig('riasec')`              | Ambil konfigurasi    |
| TestStatementModel     | `getByConfigId($configId)`               | Ambil 42 butir       |
| TestSessionModel       | `getActiveSession($studentId, 'riasec')` | Cek sesi aktif       |
| TestResultModel        | `getLatestRiasecResult($studentId)`      | Ambil hasil terakhir |

### 9.2 Write Operations

| Model             | Method              | Tujuan                |
| ----------------- | ------------------- | --------------------- |
| TestSessionModel  | `createSession()`   | Buat sesi baru        |
| TestSessionModel  | `completeSession()` | Update status selesai |
| TestResponseModel | `saveMany()`        | Bulk insert jawaban   |
| TestResultModel   | `saveResult()`      | Simpan hasil          |
| TestResultModel   | `calculateScores()` | Hitung skor           |

---

## 10. File Checklist

### 10.1 Files to Create

- [ ] `addon/Controllers/TestController.php`
- [ ] `addon/Views/(app)/tests/index.php` (optional)
- [ ] `addon/Views/(app)/tests/riasec/index.php`
- [ ] `addon/Views/(app)/tests/riasec/take.php`
- [ ] `addon/Views/(app)/tests/riasec/results.php`

### 10.2 Files to Update

- [ ] `addon/Views/(app)/layout.php` (menu sidebar)
- [ ] `addon/Views/(app)/profile/results.php` (widget tes tersedia)
- [ ] `addon/Router/index.php` (routing)

---

## 11. Testing Scenarios

### 11.1 Functional Testing

1. User belum login → akses `/tests/riasec` → redirect ke login
2. User role admin → akses `/tests/riasec` → akses ditolak
3. User role user → akses `/tests/riasec` → tampil info tes
4. Submit tanpa isi semua jawaban → error message
5. Submit dengan semua jawaban terisi → sukses, redirect ke hasil

### 11.2 Edge Cases

1. User refresh saat pengerjaan → session tetap aktif
2. User close browser saat pengerjaan → session abandoned
3. User sudah punya sesi aktif → redirect ke sesi tersebut
4. User sudah pernah tes → redirect ke hasil

---

## 12. Next Steps

1. **Implementasi Menu Sidebar** - Update layout.php
2. **Implementasi Routing** - Update router
3. **Implementasi Controller** - Buat TestController.php
4. **Implementasi Views** - Buat file view satu per satu
5. **Testing** - Functional dan edge case testing
6. **Dokumentasi** - Update user manual

---

> 📝 **Catatan:** Dokumen ini adalah planning tanpa kode. Implementasi detail akan dilakukan di file terpisah dengan mengikuti standar Mazu Framework.
