<?= $this->extend('siswa/layout') ?>

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
                            <a href="<?= base_url('siswa/tugas') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
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

                            <!-- Informasi Jadwal -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar"></i> Informasi Mata Pelajaran
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
                                                    <td><strong>Guru:</strong></td>
                                                    <td><?= esc($tugas['nama_guru']) ?></td>
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

                            <!-- Status Pengumpulan -->
                            <?php if (!empty($pengumpulan)): ?>
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-check-circle"></i> Status Pengumpulan
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td><strong>Status:</strong></td>
                                                        <td>
                                                            <span class="badge bg-<?= $pengumpulan['status'] == 'selesai' ? 'success' : ($pengumpulan['status'] == 'terlambat' ? 'danger' : 'warning') ?>">
                                                                <?= ucfirst($pengumpulan['status']) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Link Tugas:</strong></td>
                                                        <td>
                                                            <a href="<?= esc($pengumpulan['link_tugas']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-external-link-alt"></i> Buka Link
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td><strong>Tanggal Pengumpulan:</strong></td>
                                                        <td><?= date('d M Y H:i', strtotime($pengumpulan['created_at'])) ?></td>
                                                    </tr>
                                                    <?php if ($pengumpulan['catatan']): ?>
                                                        <tr>
                                                            <td><strong>Keterangan:</strong></td>
                                                            <td><?= esc($pengumpulan['catatan']) ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </table>
                                            </div>
                                        </div>

                                        <?php if ($tugas['deadline'] && strtotime($pengumpulan['created_at']) > strtotime($tugas['deadline'])): ?>
                                            <div class="alert alert-warning mt-3">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                <strong>Catatan:</strong> Tugas dikumpulkan setelah deadline (<?= date('d M Y H:i', strtotime($tugas['deadline'])) ?>)
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-3">
                                            <button class="btn btn-warning" onclick="editPengumpulan()">
                                                <i class="fas fa-edit"></i> Edit Pengumpulan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <!-- Deadline Info -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Informasi Deadline</h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($tugas['deadline']): ?>
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <i class="fas fa-clock fa-2x text-<?= (strtotime($tugas['deadline']) < time()) ? 'danger' : 'warning' ?>"></i>
                                            </div>
                                            <h5 class="text-<?= (strtotime($tugas['deadline']) < time()) ? 'danger' : 'warning' ?>">
                                                <?= date('d M Y', strtotime($tugas['deadline'])) ?>
                                            </h5>
                                            <p class="text-<?= (strtotime($tugas['deadline']) < time()) ? 'danger' : 'warning' ?>">
                                                <?= date('H:i', strtotime($tugas['deadline'])) ?>
                                            </p>
                                            <?php if (strtotime($tugas['deadline']) < time()): ?>
                                                <span class="badge bg-danger">Deadline Terlewat</span>
                                            <?php else: ?>
                                                <div id="countdown" class="mt-2"></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <i class="fas fa-infinity fa-2x mb-3"></i>
                                            <p>Tidak ada deadline</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Action Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Aksi</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($pengumpulan)): ?>
                                        <div class="d-grid">
                                            <button class="btn btn-primary btn-lg" onclick="kumpulTugas()">
                                                <i class="fas fa-upload"></i> Kumpul Tugas
                                            </button>
                                        </div>
                                        <?php if ($tugas['deadline'] && strtotime($tugas['deadline']) < time()): ?>
                                            <div class="alert alert-warning mt-3">
                                                <small>
                                                    <i class="fas fa-exclamation-triangle"></i> 
                                                    Deadline sudah lewat, tugas akan ditandai terlambat.
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="d-grid gap-2">
                                            <a href="<?= esc($pengumpulan['link_tugas']) ?>" target="_blank" class="btn btn-success">
                                                <i class="fas fa-eye"></i> Lihat Tugas Saya
                                            </a>
                                            <button class="btn btn-warning" onclick="editPengumpulan()">
                                                <i class="fas fa-edit"></i> Edit Pengumpulan
                                            </button>
                                        </div>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Anda masih dapat mengedit pengumpulan jika diperlukan.
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Detail Tugas -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Detail Tugas</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Dibuat:</strong><br>
                                            <small><?= date('d M Y H:i', strtotime($tugas['created_at'])) ?></small>
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
                    <input type="hidden" name="tugas_id" value="<?= $tugas['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Tugas:</label>
                        <p class="fw-bold"><?= esc($tugas['nama_tugas']) ?></p>
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
                    <?php if ($tugas['deadline'] && strtotime($tugas['deadline']) < time()): ?>
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Perhatian:</strong> Deadline sudah lewat, tugas akan ditandai sebagai terlambat.
                            </small>
                        </div>
                    <?php endif; ?>
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
                    <input type="hidden" name="pengumpulan_id" value="<?= $pengumpulan['id'] ?? '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Tugas:</label>
                        <p class="fw-bold"><?= esc($tugas['nama_tugas']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label for="edit_link_tugas" class="form-label">Link Tugas <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="link_tugas" id="edit_link_tugas" required 
                               value="<?= esc($pengumpulan['link_tugas'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"><?= esc($pengumpulan['catatan'] ?? '') ?></textarea>
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
    <?php if ($tugas['deadline'] && strtotime($tugas['deadline']) > time()): ?>
        // Countdown timer
        const deadline = new Date('<?= date('c', strtotime($tugas['deadline'])) ?>');
        
        function updateCountdown() {
            const now = new Date();
            const diff = deadline - now;
            
            if (diff > 0) {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                
                let countdownText = '';
                if (days > 0) countdownText += days + ' hari ';
                if (hours > 0) countdownText += hours + ' jam ';
                countdownText += minutes + ' menit';
                
                $('#countdown').html(`
                    <div class="badge bg-warning text-dark">
                        ${countdownText} lagi
                    </div>
                `);
            } else {
                $('#countdown').html('<span class="badge bg-danger">Deadline Terlewat</span>');
                location.reload();
            }
        }
        
        updateCountdown();
        setInterval(updateCountdown, 60000); // Update every minute
    <?php endif; ?>
});

function kumpulTugas() {
    $('#submitModal').modal('show');
}

function editPengumpulan() {
    $('#editModal').modal('show');
}

// Form validation
$('#submitForm, #editForm').on('submit', function(e) {
    const linkInput = $(this).find('input[name="link_tugas"]');
    const link = linkInput.val();
    if (!link.startsWith('http://') && !link.startsWith('https://')) {
        e.preventDefault();
        alert('Link harus diawali dengan http:// atau https://');
        linkInput.focus();
        return false;
    }
});
</script>
<?= $this->endSection() ?>
