# Activity Diagram - E-Learning SMK

## 1. Activity Diagram - Login Process

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

## 2. Activity Diagram - Student Taking Online Exam

```mermaid
flowchart TD
    Start([Start]) --> Login[Student login ke sistem]
    Login --> ViewExam[Lihat daftar ulangan tersedia]
    ViewExam --> SelectExam[Pilih ulangan yang akan dikerjakan]
    SelectExam --> CheckStatus{Periksa status ulangan}
    CheckStatus -->|Belum dibuka| WaitMsg[Menampilkan pesan menunggu]
    CheckStatus -->|Sudah ditutup| ClosedMsg[Menampilkan pesan sudah ditutup]
    CheckStatus -->|Aktif| StartExam[Mulai mengerjakan ulangan]
    StartExam --> AnswerQ[Menjawab soal]
    AnswerQ --> SaveAnswer[Simpan jawaban sementara]
    SaveAnswer --> MoreQ{Ada soal lain?}
    MoreQ -->|Ya| AnswerQ
    MoreQ -->|Tidak| Review[Review jawaban]
    Review --> Submit{Submit jawaban?}
    Submit -->|Tidak| AnswerQ
    Submit -->|Ya| FinalSubmit[Submit jawaban final]
    FinalSubmit --> ShowResult[Menampilkan hasil]
    ShowResult --> End([End])
    WaitMsg --> End
    ClosedMsg --> End
```

## 3. Activity Diagram - Teacher Creating Exam

```mermaid
flowchart TD
    Start([Start]) --> Login[Guru login ke sistem]
    Login --> ExamMenu[Pilih menu ulangan]
    ExamMenu --> CreateNew[Klik buat ulangan baru]
    CreateNew --> FillInfo[Isi informasi ulangan]
    FillInfo --> AddQuestions[Tambah soal]
    AddQuestions --> QuestionType{Pilih jenis soal}
    QuestionType -->|Pilihan Ganda| MCQ[Input soal pilihan ganda]
    QuestionType -->|Essay| Essay[Input soal essay]
    MCQ --> SetAnswer[Set jawaban benar]
    Essay --> SetAnswer
    SetAnswer --> MoreQ{Ada soal lain?}
    MoreQ -->|Ya| AddQuestions
    MoreQ -->|Tidak| Preview[Preview ulangan]
    Preview --> SaveDraft[Simpan sebagai draft]
    SaveDraft --> Publish{Publish ulangan?}
    Publish -->|Tidak| Edit[Edit ulangan]
    Publish -->|Ya| SetSchedule[Set jadwal ulangan]
    SetSchedule --> PublishExam[Publish ulangan]
    PublishExam --> Notify[Notifikasi ke siswa]
    Notify --> End([End])
    Edit --> Preview
```

## 4. Activity Diagram - Assignment Submission Process

```mermaid
flowchart TD
    Start([Start]) --> Login[Student login ke sistem]
    Login --> ViewAssign[Lihat daftar tugas]
    ViewAssign --> SelectAssign[Pilih tugas]
    SelectAssign --> CheckDeadline{Periksa deadline}
    CheckDeadline -->|Lewat deadline| LateMsg[Menampilkan pesan terlambat]
    CheckDeadline -->|Masih dalam waktu| ViewDetail[Lihat detail tugas]
    ViewDetail --> DownloadFile[Download file tugas jika ada]
    DownloadFile --> PrepareWork[Menyiapkan pekerjaan]
    PrepareWork --> UploadFile[Upload file jawaban]
    UploadFile --> AddComment[Tambah komentar/deskripsi]
    AddComment --> Submit[Submit tugas]
    Submit --> Confirm{Konfirmasi submit?}
    Confirm -->|Tidak| Edit[Edit submission]
    Confirm -->|Ya| Success[Tugas berhasil dikumpulkan]
    Success --> NotifyGuru[Notifikasi ke guru]
    NotifyGuru --> End([End])
    LateMsg --> End
    Edit --> UploadFile
```

## 5. Activity Diagram - Admin Managing Users

```mermaid
flowchart TD
    Start([Start]) --> Login[Admin login ke sistem]
    Login --> UserMenu[Pilih menu user management]
    UserMenu --> ViewUsers[Lihat daftar user]
    ViewUsers --> Action{Pilih aksi}
    Action -->|Aktivasi| Activate[Aktivasi user]
    Action -->|Deaktivasi| Deactivate[Deaktivasi user]
    Action -->|Edit| EditUser[Edit data user]
    Action -->|Hapus| DeleteUser[Hapus user]
    Activate --> UpdateStatus[Update status user]
    Deactivate --> UpdateStatus
    EditUser --> SaveChanges[Simpan perubahan]
    DeleteUser --> ConfirmDel{Konfirmasi hapus}
    ConfirmDel -->|Ya| RemoveUser[Hapus user dari sistem]
    ConfirmDel -->|Tidak| ViewUsers
    UpdateStatus --> NotifyUser[Notifikasi ke user]
    SaveChanges --> NotifyUser
    RemoveUser --> LogActivity[Log aktivitas]
    NotifyUser --> LogActivity
    LogActivity --> End([End])
```

## 6. Activity Diagram - Grade Management Process

```mermaid
flowchart TD
    Start([Start]) --> Login[Guru/Admin login]
    Login --> GradeMenu[Pilih menu nilai]
    GradeMenu --> SelectClass[Pilih kelas]
    SelectClass --> SelectSubject[Pilih mata pelajaran]
    SelectSubject --> ViewStudents[Lihat daftar siswa]
    ViewStudents --> InputGrade[Input nilai siswa]
    InputGrade --> ValidateGrade{Validasi nilai}
    ValidateGrade -->|Invalid| ErrorGrade[Tampilkan error]
    ValidateGrade -->|Valid| SaveGrade[Simpan nilai]
    ErrorGrade --> InputGrade
    SaveGrade --> MoreStudent{Ada siswa lain?}
    MoreStudent -->|Ya| InputGrade
    MoreStudent -->|Tidak| CalculateFinal[Hitung nilai akhir]
    CalculateFinal --> GenerateReport[Generate laporan nilai]
    GenerateReport --> ExportOption{Export laporan?}
    ExportOption -->|Ya| ExportPDF[Export ke PDF/Excel]
    ExportOption -->|Tidak| ViewReport[Lihat laporan]
    ExportPDF --> End([End])
    ViewReport --> End
```

## Deskripsi Activity Diagram

### 1. Login Process
Menggambarkan alur login user dengan validasi kredensial dan redirect berdasarkan role.

### 2. Student Taking Online Exam
Menggambarkan proses siswa mengikuti ulangan online dari mulai login hingga melihat hasil.

### 3. Teacher Creating Exam
Menggambarkan proses guru membuat ulangan online dengan berbagai jenis soal.

### 4. Assignment Submission Process
Menggambarkan proses siswa mengumpulkan tugas dengan validasi deadline.

### 5. Admin Managing Users
Menggambarkan proses admin mengelola user (aktivasi, deaktivasi, edit, hapus).

### 6. Grade Management Process
Menggambarkan proses input dan pengelolaan nilai oleh guru/admin.
