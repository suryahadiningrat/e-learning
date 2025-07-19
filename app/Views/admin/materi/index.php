<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Materi</h1>
        <a href="<?= base_url('admin/materi/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Materi
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Materi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Guru</th>
                            <th>Kelas</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>File</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($materi ?? [] as $m) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $m['nama_guru'] ?? '-' ?></td>
                                <td><?= $m['nama_kelas'] ?? '-' ?></td>
                                <td><?= $m['judul'] ?? '-' ?></td>
                                <td><?= substr($m['deskripsi'] ?? '', 0, 50) ?>...</td>
                                <td>
                                    <?php if ($m['file_materi'] ?? '') : ?>
                                        <a href="<?= base_url('admin/materi/download/' . ($m['id'] ?? '')) ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Tidak ada file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?= $m['semester'] ?? '-' ?></span>
                                </td>
                                <td><?= $m['tahun_ajaran'] ?? '-' ?></td>
                                <td>
                                    <a href="<?= base_url('admin/materi/edit/' . ($m['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/materi/delete/' . ($m['id'] ?? '')) ?>', '<?= $m['judul'] ?? 'Data' ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>
<?= $this->endSection() ?> 