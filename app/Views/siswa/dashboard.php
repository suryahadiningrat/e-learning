<?= $this->extend('siswa/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Siswa</h1>
    </div>
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning-gradient me-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">0</h4>
                            <small class="text-muted">Total Mata Pelajaran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary-gradient me-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">0</h4>
                            <small class="text-muted">Materi Tersedia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success-gradient me-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">0</h4>
                            <small class="text-muted">Nilai Rata-rata</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info-gradient me-3">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">0</h4>
                            <small class="text-muted">Tugas Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Actions & Recent Activities -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('siswa/jadwal') ?>" class="btn btn-warning">
                            <i class="fas fa-calendar-alt me-2"></i>Lihat Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <h5 class="card-title">Recent Activities</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-book text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Belum ada aktivitas</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 