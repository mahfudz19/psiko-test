# 📊 Pemahaman Tes RIASEC untuk Psiko-Test

## 1. Overview

Dokumen ini berisi pemahaman tentang instrumen tes psikologi **RIASEC (Holland, 1959)** yang akan diimplementasikan dalam aplikasi Psiko-Test. Dokumen ini menjadi dasar untuk perencanaan implementasi teknis.

---

## 2. Model Psikologi: RIASEC

**RIASEC** adalah model minat karir yang dikembangkan oleh John L. Holland pada tahun 1959. Model ini mengklasifikasikan minat individu ke dalam 6 dimensi:

| Kode  | Dimensi       | Karakteristik Utama                                                        | Warna Brand             |
| ----- | ------------- | -------------------------------------------------------------------------- | ----------------------- |
| **R** | Realistic     | Keterampilan mekanik, kerja fisik, teknis, outdoor, berorientasi pada alat | Hijau `#3B6D11`         |
| **I** | Investigative | Observasi, analisis, pemecahan masalah, berpikir kritis, ilmiah            | Biru `#185FA5`          |
| **A** | Artistic      | Kreativitas, ekspresi diri, estetika, seni, tidak terstruktur              | Coklat/Oranye `#854F0B` |
| **S** | Social        | Interaksi sosial, empati, membantu, mendidik, melayani orang lain          | Ungu `#3C3489`          |
| **E** | Enterprising  | Kepemimpinan, persuasi, wirausaha, ambisi, orientasi tujuan                | Merah `#993C1D`         |
| **C** | Conventional  | Keteraturan, detail, data, administrasi, prosedur, sistematis              | Abu-abu `#5F5E5A`       |

---

## 3. Struktur Instrumen

### 3.1 Komposisi Butir

```
Total Butir: 42 pernyataan
├─ Realistic (R): 7 butir
├─ Investigative (I): 7 butir
├─ Artistic (A): 7 butir
├─ Social (S): 7 butir
├─ Enterprising (E): 7 butir
└─ Conventional (C): 7 butir
```

### 3.2 Distribusi Nomor Butir per Dimensi

| Dimensi | Nomor Butir               |
| ------- | ------------------------- |
| **R**   | 1, 3, 7, 14, 21, 22, 37   |
| **I**   | 2, 11, 18, 19, 26, 32, 33 |
| **A**   | 8, 17, 20, 23, 27, 31, 41 |
| **S**   | 4, 12, 13, 28, 34, 38, 40 |
| **E**   | 5, 10, 15, 16, 29, 36, 42 |
| **C**   | 6, 9, 24, 25, 35, 38, 39  |

> ⚠️ **Catatan:** Butir #38 muncul di dua dimensi (S dan C). Ini perlu diklarifikasi atau disesuaikan dalam implementasi.

### 3.3 Skala Pengukuran

**Skala Likert 4 Poin:**

| Pilihan           | Nilai |
| ----------------- | ----- |
| Sangat Tidak Suka | 1     |
| Tidak Suka        | 2     |
| Suka              | 3     |
| Sangat Suka       | 4     |

- **Tidak ada butir negatif (unfavorable)** - Semua butir正向 (positif)
- **Skor minimum per dimensi:** 7 × 1 = **7**
- **Skor maksimum per dimensi:** 7 × 4 = **28**

---

## 4. Sistem Skoring & Interpretasi

### 4.1 Rumus Perhitungan

```
Skor Dimensi = Σ nilai 7 butir dalam dimensi tersebut
Persentase   = (Skor Dimensi ÷ 28) × 100%
```

### 4.2 Kategori Tingkat Minat

| Skor    | Persentase | Kategori          | Interpretasi                                    |
| ------- | ---------- | ----------------- | ----------------------------------------------- |
| 25 – 28 | 89 – 100%  | **Sangat Tinggi** | Minat dan bakat sangat dominan pada dimensi ini |
| 19 – 24 | 68 – 86%   | **Tinggi**        | Minat dan bakat cukup kuat pada dimensi ini     |
| 13 – 18 | 46 – 64%   | **Sedang**        | Minat dan bakat moderat, perlu eksplorasi lebih |
| 7 – 12  | 25 – 43%   | **Rendah**        | Minat dan bakat kurang pada dimensi ini         |

### 4.3 Penentuan Kode Holland

1. Urutkan keenam dimensi dari **skor tertinggi ke terendah**
2. Ambil **2–3 dimensi teratas** sebagai kode Holland siswa
3. Contoh kode: `ISA`, `RIE`, `ASC`, `ECR`
4. Bila skor dua dimensi sama (tie), lakukan wawancara konseling individual untuk klarifikasi

---

## 5. Daftar Pernyataan (42 Butir)

### Realistic (R)

| No  | Pernyataan                                    |
| --- | --------------------------------------------- |
| 1   | Aku suka mengulik peralatan                   |
| 3   | Aku suka bekerja mandiri (dengan tangan/alat) |
| 7   | Aku suka menyusun balok / LEGO                |
| 14  | Aku suka memelihara binatang                  |
| 21  | Aku suka mencari tahu cara kerja sebuah alat  |
| 22  | Aku suka merangkaikan atau merakit benda      |
| 37  | Aku suka berkegiatan di luar ruangan          |

### Investigative (I)

| No  | Pernyataan                                       |
| --- | ------------------------------------------------ |
| 2   | Aku suka mengerjakan puzzle                      |
| 11  | Aku suka melakukan percobaan / eksperimen        |
| 18  | Aku suka sains                                   |
| 19  | Aku suka mendapatkan tantangan baru              |
| 26  | Aku suka mencari tahu penyebab suatu kejadian    |
| 32  | Aku suka mempraktikkan hal-hal yang aku pelajari |
| 33  | Aku suka mengerjakan soal matematika atau grafik |

### Artistic (A)

| No  | Pernyataan                                   |
| --- | -------------------------------------------- |
| 8   | Aku suka membaca buku tentang seni dan musik |
| 17  | Aku suka membuat karya berbentuk tulisan     |
| 20  | Aku suka menghibur teman                     |
| 23  | Aku adalah orang yang kreatif                |
| 27  | Aku suka memainkan alat musik atau bernyanyi |
| 31  | Aku suka bermain peran / drama               |
| 41  | Aku suka menggambar                          |

### Social (S)

| No  | Pernyataan                                               |
| --- | -------------------------------------------------------- |
| 4   | Aku suka bekerja dalam kelompok                          |
| 12  | Aku suka menjelaskan sesuatu kepada teman                |
| 13  | Aku suka membantu orang lain memecahkan persoalan        |
| 28  | Aku suka mempelajari budaya berbagai daerah              |
| 34  | Aku suka mendiskusikan hal-hal yang terjadi di sekitarku |
| 38  | Aku suka berkegiatan di dalam ruangan dengan meja-kursi  |
| 40  | Aku suka menolong orang                                  |

### Enterprising (E)

| No  | Pernyataan                                                    |
| --- | ------------------------------------------------------------- |
| 5   | Aku suka membuat target untuk diriku sendiri                  |
| 10  | Aku suka meyakinkan teman untuk mengikuti caraku              |
| 15  | Aku tidak berkeberatan bekerja melebihi waktu yang ditentukan |
| 16  | Aku suka menjual sesuatu                                      |
| 29  | Aku ingin membuka usaha sendiri suatu saat nanti              |
| 36  | Aku suka memimpin kelompok atau kelas                         |
| 42  | Aku suka berbicara di depan umum                              |

### Conventional (C)

| No  | Pernyataan                                                 |
| --- | ---------------------------------------------------------- |
| 6   | Aku suka merapikan barang-barang (buku, alat tulis, kamar) |
| 9   | Aku suka mengerjakan hal-hal dengan instruksi yang jelas   |
| 24  | Aku suka memperhatikan detail                              |
| 25  | Aku suka merapikan catatan atau LKS                        |
| 35  | Aku suka merapikan kamarku                                 |
| 38  | Aku suka berkegiatan di dalam ruangan dengan meja-kursi    |
| 39  | Aku suka menghitung                                        |

> ⚠️ **Butir #38 duplikat** muncul di Social (S) dan Conventional (C). Dalam implementasi, butir ini perlu dialokasikan ke salah satu dimensi saja, atau diganti dengan butir baru untuk salah satu dimensi.

---

## 6. Deskripsi Tiap Dimensi

| Kode  | Deskripsi                                                                                                                                              |
| ----- | ------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **R** | Kamu menyukai pekerjaan yang konkret, praktis, dan menggunakan alat atau mesin. Kamu terampil secara mekanik dan senang bekerja di lapangan.           |
| **I** | Kamu memiliki rasa ingin tahu yang tinggi dan senang menganalisis masalah secara mendalam. Kamu menikmati penelitian, eksperimen, dan berpikir ilmiah. |
| **A** | Kamu memiliki jiwa kreatif dan ekspresif yang kuat. Kamu senang bekerja dalam situasi yang tidak terstruktur dan bebas mengekspresikan diri.           |
| **S** | Kamu senang berinteraksi dengan orang lain, membantu, mendidik, dan melayani. Kamu memiliki empati yang tinggi dan kemampuan komunikasi yang baik.     |
| **E** | Kamu memiliki jiwa kepemimpinan dan wirausaha. Kamu senang memengaruhi orang lain, mengambil keputusan, dan berorientasi pada pencapaian tujuan.       |
| **C** | Kamu menyukai keteraturan, ketelitian, dan hal-hal yang terorganisir. Kamu senang bekerja dengan data, angka, dan mengikuti prosedur yang jelas.       |

---

## 7. Rekomendasi Profesi per Dimensi

> **⚠️ Update Implementasi**: Rekomendasi profesi dan jurusan sekarang **di-generate oleh AI** (Gemini API) berdasarkan hasil tes lengkap siswa, konteks profil akademik, dan preferensi pribadi. Daftar di bawah ini adalah referensi dari teori Holland (1959) yang digunakan sebagai baseline knowledge AI.

| Dimensi | Daftar Profesi / Jurusan (Referensi Teori Holland)                 |
| ------- | ------------------------------------------------------------------ |
| **R**   | Teknik Sipil, Pertanian, Mekanik, Konstruksi, Komputer, Pariwisata |
| **I**   | Kedokteran, Biologi, Kimia, Fisika, Matematika, Psikologi, Hukum   |
| **A**   | Desain Komunikasi Visual, Seni, Sastra, Fotografi, Arsitektur      |
| **S**   | Konseling, Keperawatan, Pendidikan, Public Relation, Terapi Fisik  |
| **E**   | Bisnis, Pemasaran, Hukum, Sosial Politik, Perbankan, Real Estate   |
| **C**   | Akuntansi, Administrasi, Asuransi, Banking, Data Processing        |

**Implementasi AI Recommendations:**

- Rekomendasi di-generate secara dinamis via `GeminiService` setelah tes selesai
- AI mempertimbangkan: Holland Code, skor per dimensi, profil akademik, dan preferensi siswa
- Hasil rekomendasi AI disimpan di `test_results.ai_recommendations` (JSON)
- Lihat dokumentasi implementasi: [`riasec-remove-hardcoded-recommendations.md`](riasec-remove-hardcoded-recommendations.md)

---

## 8. Pemetaan ke Mata Pelajaran Lintas Minat (Kurikulum Merdeka)

| Kode  | Dimensi       | Contoh Mapel Lintas Minat                     | Arah Karir                          |
| ----- | ------------- | --------------------------------------------- | ----------------------------------- |
| **R** | Realistic     | Fisika, Prakarya, Informatika, PJOK           | Teknik, Pertanian, Konstruksi       |
| **I** | Investigative | Biologi, Kimia, Matematika, Fisika            | Kedokteran, Sains, Hukum, Psikologi |
| **A** | Artistic      | Seni Budaya, Bahasa Indonesia, Bahasa Inggris | Seni, Desain, Komunikasi, Sastra    |
| **S** | Social        | Sosiologi, Antropologi, PPKn, Sejarah         | Pendidikan, Keperawatan, Konseling  |
| **E** | Enterprising  | Ekonomi, Geografi, Sosiologi                  | Bisnis, Manajemen, Politik          |
| **C** | Conventional  | Matematika, Ekonomi, Informatika              | Akuntansi, Administrasi, Perbankan  |

---

## 9. UI/UX Flow (Dari Contoh HTML)

```
┌─────────────────────────────────────────────────────────────┐
│                    HALAMAN UTAMA TES                        │
├─────────────────────────────────────────────────────────────┤
│  [Tab Navigation]                                           │
│  ┌──────────┬──────────────┬──────────────┐                │
│  │ Kisi-kisi│ Instrumen    │ Panduan      │                │
│  └──────────┴──────────────┴──────────────┘                │
│                                                             │
│  ▶ Tab Kisi-kisi:                                           │
│    - Tabel kisi: 6 dimensi, indikator, deskripsi, no butir │
│    - Tombol "Mulai Asesmen"                                │
│                                                             │
│  ▶ Tab Instrumen:                                           │
│    - Progress bar                                          │
│    - 6 Section blocks (R, I, A, S, E, C)                   │
│    - 7 butir per section dengan radio 1-4                  │
│    - Tombol "Reset" dan "Hitung Skor"                      │
│                                                             │
│  ▶ Tab Panduan:                                             │
│    - Cara pemberan skor                                    │
│    - Rumus perhitungan                                     │
│    - Kategori tingkat minat                                │
│    - Penentuan kode Holland                                │
│    - Pemetaan ke mapel lintas minat                        │
└─────────────────────────────────────────────────────────────┘
                            ↓
            [User klik "Hitung Skor & Lihat Hasil"]
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    HALAMAN HASIL                            │
├─────────────────────────────────────────────────────────────┤
│  - Kode Holland (3 dimensi teratas dengan badge warna)     │
│  - Bar chart skor per dimensi (sorted descending)          │
│  - Deskripsi 3 dimensi dominan (dengan icon 🥇🥈🥉)        │
│  - Rekomendasi profesi per dimensi                         │
│  - Rekomendasi mata pelajaran lintas minat                 │
│  - Tabel rekap skor lengkap                                │
│                                                             │
│  [Tombol] ← Kembali & Edit                                 │
│  [Tombol] Minta Rekomendasi Guru BK → (ke Chat AI)         │
└─────────────────────────────────────────────────────────────┘
```

---

## 10. Isu yang Perlu Diklarifikasi

### 10.1 Butir Duplikat #38

**Masalah:** Butir #38 _"Aku suka berkegiatan di dalam ruangan dengan meja-kursi"_ muncul di dua dimensi:

- Social (S)
- Conventional (C)

**Opsi Solusi:**

1. **Alokasikan ke satu dimensi saja** - Pilih salah satu (misal: C saja, karena lebih cocok dengan karakteristik conventional)
2. **Ganti salah satu dengan butir baru** - Buat butir alternatif untuk dimensi yang lain
3. **Biarkan sebagai validasi silang** - Namun ini akan mengacaukan skoring

**Rekomendasi:** Ganti butir #38 di dimensi Social dengan butir baru yang lebih relevan, contoh:

> _"Aku suka mendengarkan curhatan teman"_

---

## 11. Catatan untuk Implementasi Teknis

### 11.1 Data yang Perlu Disimpan

```
test_sessions
├─ id
├─ student_profile_id (FK)
├─ started_at
├─ completed_at
├─ status (in_progress, completed)
└─ holland_code (e.g., "ISA")

test_responses
├─ id
├─ session_id (FK)
├─ question_number (1-42)
├─ dimension (R, I, A, S, E, C)
├─ answer_value (1-4)
└─ answered_at

test_results
├─ id
├─ session_id (FK)
├─ score_R, score_I, score_A, score_S, score_E, score_C
├─ percentage_R, percentage_I, ...
├─ category_R, category_I, ... (Sangat Tinggi, Tinggi, Sedang, Rendah)
├─ holland_code (e.g., "ISA")
├─ recommended_majors (JSON)
├─ recommended_subjects (JSON)
└─ calculated_at
```

### 11.2 Integrasi dengan Fitur Lain

| Fitur                  | Integrasi                                                                                                 |
| ---------------------- | --------------------------------------------------------------------------------------------------------- |
| **AI Recommendations** | **Primary**: Rekomendasi profesi & jurusan di-generate oleh AI (Gemini API) berdasarkan hasil tes lengkap |
| **Chat Consultation**  | Hasil tes menjadi context untuk AI dalam memberikan konseling lanjutan                                    |
| **PMB Journey**        | Kode Holland digunakan untuk matching jurusan di Universitas Universal                                    |
| **Dashboard Guru BK**  | Monitoring hasil tes siswa, identifikasi pola minat per kelas                                             |
| **Profile Siswa**      | Hasil tes dan rekomendasi AI ditampilkan di halaman profil akademik siswa                                 |

### 11.3 Validasi & Business Rules

- ✅ Semua 42 butir harus terisi sebelum menghitung skor
- ✅ Skor per dimensi minimal 7, maksimal 28
- ✅ Kode Holland terdiri dari 2-3 huruf
- ✅ Jika ada tie (skor sama), flag untuk konseling individual
- ✅ Satu sesi tes = satu hasil (tidak ada partial save di tengah)

---

## 12. Referensi

- Dokumen contoh: [`riasec_asesmen_minat_bakat.html`](../docs/riasec_asesmen_minat_bakat.html)
- Teori Holland: Holland, J. L. (1959). _A theory of vocational choice_
- Kurikulum Merdeka: Mata Pelajaran Lintas Minat SMA

---

> 📝 **Catatan:** Dokumen ini hanya berisi **pemahaman** tentang tes RIASEC. Perencanaan implementasi teknis detail akan dibuat di dokumen terpisah (`riasec-test-implementation.md`).
