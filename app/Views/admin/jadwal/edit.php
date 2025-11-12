<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Jadwal</h1>
        <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Jadwal</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/jadwal/update/' . ($jadwal['id'] ?? '')) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="guru_id" class="form-label">Guru <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.guru_id') ? 'is-invalid' : '' ?>" 
                                    id="guru_id" name="guru_id" required>
                                <option value="">Pilih Guru</option>
                                <?php foreach ($guru ?? [] as $guru_item): ?>
                                    <option value="<?= $guru_item['user_id'] ?>" 
                                            <?= (old('guru_id', $jadwal['guru_id'] ?? '') == $guru_item['user_id']) ? 'selected' : '' ?>>
                                        <?= $guru_item['full_name'] ?> (<?= $guru_item['bidang_studi'] ?? '-' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.guru_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.guru_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.kelas_id') ? 'is-invalid' : '' ?>" 
                                    id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas ?? [] as $kelas_item): ?>
                                    <option value="<?= $kelas_item['id'] ?>" 
                                            <?= (old('kelas_id', $jadwal['kelas_id'] ?? '') == $kelas_item['id']) ? 'selected' : '' ?>>
                                        <?= $kelas_item['nama_kelas'] ?> - <?= $kelas_item['nama_jurusan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.kelas_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.kelas_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.mata_pelajaran_id') ? 'is-invalid' : '' ?>" 
                                    id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php foreach ($mata_pelajaran ?? [] as $mapel): ?>
                                    <option value="<?= $mapel['id'] ?>" 
                                            <?= (old('mata_pelajaran_id', $jadwal['mata_pelajaran_id'] ?? '') == $mapel['id']) ? 'selected' : '' ?>>
                                        <?= $mapel['nama'] ?> (<?= $mapel['kode'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.mata_pelajaran_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.mata_pelajaran_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hari" class="form-label">Hari <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.hari') ? 'is-invalid' : '' ?>" 
                                    id="hari" name="hari" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin" <?= (old('hari', $jadwal['hari'] ?? '') == 'Senin') ? 'selected' : '' ?>>Senin</option>
                                <option value="Selasa" <?= (old('hari', $jadwal['hari'] ?? '') == 'Selasa') ? 'selected' : '' ?>>Selasa</option>
                                <option value="Rabu" <?= (old('hari', $jadwal['hari'] ?? '') == 'Rabu') ? 'selected' : '' ?>>Rabu</option>
                                <option value="Kamis" <?= (old('hari', $jadwal['hari'] ?? '') == 'Kamis') ? 'selected' : '' ?>>Kamis</option>
                                <option value="Jumat" <?= (old('hari', $jadwal['hari'] ?? '') == 'Jumat') ? 'selected' : '' ?>>Jumat</option>
                            </select>
                            <?php if (session('errors.hari')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.hari') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?= session('errors.jam_mulai') ? 'is-invalid' : '' ?>" 
                                   id="jam_mulai" name="jam_mulai" 
                                   value="<?= old('jam_mulai', $jadwal['jam_mulai'] ?? '') ?>" required>
                            <?php if (session('errors.jam_mulai')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jam_mulai') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?= session('errors.jam_selesai') ? 'is-invalid' : '' ?>" 
                                   id="jam_selesai" name="jam_selesai" 
                                   value="<?= old('jam_selesai', $jadwal['jam_selesai'] ?? '') ?>" required>
                            <?php if (session('errors.jam_selesai')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jam_selesai') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.semester') ? 'is-invalid' : '' ?>" 
                                    id="semester" name="semester" required>
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil" <?= (old('semester', $jadwal['semester'] ?? '') == 'Ganjil') ? 'selected' : '' ?>>Ganjil</option>
                                <option value="Genap" <?= (old('semester', $jadwal['semester'] ?? '') == 'Genap') ? 'selected' : '' ?>>Genap</option>
                            </select>
                            <?php if (session('errors.semester')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.semester') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.tahun_ajaran') ? 'is-invalid' : '' ?>" 
                                   id="tahun_ajaran" name="tahun_ajaran" 
                                   value="<?= old('tahun_ajaran', $jadwal['tahun_ajaran'] ?? '2024/2025') ?>" 
                                   placeholder="Contoh: 2024/2025" required>
                            <?php if (session('errors.tahun_ajaran')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tahun_ajaran') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informasi Jadwal -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Jadwal</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Guru:</strong> <?= $jadwal['nama_guru'] ?? '-' ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Kelas:</strong> <?= $jadwal['nama_kelas'] ?? '-' ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Jurusan:</strong> <?= $jadwal['nama_jurusan'] ?? '-' ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Durasi:</strong> 
                                    <?php 
                                    if (isset($jadwal['jam_mulai']) && isset($jadwal['jam_selesai'])) {
                                        $mulai = strtotime($jadwal['jam_mulai']);
                                        $selesai = strtotime($jadwal['jam_selesai']);
                                        $durasi = round(($selesai - $mulai) / 3600, 1);
                                        echo $durasi . ' jam';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-fill mata pelajaran berdasarkan bidang studi guru
document.getElementById('guru_id').addEventListener('change', function() {
    const guruSelect = this;
    const mataPelajaranInput = document.getElementById('mata_pelajaran_id');
    
    if (guruSelect.value) {
        const selectedOption = guruSelect.options[guruSelect.selectedIndex];
        const guruText = selectedOption.text;
        
        // Extract bidang studi dari text guru
        const match = guruText.match(/\(([^)]+)\)/);
        if (match) {
            const bidangStudi = match[1];
            if (bidangStudi !== '-') {
                mataPelajaranInput.value = bidangStudi;
            }
        }
    }
});

// Validasi jam selesai harus lebih besar dari jam mulai
document.getElementById('jam_selesai').addEventListener('change', function() {
    const jamMulai = document.getElementById('jam_mulai').value;
    const jamSelesai = this.value;
    
    if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
        this.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('jam_mulai').addEventListener('change', function() {
    const jamSelesai = document.getElementById('jam_selesai');
    if (jamSelesai.value) {
        jamSelesai.dispatchEvent(new Event('change'));
    }
});
</script>
<?= $this->endSection() ?> 