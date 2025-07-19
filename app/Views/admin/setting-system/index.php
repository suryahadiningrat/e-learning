<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Setting System</h1>
    </div>

    <!-- Logo Sekolah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-image me-2"></i>Logo Sekolah
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <?php if (isset($settings['logo_sekolah']) && $settings['logo_sekolah']): ?>
                            <img src="<?= base_url('uploads/logo/' . $settings['logo_sekolah']) ?>" 
                                 alt="Logo Sekolah" class="img-fluid border rounded" 
                                 style="max-height: 200px; max-width: 100%;">
                        <?php else: ?>
                            <div class="border rounded d-flex align-items-center justify-content-center" 
                                 style="height: 200px; background-color: #f8f9fa;">
                                <span class="text-muted">Logo belum diupload</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <form action="<?= base_url('admin/setting-system/update-logo') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Upload Logo Baru</label>
                            <input type="file" class="form-control <?= session('errors.logo') ? 'is-invalid' : '' ?>" 
                                   id="logo" name="logo" accept="image/*" required>
                            <div class="form-text">
                                Format: JPG, JPEG, PNG, GIF | Maksimal: 2MB
                            </div>
                            <?php if (session('errors.logo')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.logo') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Logo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Sistem -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-palette me-2"></i>Background Sistem
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <?php if (isset($settings['background_sistem']) && $settings['background_sistem']): ?>
                            <img src="<?= base_url('uploads/background/' . $settings['background_sistem']) ?>" 
                                 alt="Background Sistem" class="img-fluid border rounded" 
                                 style="max-height: 200px; max-width: 100%;">
                        <?php else: ?>
                            <div class="border rounded d-flex align-items-center justify-content-center" 
                                 style="height: 200px; background-color: #f8f9fa;">
                                <span class="text-muted">Background belum diupload</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <form action="<?= base_url('admin/setting-system/update-background') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="background" class="form-label">Upload Background Baru</label>
                            <input type="file" class="form-control <?= session('errors.background') ? 'is-invalid' : '' ?>" 
                                   id="background" name="background" accept="image/*" required>
                            <div class="form-text">
                                Format: JPG, JPEG, PNG, GIF | Maksimal: 5MB
                            </div>
                            <?php if (session('errors.background')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.background') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Background
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tahun Ajaran -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar me-2"></i>Tahun Ajaran
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/setting-system/update-tahun-ajaran') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control <?= session('errors.tahun_ajaran') ? 'is-invalid' : '' ?>" 
                                   id="tahun_ajaran" name="tahun_ajaran" 
                                   value="<?= old('tahun_ajaran', $settings['tahun_ajaran'] ?? '') ?>" 
                                   placeholder="Contoh: 2024/2025" required>
                            <div class="form-text">
                                Format: 2024/2025 atau 2024-2025
                            </div>
                            <?php if (session('errors.tahun_ajaran')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tahun_ajaran') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Tahun Ajaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Informasi Sistem -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>Informasi Sistem
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Nama Sistem:</strong></td>
                            <td>E-Learning Management System</td>
                        </tr>
                        <tr>
                            <td><strong>Versi:</strong></td>
                            <td>1.0.0</td>
                        </tr>
                        <tr>
                            <td><strong>Framework:</strong></td>
                            <td>CodeIgniter 4</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>PHP Version:</strong></td>
                            <td><?= phpversion() ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database:</strong></td>
                            <td>MySQL</td>
                        </tr>
                        <tr>
                            <td><strong>Last Updated:</strong></td>
                            <td><?= date('d/m/Y H:i:s') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image sebelum upload
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector('.col-md-4 img') || document.querySelector('.col-md-4 .border');
            if (preview) {
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-fluid border rounded" style="max-height: 200px; max-width: 100%;">`;
                }
            }
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('background').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelectorAll('.col-md-4 img')[1] || document.querySelectorAll('.col-md-4 .border')[1];
            if (preview) {
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-fluid border rounded" style="max-height: 200px; max-width: 100%;">`;
                }
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?> 