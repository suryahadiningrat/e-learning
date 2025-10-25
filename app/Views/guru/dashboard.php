<?= $this->extend('guru/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Guru</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Welcome Card -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selamat Datang
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= session()->get('username') ?>
                            </div>
                            <div class="text-muted">
                                Panel Guru - Sistem Informasi SMK
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Data Absensi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Kelola</div>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('guru/absensi') ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-clipboard-check"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Data Siswa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Lihat</div>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('guru/siswa') ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-user-graduate"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Data Kelas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Lihat</div>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('guru/kelas') ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-building"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                User Pengguna
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Lihat</div>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('guru/user-pengguna') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-user-cog"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 