<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Materi/Modul</h1>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Materi/Modul - <?= $siswa['full_name'] ?> (<?= $siswa['nis'] ?>)
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($materi)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Mata Pelajaran</th>
                                <th>Deskripsi</th>
                                <th>File</th>
                                <th>Uploader</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materi as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $item['judul'] ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $item['mata_pelajaran'] ?></span>
                                    </td>
                                    <td>
                                        <?= strlen($item['deskripsi']) > 100 ? substr($item['deskripsi'], 0, 100) . '...' : $item['deskripsi'] ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="<?= get_file_icon($item['file_type']) ?> me-2"></i>
                                            <div>
                                                <small class="text-muted"><?= $item['file_name'] ?></small><br>
                                                <small class="text-muted"><?= format_file_size($item['file_size']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $item['nama_uploader'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('siswa/materi/download/' . $item['id']) ?>" 
                                           class="btn btn-success btn-sm" title="Download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada materi</h5>
                    <p class="text-gray-400">Materi akan muncul setelah guru mengupload materi pembelajaran</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 