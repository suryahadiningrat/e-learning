<?= $this->extend('guru/layout'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Saya</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->has('message')) : ?>
                        <div class="alert alert-success">
                            <?= session('message') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session('errors') as $error) : ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('guru/user-pengguna/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <img src="<?= $user['photo'] ? base_url('uploads/profile/' . $user['photo']) : base_url('assets/img/default-profile.png') ?>" 
                                     class="img-thumbnail" alt="Profile Photo" id="preview-photo">
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="photo">Photo Profile</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Nama Lengkap</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= $user['full_name'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">Data Pribadi Guru</h5>
                        
                        <?php if($guru): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIP</label>
                                    <input type="text" class="form-control" value="<?= $guru['nip'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bidang Studi</label>
                                    <input type="text" class="form-control" value="<?= $guru['bidang_studi'] ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" class="form-control" value="<?= $guru['no_telp'] ?? '-' ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <input type="text" class="form-control" value="<?= $guru['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input type="text" class="form-control" value="<?= $guru['tempat_lahir'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($guru['tanggal_lahir'])) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" rows="3" readonly><?= $guru['alamat'] ?></textarea>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Data pribadi guru belum tersedia.
                        </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-photo').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
