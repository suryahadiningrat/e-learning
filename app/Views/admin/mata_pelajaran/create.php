<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Mata Pelajaran</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/mata-pelajaran') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('admin/mata-pelajaran/store') ?>" method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode">Kode Mata Pelajaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode" name="kode" 
                                           value="<?= old('kode') ?>" placeholder="Contoh: MTK" required>
                                    <small class="form-text text-muted">Kode unik untuk mata pelajaran (2-10 karakter)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?= old('nama') ?>" placeholder="Contoh: Matematika" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="aktif" <?= old('status') == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="nonaktif" <?= old('status') == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                              placeholder="Deskripsi mata pelajaran (opsional)"><?= old('deskripsi') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
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

<script>
$(document).ready(function() {
    // Auto uppercase untuk kode
    $('#kode').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>
<?= $this->endSection() ?> 