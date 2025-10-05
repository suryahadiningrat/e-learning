<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi') ?>">Absensi</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi/kelas/' . $kelas['jurusan_id']) ?>">
                        <?= $kelas['nama_jurusan'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= $kelas['nama_kelas'] ?>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Alert Messages -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Jadwal Mata Pelajaran - <?= $kelas['nama_kelas'] ?>
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    onclick="exportKelas(<?= $kelas['id'] ?>, '<?= $kelas['nama_kelas'] ?>')">
                <i class="fas fa-file-excel"></i> Export Kelas
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Tidak ada jadwal ditemukan</h5>
                    <p class="text-muted">Belum ada jadwal mata pelajaran yang terdaftar untuk kelas ini.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($jadwal as $item): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-left-warning h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                <?= $item['hari'] ?>
                                            </div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                <?= $item['nama'] ?>
                                            </div>
                                            <div class="text-xs text-muted mt-2">
                                                <i class="fas fa-clock"></i> <?= $item['jam_mulai'] ?> - <?= $item['jam_selesai'] ?>
                                            </div>
                                            <div class="text-xs text-muted">
                                                <i class="fas fa-user"></i> <?= $item['nama_guru'] ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="<?= base_url('admin/absensi/hari/' . $item['id']) ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Hari
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="exportJadwal(<?= $item['id'] ?>, '<?= $item['nama'] ?>')">
                                            <i class="fas fa-file-excel"></i> Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function exportKelas(kelasId, namaKelas) {
    if (confirm('Export data absensi untuk kelas ' + namaKelas + '?')) {
        window.location.href = '<?= base_url('admin/absensi/exportKelas/') ?>' + kelasId;
    }
}

function exportJadwal(jadwalId, namaMapel) {
    if (confirm('Export data absensi untuk mata pelajaran ' + namaMapel + '?')) {
        window.location.href = '<?= base_url('admin/absensi/exportJadwal/') ?>' + jadwalId;
    }
}
</script>

<?= $this->endSection() ?>