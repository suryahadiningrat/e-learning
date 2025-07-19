<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Siswa</h1>
        <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Siswa</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/siswa/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- Data Akun -->
                <h6 class="text-primary mb-3">Data Akun</h6>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User Login <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.user_id') ? 'is-invalid' : '' ?>" 
                                    id="user_id" name="user_id" required>
                                <option value="">Pilih User Login (Siswa)</option>
                                <?php foreach ($users ?? [] as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                        <?= $user['username'] ?> - <?= $user['full_name'] ?> (<?= $user['email'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Pilih user dengan role 'siswa' yang belum dipilih untuk siswa lain</small>
                            <?php if (session('errors.user_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.user_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Data Siswa -->
                <h6 class="text-primary mb-3 mt-4">Data Siswa</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.nis') ? 'is-invalid' : '' ?>" 
                                   id="nis" name="nis" value="<?= old('nis') ?>" 
                                   placeholder="Masukkan NIS" required>
                            <?php if (session('errors.nis')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.nis') ?>
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select <?= session('errors.kelas_id') ? 'is-invalid' : '' ?>" 
                                    id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas ?? [] as $kelas_item): ?>
                                    <option value="<?= $kelas_item['id'] ?>" <?= old('kelas_id') == $kelas_item['id'] ? 'selected' : '' ?>>
                                        <?= $kelas_item['nama_kelas'] ?>
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
                    
                    <div class="col-md-6">
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
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 