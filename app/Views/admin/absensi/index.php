<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jurusan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($jurusan as $item): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <?= $item['kode_jurusan'] ?>
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $item['nama_jurusan'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="<?= base_url('admin/absensi/kelas/' . $item['id']) ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Lihat Kelas
                                    </a>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            onclick="exportJurusan(<?= $item['id'] ?>, '<?= $item['nama_jurusan'] ?>')">
                                        <i class="fas fa-file-excel"></i> Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="GET" action="<?= base_url('admin/absensi/export') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                    <div class="mb-3">
                        <label for="kelas_id" class="form-label">Kelas (Opsional)</label>
                        <select class="form-select" id="kelas_id" name="kelas_id">
                            <option value="">Semua Kelas</option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportJurusan(jurusanId, namaJurusan) {
    if (confirm('Export data absensi untuk jurusan ' + namaJurusan + '?')) {
        window.location.href = '<?= base_url('admin/absensi/exportJurusan/') ?>' + jurusanId;
    }
}

// Load kelas untuk modal export
document.addEventListener('DOMContentLoaded', function() {
    // Load semua kelas untuk dropdown export
    fetch('<?= base_url('admin/kelas/getAll') ?>')
        .then(response => response.json())
        .then(data => {
            const kelasSelect = document.getElementById('kelas_id');
            data.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas.id;
                option.textContent = kelas.nama_kelas + ' - ' + kelas.nama_jurusan;
                kelasSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading kelas:', error));
});
</script>

<?= $this->endSection() ?>