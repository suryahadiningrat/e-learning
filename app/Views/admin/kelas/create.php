<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Kelas</h1>
        <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Kelas</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/kelas/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.tingkat') ? 'is-invalid' : '' ?>" 
                                    id="tingkat" name="tingkat" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="X" <?= old('tingkat') == 'X' ? 'selected' : '' ?>>X (Kelas 10)</option>
                                <option value="XI" <?= old('tingkat') == 'XI' ? 'selected' : '' ?>>XI (Kelas 11)</option>
                                <option value="XII" <?= old('tingkat') == 'XII' ? 'selected' : '' ?>>XII (Kelas 12)</option>
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
                            <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.jurusan_id') ? 'is-invalid' : '' ?>" 
                                    id="jurusan_id" name="jurusan_id" required>
                                <option value="">Pilih Jurusan</option>
                                <?php foreach ($jurusan ?? [] as $jurusan_item): ?>
                                    <option value="<?= $jurusan_item['id'] ?>" <?= old('jurusan_id') == $jurusan_item['id'] ? 'selected' : '' ?>>
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
                            <label for="kode_jurusan" class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.kode_jurusan') ? 'is-invalid' : '' ?>" 
                                   id="kode_jurusan" name="kode_jurusan" value="<?= old('kode_jurusan') ?>" 
                                   placeholder="Contoh: TKJ" required maxlength="10" 
                                   oninput="this.value = this.value.toUpperCase()">
                            <div class="form-text">
                                Masukkan singkatan jurusan (2-10 karakter)
                            </div>
                            <?php if (session('errors.kode_jurusan')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.kode_jurusan') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="paralel" class="form-label">Kelas Paralel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.paralel') ? 'is-invalid' : '' ?>" 
                                   id="paralel" name="paralel" value="<?= old('paralel') ?>" 
                                   placeholder="Contoh: A" required maxlength="1" 
                                   oninput="this.value = this.value.toUpperCase()">
                            <div class="form-text">
                                Masukkan huruf kelas (A, B, C, dst)
                            </div>
                            <?php if (session('errors.paralel')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.paralel') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control <?= session('errors.kapasitas') ? 'is-invalid' : '' ?>" 
                                   id="kapasitas" name="kapasitas" value="<?= old('kapasitas') ?>" 
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

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Preview Nama Kelas</label>
                            <div class="form-control bg-light" id="preview">
                                ...
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePreview() {
    const tingkat = document.getElementById('tingkat').value;
    const kodeJurusan = document.getElementById('kode_jurusan').value;
    const paralel = document.getElementById('paralel').value;
    
    const preview = tingkat && kodeJurusan && paralel 
        ? `${tingkat} ${kodeJurusan} ${paralel}`
        : '...';
        
    document.getElementById('preview').textContent = preview;
}

// Add event listeners
document.getElementById('tingkat').addEventListener('change', updatePreview);
document.getElementById('kode_jurusan').addEventListener('input', updatePreview);
document.getElementById('paralel').addEventListener('input', updatePreview);

// Initial preview
updatePreview();
</script>

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