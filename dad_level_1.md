# DAD Level 1 - E-Learning SMK

```mermaid
flowchart TD
    %% External Entities (Rectangles)
    Admin[Admin]
    Guru[Guru]
    Siswa[Siswa]
    Principal[Principal]
    Parents[Parents]
    
    %% Processes (Circles)
    P1((1.0<br/>Autentikasi<br/>& Login))
    P2((2.0<br/>Manajemen<br/>User))
    P3((3.0<br/>Manajemen<br/>Pembelajaran))
    P4((4.0<br/>Manajemen<br/>Penilaian))
    P5((5.0<br/>Manajemen<br/>Absensi))
    P6((6.0<br/>Laporan<br/>& Monitoring))
    
    %% Data Stores (Open rectangles)
    D1[(D1: User Data)]
    D2[(D2: Kelas Data)]
    D3[(D3: Mata Pelajaran)]
    D4[(D4: Jadwal)]
    D5[(D5: Materi)]
    D6[(D6: Nilai)]
    D7[(D7: Absensi)]
    D8[(D8: Laporan)]
    
    %% Data Flows from External Entities to Processes
    Admin -->|Login Credentials| P1
    Guru -->|Login Credentials| P1
    Siswa -->|Login Credentials| P1
    Principal -->|Login Credentials| P1
    Parents -->|Login Credentials| P1
    
    Admin -->|User Management Request| P2
    Admin -->|System Configuration| P6
    
    Guru -->|Materi Upload| P3
    Guru -->|Jadwal Request| P3
    Guru -->|Input Nilai| P4
    Guru -->|Input Absensi| P5
    
    Siswa -->|Akses Materi| P3
    Siswa -->|Submit Tugas| P4
    Siswa -->|View Nilai| P4
    Siswa -->|View Absensi| P5
    
    Principal -->|Request Laporan| P6
    Parents -->|View Progress| P6
    
    %% Data Flows from Processes to External Entities
    P1 -->|Authentication Result| Admin
    P1 -->|Authentication Result| Guru
    P1 -->|Authentication Result| Siswa
    P1 -->|Authentication Result| Principal
    P1 -->|Authentication Result| Parents
    
    P2 -->|User Status| Admin
    P3 -->|Materi Content| Siswa
    P3 -->|Jadwal Info| Guru
    P3 -->|Jadwal Info| Siswa
    P4 -->|Nilai Report| Siswa
    P4 -->|Nilai Summary| Guru
    P5 -->|Absensi Report| Siswa
    P5 -->|Absensi Summary| Guru
    P6 -->|System Reports| Admin
    P6 -->|Academic Reports| Principal
    P6 -->|Progress Reports| Parents
    
    %% Data Flows between Processes and Data Stores
    P1 <-->|User Credentials| D1
    P2 <-->|User Information| D1
    P2 <-->|Kelas Assignment| D2
    
    P3 <-->|Mata Pelajaran Info| D3
    P3 <-->|Jadwal Data| D4
    P3 <-->|Materi Content| D5
    
    P4 <-->|Nilai Data| D6
    P4 -->|Assessment Info| D5
    
    P5 <-->|Absensi Records| D7
    P5 -->|Jadwal Reference| D4
    
    P6 -->|User Reports| D1
    P6 -->|Academic Data| D6
    P6 -->|Attendance Data| D7
    P6 <-->|Generated Reports| D8
    
    %% Styling
    classDef externalEntity fill:#e1f5fe,stroke:#01579b,stroke-width:2px,color:#000
    classDef process fill:#f3e5f5,stroke:#4a148c,stroke-width:2px,color:#000
    classDef dataStore fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px,color:#000
    
    class Admin,Guru,Siswa,Principal,Parents externalEntity
    class P1,P2,P3,P4,P5,P6 process
    class D1,D2,D3,D4,D5,D6,D7,D8 dataStore
```

## Deskripsi Proses Level 1

### 1.0 Autentikasi & Login
**Input**: Login credentials dari semua external entities
**Proses**: Validasi username/password, pengecekan role user, pemberian akses sesuai hak
**Output**: Authentication result (berhasil/gagal login)
**Data Store**: D1 (User Data)

### 2.0 Manajemen User  
**Input**: User management request dari Admin
**Proses**: CRUD operasi untuk data user (Admin, Guru, Siswa), assignment kelas
**Output**: User status confirmation
**Data Store**: D1 (User Data), D2 (Kelas Data)

### 3.0 Manajemen Pembelajaran
**Input**: 
- Materi upload dari Guru
- Jadwal request dari Guru  
- Akses materi dari Siswa
**Proses**: Upload/download materi, penjadwalan kelas, manajemen konten pembelajaran
**Output**: 
- Materi content untuk Siswa
- Jadwal info untuk Guru dan Siswa
**Data Store**: D3 (Mata Pelajaran), D4 (Jadwal), D5 (Materi)

### 4.0 Manajemen Penilaian
**Input**:
- Input nilai dari Guru
- Submit tugas dari Siswa
- View nilai request dari Siswa
**Proses**: Input dan kalkulasi nilai, manajemen tugas dan ujian, generate laporan nilai
**Output**:
- Nilai report untuk Siswa
- Nilai summary untuk Guru  
**Data Store**: D5 (Materi), D6 (Nilai)

### 5.0 Manajemen Absensi
**Input**:
- Input absensi dari Guru
- View absensi request dari Siswa
**Proses**: Pencatatan kehadiran siswa, kalkulasi persentase kehadiran
**Output**:
- Absensi report untuk Siswa
- Absensi summary untuk Guru
**Data Store**: D4 (Jadwal), D7 (Absensi)

### 6.0 Laporan & Monitoring
**Input**:
- System configuration dari Admin
- Request laporan dari Principal
- View progress dari Parents
**Proses**: Generate berbagai laporan sistem, monitoring aktivitas, analisis data
**Output**:
- System reports untuk Admin
- Academic reports untuk Principal  
- Progress reports untuk Parents
**Data Store**: D1 (User Data), D6 (Nilai), D7 (Absensi), D8 (Laporan)

## External Entities

### Admin
Administrator sistem yang mengelola konfigurasi sistem dan manajemen user

### Guru  
Pengajar yang mengelola pembelajaran, materi, penilaian, dan absensi

### Siswa
Peserta didik yang mengakses materi, mengerjakan tugas, dan melihat nilai serta absensi

### Principal
Kepala sekolah yang memantau dan mendapatkan laporan akademik

### Parents
Orang tua siswa yang memantau progress belajar anak

## Data Stores

- **D1: User Data** - Menyimpan informasi semua pengguna sistem
- **D2: Kelas Data** - Menyimpan informasi kelas dan penugasan siswa
- **D3: Mata Pelajaran** - Menyimpan data mata pelajaran yang diajarkan
- **D4: Jadwal** - Menyimpan jadwal pembelajaran
- **D5: Materi** - Menyimpan konten pembelajaran dan tugas
- **D6: Nilai** - Menyimpan data penilaian siswa
- **D7: Absensi** - Menyimpan data kehadiran siswa  
- **D8: Laporan** - Menyimpan laporan yang telah di-generate