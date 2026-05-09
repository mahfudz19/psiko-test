# 📱 Psyco-Test - Sistem Tes Psikologi Sekolah

## Overview

**Psyco-Test** adalah platform tes psikologi dan konseling sekolah berbasis web yang dirancang untuk membantu siswa SMA/SMK dalam memahami potensi, minat, dan bakat mereka melalui tes psikologi online dan konsultasi AI.

## 🎯 Tujuan Aplikasi

1. **Tes Psikologi Online** - Menyediakan berbagai tes psikologi digital:
   - Tes IQ (Intelligence Quotient)
   - Tes Kecerdasan Majemuk (Multiple Intelligences - Howard Gardner)
   - Tes Gaya Belajar (Visual, Auditori, Kinestetik)
   - Tes Kepribadian

2. **Analisis Potensi Siswa** - Memberikan insight komprehensif tentang:
   - Kekuatan utama siswa
   - Potensi karir yang sesuai
   - Rekomendasi jurusan kuliah
   - Saran pengembangan diri

3. **Konsultasi AI (Chat Consultation)** - Fitur chat dengan AI konselor menggunakan Google Gemini API untuk:
   - Analisis potensi berdasarkan hasil tes
   - Bimbingan karir
   - Tips belajar
   - Pengembangan diri

4. **Manajemen Sekolah** - Dashboard administratif untuk mengelola:
   - Data sekolah
   - Data guru dan siswa
   - Jadwal konseling
   - Hasil tes siswa

## 👥 Target Market & Pengguna

### Primary Users

| Role              | Deskripsi                 | Fitur Utama                                      |
| ----------------- | ------------------------- | ------------------------------------------------ |
| **Siswa (User)**  | Siswa SMA/SMK kelas 10-12 | Tes psikologi, lihat hasil, konsultasi AI        |
| **Guru BK**       | Guru bimbingan konseling  | Kelola siswa, jadwal konseling, monitoring hasil |
| **Admin Sekolah** | Administrator sekolah     | Kelola data sekolah, guru, siswa                 |
| **Super Admin**   | Administrator pusat       | Kelola multiple sekolah                          |

### Segmentasi Pasar

- **Sekolah Menengah Atas (SMA/SMK)** - Negeri dan swasta
- **Dinas Pendidikan** - Monitoring psikologi siswa tingkat daerah
- **Lembaga Bimbingan Belajar** - Analisis potensi siswa

## 💡 Value Proposition

| Stakeholder   | Value                                                                    |
| ------------- | ------------------------------------------------------------------------ |
| **Siswa**     | Memahami potensi diri sejak dini untuk perencanaan karir yang lebih baik |
| **Guru BK**   | Tools modern untuk konseling berbasis data                               |
| **Sekolah**   | Sistem terpusat untuk tracking perkembangan siswa                        |
| **Orang Tua** | Insight tentang potensi dan bakat anak                                   |

## 🏗️ Arsitektur Teknis

### Framework

- **Mazu Framework** - Custom PHP framework dengan arsitektur MVC
- **MySQL** - Database relasional
- **Vanilla JS + SPA** - Frontend interaktif tanpa reload penuh

### Fitur Teknis Utama

- **View Auto-Discovery System** - CSS/JS auto-loading berdasarkan nama file view
- **Nested Layout System** - Next.js-style folder-based layouts
- **SPA Navigation** - Navigasi tanpa reload dengan `data-spa` attribute
- **Google Gemini AI** - Integrasi AI untuk konsultasi chat
- **Role-Based Access Control** - Middleware-based authorization

### Struktur Database

| Tabel                   | Deskripsi                          |
| ----------------------- | ---------------------------------- |
| `users`                 | User accounts (siswa, guru, admin) |
| `profiles`              | Data profil user                   |
| `student_profiles`      | Profil siswa dengan hasil tes      |
| `teacher_profiles`      | Profil guru BK                     |
| `schools`               | Data sekolah                       |
| `chat_consultations`    | Sesi konsultasi chat dengan AI     |
| `chat_messages`         | Pesan dalam sesi chat              |
| `email_verifications`   | Token verifikasi email             |
| `password_reset_tokens` | Token reset password               |

## 📁 Struktur Folder

```
project-root/
├── app/                    # Core Engine (JANGAN MODIFIKASI)
│   ├── Console/           # CLI Commands
│   └── Core/              # Framework Core
├── addon/                 # Application Code
│   ├── Controllers/       # Request handlers
│   ├── Middleware/        # Auth, Role, CSRF middleware
│   ├── Models/            # Database models
│   ├── Services/          # Business logic (GeminiService, dll)
│   ├── Router/index.php   # Route definitions
│   └── Views/             # View templates (nested layout)
├── plans/                 # Project documentation
└── config/                # Configuration files
```

## 🚀 Fitur yang Sudah Diimplementasi

### ✅ Completed Features

1. **Authentication & Authorization**
   - Login/Register dengan email verification
   - Role-based access (siswa, guru, admin, super admin)
   - Password reset dengan OTP

2. **Student Profile Management**
   - Input dan edit profil siswa
   - Upload dan manajemen hasil tes psikologi
   - Dashboard hasil tes

3. **Chat Consultation AI**
   - Integrasi Google Gemini API (`gemini-2.5-flash-preview-05-20`)
   - Riwayat sesi chat
   - Real-time chat interface
   - Context-aware AI responses

4. **School Management**
   - CRUD sekolah untuk super admin
   - CRUD guru dan siswa
   - Assignment guru BK ke sekolah

5. **UI/UX**
   - Responsive design
   - SPA navigation
   - Auto-discovered CSS/JS
   - Nested layouts

## 📋 Roadmap Fitur (TODO)

### 🔄 In Progress

- [ ] Perbaikan final chat consultation (payload issue)

### ⏳ Planned Features

- [ ] Implementasi lengkap tes psikologi online
- [ ] Dashboard analisis untuk guru BK
- [ ] Export hasil tes ke PDF
- [ ] Notifikasi email untuk jadwal konseling
- [ ] Mobile app (future consideration)

## 🔧 Development Guidelines

### CLI Commands

```bash
php mazu make:controller <name>    # Buat controller baru
php mazu make:model <name>         # Buat model baru
php mazu make:middleware <name>    # Buat middleware baru
php mazu migrate                   # Jalankan migration
php mazu serve                     # Dev server
```

### Code Standards

- Gunakan **Bahasa Indonesia** untuk komentar dan komunikasi
- Constructor property promotion untuk dependency injection
- Type hints untuk return values: `View | RedirectResponse | JsonResponse`
- Error handling dengan try-catch di setiap controller method
- Dokumentasi singkat untuk fungsi/method baru

### Database Schema

- Format timestamp yang benar: `['type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP']`
- Foreign key dengan `on_delete => 'cascade'` untuk related tables

## 📝 Related Documentation

- [`chat-consultation-architecture.md`](chat-consultation-architecture.md) - Arsitektur fitur chat
- [`school-admin-architecture.md`](school-admin-architecture.md) - Arsitektur admin sekolah
- [`super-admin-architecture.md`](super-admin-architecture.md) - Arsitektur super admin
- [`pmb-journey-architecture.md`](pmb-journey-architecture.md) - Arsitektur PMB journey

## 🎯 AI Context Reminder

Ketika bekerja dengan project ini, ingat:

1. **Ini adalah aplikasi sekolah** - Fokus pada kebutuhan siswa, guru BK, dan admin
2. **Target user adalah orang Indonesia** - Gunakan Bahasa Indonesia di UI
3. **Mazu Framework** - Ikuti standar framework (CLI, nested layout, auto-discovery)
4. **SPA-first approach** - Gunakan `data-spa` untuk navigasi
5. **AI Integration** - Gemini API untuk konsultasi chat
