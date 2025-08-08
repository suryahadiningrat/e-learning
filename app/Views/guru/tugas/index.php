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
                            <p class="text-muted mb-0">Kelola tugas untuk mata pelajaran yang Anda ampu</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fas fa-plus"></i> Tambah Tugas
                            </button>
                        </div>
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

                    <div class="table-responsive">
                        <table class="table table-striped" id="tugasTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Tugas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Deadline</th>
                                    <th>Pengumpulan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tugas)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">Belum ada tugas yang dibuat</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                                <i class="fas fa-plus"></i> Buat Tugas Pertama
                                            </button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tugas as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <div class="d-flex align-items-start">
                                                    <div>
                                                        <strong><?= esc($item['nama_tugas']) ?></strong>
                                                        <?php if (!empty($item['deskripsi'])): ?>
                                                            <br><small class="text-muted"><?= esc(substr($item['deskripsi'], 0, 50)) ?><?= strlen($item['deskripsi']) > 50 ? '...' : '' ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= esc($item['nama_mata_pelajaran']) ?></td>
                                            <td><?= esc($item['nama_kelas']) ?></td>
                                            <td>
                                                <?php if ($item['deadline']): ?>
                                                    <span class="text-<?= (strtotime($item['deadline']) < time()) ? 'danger' : 'success' ?>">
                                                        <?= date('d M Y', strtotime($item['deadline'])) ?><br>
                                                        <small><?= date('H:i', strtotime($item['deadline'])) ?></small>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= $item['total_pengumpulan'] ?? 0 ?></span>
                                                <small class="text-muted d-block">pengumpulan</small>
                                            </td>
                                            <td>
                                                <?php if ($item['deadline'] && strtotime($item['deadline']) < time()): ?>
                                                    <span class="badge bg-danger">Lewat Deadline</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('guru/tugas/detail/' . $item['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-warning" onclick="editTugas(<?= $item['id'] ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTugas(<?= $item['id'] ?>, '<?= esc($item['nama_tugas']) ?>')" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/tugas/store') ?>" method="POST" id="addForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_tugas" class="form-label">Nama Tugas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_tugas" name="nama_tugas" required>
                    </div>

                    <div class="mb-3">
                        <label for="jadwal_id" class="form-label">Jadwal <span class="text-danger">*</span></label>
                        <select class="form-select" id="jadwal_id" name="jadwal_id" required>
                            <option value="">Pilih Jadwal</option>
                            <?php foreach ($jadwal as $item): ?>
                                <option value="<?= $item['jadwal_id'] ?>">
                                    <?= esc($item['nama_mata_pelajaran']) ?> - <?= esc($item['nama_kelas']) ?> 
                                    (<?= esc($item['hari']) ?> <?= date('H:i', strtotime($item['jam_mulai'])) ?>-<?= date('H:i', strtotime($item['jam_selesai'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Hanya jadwal mata pelajaran yang Anda ampu</div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Tugas</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" 
                                  placeholder="Masukkan deskripsi tugas..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="datetime-local" class="form-control" id="deadline" name="deadline">
                        <div class="form-text">Opsional - kosongkan jika tidak ada deadline</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div id="editContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tugas <strong id="deleteName"></strong>?</p>
                <p class="text-danger"><small>Semua pengumpulan tugas juga akan terhapus!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="" method="POST" id="deleteForm" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Destroy existing DataTable if exists
    if ($.fn.DataTable.isDataTable('#tugasTable')) {
        $('#tugasTable').DataTable().destroy();
    }
    
    // DataTable
    $('#tugasTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[0, 'desc']], // Sort by created date desc
        columnDefs: [
            { targets: [7], orderable: false } // Kolom Aksi (index 7)
        ]
    });

    // Set default deadline to 1 week from now
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    nextWeek.setHours(23, 59);
    $('#deadline').val(nextWeek.toISOString().slice(0, 16));

    // Select2 for better dropdown
    $('#jadwal_id').select2({
        placeholder: 'Pilih Jadwal',
        allowClear: true,
        dropdownParent: $('#addModal')
    });
});

function editTugas(id) {
    // Load edit form via AJAX
    $.get('<?= base_url('guru/tugas/edit') ?>/' + id, function(data) {
        $('#editContent').html(data);
        $('#editForm').attr('action', '<?= base_url('guru/tugas/update') ?>/' + id);
        $('#editModal').modal('show');
        
        // Reinitialize select2 for edit form
        $('#editModal select').select2({
            placeholder: 'Pilih Jadwal',
            allowClear: true,
            dropdownParent: $('#editModal')
        });
    }).fail(function() {
        alert('Gagal memuat form edit');
    });
}

function deleteTugas(id, name) {
    $('#deleteName').text(name);
    $('#deleteForm').attr('action', '<?= base_url('guru/tugas/delete') ?>/' + id);
    $('#deleteModal').modal('show');
}
</script>
<?= $this->endSection() ?>
