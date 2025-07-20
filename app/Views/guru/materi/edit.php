<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Materi/Modul</h1>
        <a href="<?= base_url('guru/materi') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Materi/Modul</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('guru/materi/update/' . $materi['id']) ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="judul" name="judul" 
                                   value="<?= old('judul', $materi['judul']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mata_pelajaran" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="mata_pelajaran" name="mata_pelajaran" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php foreach ($mata_pelajaran as $mapel): ?>
                                    <option value="<?= $mapel['mata_pelajaran'] ?>" 
                                            <?= old('mata_pelajaran', $materi['mata_pelajaran']) == $mapel['mata_pelajaran'] ? 'selected' : '' ?>>
                                        <?= $mapel['mata_pelajaran'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?= old('deskripsi', $materi['deskripsi']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="file_materi" class="form-label">File Materi</label>
                    <input type="file" class="form-control" id="file_materi" name="file_materi">
                    <div class="form-text">
                        Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, TXT. Maksimal 10MB.
                        <br>Kosongkan jika tidak ingin mengubah file.
                    </div>
                    
                    <!-- File saat ini -->
                    <div class="mt-2">
                        <small class="text-muted">File saat ini:</small><br>
                        <div class="d-flex align-items-center">
                            <i class="<?= get_file_icon($materi['file_type']) ?> me-2"></i>
                            <div>
                                <small class="text-muted"><?= $materi['file_name'] ?></small><br>
                                <small class="text-muted"><?= format_file_size($materi['file_size']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 