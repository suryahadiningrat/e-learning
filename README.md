# E-Learning SMK

Sistem E-Learning untuk Sekolah Menengah Kejuruan (SMK) yang dibangun menggunakan CodeIgniter 4.

## Fitur Utama

### 🔐 Sistem Autentikasi & Autorisasi
- Login dan Register dengan 3 role: Admin, Guru, dan Siswa
- Sistem aktivasi user oleh Admin
- Filter autentikasi dan autorisasi berdasarkan role

### 👨‍💼 Panel Admin
- Dashboard dengan statistik user
- Manajemen aktivasi user
- Data Master:
  - Data Siswa
  - Data Guru
  - Data Jurusan/Kelas
  - Data Jadwal
  - Data Absensi
  - Data Nilai
  - Data Ulangan (membuat soal online)
  - Data Materi/Modul
  - Data Link Pengumpulan Tugas
  - Data Tahun Akademik

### 👨‍🏫 Panel Guru
- Dashboard dengan statistik mengajar
- Data Master:
  - Data Siswa
  - Data Jurusan/Kelas
  - Data Absensi
  - Data Nilai
  - Data Ulangan (membuat soal online)
  - Data Materi/Modul
  - Data Link Pengumpulan Tugas

### 👨‍🎓 Panel Siswa
- Dashboard dengan statistik pembelajaran
- Data:
  - Jadwal
  - Nilai
  - Ulangan
  - Materi
  - Link Tugas

## Teknologi yang Digunakan

- **Framework**: CodeIgniter 4
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Font Awesome
- **PHP**: 8.0+

## Instalasi

### Prerequisites
- PHP 8.0 atau lebih tinggi
- Composer
- Web server (Apache/Nginx) atau PHP built-in server

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd e-learning
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp env .env
   ```

4. **Konfigurasi database**
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   CI_ENVIRONMENT = development
   database.default.hostname = localhost
   database.default.database = e_learning_smk
   database.default.username = root
   database.default.password = eleonora
   database.default.DBDriver = MySQLi
   ```

5. **Jalankan migration**
   ```bash
   php spark migrate
   ```

6. **Jalankan seeder untuk membuat admin default**
   ```bash
   php spark db:seed AdminSeeder
   ```

7. **Jalankan server development**
   ```bash
   php spark serve
   ```

8. **Akses aplikasi**
   Buka browser dan akses: `http://localhost:8080`

## Akun Default

### Admin
- **Username**: admin
- **Password**: admin123
- **Email**: admin@smk.edu

## Struktur Database

### Tabel Users
- `id` - Primary Key
- `username` - Username untuk login
- `email` - Email user
- `password` - Password (terenkripsi)
- `full_name` - Nama lengkap
- `role` - Role user (admin/guru/siswa)
- `is_active` - Status aktivasi (0/1)
- `created_at` - Tanggal dibuat
- `updated_at` - Tanggal diupdate

### Tabel Jurusan
- `id` - Primary Key
- `nama_jurusan` - Nama jurusan
- `kode_jurusan` - Kode jurusan
- `deskripsi` - Deskripsi jurusan

### Tabel Kelas
- `id` - Primary Key
- `nama_kelas` - Nama kelas
- `jurusan_id` - Foreign Key ke tabel jurusan
- `tingkat` - Tingkat kelas (1=X, 2=XI, 3=XII)
- `kapasitas` - Kapasitas kelas

### Tabel Siswa
- `id` - Primary Key
- `user_id` - Foreign Key ke tabel users
- `nis` - Nomor Induk Siswa
- `kelas_id` - Foreign Key ke tabel kelas
- `jenis_kelamin` - Jenis kelamin (L/P)
- `tempat_lahir` - Tempat lahir
- `tanggal_lahir` - Tanggal lahir
- `alamat` - Alamat
- `no_telp` - Nomor telepon

### Tabel Guru
- `id` - Primary Key
- `user_id` - Foreign Key ke tabel users
- `nip` - Nomor Induk Pegawai
- `bidang_studi` - Bidang studi yang diajar
- `jenis_kelamin` - Jenis kelamin (L/P)
- `tempat_lahir` - Tempat lahir
- `tanggal_lahir` - Tanggal lahir
- `alamat` - Alamat
- `no_telp` - Nomor telepon

### Tabel Tahun Akademik
- `id` - Primary Key
- `tahun_akademik` - Tahun akademik
- `semester` - Semester (Ganjil/Genap)
- `tanggal_mulai` - Tanggal mulai
- `tanggal_selesai` - Tanggal selesai
- `is_active` - Status aktif (0/1)

## Struktur Folder

```
e-learning/
├── app/
│   ├── Config/           # Konfigurasi aplikasi
│   ├── Controllers/      # Controller
│   │   ├── Admin/        # Controller untuk Admin
│   │   ├── Guru/         # Controller untuk Guru
│   │   └── Siswa/        # Controller untuk Siswa
│   ├── Database/
│   │   ├── Migrations/   # File migration
│   │   └── Seeds/        # File seeder
│   ├── Filters/          # Filter autentikasi & autorisasi
│   ├── Models/           # Model database
│   └── Views/            # View/template
│       ├── admin/        # View untuk Admin
│       ├── auth/         # View untuk autentikasi
│       ├── guru/         # View untuk Guru
│       └── siswa/        # View untuk Siswa
├── public/               # File publik (CSS, JS, gambar)
├── system/               # Core CodeIgniter
├── writable/             # File yang dapat ditulis (log, cache, database)
└── .env                  # Konfigurasi environment
```

## Pengembangan

### Menambah Migration Baru
```bash
php spark make:migration CreateNamaTabel
```

### Menambah Seeder Baru
```bash
php spark make:seeder NamaSeeder
```

### Menambah Controller Baru
```bash
php spark make:controller NamaController
```

### Menambah Model Baru
```bash
php spark make:model NamaModel
```

## Kontribusi

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/nama-fitur`)
3. Commit perubahan (`git commit -am 'Menambah fitur baru'`)
4. Push ke branch (`git push origin feature/nama-fitur`)
5. Buat Pull Request

## Lisensi

Project ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## Support

Untuk pertanyaan atau dukungan, silakan buat issue di repository ini.
