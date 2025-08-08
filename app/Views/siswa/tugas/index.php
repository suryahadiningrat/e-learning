<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?= count($tugas ?? []) ?></h5>
                            <p class="card-text mb-0">Total Tugas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?= count(array_filter($tugas ?? [], fn($t) => !empty($t['pengumpulan_id']))) ?></h5>
                            <p class="card-text mb-0">Sudah Dikumpul</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?= count(array_filter($tugas ?? [], fn($t) => empty($t['pengumpulan_id']))) ?></h5>
                            <p class="card-text mb-0">Belum Dikumpul</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?= count(array_filter($tugas ?? [], fn($t) => $t['deadline'] && strtotime($t['deadline']) < time() && empty($t['pengumpulan_id']))) ?></h5>
                            <p class="card-text mb-0">Terlambat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0"><?= $title ?></h4>
                            <p class="text-muted mb-0">Daftar tugas yang harus dikerjakan</p>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="filterTugas('semua')">Semua</button>
                                <button type="button" class="btn btn-outline-warning" onclick="filterTugas('belum')">Belum Dikumpul</button>
                                <button type="button" class="btn btn-outline-success" onclick="filterTugas('sudah')">Sudah Dikumpul</button>
                                <button type="button" class="btn btn-outline-danger" onclick="filterTugas('terlambat')">Terlambat</button>
                            </div>
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

                    <?php if (empty($tugas)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada tugas</h5>
                            <p class="text-muted">Saat ini tidak ada tugas yang tersedia untuk Anda</p>
                        </div>
                    <?php else: ?>
                        <div class="row" id="tugasContainer">
                            <?php foreach ($tugas as $item): ?>
                                <div class="col-md-6 col-lg-4 mb-4 tugas-card" 
                                     data-status="<?= empty($item['pengumpulan_id']) ? 'belum' : 'sudah' ?>"
                                     data-terlambat="<?= ($item['deadline'] && strtotime($item['deadline']) < time() && empty($item['pengumpulan_id'])) ? 'ya' : 'tidak' ?>">
                                    <div class="card h-100 <?= empty($item['pengumpulan_id']) ? 'border-warning' : 'border-success' ?>">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1"><?= esc($item['nama_tugas']) ?></h6>
                                                    <small class="text-muted">
                                                        <?= esc($item['nama_mata_pelajaran']) ?> - <?= esc($item['nama_guru']) ?>
                                                    </small>
                                                </div>
                                                <?php if (!empty($item['pengumpulan_id'])): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Dikumpul
                                                    </span>
                                                <?php elseif ($item['deadline'] && strtotime($item['deadline']) < time()): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-clock"></i> Terlambat
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-hourglass-half"></i> Pending
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!empty($item['deskripsi'])): ?>
                                                <p class="card-text text-muted">
                                                    <?= esc(substr($item['deskripsi'], 0, 100)) ?><?= strlen($item['deskripsi']) > 100 ? '...' : '' ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> <?= esc($item['hari']) ?> 
                                                    <?= date('H:i', strtotime($item['jam_mulai'])) ?>-<?= date('H:i', strtotime($item['jam_selesai'])) ?>
                                                </small>
                                            </div>

                                            <?php if ($item['deadline']): ?>
                                                <div class="alert alert-<?= (strtotime($item['deadline']) < time()) ? 'danger' : 'info' ?> py-2 mb-3">
                                                    <small>
                                                        <i class="fas fa-clock"></i> 
                                                        <strong>Deadline:</strong> <?= date('d M Y H:i', strtotime($item['deadline'])) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($item['pengumpulan_id'])): ?>
                                                <div class="alert alert-success py-2">
                                                    <small>
                                                        <i class="fas fa-check-circle"></i> 
                                                        Dikumpul: <?= date('d M Y H:i', strtotime($item['submitted_at'])) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-grid gap-2">
                                                <?php if (empty($item['pengumpulan_id'])): ?>
                                                    <button class="btn btn-primary" onclick="kumpulTugas(<?= $item['id'] ?>, '<?= esc($item['nama_tugas']) ?>')">
                                                        <i class="fas fa-upload"></i> Kumpul Tugas
                                                    </button>
                                                <?php else: ?>
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <a href="<?= base_url('siswa/tugas/detail/' . $item['id']) ?>" class="btn btn-success btn-sm w-100">
                                                                <i class="fas fa-eye"></i> Detail
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <a href="<?= esc($item['link_tugas']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                                                <i class="fas fa-eye"></i> Lihat Tugas
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <button class="btn btn-warning btn-sm w-100" onclick="editPengumpulan(<?= $item['pengumpulan_id'] ?>, '<?= esc($item['nama_tugas']) ?>', '<?= esc($item['link_tugas']) ?>', '<?= esc($item['catatan']) ?>')">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kumpul Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('siswa/tugas/submit') ?>" method="POST" id="submitForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="tugas_id" id="submitTugasId">
                    <div class="mb-3">
                        <label class="form-label">Nama Tugas:</label>
                        <p id="submitNamaTugas" class="fw-bold"></p>
                    </div>
                    <div class="mb-3">
                        <label for="link_tugas" class="form-label">Link Tugas <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="link_tugas" id="link_tugas" required 
                               placeholder="https://...">
                        <div class="form-text">
                            Masukkan link Google Drive, Dropbox, GitHub, atau platform lainnya
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3" 
                                  placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Pastikan link dapat diakses oleh guru Anda. Gunakan "Share" dengan akses publik atau spesifik ke email guru.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kumpul Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengumpulan Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('siswa/tugas/update') ?>" method="POST" id="editForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="pengumpulan_id" id="editPengumpulanId">
                    <div class="mb-3">
                        <label class="form-label">Nama Tugas:</label>
                        <p id="editNamaTugas" class="fw-bold"></p>
                    </div>
                    <div class="mb-3">
                        <label for="edit_link_tugas" class="form-label">Link Tugas <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="link_tugas" id="edit_link_tugas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Nothing special needed for now
});

function filterTugas(status) {
    // Remove active class from all buttons
    $('.btn-group .btn').removeClass('active');
    
    // Add active class to clicked button
    $(`[onclick="filterTugas('${status}')"]`).addClass('active');
    
    const cards = $('.tugas-card');
    
    cards.show(); // Show all first
    
    switch(status) {
        case 'belum':
            cards.filter('[data-status="sudah"]').hide();
            break;
        case 'sudah':
            cards.filter('[data-status="belum"]').hide();
            break;
        case 'terlambat':
            cards.filter('[data-terlambat="tidak"]').hide();
            break;
        case 'semua':
        default:
            // Show all - already shown above
            break;
    }
}

function kumpulTugas(id, nama) {
    $('#submitTugasId').val(id);
    $('#submitNamaTugas').text(nama);
    $('#link_tugas').val('');
    $('#keterangan').val('');
    $('#submitModal').modal('show');
}

function editPengumpulan(id, nama, link, keterangan) {
    $('#editPengumpulanId').val(id);
    $('#editNamaTugas').text(nama);
    $('#edit_link_tugas').val(link);
    $('#edit_keterangan').val(keterangan);
    $('#editModal').modal('show');
}

// Form validation
$('#submitForm').on('submit', function(e) {
    const link = $('#link_tugas').val();
    if (!link.startsWith('http://') && !link.startsWith('https://')) {
        e.preventDefault();
        alert('Link harus diawali dengan http:// atau https://');
        return false;
    }
});

$('#editForm').on('submit', function(e) {
    const link = $('#edit_link_tugas').val();
    if (!link.startsWith('http://') && !link.startsWith('https://')) {
        e.preventDefault();
        alert('Link harus diawali dengan http:// atau https://');
        return false;
    }
});
</script>
<?= $this->endSection() ?>
