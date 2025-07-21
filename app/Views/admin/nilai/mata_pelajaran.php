<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mata Pelajaran - <?= $jurusan['nama_jurusan'] ?></h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/nilai/export/' . $jurusan['id']) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            <a href="<?= base_url('admin/nilai') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($mata_pelajaran as $mapel): ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Mata Pelajaran
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $mapel['nama_mata_pelajaran'] ?>
                                </div>
                                <div class="text-xs text-gray-600">
                                    Kelas: <?= $mapel['nama_kelas'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="<?= base_url('admin/nilai/input/' . $mapel['id']) ?>" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-edit fa-sm"></i>
                                    Input Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($mata_pelajaran)): ?>
        <div class="text-center py-4">
            <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-500">Belum ada mata pelajaran</h5>
            <p class="text-gray-400">Silakan tambahkan jadwal mata pelajaran terlebih dahulu</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?> 