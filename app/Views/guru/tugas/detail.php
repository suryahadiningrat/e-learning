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
                        </div>  
                        <div class="col-auto">
                            <a href="<?= base_url('guru/tugas') ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button class="btn btn-warning" onclick="editTugas(<?= $tugas['id'] ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h3 class="mb-3"><?= esc($tugas['nama_tugas']) ?></h3>
                                
                                <?php if (!empty($tugas['deskripsi'])): ?>
                                    <div class="alert alert-light">
                                        <h6>Deskripsi:</h6>
                                        <p class="mb-0"><?= nl2br(esc($tugas['deskripsi'])) ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light">
                                        <em>Tidak ada deskripsi tugas</em>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Jadwal Info -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar"></i> Informasi Jadwal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Mata Pelajaran:</strong></td>
                                                    <td><?= esc($tugas['nama_mata_pelajaran']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Kelas:</strong></td>
                                                    <td><?= esc($tugas['nama_kelas']) ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Hari:</strong></td>
                                                    <td><?= esc($tugas['hari']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Waktu:</strong></td>
                                                    <td><?= date('H:i', strtotime($tugas['jam_mulai'])) ?> - <?= date('H:i', strtotime($tugas['jam_selesai'])) ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Status Card -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Status Tugas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Deadline:</strong>
                                        <?php if ($tugas['deadline']): ?>
                                            <span class="d-block text-<?= (strtotime($tugas['deadline']) < time()) ? 'danger' : 'success' ?>">
                                                <?= date('d M Y H:i', strtotime($tugas['deadline'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted d-block">Tidak ada deadline</span>
                                        <?php endif; ?>
                                    </div>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h3 class="text-primary mb-0"><?= count($pengumpulan) ?></h3>
                                                <small class="text-muted">Total</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-success mb-0"><?= count(array_filter($pengumpulan, fn($p) => $p['status'] == 'reviewed')) ?></h3>
                                            <small class="text-muted">Reviewed</small>
                                        </div>
                                    </div>
                                    <div class="row text-center mt-2">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h3 class="text-warning mb-0"><?= count(array_filter($pengumpulan, fn($p) => $p['status'] == 'submitted')) ?></h3>
                                                <small class="text-muted">Submitted</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-danger mb-0"><?= count(array_filter($pengumpulan, fn($p) => $p['status'] == 'late')) ?></h3>
                                            <small class="text-muted">Late</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Info -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Detail Tugas</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Dibuat:</strong><br>
                                            <small><?= date('d M Y H:i', strtotime($tugas['created_at'])) ?></small>
                                        </li>
                                        <li class="mt-2"><strong>Diupdate:</strong><br>
                                            <small><?= date('d M Y H:i', strtotime($tugas['updated_at'])) ?></small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pengumpulan Tugas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload"></i> Daftar Pengumpulan Tugas
                </h5>
                <div>
                    <button class="btn btn-sm btn-success" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($pengumpulan)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada pengumpulan tugas</h5>
                        <p class="text-muted">Siswa belum mengumpulkan tugas ini</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped" id="pengumpulanTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Link Tugas</th>
                                    <th>Status</th>
                                    <th>Tgl Pengumpulan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengumpulan as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <?= strtoupper(substr($item['nama_siswa'], 0, 1)) ?>
                                                </div>
                                                <strong><?= esc($item['nama_siswa']) ?></strong>
                                            </div>
                                        </td>
                                        <td><?= esc($item['nis']) ?></td>
                                        <td>
                                            <a href="<?= esc($item['link_tugas']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> Lihat Link
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $item['status'] == 'reviewed' ? 'success' : ($item['status'] == 'late' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($item['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($item['submitted_at'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['submitted_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($tugas['deadline'] && strtotime($item['submitted_at']) > strtotime($tugas['deadline'])): ?>
                                                <small class="text-danger">
                                                    <i class="fas fa-clock"></i> 
                                                    Terlambat
                                                </small>
                                            <?php else: ?>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Tepat waktu
                                                </small>
                                            <?php endif; ?>
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

<!-- Edit Modal (similar to index but for AJAX content) -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be available
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        // DataTable for pengumpulan
        $('#pengumpulanTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            order: [[5, 'desc']], // Sort by tanggal pengumpulan desc
            columnDefs: [
                { targets: [6], orderable: false } // Keterangan column
            ]
        });
    } else {
        console.error('jQuery or DataTables not loaded');
    }
});

function editTugas(id) {
    // Redirect to edit page or load via AJAX
    window.location.href = '<?= base_url('guru/tugas/edit') ?>/' + id;
}

function exportData() {
    window.open('<?= base_url('guru/tugas/export/' . $tugas['id']) ?>', '_blank');
}
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
<?= $this->endSection() ?>
