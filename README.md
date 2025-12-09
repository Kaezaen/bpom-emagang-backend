# 🎓 BPOM E-Magang — REST API Backend

Backend resmi untuk sistem E-Magang BPOM, digunakan oleh Admin, Pembimbing, dan Mahasiswa dalam mengelola seluruh proses magang mulai dari pendaftaran, bimbingan, logbook, laporan, proyek, penilaian sampai kalender event.

## 🛠️ Tech Stack

- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **Sanctum** - Authentication
- **Clean JSON API Structure**

---

## 🚀 Fitur Utama

### 1. Formulir & Penerimaan Mahasiswa
- Mahasiswa daftar magang melalui formulir
- Admin dapat melihat, menerima, atau menolak formulir

### 2. Manajemen Pembimbing & Divisi
- Admin mengatur divisi
- Admin menambah & mengelola akun pembimbing

### 3. Data Mahasiswa
- Profil mahasiswa lengkap
- Status aktif / selesai mengikuti magang

### 4. Logbook Harian (Absensi)
**Mahasiswa:**
- Upload logbook harian (PDF/Word)
- Edit & hapus hanya di hari yang sama

**Pembimbing:**
- Verifikasi logbook mahasiswa bimbingan
- Logbook terverifikasi menghasilkan absensi hadir

### 5. Laporan Akhir
- Mahasiswa upload 1x laporan akhir
- Pembimbing dapat verifikasi atau tolak dengan catatan

### 6. Progress Magang
- Progress otomatis berdasarkan tanggal mulai & selesai

### 7. Project Management
**Pembimbing:**
- Membuat project (deadline wajib)
- Menambah / menghapus / mengganti anggota project
- Mengakhiri project lebih cepat
- Melihat progress mahasiswa

**Mahasiswa:**
- Melihat daftar project aktif
- Upload progress
- Upload final submission (zip/pdf/dll)

### 8. Review Submission Project
**Pembimbing:**
- Approve submission
- Minta revisi (with notes)

### 9. Penilaian Mahasiswa
**Pembimbing dapat mengisi:**
- Penilaian BPOM (kehadiran, sikap, pemahaman, praktik, komunikasi, laporan, presentasi)
- Penilaian Kampus (nilai akhir)

**Mahasiswa dapat:**
- Melihat nilai
- Mengajukan protes nilai
- Pembimbing memberikan tanggapan & resolve

### 10. Kalender Event (Admin)
- Admin membuat event untuk mahasiswa
- Event otomatis berubah status menjadi expired ketika lewat
- Menghilang otomatis setelah 7 hari

---

## 📦 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/Kaezaen/bpom-emagang.git
cd bpom-emagang
```

### 2. Install Dependency
```bash
composer install
```

### 3. Copy .env
```bash
cp .env.example .env
```

Edit file `.env`:
```env
APP_NAME=BPOM_EMAGANG
APP_KEY=base64:xxxxxxxx
DB_DATABASE=bpom_emagang
DB_USERNAME=root
DB_PASSWORD=
FILESYSTEM_DISK=public
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. Jalankan Migration & Seeder
```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat:
- Role Admin, Pembimbing, Mahasiswa
- Akun admin default
- Divisi contoh

### 6. Storage Link
```bash
php artisan storage:link
```

### 7. Jalankan Server
```bash
php artisan serve
```

API akan berjalan di:
```
http://localhost:8000/api
```

---

## 🔐 Autentikasi

Semua endpoint (kecuali login, formulir, event public) menggunakan:

```
Authorization: Bearer {token}
```

---

## 📘 Dokumentasi Endpoint

Tersedia lengkap melalui Postman Collection:

👉 **docs/postman_collection.json**

### Endpoint Utama

| Role | Endpoint | Keterangan |
|------|----------|------------|
| Public | `/formulir` | Daftar magang |
| Admin | `/pembimbing/*` | CRUD pembimbing |
| Mahasiswa | `/mahasiswa/logbook` | Upload logbook |
| Pembimbing | `/pembimbing/logbook` | Verifikasi |
| Mahasiswa | `/mahasiswa/laporan-akhir` | Upload laporan |
| Pembimbing | `/pembimbing/laporan-akhir` | Review |
| Proyek | `/pembimbing/projects` | Manage project |
| Penilaian | `/pembimbing/nilai/*` | Input nilai |
| Protes nilai | `/mahasiswa/nilai/protes` | Ajukan protes |
| Event | `/event` | Public list event |

---

## 🗂 Struktur Folder Penting

```
app/
 ├─ Http/
 │   ├─ Controllers/
 │   │     ├─ AuthController.php
 │   │     ├─ PembimbingController.php
 │   │     ├─ LogbookHarianController.php
 │   │     ├─ LaporanAkhirController.php
 │   │     ├─ ProjectController.php
 │   │     ├─ ProjectProgressController.php
 │   │     ├─ ProjectSubmissionController.php
 │   │     ├─ PenilaianController.php
 │   │     └─ EventController.php
 │   └─ Middleware/
 ├─ Models/
 │   ├─ MahasiswaData.php
 │   ├─ PembimbingData.php
 │   ├─ LogbookHarian.php
 │   ├─ LaporanAkhir.php
 │   ├─ Project.php
 │   ├─ ProjectMember.php
 │   ├─ ProjectSubmission.php
 │   ├─ PenilaianBPOM.php
 │   ├─ PenilaianKampus.php
 │   └─ ProtesNilai.php
```

---

## 👥 User Roles

### Admin
- CRUD Divisi
- CRUD Pembimbing
- Melihat seluruh bimbingan
- Mengelola event

### Pembimbing
- Mengelola mahasiswa bimbingan
- Verifikasi logbook & laporan akhir
- Mengelola project & progres mahasiswa
- Input nilai & tanggapi protes

### Mahasiswa
- Upload logbook
- Upload laporan akhir
- Kerjakan project + upload progress
- Upload final submission
- Lihat nilai + ajukan protes

---

## 🛠 Development Notes

- Semua response menggunakan format JSON clean & konsisten
- Validasi ketat pada setiap endpoint
- Semua file di-upload ke folder `/storage/app/public/...`
- Sistem absensi otomatis berdasarkan logbook yang diverifikasi
- Event otomatis expire dan auto-hide setelah 7 hari

---

## 📄 License

Project private untuk pengembangan internal BPOM.  
Tidak digunakan untuk komersial tanpa izin.

---

## 📞 Contact

Untuk pertanyaan atau support, silakan hubungi tim pengembang BPOM E-Magang.

---

**Made with ❤️ for BPOM**
