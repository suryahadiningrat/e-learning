<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Siswa Dashboard' ?> - E-Learning SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">E-Learning SMK</h4>
                        <small class="text-white-50">Panel Siswa</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'siswa/jadwal') !== false) ? 'active' : '' ?>" href="<?= base_url('siswa/jadwal') ?>">
                                <i class="fas fa-calendar-alt"></i> Jadwal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'siswa/nilai') !== false) ? 'active' : '' ?>" href="<?= base_url('siswa/nilai') ?>">
                                <i class="fas fa-chart-line"></i> Nilai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(current_url(), 'siswa/materi') !== false) ? 'active' : '' ?>" href="<?= base_url('siswa/materi') ?>">
                                <i class="fas fa-book"></i> Materi
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
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
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Only initialize DataTable if it exists and hasn't been initialized yet
        if ($('#dataTable').length && !$.fn.DataTable.isDataTable('#dataTable')) {
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
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html> 