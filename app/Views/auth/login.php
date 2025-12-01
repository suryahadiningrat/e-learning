<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php
    // Get login background settings
    $db = \Config\Database::connect();
    $settings = $db->table('settings')
        ->whereIn('key', ['login_background_color', 'login_background_image'])
        ->get()
        ->getResultArray();
    
    $backgroundSettings = [];
    foreach ($settings as $setting) {
        $backgroundSettings[$setting['key']] = $setting['value'];
    }
    
    // Default background if no settings found
    $backgroundColor = $backgroundSettings['login_background_color'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    $backgroundImage = $backgroundSettings['login_background_image'] ?? null;
    ?>
    <style>
        body {
            background: <?= $backgroundColor ?>;
            <?php if ($backgroundImage): ?>
            background-image: url('<?= base_url('uploads/background/' . $backgroundImage) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            <?php endif; ?>
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
        }
        
        <?php if ($backgroundImage): ?>
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: <?= $backgroundColor ?>;
            opacity: 0.8;
            z-index: 1;
        }
        <?php endif; ?>
        
        .login-wrapper {
            display: flex;
            gap: 30px;
            max-width: 1000px;
            width: 100%;
            position: relative;
            z-index: 2;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 2;
        }
        
        .guide-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 2;
            max-height: 520px;
            overflow-y: auto;
        }
        
        .guide-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .guide-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .guide-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .guide-container::-webkit-scrollbar-thumb:hover {
            background: #5a67d8;
        }
        
        .guide-container h5 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .guide-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .guide-section h6 {
            color: #764ba2;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .guide-section ul {
            margin: 0;
            padding-left: 20px;
            font-size: 14px;
        }
        
        .guide-section ul li {
            margin-bottom: 5px;
            color: #495057;
        }
        
        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
                align-items: center;
            }
            
            .guide-container {
                max-width: 400px;
            }
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 20px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .register-link {
            text-align: center;
            color: #6c757d;
        }
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: transparent;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <h2><i class="fas fa-graduation-cap me-2"></i>Sistem Informasi SMK</h2>
                <p class="mb-0 mt-2">Silakan login untuk melanjutkan</p>
            </div>
            
            <div class="login-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/login') ?>" method="post">
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    
                    <div class="input-group mb-4">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <div class="register-link">
                    Belum punya akun? <a href="<?= base_url('auth/register') ?>">Daftar disini</a>
                </div>
            </div>
        </div>
        
        <div class="guide-container">
            <h5><i class="fas fa-info-circle me-2"></i>Panduan Penggunaan Sistem</h5>
            
            <!-- Panduan Sebelum Masuk -->
            <div class="guide-section">
                <h6><i class="fas fa-door-open me-2"></i>Sebelum Masuk Sistem</h6>
                <ul>
                    <li><strong>Registrasi Akun:</strong> Klik "Daftar disini" di bawah tombol Login</li>
                    <li>Isi form registrasi dengan lengkap (Username, Email, Nama, Role, Password, dan data pribadi)</li>
                    <li>Pilih role sesuai status Anda (Guru atau Siswa)</li>
                    <li>Tunggu aktivasi akun oleh Admin</li>
                    <li>Setelah diaktivasi, login menggunakan username dan password</li>
                </ul>
            </div>
            
            <div class="guide-section">
                <h6><i class="fas fa-user-shield me-2"></i>Panduan Admin</h6>
                <ul>
                    <li><strong>User Activation:</strong> Aktifkan akun guru/siswa yang baru mendaftar</li>
                    <li><strong>Data Master:</strong> Kelola data Guru, Siswa, Jurusan, Kelas, Mata Pelajaran, dan Jadwal</li>
                    <li><strong>Menu Presensi:</strong>
                        <ul>
                            <li>Lihat rekap presensi per jurusan dan kelas</li>
                            <li>Export data presensi ke Excel atau PDF</li>
                        </ul>
                    </li>
                    <li><strong>Menu Nilai:</strong>
                        <ul>
                            <li>Pilih jurusan → pilih mata pelajaran</li>
                            <li>Klik "Input Nilai" untuk menginput nilai siswa</li>
                            <li>Klik "Rekap Nilai" untuk mencetak/export PDF</li>
                            <li>Tambah kolom tugas/ulangan dengan tombol "Tambah Tugas" atau "Tambah Ulangan"</li>
                        </ul>
                    </li>
                    <li><strong>Menu Materi:</strong> Kelola modul pembelajaran yang diupload guru</li>
                    <li><strong>Menu Tugas/Ulangan:</strong> Kelola soal online dan link pengumpulan tugas</li>
                </ul>
            </div>
            
            <div class="guide-section">
                <h6><i class="fas fa-chalkboard-teacher me-2"></i>Panduan Guru</h6>
                <ul>
                    <li><strong>Dashboard:</strong> Lihat ringkasan jadwal dan aktivitas mengajar</li>
                    <li><strong>Menu User:</strong> Update profil dan data pribadi</li>
                    <li><strong>Menu Presensi:</strong>
                        <ul>
                            <li>Pilih kelas yang diajar</li>
                            <li>Pilih jadwal dan tanggal presensi</li>
                            <li>Centang kehadiran siswa (default: Hadir)</li>
                            <li>Ubah status jika ada siswa tidak hadir (Izin/Sakit/Alpha)</li>
                            <li>Klik "Simpan Presensi"</li>
                        </ul>
                    </li>
                    <li><strong>Menu Nilai:</strong>
                        <ul>
                            <li>Pilih mata pelajaran yang diajar</li>
                            <li>Klik "Input Nilai" untuk menginput nilai</li>
                            <li>Isi nilai tugas, ulangan, UTS dan UAS</li>
                            <li>Jika kolom kurang, klik "Tambah Tugas" atau "Tambah Ulangan"</li>
                            <li>Rata-rata akan dihitung otomatis</li>
                            <li>Klik "Simpan Nilai" untuk menyimpan</li>
                            <li>Klik "Rekap Nilai" untuk mencetak/export PDF</li>
                        </ul>
                    </li>
                    <li><strong>Menu Materi:</strong> Upload modul pembelajaran untuk siswa</li>
                    <li><strong>Menu Tugas Online:</strong> Buat soal ulangan dan kuis online</li>
                </ul>
            </div>
            
            <div class="guide-section">
                <h6><i class="fas fa-user-graduate me-2"></i>Panduan Siswa</h6>
                <ul>
                    <li><strong>Dashboard:</strong> Lihat ringkasan jadwal, nilai, dan tugas</li>
                    <li><strong>Menu User:</strong> Update profil dan data pribadi</li>
                    <li><strong>Menu Jadwal:</strong> Lihat jadwal pelajaran harian</li>
                    <li><strong>Menu Presensi:</strong> Lihat riwayat kehadiran Anda</li>
                    <li><strong>Menu Materi:</strong> Download modul pembelajaran dari guru</li>
                    <li><strong>Menu Tugas:</strong>
                        <ul>
                            <li>Lihat daftar tugas yang harus dikumpulkan</li>
                            <li>Klik link pengumpulan untuk submit tugas</li>
                        </ul>
                    </li>
                    <li><strong>Menu Ulangan Online:</strong>
                        <ul>
                            <li>Lihat daftar ulangan yang tersedia</li>
                            <li>Klik "Kerjakan" untuk memulai ujian</li>
                            <li>Jawab semua soal dan klik "Submit"</li>
                        </ul>
                    </li>
                    <li><strong>Menu Nilai:</strong> Lihat nilai tugas, ulangan, dan rata-rata</li>
                </ul>
            </div>
            
            <div class="guide-section">
                <h6><i class="fas fa-question-circle me-2"></i>Bantuan</h6>
                <ul>
                    <li>Jika lupa password, hubungi Admin untuk reset</li>
                    <li>Pastikan data pribadi selalu terupdate</li>
                    <li>Gunakan browser versi terbaru untuk pengalaman terbaik</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>