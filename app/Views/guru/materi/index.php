<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Materi/Modul</h1>
        <a href="<?= base_url('guru/materi/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Materi
        </a>
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Materi/Modul Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Mata Pelajaran</th>
                            <th>Deskripsi</th>
                            <th>File</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($materi ?? [] as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
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
                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('guru/materi/download/' . $item['id']) ?>" 
                                           class="btn btn-success btn-sm" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="<?= base_url('guru/materi/edit/' . $item['id']) ?>" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('guru/materi/delete/' . $item['id']) ?>" 
                                           class="btn btn-danger btn-sm" title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 