<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Absensi</h1>
        <div>
            <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </button>
            <a href="<?= base_url('admin/absensi/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm"></i> Tambah Absensi
            </a>
        </div>
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($absensi ?? [] as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= date('d/m/Y', strtotime($item['tanggal'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= $item['nama_siswa'] ?></strong><br>
                                    <small class="text-muted">NIS: <?= $item['nis'] ?></small>
                                </td>
                                <td>
                                    <?= $item['nama_kelas'] ?><br>
                                    <small class="text-muted"><?= $item['nama_jurusan'] ?></small>
                                </td>
                                <td>
                                    <strong><?= $item['nama_mata_pelajaran'] ?></strong>
                                </td>
                                <td>
                                    <?= $item['nama_guru'] ?>
                                </td>
                                <td>
                                    <?php
                                    $hariColors = [
                                        'Senin' => 'primary',
                                        'Selasa' => 'success',
                                        'Rabu' => 'warning',
                                        'Kamis' => 'info',
                                        'Jumat' => 'secondary',
                                        'Sabtu' => 'dark'
                                    ];
                                    $color = $hariColors[$item['hari']] ?? 'primary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= $item['hari'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $item['jam_mulai'] ?> - <?= $item['jam_selesai'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'Hadir' => 'success',
                                        'Sakit' => 'warning',
                                        'Izin' => 'info',
                                        'Alpha' => 'danger'
                                    ];
                                    $color = $statusColors[$item['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= $item['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['keterangan']): ?>
                                        <span class="text-muted" title="<?= $item['keterangan'] ?>">
                                            <?= strlen($item['keterangan']) > 30 ? substr($item['keterangan'], 0, 30) . '...' : $item['keterangan'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/absensi/edit/' . $item['id']) ?>" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteAbsensi(<?= $item['id'] ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
            <form action="<?= base_url('admin/absensi/export') ?>" method="get">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="export_start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="export_start_date" name="start_date" 
                                       value="<?= esc($filter['start_date'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="export_end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="export_end_date" name="end_date" 
                                       value="<?= esc($filter['end_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="export_kelas_id" class="form-label">Kelas</label>
                        <select class="form-select" id="export_kelas_id" name="kelas_id">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelas ?? [] as $kelas_item): ?>
                                <option value="<?= $kelas_item['id'] ?>" 
                                        <?= ($filter['kelas_id'] ?? '') == $kelas_item['id'] ? 'selected' : '' ?>>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data absensi ini?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

function deleteAbsensi(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `<?= base_url('admin/absensi/delete') ?>/${id}`;
    modal.show();
}
</script>
<?= $this->endSection() ?> 