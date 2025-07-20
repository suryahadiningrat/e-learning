<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Nilai</h1>
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
        <?php foreach ($jurusan as $jur): ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Jurusan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $jur['nama_jurusan'] ?>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <?= $jur['jumlah_mapel'] ?> Mata Pelajaran
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex gap-1">
                                    <a href="<?= base_url('guru/nilai/export/' . $jur['id']) ?>" 
                                       class="btn btn-success btn-sm" title="Export Excel">
                                        <i class="fas fa-file-excel fa-sm"></i>
                                    </a>
                                    <a href="<?= base_url('guru/nilai/mata-pelajaran/' . $jur['id']) ?>" 
                                       class="btn btn-primary btn-sm" title="Pilih Jurusan">
                                        <i class="fas fa-arrow-right fa-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($jurusan)): ?>
        <div class="text-center py-4">
            <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-500">Belum ada mata pelajaran yang diajar</h5>
            <p class="text-gray-400">Silakan hubungi admin untuk menambahkan jadwal mengajar</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?> 