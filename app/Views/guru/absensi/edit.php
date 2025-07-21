<?php
// Copy of admin/absensi/edit.php, but all URLs and actions use 'guru/absensi' instead of 'admin/absensi'.
?>
<?= $this->extend('guru/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Absensi</h1>
        <a href="<?= base_url('guru/absensi') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Absensi</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('guru/absensi/update/' . $absensi['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Filter Kelas</label>
                            <select class="form-select" id="kelas_id" name="kelas_id">
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas ?? [] as $kelas_item): ?>
                                    <option value="<?= $kelas_item['id'] ?>" <?= (old('kelas_id', $absensi['kelas_id'] ?? '') == $kelas_item['id']) ? 'selected' : '' ?>>
                                        <?= $kelas_item['nama_kelas'] ?> - <?= $kelas_item['nama_jurusan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Pilih kelas untuk memfilter siswa dan jadwal</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="siswa_id" class="form-label">Siswa <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.siswa_id') ? 'is-invalid' : '' ?>" id="siswa_id" name="siswa_id" required>
                                <option value="">Pilih Siswa</option>
                            </select>
                            <?php if (session('errors.siswa_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.siswa_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jadwal_id" class="form-label">Jadwal <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.jadwal_id') ? 'is-invalid' : '' ?>" id="jadwal_id" name="jadwal_id" required>
                                <option value="">Pilih Jadwal</option>
                            </select>
                            <?php if (session('errors.jadwal_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jadwal_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?= session('errors.tanggal') ? 'is-invalid' : '' ?>" id="tanggal" name="tanggal" value="<?= old('tanggal', $absensi['tanggal'] ?? date('Y-m-d')) ?>" required>
                            <?php if (session('errors.tanggal')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tanggal') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Hadir" <?= (old('status', $absensi['status'] ?? '') == 'Hadir') ? 'selected' : '' ?>>Hadir</option>
                                <option value="Sakit" <?= (old('status', $absensi['status'] ?? '') == 'Sakit') ? 'selected' : '' ?>>Sakit</option>
                                <option value="Izin" <?= (old('status', $absensi['status'] ?? '') == 'Izin') ? 'selected' : '' ?>>Izin</option>
                                <option value="Alpha" <?= (old('status', $absensi['status'] ?? '') == 'Alpha') ? 'selected' : '' ?>>Alpha</option>
                            </select>
                            <?php if (session('errors.status')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.status') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control <?= session('errors.keterangan') ? 'is-invalid' : '' ?>" id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"><?= old('keterangan', $absensi['keterangan'] ?? '') ?></textarea>
                            <?php if (session('errors.keterangan')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.keterangan') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info" id="jadwal-info" style="display: none;">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Jadwal</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Mata Pelajaran:</strong> <span id="mata-pelajaran">-</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Hari:</strong> <span id="hari-jadwal">-</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Jam:</strong> <span id="jam-jadwal">-</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Guru:</strong> <span id="nama-guru">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
const currentAbsensi = {
    siswa_id: '<?= $absensi['siswa_id'] ?? '' ?>',
    jadwal_id: '<?= $absensi['jadwal_id'] ?? '' ?>',
    kelas_id: '<?= $absensi['kelas_id'] ?? '' ?>'
};
document.getElementById('kelas_id').addEventListener('change', function() {
    const kelasId = this.value;
    const siswaSelect = document.getElementById('siswa_id');
    const jadwalSelect = document.getElementById('jadwal_id');
    siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>';
    jadwalSelect.innerHTML = '<option value="">Pilih Jadwal</option>';
    if (kelasId) {
        fetch(`<?= base_url('guru/absensi/get-siswa-by-kelas') ?>/${kelasId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(siswa => {
                    const option = document.createElement('option');
                    option.value = siswa.id;
                    option.textContent = `${siswa.nis} - ${siswa.full_name}`;
                    if (siswa.id == currentAbsensi.siswa_id) {
                        option.selected = true;
                    }
                    siswaSelect.appendChild(option);
                });
            });
        fetch(`<?= base_url('guru/absensi/get-jadwal-by-kelas') ?>/${kelasId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(jadwal => {
                    const option = document.createElement('option');
                    option.value = jadwal.id;
                    option.textContent = `${jadwal.nama_mata_pelajaran} (${jadwal.hari} ${jadwal.jam_mulai}-${jadwal.jam_selesai})`;
                    option.dataset.mataPelajaran = jadwal.nama_mata_pelajaran;
                    option.dataset.hari = jadwal.hari;
                    option.dataset.jamMulai = jadwal.jam_mulai;
                    option.dataset.jamSelesai = jadwal.jam_selesai;
                    option.dataset.namaGuru = jadwal.nama_guru;
                    if (jadwal.id == currentAbsensi.jadwal_id) {
                        option.selected = true;
                        updateJadwalInfo(option);
                    }
                    jadwalSelect.appendChild(option);
                });
            });
    }
});
document.getElementById('jadwal_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    updateJadwalInfo(selectedOption);
});
function updateJadwalInfo(selectedOption) {
    const jadwalInfo = document.getElementById('jadwal-info');
    if (selectedOption && selectedOption.value) {
        document.getElementById('mata-pelajaran').textContent = selectedOption.dataset.mataPelajaran || '-';
        document.getElementById('hari-jadwal').textContent = selectedOption.dataset.hari || '-';
        document.getElementById('jam-jadwal').textContent = `${selectedOption.dataset.jamMulai || '-'} - ${selectedOption.dataset.jamSelesai || '-'}`;
        document.getElementById('nama-guru').textContent = selectedOption.dataset.namaGuru || '-';
        jadwalInfo.style.display = 'block';
    } else {
        jadwalInfo.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    if (currentAbsensi.kelas_id) {
        document.getElementById('kelas_id').value = currentAbsensi.kelas_id;
        setTimeout(() => {
            document.getElementById('kelas_id').dispatchEvent(new Event('change'));
        }, 100);
    }
});
</script>
<?= $this->endSection() ?> 