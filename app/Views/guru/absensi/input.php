<?= $this->extend('guru/layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('guru/absensi/hari/' . $hari_absensi['jadwal_id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Hari
                        </a>
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

                    <div class="mb-4">
                        <h5>Informasi Absensi</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($hari_absensi['tanggal'])) ?></p>
                                <p><strong>Mata Pelajaran:</strong> <?= esc($jadwal['nama_mata_pelajaran']) ?></p>
                                <p><strong>Kelas:</strong> <?= esc($jadwal['nama_kelas']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Hari:</strong> <?= esc($jadwal['hari']) ?></p>
                                <p><strong>Jam:</strong> <?= esc($jadwal['jam_mulai']) ?> - <?= esc($jadwal['jam_selesai']) ?></p>
                                <?php if (!empty($hari_absensi['keterangan'])): ?>
                                    <p><strong>Keterangan:</strong> <?= esc($hari_absensi['keterangan']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <form action="<?= base_url('guru/absensi/store-absensi') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="hari_absensi_id" value="<?= $hari_absensi['id'] ?>">
                        <input type="hidden" name="jadwal_id" value="<?= $hari_absensi['jadwal_id'] ?>">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Status Kehadiran</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($siswa as $item): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($item['nis']) ?></td>
                                            <td><?= esc($item['full_name']) ?></td>
                                            <td>
                                                <input type="hidden" name="siswa_id[]" value="<?= $item['id'] ?>">
                                                <?php 
                                                    // Set default to Hadir if no existing data
                                                    $currentStatus = isset($absensi[$item['id']]) ? $absensi[$item['id']]['status'] : 'Hadir';
                                                ?>
                                                <select class="form-select" name="status[]" required>
                                                    <option value="Hadir" <?= ($currentStatus == 'Hadir') ? 'selected' : '' ?>>Hadir</option>
                                                    <option value="Sakit" <?= ($currentStatus == 'Sakit') ? 'selected' : '' ?>>Sakit</option>
                                                    <option value="Izin" <?= ($currentStatus == 'Izin') ? 'selected' : '' ?>>Izin</option>
                                                    <option value="Alpha" <?= ($currentStatus == 'Alpha') ? 'selected' : '' ?>>Alpha</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="keterangan[]" 
                                                       value="<?= isset($absensi[$item['id']]) ? esc($absensi[$item['id']]['keterangan']) : '' ?>"
                                                       placeholder="Keterangan (opsional)">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Presensi
                            </button>
                            <button type="button" class="btn btn-success" onclick="setAllStatus('Hadir')">
                                <i class="fas fa-check"></i> Semua Hadir
                            </button>
                            <button type="button" class="btn btn-warning" onclick="clearAll()">
                                <i class="fas fa-eraser"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setAllStatus(status) {
    const selects = document.querySelectorAll('select[name="status[]"]');
    selects.forEach(select => {
        select.value = status;
    });
}

function clearAll() {
    const selects = document.querySelectorAll('select[name="status[]"]');
    const inputs = document.querySelectorAll('input[name="keterangan[]"]');
    
    selects.forEach(select => {
        select.value = '';
    });
    
    inputs.forEach(input => {
        input.value = '';
    });
}
</script>
<?= $this->endSection() ?>