<?= $this->extend('guru/layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('guru/absensi') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Jurusan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <h5>Jurusan: <?= esc($jurusan['nama_jurusan']) ?></h5>
                        <p class="text-muted">Kode: <?= esc($jurusan['kode_jurusan']) ?></p>
                    </div>

                    <div class="row">
                        <?php if (!empty($kelas)): ?>
                            <?php foreach ($kelas as $item): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= esc($item['nama_kelas']) ?></h5>
                                            <p class="card-text text-muted">
                                                Tingkat: <?= esc($item['tingkat']) ?>
                                            </p>
                                            <div class="mt-auto">
                                                <a href="<?= base_url('guru/absensi/jadwal/' . $item['id']) ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-arrow-right"></i> Lihat Jadwal
                                                </a>
                                                <a href="<?= base_url('guru/absensi/export-kelas/' . $item['id']) ?>" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-download"></i> Export
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Tidak ada kelas yang memiliki jadwal yang Anda ajar di jurusan ini.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>