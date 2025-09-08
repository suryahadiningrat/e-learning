<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Mata Pelajaran</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/mata-pelajaran') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->has('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error) : ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/mata-pelajaran/update/' . $mata_pelajaran['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="nama">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= old('nama', $mata_pelajaran['nama']) ?>" 
                                   placeholder="Masukkan nama mata pelajaran" required>
                        </div>

                        <div class="form-group">
                            <label for="kode">Kode Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode" name="kode" 
                                   value="<?= old('kode', $mata_pelajaran['kode']) ?>" 
                                   placeholder="Masukkan kode mata pelajaran" readonly disabled>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" 
                                      placeholder="Masukkan deskripsi mata pelajaran"><?= old('deskripsi', $mata_pelajaran['deskripsi']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="aktif" <?= old('status', $mata_pelajaran['status']) == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= old('status', $mata_pelajaran['status']) == 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?= base_url('admin/mata-pelajaran') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 