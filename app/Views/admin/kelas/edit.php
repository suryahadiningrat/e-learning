<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kelas</h1>
        <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Kelas</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/kelas/update/' . ($kelas['id'] ?? '')) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.nama_kelas') ? 'is-invalid' : '' ?>" 
                                   id="nama_kelas" name="nama_kelas" 
                                   value="<?= old('nama_kelas', $kelas['nama_kelas'] ?? '') ?>" 
                                   placeholder="Contoh: X IPA 1, XI IPS 2" required>
                            <?php if (session('errors.nama_kelas')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.nama_kelas') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.jurusan_id') ? 'is-invalid' : '' ?>" 
                                    id="jurusan_id" name="jurusan_id" required>
                                <option value="">Pilih Jurusan</option>
                                <?php foreach ($jurusan ?? [] as $jurusan_item): ?>
                                    <option value="<?= $jurusan_item['id'] ?>" 
                                            <?= (old('jurusan_id', $kelas['jurusan_id'] ?? '') == $jurusan_item['id']) ? 'selected' : '' ?>>
                                        <?= $jurusan_item['nama_jurusan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.jurusan_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jurusan_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.tingkat') ? 'is-invalid' : '' ?>" 
                                    id="tingkat" name="tingkat" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="X" <?= (old('tingkat', $kelas['tingkat'] ?? '') == 'X') ? 'selected' : '' ?>>X (Kelas 10)</option>
                                <option value="XI" <?= (old('tingkat', $kelas['tingkat'] ?? '') == 'XI') ? 'selected' : '' ?>>XI (Kelas 11)</option>
                                <option value="XII" <?= (old('tingkat', $kelas['tingkat'] ?? '') == 'XII') ? 'selected' : '' ?>>XII (Kelas 12)</option>
                            </select>
                            <?php if (session('errors.tingkat')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tingkat') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control <?= session('errors.kapasitas') ? 'is-invalid' : '' ?>" 
                                   id="kapasitas" name="kapasitas" 
                                   value="<?= old('kapasitas', $kelas['kapasitas'] ?? '') ?>" 
                                   placeholder="Contoh: 30" min="1" max="50" required>
                            <div class="form-text">
                                Maksimal 50 siswa per kelas
                            </div>
                            <?php if (session('errors.kapasitas')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.kapasitas') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kelas -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Kelas</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Jumlah Siswa:</strong> <?= $kelas['jumlah_siswa'] ?? 0 ?> siswa
                                </div>
                                <div class="col-md-3">
                                    <strong>Kapasitas:</strong> <?= $kelas['kapasitas'] ?? 0 ?> siswa
                                </div>
                                <div class="col-md-3">
                                    <strong>Sisa Kuota:</strong> <?= ($kelas['kapasitas'] ?? 0) - ($kelas['jumlah_siswa'] ?? 0) ?> siswa
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <?php 
                                    $sisaKuota = ($kelas['kapasitas'] ?? 0) - ($kelas['jumlah_siswa'] ?? 0);
                                    if ($sisaKuota > 0): ?>
                                        <span class="badge bg-success">Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Penuh</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate nama kelas berdasarkan jurusan dan tingkat
document.getElementById('jurusan_id').addEventListener('change', function() {
    generateNamaKelas();
});

document.getElementById('tingkat').addEventListener('change', function() {
    generateNamaKelas();
});

function generateNamaKelas() {
    const jurusanSelect = document.getElementById('jurusan_id');
    const tingkatSelect = document.getElementById('tingkat');
    const namaKelasInput = document.getElementById('nama_kelas');
    
    if (jurusanSelect.value && tingkatSelect.value) {
        const jurusanText = jurusanSelect.options[jurusanSelect.selectedIndex].text;
        const tingkat = tingkatSelect.value;
        
        // Extract jurusan short name (e.g., "IPA" from "Ilmu Pengetahuan Alam")
        let jurusanShort = jurusanText;
        if (jurusanText.includes('Ilmu Pengetahuan Alam')) {
            jurusanShort = 'IPA';
        } else if (jurusanText.includes('Ilmu Pengetahuan Sosial')) {
            jurusanShort = 'IPS';
        } else if (jurusanText.includes('Bahasa')) {
            jurusanShort = 'Bahasa';
        }
        
        // Generate nama kelas
        const namaKelas = `${tingkat} ${jurusanShort} 1`;
        namaKelasInput.value = namaKelas;
    }
}
</script>
<?= $this->endSection() ?> 