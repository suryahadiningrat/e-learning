<?= $this->extend('guru/layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?= $title ?></h3>
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download me-1"></i> Rekap Presensi
                    </button>
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

                    <?php if (isset($error)): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-1"></i> <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if (!empty($kelasData)): ?>
                            <?php foreach ($kelasData as $kelas): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= esc($kelas['nama_kelas']) ?></h5>
                                            <p class="card-text text-muted mb-2">
                                                <strong><?= esc($kelas['nama_jurusan']) ?></strong>
                                            </p>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">Mata Pelajaran yang Diajar:</small>
                                                <?php foreach ($kelas['mata_pelajaran'] as $mapel): ?>
                                                    <span class="badge bg-primary mb-1"><?= esc($mapel['nama']) ?></span>
                                                    <small class="text-muted d-block"><?= esc($mapel['hari']) ?>, <?= esc($mapel['jam']) ?></small>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <div class="mt-auto">
                                                <a href="<?= base_url('guru/absensi/jadwal/' . $kelas['kelas_id']) ?>" 
                                                   class="btn btn-primary btn-block">
                                                    <i class="fas fa-edit"></i> Input Presensi
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
                                    Anda belum memiliki jadwal mengajar.
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
                <h5 class="modal-title" id="exportModalLabel">Export Data Presensi</h5>
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
                            <?php foreach ($kelasData ?? [] as $kelas_item): ?>
                                <option value="<?= $kelas_item['kelas_id'] ?>" <?= (($_GET['kelas_id'] ?? '') == $kelas_item['kelas_id']) ? 'selected' : '' ?>>
                                    <?= $kelas_item['nama_kelas'] ?> - <?= $kelas_item['nama_jurusan'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Info:</strong> Pilih format export yang diinginkan
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="format" value="excel" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <button type="submit" name="format" value="pdf" class="btn btn-danger" formaction="<?= base_url('guru/absensi/export-pdf') ?>">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>