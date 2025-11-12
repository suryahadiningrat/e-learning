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
                    <a href="<?= base_url('admin/absensi/kelas/' . $jadwal['id']) ?>">
                        <?= $jadwal['nama_jurusan'] ?>
                    </a>
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
                    <?= date('d M Y', strtotime($hari_absensi['tanggal'])) ?>
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

    <!-- Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Absensi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tanggal:</strong></td>
                            <td><?= date('d/m/Y', strtotime($hari_absensi['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran:</strong></td>
                            <td><?= $jadwal['nama'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas:</strong></td>
                            <td><?= $jadwal['nama_kelas'] ?></td>
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

    <!-- Absensi Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi Siswa</h6>
            <button type="button" class="btn btn-success btn-sm" 
                    onclick="exportHari(<?= $hari_absensi_id ?>, '<?= date('d M Y', strtotime($hari_absensi['tanggal'])) ?>')">
                <i class="fas fa-file-excel"></i> Export
            </button>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/absensi/store-absensi') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="hari_absensi_id" value="<?= $hari_absensi_id ?>">
                <input type="hidden" name="jadwal_id" value="<?= $hari_absensi['jadwal_id'] ?>">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="20%">Status Kehadiran</th>
                                <th width="30%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($siswa as $key => $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $item['nis'] ?></td>
                                    <td><?= $item['full_name'] ?></td>
                                    <td>
                                        <input type="hidden" name="siswa_id[]" value="<?= $item['id'] ?>">
                                        <select class="form-select form-select-sm" name="status[]" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Hadir" <?= (isset($absensi[$key]) && $absensi[$key]['status'] == 'Hadir') ? 'selected' : '' ?>>Hadir</option>
                                            <option value="Sakit" <?= (isset($absensi[$key]) && $absensi[$key]['status'] == 'Sakit') ? 'selected' : '' ?>>Sakit</option>
                                            <option value="Izin" <?= (isset($absensi[$key]) && $absensi[$key]['status'] == 'Izin') ? 'selected' : '' ?>>Izin</option>
                                            <option value="Alpha" <?= (isset($absensi[$key]) && $absensi[$key]['status'] == 'Alpha') ? 'selected' : '' ?>>Alpha</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="keterangan[]" 
                                               value="<?= isset($absensi[$key]) ? $absensi[$key]['keterangan'] : '' ?>"
                                               placeholder="Keterangan (opsional)">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <a href="<?= base_url('admin/absensi/hari/' . $hari_absensi['jadwal_id']) ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportHari(hariId, tanggal) {
    if (confirm('Export data presensi untuk tanggal ' + tanggal + '?')) {
        window.location.href = '<?= base_url('admin/absensi/exportHari/') ?>' + hariId;
    }
}

// Auto-save functionality (optional)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const statusSelects = document.querySelectorAll('select[name="status[]"]');
    
    // Add change event listeners for quick feedback
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const status = this.value;
            
            // Add visual feedback based on status
            row.classList.remove('table-success', 'table-warning', 'table-info', 'table-danger');
            
            switch(status) {
                case 'Hadir':
                    row.classList.add('table-success');
                    break;
                case 'Sakit':
                    row.classList.add('table-warning');
                    break;
                case 'Izin':
                    row.classList.add('table-info');
                    break;
                case 'Alpha':
                    row.classList.add('table-danger');
                    break;
            }
        });
        
        // Trigger change event for existing values
        if (select.value) {
            select.dispatchEvent(new Event('change'));
        }
    });
});
</script>

<?= $this->endSection() ?>