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
                        <a href="<?= base_url('guru/absensi/jadwal/' . $jadwal['kelas_id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHariModal">
                            <i class="fas fa-plus"></i> Buat Hari Absensi
                        </button>
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
                        <h5><?= esc($jadwal['nama_mata_pelajaran']) ?></h5>
                        <p class="text-muted">
                            Kelas: <?= esc($jadwal['nama_kelas']) ?> | 
                            Hari: <?= esc($jadwal['hari']) ?> | 
                            Jam: <?= esc($jadwal['jam_mulai']) ?> - <?= esc($jadwal['jam_selesai']) ?>
                        </p>
                    </div>

                    <div class="row">
                        <?php if (!empty($hari_absensi)): ?>
                            <?php foreach ($hari_absensi as $item): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">
                                                <?= date('d/m/Y', strtotime($item['tanggal'])) ?>
                                            </h5>
                                            <p class="card-text">
                                                <strong>Hari:</strong> <?= date('l', strtotime($item['tanggal'])) ?><br>
                                                <?php if (!empty($item['keterangan'])): ?>
                                                    <strong>Keterangan:</strong> <?= esc($item['keterangan']) ?>
                                                <?php endif; ?>
                                            </p>
                                            <div class="mt-auto">
                                                <a href="<?= base_url('guru/absensi/input/' . $item['id']) ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-edit"></i> Input Absensi
                                                </a>
                                                <a href="<?= base_url('guru/absensi/export-hari/' . $item['id']) ?>" 
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
                                    Belum ada hari absensi yang dibuat untuk jadwal ini. 
                                    Klik tombol "Buat Hari Absensi" untuk membuat hari absensi baru.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Hari Absensi -->
<div class="modal fade" id="createHariModal" tabindex="-1" aria-labelledby="createHariModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('guru/absensi/store-hari') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="createHariModalLabel">Buat Hari Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                  placeholder="Contoh: Materi Bab 1, Ulangan Harian, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Hari Absensi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>