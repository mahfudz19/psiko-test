# Implementasi Browser Storage untuk RIASEC Test

## Tanggal

14 Mei 2026

## Deskripsi

Implementasi sessionStorage caching untuk mencegah kehilangan jawaban user saat mengerjakan tes RIASEC akibat refresh, tutup tab, atau navigasi tidak sengaja.

## Fitur yang Diimplementasikan

### 1. SessionStorage Caching ✅

**Storage Key Format:**

```
riasec_answers_{sessionId}
```

**Data Structure:**

```json
{
  "sessionId": 123,
  "answers": {
    "1": 3,
    "2": 4,
    "3": 2
  },
  "currentStep": 2,
  "timestamp": 1715670000
}
```

**Implementasi di:** [`take.js`](<addon/Views/(app)/tests/riasec/take.js>)

### 2. TTL (Time-To-Live) Mechanism ✅

**Durasi:** 2 jam (7200 detik)

**Fungsi:**

- `getCachedData()` - Cek dan validasi TTL
- `cleanupExpiredCache()` - Hapus data expired saat page load

**Rationale:**

- Mencegah storage "sampah" dari sesi yang sudah tidak relevan
- Jika user kembali setelah >2 jam, lebih baik mulai fresh

### 3. Auto-Restore on Page Load ✅

**Fungsi:** `restoreAnswers()`

**Behavior:**

- Restore semua jawaban yang sudah dipilih
- Restore posisi stepper (currentStep)
- Update UI (progress bars, stepper, counters)
- Scroll otomatis ke step yang direstore

### 4. Auto-Save on Answer ✅

**Trigger:** Setiap kali user memilih jawaban

**Implementasi:**

```javascript
function handleOptionClick(event, optionEl) {
  // ... existing logic ...

  // Save to cache
  saveCurrentState();
}
```

### 5. Auto-Save on Navigation ✅

**Trigger:** Setiap kali user navigasi antar step

**Implementasi:**

```javascript
function goToStep(stepIndex) {
  // ... existing logic ...

  // Save current step to cache
  saveCurrentState();
}
```

### 6. Beforeunload Warning ✅

**Kondisi:** User sudah menjawab beberapa pertanyaan tapi belum semua

**Implementasi:** `initBeforeUnloadWarning()`

**Behavior:**

- Browser menampilkan konfirmasi native
- Mencegah user keluar tidak sengaja
- Tidak muncul jika belum ada jawaban atau sudah semua terjawab

### 7. Double-Submit Prevention ✅

**Implementasi:** `initModalHandlers()`

**Behavior:**

- Disable tombol "Ya, Kirim Jawaban" setelah diklik
- Ubah text tombol jadi "Mengirim..."
- Mencegah duplicate submission

### 8. Cleanup on Submit Success ✅

**Implementasi:** [`results.php`](<addon/Views/(app)/tests/riasec/results.php>)

**Behavior:**

- Clear cache untuk sesi ini setelah submit berhasil
- Cleanup expired entries dari sesi lain
- Dijalankan otomatis saat halaman results dimuat

## Files Modified

### 1. `addon/Views/(app)/tests/riasec/take.js`

**Functions Added:**

- `getCachedData()` - Get cached data dengan TTL validation
- `saveToCache(data)` - Save data ke sessionStorage
- `clearCache()` - Clear cache untuk sesi ini
- `restoreAnswers()` - Restore jawaban dan UI state
- `cleanupExpiredCache()` - Cleanup expired entries
- `saveCurrentState()` - Save current answers + step
- `initBeforeUnloadWarning()` - Setup beforeunload handler
- `initFormSubmitHandler()` - Setup form submit handler

**Functions Modified:**

- `handleOptionClick()` - Added auto-save
- `goToStep()` - Added auto-save on navigation
- `initModalHandlers()` - Added double-submit prevention
- `initRiasecTake()` - Added initialization calls

### 2. `addon/Views/(app)/tests/riasec/results.php`

**Added:**

- Inline script untuk clear cache setelah submit
- TTL-based cleanup untuk expired entries

## Testing Checklist

### Functional Testing

- [ ] **Page Load dengan Cache Kosong**
  - Mulai tes baru
  - Jawab beberapa pertanyaan
  - Refresh halaman
  - ✅ Jawaban ter-restore
  - ✅ Posisi stepper ter-restore
  - ✅ Progress bar ter-update

- [ ] **Page Load dengan Cache Expired**
  - Mulai tes baru
  - Jawab beberapa pertanyaan
  - Tunggu >2 jam (atau mock timestamp)
  - Refresh halaman
  - ✅ Cache diabaikan, mulai dari awal

- [ ] **Navigation antar Step**
  - Jawab pertanyaan di kategori R
  - Klik "Lanjut" ke kategori I
  - Klik "Sebelumnya" kembali ke R
  - ✅ Jawaban di R masih ada

- [ ] **Beforeunload Warning**
  - Jawab beberapa pertanyaan (belum semua)
  - Coba tutup tab/browser
  - ✅ Browser tampilkan konfirmasi
  - ✅ Jika cancel, tetap di halaman
  - ✅ Jika confirm, tab tertutup

- [ ] **Double-Submit Prevention**
  - Jawab semua pertanyaan
  - Klik "Kirim Jawaban"
  - Klik "Ya, Kirim Jawaban" di modal
  - ✅ Tombol disabled
  - ✅ Text berubah jadi "Mengirim..."
  - ✅ Tidak ada double submission

- [ ] **Cache Cleanup on Success**
  - Submit tes dengan sukses
  - Redirect ke results page
  - Cek sessionStorage
  - ✅ Cache untuk sesi ini terhapus
  - ✅ Expired entries lain terhapus

### Edge Cases

- [ ] **User buka 2 tab dengan sesi yang sama**
  - Tab terakhir yang update akan overwrite
  - ✅ Accept this limitation

- [ ] **Submit gagal (network error)**
  - Cache tidak terhapus
  - User bisa retry
  - ✅ Data masih ada

- [ ] **Session expired di server**
  - Detect saat submit
  - Redirect ke login
  - ✅ Cache terhapus saat redirect

## Privacy & Security Considerations

### Data yang Disimpan

- ✅ Hanya `statement_id` → `answer_value` mapping
- ✅ Session ID untuk validasi
- ✅ Timestamp untuk TTL
- ✅ Current step untuk UX

### Data yang TIDAK Disimpan

- ❌ Full statement text (sudah ada di DOM)
- ❌ User metadata (sudah ada di session server)
- ❌ UI state yang bisa di-recompute

### Storage Location

- **sessionStorage** (bukan localStorage)
- Auto-cleared saat tab ditutup
- Tidak persist across sessions
- Tidak accessible dari domain lain (same-origin policy)

## Benefits

1. **Mencegah Frustrasi User** - Tidak kehilangan jawaban karena refresh/tutup tab
2. **Better UX** - User bisa lanjut dari posisi terakhir
3. **Minimal Overhead** - Hanya simpan data esensial
4. **Auto-Cleanup** - TTL dan cleanup on success
5. **Privacy-Friendly** - sessionStorage, bukan localStorage

## Known Limitations

1. **Multi-Tab Conflict** - Jika user buka 2 tab dengan sesi yang sama, tab terakhir yang update akan overwrite
2. **No Server-Side Backup** - Jika browser crash sebelum submit, data hilang
3. **TTL Trade-off** - Data expired setelah 2 jam, user harus mulai ulang

## Future Improvements (Optional)

1. **Server-Side Auto-Save** - Periodic save ke server via AJAX
2. **Longer TTL** - Sesuaikan dengan durasi tes rata-rata
3. **Storage Compression** - Jika jumlah pertanyaan sangat besar
4. **Offline Support** - Service Worker untuk full offline capability

## Related Files

- [`take.js`](<addon/Views/(app)/tests/riasec/take.js>) - Main implementation
- [`results.php`](<addon/Views/(app)/tests/riasec/results.php>) - Cache cleanup on success
- [`take.php`](<addon/Views/(app)/tests/riasec/take.php>) - View file (no changes)
- [`take.css`](<addon/Views/(app)/tests/riasec/take.css>) - Styles (no changes)

## Conclusion

Implementasi browser storage caching untuk RIASEC test sudah lengkap dengan:

- ✅ Auto-save on answer
- ✅ Auto-restore on page load
- ✅ TTL mechanism (2 jam)
- ✅ Cleanup on submit success
- ✅ Beforeunload warning
- ✅ Double-submit prevention

Fitur ini significantly meningkatkan UX dengan mencegah kehilangan jawaban user akibat human error atau technical issues.
