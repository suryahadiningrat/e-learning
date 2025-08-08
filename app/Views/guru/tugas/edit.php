<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0"><?= $title ?></h4>
                            <p class="text-muted mb-0">Tugas: <?= esc($tugas['nama_tugas']) ?></p>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('guru/tugas') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('guru/tugas/update/' . $tugas['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nama_tugas" class="form-label">Nama Tugas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_tugas" name="nama_tugas" 
                                           value="<?= old('nama_tugas', $tugas['nama_tugas']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="jadwal_id" class="form-label">Jadwal <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jadwal_id" name="jadwal_id" required>
                                        <option value="">Pilih Jadwal</option>
                                        <?php foreach ($jadwal as $item): ?>
                                            <option value="<?= $item['jadwal_id'] ?>" <?= old('jadwal_id', $tugas['jadwal_id']) == $item['jadwal_id'] ? 'selected' : '' ?>>
                                                <?= esc($item['nama_mata_pelajaran']) ?> - <?= esc($item['nama_kelas']) ?> 
                                                (<?= esc($item['hari']) ?> <?= date('H:i', strtotime($item['jam_mulai'])) ?>-<?= date('H:i', strtotime($item['jam_selesai'])) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Hanya jadwal mata pelajaran yang Anda ampu</div>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Tugas</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                              placeholder="Masukkan deskripsi tugas..."><?= old('deskripsi', $tugas['deskripsi']) ?></textarea>
                                    <div class="form-text">Maksimal 1000 karakter</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Deadline</label>
                                    <input type="datetime-local" class="form-control" id="deadline" name="deadline" 
                                           value="<?= old('deadline', $tugas['deadline'] ? date('Y-m-d\TH:i', strtotime($tugas['deadline'])) : '') ?>">
                                    <div class="form-text">Opsional - kosongkan jika tidak ada deadline</div>
                                </div>

                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Detail Tugas</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-3">
                                            <li><strong>Dibuat:</strong><br>
                                                <small><?= date('d M Y H:i', strtotime($tugas['created_at'])) ?></small>
                                            </li>
                                            <li class="mt-2"><strong>Diupdate:</strong><br>
                                                <small><?= date('d M Y H:i', strtotime($tugas['updated_at'])) ?></small>
                                            </li>
                                        </ul>
                                        <hr>
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-info-circle text-info"></i> Nama tugas minimal 3 karakter</li>
                                            <li><i class="fas fa-info-circle text-info"></i> Hati-hati mengubah jadwal</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('guru/tugas') ?>" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Tugas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Select2 for better dropdown experience
    $('#jadwal_id').select2({
        placeholder: 'Pilih Jadwal',
        allowClear: true
    });
});
</script>
<?= $this->endSection() ?>
