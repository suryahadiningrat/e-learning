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

                    <div class="row">
                        <?php if (!empty($jurusan)): ?>
                            <?php foreach ($jurusan as $item): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= esc($item['nama_jurusan']) ?></h5>
                                            <p class="card-text text-muted">
                                                Kode: <?= esc($item['kode_jurusan']) ?>
                                            </p>
                                            <div class="mt-auto">
                                                <a href="<?= base_url('guru/absensi/kelas/' . $item['id']) ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-arrow-right"></i> Lihat Kelas
                                                </a>
                                                <a href="<?= base_url('guru/absensi/export-jurusan/' . $item['id']) ?>" 
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
                                    Tidak ada jurusan yang memiliki jadwal yang Anda ajar.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/absensi/export') ?>" method="get">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="export_start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="export_start_date" name="start_date" value="<?= esc($_GET['start_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="export_end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="export_end_date" name="end_date" value="<?= esc($_GET['end_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="export_kelas_id" class="form-label">Kelas</label>
                        <select class="form-select" id="export_kelas_id" name="kelas_id">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelas ?? [] as $kelas_item): ?>
                                <option value="<?= $kelas_item['id'] ?>" <?= (($_GET['kelas_id'] ?? '') == $kelas_item['id']) ? 'selected' : '' ?>>
                                    <?= $kelas_item['nama_kelas'] ?> - <?= $kelas_item['nama_jurusan'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Info:</strong> File akan di-download dalam format Excel (.xlsx)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>