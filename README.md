# 📚 Mini LMS (Learning Management System)

Sebuah sistem pembelajaran daring sederhana berbasis web yang memungkinkan guru dan siswa untuk berinteraksi melalui kelas, materi, tugas, dan penilaian. Proyek ini dibangun menggunakan PHP tanpa framework serta database MySQL.

## 🔧 Fitur Utama

### 👨‍🏫 Guru
- Dashboard dengan statistik kelas
- Membuat kelas dan materi pembelajaran
- Memberi tugas dan pengumuman
- Menilai tugas yang dikumpulkan siswa

### 👨‍🎓 Siswa
- Dashboard pribadi
- Bergabung ke kelas dengan kode
- Melihat materi dan tugas
- Mengumpulkan tugas
- Melihat nilai

### 👤 Pengguna Umum
- Registrasi & login
- Edit profil dan foto profil
- Ubah password
- Logout

---

## 🗂️ Struktur Folder

```plaintext
/ (root folder LMS)
│
│   index.php               -> Halaman utama
│   login.php               -> Halaman login
│   logout.php              -> Logout user
│   register.php            -> Halaman registrasi
│   profile.php             -> Profil pengguna
│   update_password.php     -> Ganti password
│   lms.sql                 -> File backup database
│
├───assets/                 -> Aset statis (CSS, JS, gambar)
│   ├───css/
│   │   ├── login.css
│   │   ├── students.css
│   │   └── style.css
│   └───js/
│       └── (jika ada JavaScript tambahan)
│
├───includes/               -> File PHP umum yang digunakan di banyak halaman
│   ├── auth.php            -> Cek autentikasi login
│   ├── db.php              -> Koneksi ke database
│   ├── functions.php       -> Fungsi bantu (helper)
│   ├── header.php          -> Template header
│   ├── footer.php          -> Template footer
│   ├── sidebar_guru.php    -> Sidebar khusus guru
│   ├── sidebar_siswa.php   -> Sidebar khusus siswa
│
├───student/                -> Halaman khusus siswa
│   ├── dashboard.php       -> Dashboard siswa
│   ├── join_class.php      -> Form bergabung ke kelas
│   ├── view_material.php   -> Lihat materi pembelajaran
│   ├── assignment.php      -> Lihat & kumpul tugas
│   ├── submit_assignment.php -> Form upload tugas
│   ├── grades.php          -> Lihat nilai
│
├───teacher/                -> Halaman khusus guru
│   ├── dashboard.php       -> Dashboard guru (statistik, info tugas, dll.)
│   ├── create_class.php    -> Buat kelas baru
│   ├── add_material.php    -> Upload materi ke kelas
│   ├── create_assignment.php -> Buat tugas baru
│   ├── grade_submissions.php -> Koreksi dan beri nilai
│   ├── add_announcement.php -> Tambah pengumuman
│
└───uploads/                -> Folder file upload (materi, tugas siswa, dll.)
    ├───profile_photos/     -> Foto profil pengguna
    ├───materials/          -> Materi yang diunggah guru
    └───submissions/        -> Tugas yang dikumpulkan siswa
```
## 🧪 Teknologi yang Digunakan

- PHP Native (tanpa framework)
- MySQL
- HTML, CSS (custom & sedikit JS)
- Struktur file modular (berbasis folder per-peran: siswa/guru)
- Upload file materi dan tugas

---

## 💾 Instalasi Lokal

1. **Clone atau download** repository ini ke folder `htdocs`:
   ```bash
   git clone https://github.com/username/lms-php.git
Import database:

Buka phpMyAdmin

Buat database baru (contoh: lms)

Import file lms.sql

Konfigurasi koneksi database:

Buka includes/db.php

Ubah sesuai konfigurasi MySQL-mu:

php
Copy
Edit
$conn = new mysqli("localhost", "root", "", "lms");
Jalankan server melalui XAMPP

Buka http://localhost/lms-php di browser

👨‍💻 Tentang Pengembang
Website ini dikembangkan oleh:

Nabil
Mahasiswa Sistem Informasi
Universitas Muhammadiyah Sumatera Utara
Alumni SMK Negeri 1 Air Putih – Rekayasa Perangkat Lunak

📜 Lisensi
Proyek ini open-source. Silakan gunakan dan modifikasi sesuai kebutuhan.
Lisensi: MIT

