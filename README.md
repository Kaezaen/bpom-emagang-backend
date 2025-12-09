# BPOM e-Magang Backend API  
Sistem backend untuk platform e-Magang BPOM, dibangun menggunakan Laravel 10 dengan arsitektur REST API.  
Fitur ini mencakup manajemen magang, logbook harian, laporan akhir, project mahasiswa, penilaian pembimbing & kampus, serta event/kegiatan BPOM.

---

## 🚀 Tech Stack
- **Laravel 10**
- **MySQL 8**
- **Laravel Sanctum (Authentication)**
- **MVC Architecture**
- **File Storage (Public Disk)**

---

## 📂 Struktur Fitur Utama

### **1. Authentication**
- Login (role-based: admin, pembimbing, mahasiswa)
- Logout
- Get current user (`/me`)

---

### **2. Formulir Pendaftaran Magang**
- Public pendaftaran
- Admin verifikasi / tolak formulir
- Tracking status oleh peserta

---

### **3. Manajemen Pembimbing**
- Admin CRUD pembimbing
- Lihat mahasiswa bimbingan
- Filter mahasiswa **aktif / selesai**

---

### **4. Mahasiswa**
- Profil
- Progress Magang (berdasarkan tanggal mulai-selesai)
- Absensi otomatis berdasarkan **logbook terverifikasi**
- Dashboard project & submission

---

### **5. Logbook Harian**
Mahasiswa:
- Create logbook harian (PDF/Word)
- Edit & delete hanya untuk **hari yang sama**
- Tidak dapat mengubah jika sudah diverifikasi

Pembimbing:
- Lihat semua logbook mahasiswa bimbingan
- Verifikasi logbook

---

### **6. Laporan Akhir**
Mahasiswa:
- Upload laporan akhir (PDF/Word)
- Replace or delete until verified

Pembimbing:
- Lihat semua laporan akhir bimbingan
- Verifikasi / reject + catatan

---

### **7. Project Mahasiswa**
Pembimbing:
- Membuat project
- Menambahkan/menghapus/mengganti anggota
- Update deadline
- End project (completed/cancelled)

Mahasiswa:
- Melihat project
- Upload progres pekerjaan
- Upload final submission (ZIP/PDF/Doc)

Pembimbing:
- Review submission → approve / minta revisi

---

### **8. Project Progress**
Mahasiswa:
- Upload progress harian / mingguan

Pembimbing:
- Review & beri komentar progress mahasiswa

---

### **9. Penilaian Magang**
Pembimbing dapat memberikan:
#### **🔹 Penilaian BPOM (0–100)**  
- Kehadiran  
- Taat jadwal  
- Pemahaman materi  
- Praktek kerja  
- Komunikasi  
- Laporan  
- Presentasi  

→ Sistem otomatis menghitung **nilai rata-rata (nilai akhir)**.

#### **🔹 Penilaian Kampus (0–100)**
- Input nilai final langsung

#### **Protes Nilai**
Mahasiswa:
- Mengajukan protes nilai (bpom/kampus)

Pembimbing:
- Melihat daftar protes nilai
- Memberi tanggapan
- Update nilai apabila diperlukan

---

### **10. Event / Kalender BPOM**
Admin:
- Create / update / delete event

Public:
- Melihat event aktif  
- Event yang sudah lewat 7 hari otomatis disembunyikan

---

## 📌 Instalasi

### 1️⃣ Clone Repository
```bash
git clone https://github.com/USERNAME/bpom-emagang-backend.git
cd bpom-emagang-backend
