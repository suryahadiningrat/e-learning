<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ulangan</h1>
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
        <?php foreach ($ulangan as $item): ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <?= $item['nama_mata_pelajaran'] ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $item['judul_ulangan'] ?>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <i class="fas fa-calendar"></i> 
                                    <?= date('d/m/Y H:i', strtotime($item['waktu_mulai'])) ?> - 
                                    <?= date('H:i', strtotime($item['waktu_selesai'])) ?>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <i class="fas fa-clock"></i> Durasi: <?= $item['durasi_menit'] ?> menit
                                </div>
                                <div class="text-xs text-gray-600">
                                    <i class="fas fa-user"></i> Dibuat oleh: <?= $item['nama_creator'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <?php 
                                $now = time();
                                $waktuMulai = strtotime($item['waktu_mulai']);
                                $waktuSelesai = strtotime($item['waktu_selesai']);
                                $sudahMengerjakan = $item['sudah_mengerjakan'] ?? false;
                                ?>

                                <?php if ($sudahMengerjakan): ?>
                                    <a href="<?= base_url('siswa/ulangan/hasil/' . $item['id']) ?>" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-eye fa-sm"></i>
                                        Lihat Hasil
                                    </a>
                                <?php elseif ($now < $waktuMulai): ?>
                                    <span class="badge bg-warning">Belum Dimulai</span>
                                <?php elseif ($now > $waktuSelesai): ?>
                                    <span class="badge bg-danger">Sudah Berakhir</span>
                                <?php else: ?>
                                    <a href="<?= base_url('siswa/ulangan/kerjakan/' . $item['id']) ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit fa-sm"></i>
                                        Kerjakan
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($ulangan)): ?>
        <div class="text-center py-4">
            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-500">Belum ada ulangan</h5>
            <p class="text-gray-400">Guru belum membuat ulangan untuk kelas Anda</p>
        </div>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-12">
            <a href="<?= base_url('siswa/ulangan/riwayat') ?>" class="btn btn-info btn-sm">
                <i class="fas fa-history fa-sm"></i> Riwayat Ulangan
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 