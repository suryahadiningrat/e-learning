# Diagram Berjenjang - E-Learning SMK

## Level 0 - System Overview

```mermaid
graph TD
    System[E-Learning SMK System]
```

## Level 1 - Main Modules

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

## Level 2 - Detailed Modules

```mermaid
graph TD
    subgraph "Authentication Module"
        Login[Login System]
        Register[Registration System]
        Session[Session Management]
        Role[Role Management]
    end
    
    subgraph "Admin Module"
        UserMgmt[User Management]
        DataMaster[Data Master Management]
        SystemConfig[System Configuration]
        Reports[Reports & Analytics]
    end
    
    subgraph "Teacher Module"
        Teaching[Teaching Management]
        Assessment[Assessment Management]
        GradeMgmt[Grade Management]
        Attendance[Attendance Management]
    end
    
    subgraph "Student Module"
        Learning[Learning Management]
        Exam[Exam System]
        Assignment[Assignment System]
        Progress[Progress Tracking]
    end
    
    subgraph "Common Services"
        FileMgmt[File Management]
        Notification[Notification System]
        Security[Security Services]
        Database[Database Services]
    end
```

## Level 3 - Sub-modules Detail

```mermaid
graph TD
    subgraph "User Management"
        UserCRUD[User CRUD Operations]
        UserActivation[User Activation]
        UserProfile[User Profile Management]
        UserRole[User Role Assignment]
    end
    
    subgraph "Data Master Management"
        StudentData[Student Data Management]
        TeacherData[Teacher Data Management]
        ClassData[Class Data Management]
        SubjectData[Subject Data Management]
        ScheduleData[Schedule Data Management]
        AcademicYear[Academic Year Management]
    end
    
    subgraph "Teaching Management"
        MaterialMgmt[Material Management]
        LessonPlan[Lesson Plan Management]
        ResourceMgmt[Resource Management]
        TeachingTools[Teaching Tools]
    end
    
    subgraph "Assessment Management"
        ExamCreation[Exam Creation]
        QuestionBank[Question Bank Management]
        ExamScheduling[Exam Scheduling]
        ExamMonitoring[Exam Monitoring]
    end
    
    subgraph "Learning Management"
        MaterialAccess[Material Access]
        LearningPath[Learning Path]
        ResourceAccess[Resource Access]
        StudyTools[Study Tools]
    end
    
    subgraph "Exam System"
        ExamTaking[Exam Taking]
        AnswerSubmission[Answer Submission]
        ResultViewing[Result Viewing]
        ExamHistory[Exam History]
    end
    
    subgraph "Assignment System"
        AssignmentView[Assignment Viewing]
        AssignmentSubmission[Assignment Submission]
        SubmissionTracking[Submission Tracking]
        FeedbackView[Feedback Viewing]
    end
    
    subgraph "File Management"
        FileUpload[File Upload]
        FileDownload[File Download]
        FileStorage[File Storage]
        FileSecurity[File Security]
    end
    
    subgraph "Notification System"
        EmailNotif[Email Notifications]
        InAppNotif[In-App Notifications]
        SMSNotif[SMS Notifications]
        PushNotif[Push Notifications]
    end
```

## Level 4 - Component Level

```mermaid
graph TD
    subgraph "User CRUD Operations"
        CreateUser[Create User]
        ReadUser[Read User]
        UpdateUser[Update User]
        DeleteUser[Delete User]
        SearchUser[Search User]
        FilterUser[Filter User]
    end
    
    subgraph "Material Management"
        CreateMaterial[Create Material]
        EditMaterial[Edit Material]
        DeleteMaterial[Delete Material]
        UploadFile[Upload File]
        DownloadFile[Download File]
        ShareMaterial[Share Material]
    end
    
    subgraph "Exam Creation"
        CreateExam[Create Exam]
        AddQuestions[Add Questions]
        SetTimeLimit[Set Time Limit]
        SetSchedule[Set Schedule]
        PublishExam[Publish Exam]
        CloseExam[Close Exam]
    end
    
    subgraph "Grade Management"
        InputGrade[Input Grade]
        CalculateGrade[Calculate Grade]
        GenerateReport[Generate Report]
        ExportGrade[Export Grade]
        GradeHistory[Grade History]
    end
```

## Hierarchical Structure Summary

### Level 0: System Overview
- **E-Learning SMK System**: Sistem utama e-learning untuk SMK

### Level 1: Main Modules
1. **Authentication Module**: Sistem autentikasi dan otorisasi
2. **Admin Module**: Modul untuk administrator
3. **Teacher Module**: Modul untuk guru/pengajar
4. **Student Module**: Modul untuk siswa
5. **Common Services**: Layanan umum yang digunakan semua modul

### Level 2: Detailed Modules
- **Authentication**: Login, Register, Session, Role
- **Admin**: User Management, Data Master, System Config, Reports
- **Teacher**: Teaching, Assessment, Grade, Attendance
- **Student**: Learning, Exam, Assignment, Progress
- **Common**: File, Notification, Security, Database

### Level 3: Sub-modules
- **User Management**: CRUD, Activation, Profile, Role Assignment
- **Data Master**: Student, Teacher, Class, Subject, Schedule, Academic Year
- **Teaching**: Material, Lesson Plan, Resource, Tools
- **Assessment**: Creation, Question Bank, Scheduling, Monitoring
- **Learning**: Access, Path, Resource, Tools
- **Exam**: Taking, Submission, Results, History
- **Assignment**: Viewing, Submission, Tracking, Feedback
- **File**: Upload, Download, Storage, Security
- **Notification**: Email, In-App, SMS, Push

### Level 4: Component Level
- **User CRUD**: Create, Read, Update, Delete, Search, Filter
- **Material**: Create, Edit, Delete, Upload, Download, Share
- **Exam**: Create, Add Questions, Set Time, Set Schedule, Publish, Close
- **Grade**: Input, Calculate, Generate Report, Export, History

## Benefits of Hierarchical Structure

1. **Modularity**: Setiap level memiliki tanggung jawab yang jelas
2. **Scalability**: Mudah untuk menambah fitur baru di level yang sesuai
3. **Maintainability**: Perubahan di satu level tidak mempengaruhi level lain
4. **Reusability**: Komponen di level bawah dapat digunakan ulang
5. **Testing**: Mudah untuk melakukan unit testing pada setiap level
6. **Documentation**: Struktur yang jelas memudahkan dokumentasi
