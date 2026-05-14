# Analisis: Menghapus Hardcoded Recommendations dari Test Results

## 📋 Ringkasan

Dokumen ini menganalisis perubahan yang diperlukan untuk menghapus logic hardcoded recommendations (profesi & jurusan) dari hasil tes RIASEC, karena rekomendasi akan di-handle oleh AI.

---

## 🔍 File-File yang Terlibat

### 1. **`addon/Controllers/TestController.php`**

#### A. Method `getRecommendations()` (Line 425-472)

**Status:** ⛔ **HAPUS**

Method ini berisi hardcoded data rekomendasi untuk setiap dimensi RIASEC:

```php
private function getRecommendations(string $hollandCode): array
{
    $recommendations = [
        'R' => [
            'profesi' => ['Teknisi Mesin', 'Insinyur Sipil', 'Arsitek', 'Pilot', 'Chef'],
            'jurusan' => ['Teknik Mesin', 'Teknik Sipil', 'Arsitektur', 'Teknik Industri']
        ],
        'I' => [...],
        'A' => [...],
        // ...
    ];
    // ...
}
```

**Action:** Hapus seluruh method ini.

---

#### B. Method `submitTest()` (Line 239-349)

**Status:** ⚠️ **MODIFIKASI**

Pada line 339, method ini memanggil `getRecommendations()`:

```php
// Line 332-340
$resultData = [
    'session_id' => $sessionId,
    'test_type' => $config['test_type'],
    'scores' => json_encode($scoreResult['scores']),
    'categories' => json_encode($scoreResult['categories']),
    'holland_code' => $scoreResult['holland_code'],
    'holland_description' => $this->getHollandDescription($scoreResult['holland_code']),
    'recommendations' => json_encode($this->getRecommendations($scoreResult['holland_code'])) // ⛔ HAPUS
];
```

**Action:**

1. Hapus baris `'recommendations' => json_encode($this->getRecommendations(...))`
2. (Optional) Tambahkan trigger untuk AI analysis di sini

**Hasil setelah modifikasi:**

```php
$resultData = [
    'session_id' => $sessionId,
    'test_type' => $config['test_type'],
    'scores' => json_encode($scoreResult['scores']),
    'categories' => json_encode($scoreResult['categories']),
    'holland_code' => $scoreResult['holland_code'],
    'holland_description' => $this->getHollandDescription($scoreResult['holland_code']),
    // recommendations dihapus - akan diisi oleh AI nanti
];
```

---

#### C. Method `viewResults()` (Line 354-384)

**Status:** ✅ **NO CHANGE**

Method ini hanya mengambil data hasil tes dari database dan melempar ke view. Tidak ada perubahan diperlukan di sini.

---

### 2. **`addon/Views/(app)/tests/riasec/results.php`**

**Status:** ⚠️ **MODIFIKASI**

#### A. Decode Recommendations (Line 13)

```php
// Line 13
$recommendations = isset($result['recommendations']) ? json_decode($result['recommendations'], true) : ['profesi' => [], 'jurusan' => []];
```

**Action:** Tetap pertahankan untuk backward compatibility, tapi akan selalu empty.

---

#### B. Section "Rekomendasi Profesi" (Line 170-188)

```php
<!-- Line 170-188 -->
<div class="riasec-recommendations">
    <h2>Rekomendasi Profesi</h2>
    <?php if (!empty($recommendations['profesi'])): ?>
        <div class="recommendations-grid">
            <?php foreach (array_slice($recommendations['profesi'], 0, 9) as $profesi): ?>
                <div class="recommendation-item">
                    <svg>...</svg>
                    <span><?= htmlspecialchars($profesi) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-recommendations">Belum ada rekomendasi tersedia.</p>
    <?php endif; ?>
</div>
```

**Action Options:**

**Option 1: Hapus Section**

- Hapus seluruh block Line 170-188
- User tidak akan melihat section rekomendasi profesi sama sekali

**Option 2: Tampilkan Placeholder AI**

- Modifikasi untuk menampilkan pesan "Rekomendasi AI akan tersedia segera"
- Atau tambahkan button "Minta Rekomendasi dari AI" yang redirect ke Chat

**Rekomendasi:** Option 2 (lebih user-friendly)

---

#### C. Section "Rekomendasi Jurusan Kuliah" (Line 190-208)

```php
<!-- Line 190-208 -->
<div class="riasec-recommendations">
    <h2>Rekomendasi Jurusan Kuliah</h2>
    <?php if (!empty($recommendations['jurusan'])): ?>
        ...
    <?php else: ?>
        <p class="no-recommendations">Belum ada rekomendasi tersedia.</p>
    <?php endif; ?>
</div>
```

**Action:** Sama seperti section profesi - pilih Option 1 atau Option 2.

---

### 3. **`addon/Models/TestResultModel.php`**

**Status:** ✅ **NO CHANGE** (atau optional enhancement)

Model ini hanya menyimpan dan mengambil data. Field `recommendations` di tabel `test_results` masih bisa ada untuk:

- Backward compatibility
- Future AI recommendations storage

**Optional:** Jika ingin benar-benar bersih, bisa tambahkan migration untuk DROP COLUMN `recommendations` dari tabel `test_results`.

---

### 4. **Database Schema**

#### Tabel `test_results`

**Status:** ⚠️ **OPTIONAL ALTER**

```sql
-- Column recommendations masih ada
ALTER TABLE test_results
MODIFY COLUMN recommendations JSON NULL DEFAULT NULL;
```

**Action:**

- **Short term:** Biarkan column, hanya tidak diisi (NULL)
- **Long term:** Buat migration untuk DROP COLUMN jika yakin tidak akan dipakai

---

### 5. **`plans/riasec-test-understanding.md`**

**Status:** ⚠️ **UPDATE DOKUMENTASI**

Section yang perlu diupdate:

#### A. Section 7 - Rekomendasi Profesi per Dimensi (Line 186-196)

```markdown
## 7. Rekomendasi Profesi per Dimensi

| Dimensi | Daftar Profesi / Jurusan                                                  |
| ------- | ------------------------------------------------------------------------- |
| **R**   | Teknik Sipil, Pertanian, Mekanik, Konstruksi, Komputer, Pariwisata        |
| **I**   | Kedokteran, Biologi, Kimia, Fisika, Matematika, Psikologi, Hukum, Ekonomi |

...
```

**Action:** Tambahkan note bahwa rekomendasi sekarang di-handle oleh AI

#### B. Section 11.2 - Integrasi dengan Fitur Lain (Line 314-322)

```markdown
### 11.2 Integrasi dengan Fitur Lain

| Fitur                 | Integrasi                                                       |
| --------------------- | --------------------------------------------------------------- |
| **Chat Consultation** | Hasil tes menjadi context untuk AI dalam memberikan rekomendasi |
```

**Action:** Update untuk mempertegas bahwa AI adalah sumber utama rekomendasi

---

## 📝 Rencana Implementasi

### Phase 1: Hapus Hardcoded Logic (Immediate)

**Step 1:** Hapus `getRecommendations()` dari TestController.php

```bash
# File: addon/Controllers/TestController.php
# Line: 425-472
```

**Step 2:** Update `submitTest()` untuk tidak mengisi recommendations

```bash
# File: addon/Controllers/TestController.php
# Line: 339 - hapus baris ini
```

**Step 3:** Update results.php untuk menampilkan placeholder

```bash
# File: addon/Views/(app)/tests/riasec/results.php
# Line: 170-208 - modifikasi section rekomendasi
```

---

### Phase 2: AI Integration (Future)

**Step 4:** Tambahkan AI analysis trigger setelah submit

- Call Gemini API dengan context: holland_code, scores, student profile
- Simpan hasil AI ke `student_profiles.ai_analysis` atau tabel terpisah

**Step 5:** Tampilkan AI recommendations di results page

- Fetch dari AI analysis result
- Display dengan format yang lebih personal

---

## 🎯 Impact Analysis

### Yang TIDAK Berubah:

- ✅ Scoring system
- ✅ Holland code calculation
- ✅ Category classification
- ✅ Data storage structure (scores, categories, holland_code)
- ✅ Results page layout (scores, visualization)

### Yang Berubah:

- ⚠️ Recommendations tidak lagi ditampilkan (atau ditampilkan dari AI)
- ⚠️ Field `recommendations` di database akan NULL
- ⚠️ User tidak melihat list profesi/jurusan hardcoded

### Potensi Issues:

1. **Backward Compatibility:** Existing results yang sudah punya recommendations akan tetap menampilkannya
2. **Empty State:** User mungkin bingung kenapa tidak ada rekomendasi
3. **Documentation:** Perlu update user guide/sop

---

## ✅ Testing Checklist

- [ ] Submit test baru → field recommendations NULL di database
- [ ] View results → section rekomendasi menampilkan placeholder/empty
- [ ] Existing results (sebelum change) → masih menampilkan recommendations lama
- [ ] Tidak ada PHP error di results page
- [ ] Redirect setelah submit tetap berfungsi
- [ ] Holland code dan scores tetap terhitung dengan benar

---

## 🔗 Related Files

| File                                         | Line             | Action                                           |
| -------------------------------------------- | ---------------- | ------------------------------------------------ |
| `addon/Controllers/TestController.php`       | 425-472          | DELETE method `getRecommendations()`             |
| `addon/Controllers/TestController.php`       | 339              | REMOVE recommendations dari resultData           |
| `addon/Views/(app)/tests/riasec/results.php` | 170-208          | MODIFY/OPTIONALLY REMOVE recommendation sections |
| `addon/Models/TestResultModel.php`           | -                | NO CHANGE (optional: add AI analysis method)     |
| `plans/riasec-test-understanding.md`         | 186-196, 314-322 | UPDATE documentation                             |

---

## 💡 Rekomendasi Tambahan

1. **AI Prompt Design:** Siapkan prompt template untuk Gemini yang mencakup:
   - Holland code (3 huruf teratas)
   - Scores per dimensi
   - Categories (Sangat Tinggi, Tinggi, Sedang, Rendah)
   - Student academic data (if available)
   - Local job market context

2. **Fallback Strategy:** Jika AI gagal generate recommendations:
   - Tampilkan message: "Rekomendasi sedang tidak tersedia, silakan konsultasi dengan guru BK"
   - Atau gunakan rule-based fallback (jika diperlukan)

3. **Caching:** Cache AI recommendations untuk menghindari repeated API calls

4. **Cost Optimization:** Trigger AI hanya sekali per student (saat submit pertama kali)

---

## 📊 Database Schema (Reference)

```sql
CREATE TABLE test_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id BIGINT UNSIGNED NOT NULL,
    test_type VARCHAR(50) NOT NULL,
    scores JSON NOT NULL,
    categories JSON NOT NULL,
    holland_code VARCHAR(10) NOT NULL,
    holland_description TEXT,
    recommendations JSON NULL,  -- ← Field ini akan NULL / tidak diisi
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (session_id) REFERENCES test_sessions(id)
);
```
