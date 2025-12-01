<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User Pengguna</h1>
        <a href="<?= base_url('admin/user-pengguna') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit User</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('admin/user-pengguna/update/' . ($user['id'] ?? '')) ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- Data Akun Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Data Akun</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                                           id="username" name="username" 
                                           value="<?= old('username', $user['username'] ?? '') ?>" 
                                           placeholder="Masukkan username" required>
                                    <?php if (session('errors.username')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.username') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" 
                                           value="<?= old('email', $user['email'] ?? '') ?>" 
                                           placeholder="Masukkan email" required>
                                    <?php if (session('errors.email')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.full_name') ? 'is-invalid' : '' ?>" 
                                   id="full_name" name="full_name" 
                                   value="<?= old('full_name', $user['full_name'] ?? '') ?>" 
                                   placeholder="Masukkan nama lengkap" required>
                            <?php if (session('errors.full_name')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.full_name') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                                               id="password" name="password" placeholder="Masukkan password baru">
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
                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" 
                                               id="confirm_password" name="confirm_password" placeholder="Konfirmasi password baru">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select <?= session('errors.role') ? 'is-invalid' : '' ?>" 
                                            id="role" name="role" required onchange="toggleDataPribadi()">
                                        <option value="">Pilih Role</option>
                                        <option value="admin" <?= (old('role', $user['role'] ?? '') == 'admin') ? 'selected' : '' ?>>Admin</option>
                                        <option value="guru" <?= (old('role', $user['role'] ?? '') == 'guru') ? 'selected' : '' ?>>Guru</option>
                                        <option value="siswa" <?= (old('role', $user['role'] ?? '') == 'siswa') ? 'selected' : '' ?>>Siswa</option>
                                    </select>
                                    <?php if (session('errors.role')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.role') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        <?php if ($user['is_active'] ?? false): ?>
                                            <span class="badge bg-success fs-6">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning fs-6">Nonaktif</span>
                                        <?php endif; ?>
                                        <small class="text-muted d-block mt-1">
                                            Status dapat diubah melalui halaman daftar user
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi Guru Section -->
                <div class="card mb-4" id="guru-section" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Data Pribadi Guru</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control" id="nip" name="nip" 
                                           value="<?= old('nip', $guru['nip'] ?? '') ?>" 
                                           placeholder="Masukkan NIP">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bidang_studi" class="form-label">Bidang Studi</label>
                                    <input type="text" class="form-control" id="bidang_studi" name="bidang_studi" 
                                           value="<?= old('bidang_studi', $guru['bidang_studi'] ?? '') ?>" 
                                           placeholder="Masukkan bidang studi">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kelamin_guru" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin_guru" name="jenis_kelamin">
                                        <option value="L" <?= (old('jenis_kelamin', $guru['jenis_kelamin'] ?? '') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= (old('jenis_kelamin', $guru['jenis_kelamin'] ?? '') == 'P') ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telp_guru" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telp_guru" name="no_telp" 
                                           value="<?= old('no_telp', $guru['no_telp'] ?? '') ?>" 
                                           placeholder="Masukkan no. telepon">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_lahir_guru" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir_guru" name="tempat_lahir" 
                                           value="<?= old('tempat_lahir', $guru['tempat_lahir'] ?? '') ?>" 
                                           placeholder="Masukkan tempat lahir">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_lahir_guru" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir_guru" name="tanggal_lahir" 
                                           value="<?= old('tanggal_lahir', $guru['tanggal_lahir'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_guru" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat_guru" name="alamat" rows="3" 
                                      placeholder="Masukkan alamat lengkap"><?= old('alamat', $guru['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi Siswa Section -->
                <div class="card mb-4" id="siswa-section" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Data Pribadi Siswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nis" class="form-label">NIS</label>
                                    <input type="text" class="form-control" id="nis" name="nis" 
                                           value="<?= old('nis', $siswa['nis'] ?? '') ?>" 
                                           placeholder="Masukkan NIS">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select class="form-select" id="kelas_id" name="kelas_id">
                                        <option value="">Pilih Kelas</option>
                                        <?php if (!empty($kelas)): ?>
                                            <?php foreach ($kelas as $k): ?>
                                                <option value="<?= $k['id'] ?>" <?= (old('kelas_id', $siswa['kelas_id'] ?? '') == $k['id']) ? 'selected' : '' ?>>
                                                    <?= esc($k['tingkat'] . ' ' . $k['kode_jurusan'] . ' ' . $k['paralel']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kelamin_siswa" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin_siswa" name="jenis_kelamin">
                                        <option value="L" <?= (old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= (old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') == 'P') ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telp_siswa" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telp_siswa" name="no_telp" 
                                           value="<?= old('no_telp', $siswa['no_telp'] ?? '') ?>" 
                                           placeholder="Masukkan no. telepon">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_lahir_siswa" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir_siswa" name="tempat_lahir" 
                                           value="<?= old('tempat_lahir', $siswa['tempat_lahir'] ?? '') ?>" 
                                           placeholder="Masukkan tempat lahir">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_lahir_siswa" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir_siswa" name="tanggal_lahir" 
                                           value="<?= old('tanggal_lahir', $siswa['tanggal_lahir'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_siswa" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat_siswa" name="alamat" rows="3" 
                                      placeholder="Masukkan alamat lengkap"><?= old('alamat', $siswa['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update User
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

function toggleDataPribadi() {
    const role = document.getElementById('role').value;
    const guruSection = document.getElementById('guru-section');
    const siswaSection = document.getElementById('siswa-section');
    
    guruSection.style.display = 'none';
    siswaSection.style.display = 'none';
    
    if (role === 'guru') {
        guruSection.style.display = 'block';
    } else if (role === 'siswa') {
        siswaSection.style.display = 'block';
    }
}

// Validasi password match jika password diisi
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDataPribadi();
});
</script>
<?= $this->endSection() ?>
