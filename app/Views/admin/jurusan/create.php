<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Jurusan</h1>
        <a href="<?= base_url('admin/jurusan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Jurusan</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/jurusan/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="kode_jurusan" class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (session('errors.kode_jurusan')) ? 'is-invalid' : '' ?>" 
                           id="kode_jurusan" name="kode_jurusan" value="<?= old('kode_jurusan') ?>" 
                           placeholder="Contoh: TKJ, RPL, MM">
                    <?php if (session('errors.kode_jurusan')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.kode_jurusan') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="nama_jurusan" class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (session('errors.nama_jurusan')) ? 'is-invalid' : '' ?>" 
                           id="nama_jurusan" name="nama_jurusan" value="<?= old('nama_jurusan') ?>" 
                           placeholder="Contoh: Teknik Komputer dan Jaringan">
                    <?php if (session('errors.nama_jurusan')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.nama_jurusan') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control <?= (session('errors.deskripsi')) ? 'is-invalid' : '' ?>" 
                              id="deskripsi" name="deskripsi" rows="3" 
                              placeholder="Deskripsi singkat tentang jurusan"><?= old('deskripsi') ?></textarea>
                    <?php if (session('errors.deskripsi')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.deskripsi') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 