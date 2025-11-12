<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi') ?>">Presensi</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi/jadwal/' . $jadwal['kelas_id']) ?>">
                        <?= $jadwal['nama_kelas'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('admin/absensi/hari/' . $jadwal['id']) ?>">
                        <?= $jadwal['nama'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Tambah Hari
                </li>
            </ol>
        </nav>
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

    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Jadwal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Mata Pelajaran:</strong></td>
                            <td><?= $jadwal['nama'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas:</strong></td>
                            <td><?= $jadwal['nama_kelas'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jurusan:</strong></td>
                            <td><?= $jadwal['nama_jurusan'] ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Hari:</strong></td>
                            <td><?= $jadwal['hari'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Jam:</strong></td>
                            <td><?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Guru:</strong></td>
                            <td><?= $jadwal['nama_guru'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Hari Presensi</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/absensi/storeHari') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">
                                <i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="<?= old('tanggal') ?>" required>
                            <div class="form-text">Pilih tanggal pelaksanaan pembelajaran</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">
                        <i class="fas fa-sticky-note"></i> Keterangan (Opsional)
                    </label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                              placeholder="Keterangan tambahan (opsional)..."><?= old('keterangan') ?></textarea>
                    <div class="form-text">Catatan tambahan untuk pertemuan ini</div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/absensi/hari/' . $jadwal['id']) ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Hari Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').setAttribute('min', today);
    
    // Auto-focus on first input
    document.getElementById('tanggal').focus();
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tanggal = document.getElementById('tanggal').value;
        const pertemuan = document.getElementById('pertemuan').value;
        const materi = document.getElementById('materi').value.trim();
        
        if (!tanggal || !pertemuan || !materi) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        if (parseInt(pertemuan) < 1) {
            e.preventDefault();
            alert('Nomor pertemuan harus lebih dari 0!');
            return false;
        }
        
        // Confirm submission
        if (!confirm('Apakah Anda yakin ingin menyimpan hari absensi ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?= $this->endSection() ?>