<?= $this->extend('admin/layout') ?>

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
                            <a href="<?= base_url('admin/tugas') ?>" class="btn btn-secondary">
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

                    <form action="<?= base_url('admin/tugas/update/' . $tugas['id']) ?>" method="POST">
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
                                                - <?= esc($item['nama_guru']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Pilih jadwal untuk menentukan mata pelajaran, kelas, dan guru yang bertanggung jawab</div>
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
                                            <li><strong>Dibuat:</strong> <?= date('d M Y H:i', strtotime($tugas['created_at'])) ?></li>
                                            <li><strong>Diupdate:</strong> <?= date('d M Y H:i', strtotime($tugas['updated_at'])) ?></li>
                                            <li><strong>Pembuat:</strong> <?= esc($tugas['nama_pembuat']) ?></li>
                                        </ul>
                                        <hr>
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-info-circle text-info"></i> Nama tugas minimal 3 karakter</li>
                                            <li><i class="fas fa-info-circle text-info"></i> Hati-hati mengubah jadwal, akan mempengaruhi akses siswa</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('admin/tugas') ?>" class="btn btn-secondary">Batal</a>
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

<!-- Pengumpulan Tugas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload"></i> Pengumpulan Tugas
                    <span class="badge bg-secondary ms-2"><?= count($pengumpulan ?? []) ?> pengumpulan</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($pengumpulan)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada pengumpulan tugas</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped" id="pengumpulanTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Link Tugas</th>
                                    <th>Status</th>
                                    <th>Tgl Pengumpulan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengumpulan as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($item['nama_siswa']) ?></td>
                                        <td><?= esc($item['nis']) ?></td>
                                        <td>
                                            <a href="<?= esc($item['link_tugas']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> Lihat
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $item['status'] == 'selesai' ? 'success' : ($item['status'] == 'terlambat' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($item['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($item['submitted_at'])) ?></td>
                                        <td>
                                            <a href="<?= esc($item['link_tugas']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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

    // DataTable for pengumpulan
    $('#pengumpulanTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[5, 'desc']], // Sort by tanggal pengumpulan desc
        columnDefs: [
            { targets: [6], orderable: false }
        ]
    });
});
</script>
<?= $this->endSection() ?>
