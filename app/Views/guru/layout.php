<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Guru Dashboard' ?> - E-Learning SMK</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: <?= session()->get('sidebar_color_' . session()->get('role')) ?? 'linear-gradient(to bottom, #1cc88a, #169b6b)' ?>;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0;
            text-decoration: none;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
            text-decoration: none;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.2);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.5rem;
        }
        .main-content {
            background-color: #f8f9fc;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        .border-left-secondary {
            border-left: 0.25rem solid #858796 !important;
        }
        .nav-link.dropdown-toggle img {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <?php
                        // Load setting helper
                        helper('setting');
                        $logoUrl = get_logo_sekolah();
                        ?>
                        <?php if ($logoUrl): ?>
                            <img src="<?= $logoUrl ?>" alt="Logo Sekolah" class="mb-2" style="max-width: 60px; max-height: 60px; object-fit: contain;">
                        <?php endif; ?>
                        <h4 class="text-white">E-Learning SMK</h4>
                        <small class="text-white-50">Panel Guru</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <!-- Panel Guru -->
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                <span>Panel Guru</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/dashboard') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/dashboard') ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/user-pengguna') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/user-pengguna') ?>">
                                <i class="fas fa-user-cog"></i> User
                            </a>
                        </li>
                        
                        <!-- Data -->
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                <span>Data</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/siswa') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/siswa') ?>">
                                <i class="fas fa-user-graduate"></i> Data Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/jurusan') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/jurusan') ?>">
                                <i class="fas fa-building"></i> Data Jurusan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/kelas') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/kelas') ?>">
                                <i class="fas fa-building"></i> Data Kelas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/absensi') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/absensi') ?>">
                                <i class="fas fa-clipboard-check"></i> Data Absensi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/nilai') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/nilai') ?>">
                                <i class="fas fa-chart-line"></i> Data Nilai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/materi') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/materi') ?>">
                                <i class="fas fa-book"></i> Materi/Modul
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/ulangan') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/ulangan') ?>">
                                <i class="fas fa-clipboard-list"></i> Ulangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'guru/tugas') !== false) ? 'active' : '' ?>" href="<?= base_url('guru/tugas') ?>">
                                <i class="fas fa-link"></i> Data Link Pengumpulan Tugas
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <?php 
                                    $userModel = new \App\Models\UserModel();
                                    $user = $userModel->find(session()->get('user_id'));
                                    if (!empty($user['photo'])): ?>
                                        <img src="<?= base_url('uploads/profile/' . $user['photo']) ?>" 
                                             class="rounded-circle me-2" 
                                             alt="Profile Photo" 
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle fa-lg me-2"></i>
                                    <?php endif; ?>
                                    <?= session()->get('username') ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= base_url('auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page content -->
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <script>
    // DataTables initialization
    $(document).ready(function() {
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });
        }
    });
    
    // SweetAlert untuk flash messages
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= session()->getFlashdata('success') ?>',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= session()->getFlashdata('error') ?>',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>
    
    // Konfirmasi delete dengan SweetAlert
    function confirmDelete(url, name) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Data ${name} akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html> 