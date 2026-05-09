# Strategi Integrasi Sistem PMB (Penerimaan Mahasiswa Baru)

Dokumen ini mendeskripsikan arsitektur dan alur kerja untuk modul PMB (khususnya halaman _Journey_, _Scholarship_, dan _Simulation_). Tujuan utama dari modul ini adalah mengonversi pengguna (siswa) yang telah melakukan tes psikologi agar mendaftar ke **Universitas Univeral**, namun tetap menjaga integritas rekomendasi jika potensi siswa tidak sesuai dengan program yang tersedia.

## 1. Arsitektur Data (Loose Coupling)

Berdasarkan kesepakatan bisnis, Universitas Univeral **sudah memiliki Aplikasi PMB terpisah**. Oleh karena itu, aplikasi Psiko-Test ini diposisikan sebagai _Top-of-Funnel_ (sistem pemicu minat/asesmen awal) yang nantinya akan melempar data ke Aplikasi PMB utama.

**Keputusan Desain Data:**

- ❌ **TIDAK** membuat tabel/model khusus untuk Data Master Jurusan, Fakultas, dan Beasiswa di aplikasi Psiko-Test.
- ✅ **MENGGUNAKAN** pendekatan statis (konfigurasi atau _hardcode_ teks di dalam sistem AI) untuk daftar jurusan Universitas Univeral di tahap awal.
- ✅ Di masa depan (skalabilitas jangka panjang), daftar jurusan dan beasiswa akan ditarik melalui _API Fetch/Webhooks_ dari Aplikasi PMB utama agar tidak terjadi duplikasi pemeliharaan data.

## 2. Alur AI Matching (Just-In-Time Generation)

Proses mencocokkan profil siswa dengan program studi di Universitas Univeral tidak dijalankan sembarangan untuk menghemat biaya API Google Gemini.

**Alur Kerja (Workflow):**

1. **Pemicu (Trigger):** Siswa mengunjungi halaman `/pmb/journey` untuk pertama kalinya.
2. **Pengecekan Hash:** `PmbController` memeriksa tabel `pmb_journeys`. Jika profil siswa baru saja diperbarui (dideteksi dari perbedaan `last_data_hash`), maka sistem memanggil `GeminiService::generatePmbMatch()`.
3. **Penyimpanan:** AI akan merespons dengan JSON (berisi daftar jurusan yang cocok dan rekomendasinya). Data ini disimpan ke tabel `pmb_journeys` agar pada kunjungan halaman berikutnya, data dirender secara instan (0.1 detik).

## 3. Strategi Rekomendasi (Fallback System)

Tujuan utama sistem adalah mengarahkan (_direct_) siswa ke Universitas Univeral. Namun, agar AI tetap relevan dan dipercaya oleh siswa, kita mengimplementasikan strategi berikut di dalam _Prompt_ AI:

- **Prioritas Utama:** Jika bakat dan nilai siswa relevan dengan fakultas yang ada di Universitas Univeral (misal: Teknologi, Bisnis, Desain, Komunikasi), maka seluruh top 3 rekomendasi difokuskan pada program studi internal.
- **Skenario Fallback (Tidak Relevan):** Jika siswa memiliki potensi kuat di ranah yang tidak dimiliki Universitas Univeral (contoh ekstrim: Ilmu Kedokteran atau Kedokteran Gigi), AI diperintahkan untuk dengan jujur merekomendasikan program studi eksternal (di kampus lain) yang paling tepat untuk masa depannya. **NAMUN**, AI tetap wajib menawarkan 1 opsi program studi terdekat/irisannya di Universitas Univeral sebagai alternatif (misal: Sistem Informasi Manajemen Rumah Sakit / Teknologi Kesehatan).

## 4. Sistem Kelayakan Beasiswa (Scholarship)

Untuk halaman `/pmb/scholarship`, penentuan apakah siswa berhak mendapatkan Beasiswa Akademik atau Beasiswa Jalur Prestasi akan ditentukan melalui **Rule-Based Algorithm (Logika Kondisional PHP Biasa)**, BUKAN melalui AI.

- **Alasan:** Peraturan beasiswa bersifat matematis, mutlak, dan kaku (contoh: Nilai Rapor > 85). Penggunaan `if/else` jauh lebih hemat biaya, sangat cepat, dan nol kemungkinan halusinasi AI.
- Rekomendasi Beasiswa kemudian akan disimpan ke `pmb_journeys` dan direpresentasikan di halaman Kalkulator Beasiswa.

## 5. Rencana Eksekusi (Langkah Selanjutnya)

1. Mengimplementasikan instruksi _Prompt_ canggih di `GeminiService::generatePmbMatch()`.
2. Menautkan `PmbJourneyModel` ke `PmbController` dan mengganti data _dummy_.
3. Membuat logika _Rule-Based_ untuk kalkulator Beasiswa berdasarkan data akademik.
