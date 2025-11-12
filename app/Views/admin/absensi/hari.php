<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi') ?>">Presensi</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi/kelas/' . $jadwal['id']) ?>">
                        <?= $jadwal['nama_jurusan'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi/jadwal/' . $jadwal['kelas_id']) ?>">
                        <?= $jadwal['nama_kelas'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= $jadwal['nama'] ?>
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
            <div>
                <h6 class="m-0 font-weight-bold text-primary">
                    Hari Presensi - <?= $jadwal['nama'] ?> (<?= $jadwal['nama_kelas'] ?>)
                </h6>
                <small class="text-muted">
                    <i class="fas fa-clock"></i> <?= $jadwal['hari'] ?>, <?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?>
                    | <i class="fas fa-user"></i> <?= $jadwal['nama_guru'] ?>
                </small>
            </div>
            <div>
                <a href="<?= base_url('admin/absensi/createHari/' . $jadwal['id']) ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Hari
                </a>
                <button type="button" class="btn btn-success btn-sm" 
                        onclick="exportJadwal(<?= $jadwal['id'] ?>, '<?= $jadwal['nama'] ?>')">
                    <i class="fas fa-file-excel"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($hari_absensi)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-day fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Belum ada hari absensi</h5>
                    <p class="text-muted">Silakan tambah hari absensi untuk mata pelajaran ini.</p>
                    <a href="<?= base_url('admin/absensi/createHari/' . $jadwal['id']) ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Hari Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($hari_absensi as $item): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                <?= date('d M Y', strtotime($item['tanggal'])) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="<?= base_url('admin/absensi/input/' . $item['id']) ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="exportHari(<?= $item['id'] ?>, '<?= date('d M Y', strtotime($item['tanggal'])) ?>')">
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
function exportJadwal(jadwalId, namaMapel) {
    if (confirm('Export data presensi untuk mata pelajaran ' + namaMapel + '?')) {
        window.location.href = '<?= base_url('admin/absensi/exportJadwal/') ?>' + jadwalId;
    }
}

function exportHari(hariId, tanggal) {
    if (confirm('Export data presensi untuk tanggal ' + tanggal + '?')) {
        window.location.href = '<?= base_url('admin/absensi/exportHari/') ?>' + hariId;
    }
}
</script>

<?= $this->endSection() ?>