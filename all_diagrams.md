# E-Learning SMK - Complete System Diagrams

## 1. Use Case Diagram

```mermaid
graph TB
    %% Actors
    Admin[👨‍💼 Admin]
    Guru[👨‍🏫 Guru]
    Siswa[👨‍🎓 Siswa]
    
    %% System Boundary
    subgraph "E-Learning SMK System"
        %% Admin Use Cases
        subgraph "Admin Functions"
            UC1[User Activation]
            UC2[User Management]
            UC3[System Settings]
            UC4[Data Master Management]
            UC5[Student Data Management]
            UC6[Teacher Data Management]
            UC7[Class Management]
            UC8[Schedule Management]
            UC9[Attendance Management]
            UC10[Grade Management]
            UC11[Exam Management]
            UC12[Material Management]
            UC13[Assignment Management]
            UC14[Academic Year Management]
        end
        
        %% Guru Use Cases
        subgraph "Teacher Functions"
            UC15[View Student Data]
            UC16[View Class Data]
            UC17[Attendance Management]
            UC18[Grade Input]
            UC19[Create Online Exam]
            UC20[Material Management]
            UC21[Assignment Management]
            UC22[View Exam Results]
        end
        
        %% Siswa Use Cases
        subgraph "Student Functions"
            UC23[View Schedule]
            UC24[View Grades]
            UC25[Take Online Exam]
            UC26[View Materials]
            UC27[Submit Assignments]
            UC28[View Exam Results]
        end
        
        %% Common Use Cases
        subgraph "Common Functions"
            UC29[Login]
            UC30[Register]
            UC31[Logout]
            UC32[Profile Management]
        end
    end
    
    %% Admin Relationships
    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC29
    Admin --> UC31
    Admin --> UC32
    
    %% Guru Relationships
    Guru --> UC15
    Guru --> UC16
    Guru --> UC17
    Guru --> UC18
    Guru --> UC19
    Guru --> UC20
    Guru --> UC21
    Guru --> UC22
    Guru --> UC29
    Guru --> UC31
    Guru --> UC32
    
    %% Siswa Relationships
    Siswa --> UC23
    Siswa --> UC24
    Siswa --> UC25
    Siswa --> UC26
    Siswa --> UC27
    Siswa --> UC28
    Siswa --> UC29
    Siswa --> UC30
    Siswa --> UC31
    Siswa --> UC32
```

## 2. Activity Diagram - Login Process

```mermaid
flowchart TD
    Start([Start]) --> Access[User mengakses sistem]
    Access --> LoginPage[Menampilkan halaman login]
    LoginPage --> InputCred[User memasukkan username & password]
    InputCred --> Validate{Validasi kredensial}
    Validate -->|Invalid| ErrorMsg[Menampilkan pesan error]
    ErrorMsg --> LoginPage
    Validate -->|Valid| CheckRole{Periksa role user}
    CheckRole -->|Admin| AdminDash[Redirect ke Dashboard Admin]
    CheckRole -->|Guru| GuruDash[Redirect ke Dashboard Guru]
    CheckRole -->|Siswa| SiswaDash[Redirect ke Dashboard Siswa]
    AdminDash --> End([End])
    GuruDash --> End
    SiswaDash --> End
```

## 3. Context Diagram

```mermaid
graph TB
    %% External Entities
    Admin[👨‍💼 Admin<br/>Sekolah]
    Guru[👨‍🏫 Guru<br/>Pengajar]
    Siswa[👨‍🎓 Siswa<br/>Peserta Didik]
    KepalaSekolah[👨‍💼 Kepala<br/>Sekolah]
    OrangTua[👨‍👩‍👧‍👦 Orang Tua<br/>Wali]
    SistemAkademik[📊 Sistem<br/>Akademik<br/>Eksternal]
    
    %% Central System
    subgraph "E-Learning SMK System"
        ELearning[🎓 E-Learning<br/>SMK System]
    end
    
    %% Data Flows - Admin
    Admin -->|"1. Login Request"| ELearning
    ELearning -->|"2. Dashboard Data"| Admin
    Admin -->|"3. User Management"| ELearning
    ELearning -->|"4. User Status Update"| Admin
    
    %% Data Flows - Guru
    Guru -->|"5. Login Request"| ELearning
    ELearning -->|"6. Teaching Dashboard"| Guru
    Guru -->|"7. Material Upload"| ELearning
    ELearning -->|"8. Upload Confirmation"| Guru
    
    %% Data Flows - Siswa
    Siswa -->|"9. Login Request"| ELearning
    ELearning -->|"10. Student Dashboard"| Siswa
    Siswa -->|"11. View Materials"| ELearning
    ELearning -->|"12. Material Files"| Siswa
    
    %% Styling
    classDef external fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef system fill:#f3e5f5,stroke:#4a148c,stroke-width:3px
    
    class Admin,Guru,Siswa,KepalaSekolah,OrangTua,SistemAkademik external
    class ELearning system
```

## 4. Hierarchical Diagram - Level 1

```mermaid
graph TD
    subgraph "E-Learning SMK System"
        Auth[Authentication Module]
        Admin[Admin Module]
        Guru[Teacher Module]
        Siswa[Student Module]
        Common[Common Services]
    end
```

## 5. DAD Level 1

```mermaid
graph TD
    %% External Entities
    Admin[👨‍💼 Admin]
    Guru[👨‍🏫 Guru]
    Siswa[👨‍🎓 Siswa]
    
    %% Processes
    subgraph "E-Learning SMK System"
        P1[1.0<br/>Authentication<br/>Process]
        P2[2.0<br/>User Management<br/>Process]
        P3[3.0<br/>Learning Content<br/>Management]
        P4[4.0<br/>Assessment<br/>Management]
        P5[5.0<br/>Grade & Progress<br/>Management]
        P6[6.0<br/>System Administration<br/>Process]
    end
    
    %% Data Stores
    D1[(D1: User Database)]
    D2[(D2: Learning Content<br/>Database)]
    D3[(D3: Assessment<br/>Database)]
    D4[(D4: Grade Database)]
    D5[(D5: System Configuration<br/>Database)]
    D6[(D6: File Storage)]
    
    %% Admin Flows
    Admin -->|"Login Request"| P1
    P1 -->|"Authentication Response"| Admin
    Admin -->|"User Management Request"| P2
    P2 -->|"User Management Response"| Admin
    
    %% Guru Flows
    Guru -->|"Login Request"| P1
    P1 -->|"Authentication Response"| Guru
    Guru -->|"Content Management Request"| P3
    P3 -->|"Content Management Response"| Guru
    
    %% Siswa Flows
    Siswa -->|"Login Request"| P1
    P1 -->|"Authentication Response"| Siswa
    Siswa -->|"Content Access Request"| P3
    P3 -->|"Content Access Response"| Siswa
    
    %% Process to Data Store Flows
    P1 <-->|"User Data"| D1
    P2 <-->|"User Data"| D1
    P3 <-->|"Content Data"| D2
    P3 <-->|"File Data"| D6
    P4 <-->|"Assessment Data"| D3
    P5 <-->|"Grade Data"| D4
    P6 <-->|"Config Data"| D5
    
    %% Styling
    classDef external fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef process fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef datastore fill:#e8f5e8,stroke:#2e7d32,stroke-width:2px
    
    class Admin,Guru,Siswa external
    class P1,P2,P3,P4,P5,P6 process
    class D1,D2,D3,D4,D5,D6 datastore
```

## 6. DAD Level 2 - Authentication Process

```mermaid
graph TD
    %% External Entities
    User[👤 User<br/>(Admin/Guru/Siswa)]
    
    %% Sub-processes
    subgraph "1.0 Authentication Process"
        P1_1[1.1<br/>Validate<br/>Credentials]
        P1_2[1.2<br/>Check User<br/>Status]
        P1_3[1.3<br/>Generate<br/>Session]
        P1_4[1.4<br/>Set User<br/>Permissions]
    end
    
    %% Data Stores
    D1[(D1: User Database)]
    D7[(D7: Session Database)]
    
    %% Flows
    User -->|"Username & Password"| P1_1
    P1_1 -->|"User Data Request"| D1
    D1 -->|"User Data"| P1_1
    P1_1 -->|"Validated User"| P1_2
    P1_2 -->|"User Status Check"| D1
    D1 -->|"User Status"| P1_2
    P1_2 -->|"Active User"| P1_3
    P1_3 -->|"Session Data"| D7
    D7 -->|"Session Created"| P1_3
    P1_3 -->|"Session Info"| P1_4
    P1_4 -->|"Permission Data"| D1
    D1 -->|"User Permissions"| P1_4
    P1_4 -->|"Login Success"| User
    
    %% Styling
    classDef external fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef process fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef datastore fill:#e8f5e8,stroke:#2e7d32,stroke-width:2px
    
    class User external
    class P1_1,P1_2,P1_3,P1_4 process
    class D1,D7 datastore
```

## 7. ERD (Entity Relationship Diagram)

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
    
    NILAI {
        int id PK
        int siswa_id FK
        int mata_pelajaran_id FK
        int guru_id FK
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
    
    %% Relationships
    USERS ||--o{ SISWA : "has"
    USERS ||--o{ GURU : "has"
    
    JURUSAN ||--o{ KELAS : "contains"
    KELAS ||--o{ SISWA : "enrolls"
    
    GURU ||--o{ JADWAL : "teaches"
    MATA_PELAJARAN ||--o{ JADWAL : "scheduled_in"
    KELAS ||--o{ JADWAL : "has_schedule"
    
    SISWA ||--o{ NILAI : "receives"
    MATA_PELAJARAN ||--o{ NILAI : "graded_in"
    GURU ||--o{ NILAI : "gives"
    
    GURU ||--o{ ULANGAN : "creates"
    MATA_PELAJARAN ||--o{ ULANGAN : "tested_in"
    KELAS ||--o{ ULANGAN : "takes"
    
    GURU ||--o{ MATERI : "creates"
    MATA_PELAJARAN ||--o{ MATERI : "covers"
    KELAS ||--o{ MATERI : "studies"
    
    GURU ||--o{ TUGAS : "assigns"
    MATA_PELAJARAN ||--o{ TUGAS : "assigned_in"
    KELAS ||--o{ TUGAS : "receives"
```

## Summary

Dokumen ini berisi semua diagram sistem E-Learning SMK:

1. **Use Case Diagram**: Menunjukkan interaksi antara aktor (Admin, Guru, Siswa) dengan sistem
2. **Activity Diagram**: Menggambarkan alur proses login dalam sistem
3. **Context Diagram**: Menunjukkan sistem dalam konteks lingkungan eksternal
4. **Hierarchical Diagram**: Struktur modul sistem secara berjenjang
5. **DAD Level 1**: Data Flow Diagram tingkat pertama
6. **DAD Level 2**: Data Flow Diagram tingkat kedua (detail proses authentication)
7. **ERD**: Entity Relationship Diagram yang menunjukkan struktur database

Semua diagram dibuat menggunakan Mermaid syntax dan dapat di-render dalam berbagai format.
