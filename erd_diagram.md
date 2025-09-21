# ERD (Entity Relationship Diagram) - E-Learning SMK

## Entity Relationship Diagram

```mermaid
erDiagram
    %% Core Entities
    USERS {
        int id PK
        varchar username UK
        varchar email UK
        varchar password
        varchar full_name
        enum role
        tinyint is_active
        datetime created_at
        datetime updated_at
    }
    
    JURUSAN {
        int id PK
        varchar nama_jurusan
        varchar kode_jurusan UK
        text deskripsi
        datetime created_at
        datetime updated_at
    }
    
    KELAS {
        int id PK
        varchar nama_kelas
        int jurusan_id FK
        int tingkat
        int kapasitas
        datetime created_at
        datetime updated_at
    }
    
    SISWA {
        int id PK
        int user_id FK
        varchar nis UK
        int kelas_id FK
        enum jenis_kelamin
        varchar tempat_lahir
        date tanggal_lahir
        text alamat
        varchar no_telp
        datetime created_at
        datetime updated_at
    }
    
    GURU {
        int id PK
        int user_id FK
        varchar nip UK
        varchar bidang_studi
        enum jenis_kelamin
        varchar tempat_lahir
        date tanggal_lahir
        text alamat
        varchar no_telp
        datetime created_at
        datetime updated_at
    }
    
    TAHUN_AKADEMIK {
        int id PK
        varchar tahun_akademik
        varchar semester
        date tanggal_mulai
        date tanggal_selesai
        tinyint is_active
        datetime created_at
        datetime updated_at
    }
    
    MATA_PELAJARAN {
        int id PK
        varchar nama_mata_pelajaran
        varchar kode_mata_pelajaran UK
        text deskripsi
        tinyint is_active
        datetime created_at
        datetime updated_at
    }
    
    JADWAL {
        int id PK
        int guru_id FK
        int mata_pelajaran_id FK
        int kelas_id FK
        varchar hari
        time jam_mulai
        time jam_selesai
        varchar semester
        varchar tahun_ajaran
        datetime created_at
        datetime updated_at
    }
    
    ABSENSI {
        int id PK
        int jadwal_id FK
        int siswa_id FK
        date tanggal
        enum status_kehadiran
        text keterangan
        datetime created_at
        datetime updated_at
    }
    
    NILAI {
        int id PK
        int siswa_id FK
        int mata_pelajaran_id FK
        int guru_id FK
        int tahun_akademik_id FK
        decimal nilai_tugas
        decimal nilai_ulangan
        decimal nilai_uts
        decimal nilai_uas
        decimal nilai_akhir
        varchar grade
        text catatan
        datetime created_at
        datetime updated_at
    }
    
    ULANGAN {
        int id PK
        int guru_id FK
        int mata_pelajaran_id FK
        int kelas_id FK
        varchar judul_ulangan
        text deskripsi
        int durasi_menit
        datetime tanggal_mulai
        datetime tanggal_selesai
        enum status
        text soal_data
        datetime created_at
        datetime updated_at
    }
    
    JAWABAN_ULANGAN {
        int id PK
        int ulangan_id FK
        int siswa_id FK
        text jawaban_data
        decimal nilai
        datetime waktu_mulai
        datetime waktu_selesai
        enum status
        datetime created_at
        datetime updated_at
    }
    
    MATERI {
        int id PK
        int guru_id FK
        int mata_pelajaran_id FK
        int kelas_id FK
        varchar judul_materi
        text deskripsi
        varchar file_path
        varchar file_name
        int file_size
        enum status
        datetime created_at
        datetime updated_at
    }
    
    TUGAS {
        int id PK
        int guru_id FK
        int mata_pelajaran_id FK
        int kelas_id FK
        varchar judul_tugas
        text deskripsi
        varchar file_path
        varchar file_name
        datetime deadline
        int max_file_size
        enum status
        datetime created_at
        datetime updated_at
    }
    
    PENGUMPULAN_TUGAS {
        int id PK
        int tugas_id FK
        int siswa_id FK
        varchar file_path
        varchar file_name
        int file_size
        text komentar
        decimal nilai
        text feedback
        datetime submitted_at
        enum status
        datetime created_at
        datetime updated_at
    }
    
    SETTINGS {
        int id PK
        varchar setting_key UK
        text setting_value
        varchar setting_type
        text description
        datetime created_at
        datetime updated_at
    }
    
    %% Relationships
    USERS ||--o{ SISWA : "has"
    USERS ||--o{ GURU : "has"
    
    JURUSAN ||--o{ KELAS : "contains"
    KELAS ||--o{ SISWA : "enrolls"
    
    GURU ||--o{ JADWAL : "teaches"
    MATA_PELAJARAN ||--o{ JADWAL : "scheduled_in"
    KELAS ||--o{ JADWAL : "has_schedule"
    
    JADWAL ||--o{ ABSENSI : "has_attendance"
    SISWA ||--o{ ABSENSI : "attends"
    
    SISWA ||--o{ NILAI : "receives"
    MATA_PELAJARAN ||--o{ NILAI : "graded_in"
    GURU ||--o{ NILAI : "gives"
    TAHUN_AKADEMIK ||--o{ NILAI : "for_academic_year"
    
    GURU ||--o{ ULANGAN : "creates"
    MATA_PELAJARAN ||--o{ ULANGAN : "tested_in"
    KELAS ||--o{ ULANGAN : "takes"
    
    ULANGAN ||--o{ JAWABAN_ULANGAN : "answered_by"
    SISWA ||--o{ JAWABAN_ULANGAN : "submits"
    
    GURU ||--o{ MATERI : "creates"
    MATA_PELAJARAN ||--o{ MATERI : "covers"
    KELAS ||--o{ MATERI : "studies"
    
    GURU ||--o{ TUGAS : "assigns"
    MATA_PELAJARAN ||--o{ TUGAS : "assigned_in"
    KELAS ||--o{ TUGAS : "receives"
    
    TUGAS ||--o{ PENGUMPULAN_TUGAS : "submitted_for"
    SISWA ||--o{ PENGUMPULAN_TUGAS : "submits"
```

## Deskripsi ERD

### Core Entities (Entitas Utama)

#### 1. USERS
- **Primary Key**: id
- **Unique Keys**: username, email
- **Attributes**: password, full_name, role (admin/guru/siswa), is_active
- **Purpose**: Menyimpan data autentikasi dan informasi dasar user

#### 2. JURUSAN
- **Primary Key**: id
- **Unique Key**: kode_jurusan
- **Attributes**: nama_jurusan, deskripsi
- **Purpose**: Menyimpan data jurusan/program studi

#### 3. KELAS
- **Primary Key**: id
- **Foreign Key**: jurusan_id → JURUSAN.id
- **Attributes**: nama_kelas, tingkat, kapasitas
- **Purpose**: Menyimpan data kelas dalam setiap jurusan

#### 4. SISWA
- **Primary Key**: id
- **Foreign Keys**: user_id → USERS.id, kelas_id → KELAS.id
- **Unique Key**: nis
- **Attributes**: jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_telp
- **Purpose**: Menyimpan data detail siswa

#### 5. GURU
- **Primary Key**: id
- **Foreign Key**: user_id → USERS.id
- **Unique Key**: nip
- **Attributes**: bidang_studi, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_telp
- **Purpose**: Menyimpan data detail guru

### Academic Entities (Entitas Akademik)

#### 6. TAHUN_AKADEMIK
- **Primary Key**: id
- **Attributes**: tahun_akademik, semester, tanggal_mulai, tanggal_selesai, is_active
- **Purpose**: Menyimpan data tahun akademik dan semester

#### 7. MATA_PELAJARAN
- **Primary Key**: id
- **Unique Key**: kode_mata_pelajaran
- **Attributes**: nama_mata_pelajaran, deskripsi, is_active
- **Purpose**: Menyimpan data mata pelajaran

#### 8. JADWAL
- **Primary Key**: id
- **Foreign Keys**: guru_id → GURU.id, mata_pelajaran_id → MATA_PELAJARAN.id, kelas_id → KELAS.id
- **Attributes**: hari, jam_mulai, jam_selesai, semester, tahun_ajaran
- **Purpose**: Menyimpan jadwal pembelajaran

### Assessment Entities (Entitas Penilaian)

#### 9. ABSENSI
- **Primary Key**: id
- **Foreign Keys**: jadwal_id → JADWAL.id, siswa_id → SISWA.id
- **Attributes**: tanggal, status_kehadiran, keterangan
- **Purpose**: Menyimpan data kehadiran siswa

#### 10. NILAI
- **Primary Key**: id
- **Foreign Keys**: siswa_id → SISWA.id, mata_pelajaran_id → MATA_PELAJARAN.id, guru_id → GURU.id, tahun_akademik_id → TAHUN_AKADEMIK.id
- **Attributes**: nilai_tugas, nilai_ulangan, nilai_uts, nilai_uas, nilai_akhir, grade, catatan
- **Purpose**: Menyimpan data nilai siswa

#### 11. ULANGAN
- **Primary Key**: id
- **Foreign Keys**: guru_id → GURU.id, mata_pelajaran_id → MATA_PELAJARAN.id, kelas_id → KELAS.id
- **Attributes**: judul_ulangan, deskripsi, durasi_menit, tanggal_mulai, tanggal_selesai, status, soal_data
- **Purpose**: Menyimpan data ulangan online

#### 12. JAWABAN_ULANGAN
- **Primary Key**: id
- **Foreign Keys**: ulangan_id → ULANGAN.id, siswa_id → SISWA.id
- **Attributes**: jawaban_data, nilai, waktu_mulai, waktu_selesai, status
- **Purpose**: Menyimpan jawaban dan hasil ulangan siswa

### Content Entities (Entitas Konten)

#### 13. MATERI
- **Primary Key**: id
- **Foreign Keys**: guru_id → GURU.id, mata_pelajaran_id → MATA_PELAJARAN.id, kelas_id → KELAS.id
- **Attributes**: judul_materi, deskripsi, file_path, file_name, file_size, status
- **Purpose**: Menyimpan data materi pembelajaran

#### 14. TUGAS
- **Primary Key**: id
- **Foreign Keys**: guru_id → GURU.id, mata_pelajaran_id → MATA_PELAJARAN.id, kelas_id → KELAS.id
- **Attributes**: judul_tugas, deskripsi, file_path, file_name, deadline, max_file_size, status
- **Purpose**: Menyimpan data tugas yang diberikan guru

#### 15. PENGUMPULAN_TUGAS
- **Primary Key**: id
- **Foreign Keys**: tugas_id → TUGAS.id, siswa_id → SISWA.id
- **Attributes**: file_path, file_name, file_size, komentar, nilai, feedback, submitted_at, status
- **Purpose**: Menyimpan data pengumpulan tugas siswa

### System Entities (Entitas Sistem)

#### 16. SETTINGS
- **Primary Key**: id
- **Unique Key**: setting_key
- **Attributes**: setting_value, setting_type, description
- **Purpose**: Menyimpan konfigurasi sistem

### Key Relationships (Relasi Utama)

1. **USERS → SISWA/GURU**: One-to-Many (satu user bisa menjadi siswa atau guru)
2. **JURUSAN → KELAS**: One-to-Many (satu jurusan memiliki banyak kelas)
3. **KELAS → SISWA**: One-to-Many (satu kelas memiliki banyak siswa)
4. **GURU → JADWAL**: One-to-Many (satu guru mengajar di banyak jadwal)
5. **MATA_PELAJARAN → JADWAL**: One-to-Many (satu mata pelajaran dijadwalkan berkali-kali)
6. **ULANGAN → JAWABAN_ULANGAN**: One-to-Many (satu ulangan dijawab oleh banyak siswa)
7. **TUGAS → PENGUMPULAN_TUGAS**: One-to-Many (satu tugas dikumpulkan oleh banyak siswa)

### Database Constraints

- **Foreign Key Constraints**: Semua relasi menggunakan foreign key dengan CASCADE untuk integritas data
- **Unique Constraints**: Username, email, NIS, NIP, dan kode jurusan/mata pelajaran harus unik
- **Check Constraints**: Role harus admin/guru/siswa, jenis_kelamin harus L/P
- **Indexes**: Primary keys dan foreign keys di-index untuk performa optimal
