<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Guru</h1>
        <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Guru</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/guru/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- Data Akun -->
                <h6 class="text-primary mb-3">Data Akun</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                                   id="username" name="username" value="<?= old('username') ?>" 
                                   placeholder="Masukkan username" required>
                            <?php if (session('errors.username')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.username') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" value="<?= old('email') ?>" 
                                   placeholder="Masukkan email" required>
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.email') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.full_name') ? 'is-invalid' : '' ?>" 
                                   id="full_name" name="full_name" value="<?= old('full_name') ?>" 
                                   placeholder="Masukkan nama lengkap" required>
                            <?php if (session('errors.full_name')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.full_name') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                                       id="password" name="password" placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.password') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" 
                                       id="confirm_password" name="confirm_password" placeholder="Konfirmasi password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye" id="confirm_password-icon"></i>
                                </button>
                            </div>
                            <?php if (session('errors.confirm_password')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.confirm_password') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Data Guru -->
                <h6 class="text-primary mb-3 mt-4">Data Guru</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.nip') ? 'is-invalid' : '' ?>" 
                                   id="nip" name="nip" value="<?= old('nip') ?>" 
                                   placeholder="Masukkan NIP" required>
                            <?php if (session('errors.nip')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.nip') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.jenis_kelamin') ? 'is-invalid' : '' ?>" 
                                    id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" <?= old('jenis_kelamin') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                            <?php if (session('errors.jenis_kelamin')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jenis_kelamin') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.no_telp') ? 'is-invalid' : '' ?>" 
                                   id="no_telp" name="no_telp" value="<?= old('no_telp') ?>" 
                                   placeholder="Masukkan no. telepon" required>
                            <?php if (session('errors.no_telp')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.no_telp') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.tempat_lahir') ? 'is-invalid' : '' ?>" 
                                   id="tempat_lahir" name="tempat_lahir" value="<?= old('tempat_lahir') ?>" 
                                   placeholder="Masukkan tempat lahir" required>
                            <?php if (session('errors.tempat_lahir')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tempat_lahir') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?= session('errors.tanggal_lahir') ? 'is-invalid' : '' ?>" 
                                   id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir') ?>" required>
                            <?php if (session('errors.tanggal_lahir')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tanggal_lahir') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bidang_studi" class="form-label">Bidang Studi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= session('errors.bidang_studi') ? 'is-invalid' : '' ?>" 
                           id="bidang_studi" name="bidang_studi" value="<?= old('bidang_studi') ?>" 
                           placeholder="Masukkan bidang studi (contoh: Matematika, Bahasa Indonesia, dll)" required>
                    <?php if (session('errors.bidang_studi')): ?>
                        <div class="invalid-feedback">
                            <?= session('errors.bidang_studi') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= session('errors.alamat') ? 'is-invalid' : '' ?>" 
                              id="alamat" name="alamat" rows="3" 
                              placeholder="Masukkan alamat lengkap" required><?= old('alamat') ?></textarea>
                    <?php if (session('errors.alamat')): ?>
                        <div class="invalid-feedback">
                            <?= session('errors.alamat') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Guru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validasi password match
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.setCustomValidity('');
    }
});
</script>
<?= $this->endSection() ?> 