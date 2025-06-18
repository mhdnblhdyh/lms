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

/ (root)
│ dashboard.php
│ index.php
│ login.php
│ logout.php
│ register.php
│ profile.php
│ update_password.php
│ lms.sql
│
├───assets/
│ ├───css/
│ │ ├── login.css
│ │ ├── students.css
│ │ └── style.css
│ └───js/
│
├───includes/
│ ├── auth.php
│ ├── db.php
│ ├── functions.php
│ ├── header.php
│ ├── footer.php
│ ├── sidebar_guru.php
│ └── sidebar_siswa.php
│
├───student/
│ ├── assignment.php
│ ├── dashboard.php
│ ├── grades.php
│ ├── join_class.php
│ ├── submit_assignment.php
│ └── view_material.php
│
├───teacher/
│ ├── add_announcement.php
│ ├── add_material.php
│ ├── create_assignment.php
│ ├── create_class.php
│ ├── dashboard.php
│ └── grade_submissions.php
│
└───uploads/
├───(berbagai file materi, gambar, dan dokumen)
└───submissions/
└───(file tugas yang dikumpulkan siswa)

yaml
Copy
Edit

---

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

