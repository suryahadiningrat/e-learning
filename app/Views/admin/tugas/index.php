<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Tugas</h1>
        <a href="<?= base_url('admin/tugas/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Tugas
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tugas</h6>
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
                            <th>Link Pengumpulan</th>
                            <th>Deadline</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($tugas ?? [] as $t) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $t['nama_guru'] ?? '-' ?></td>
                                <td><?= $t['nama_kelas'] ?? '-' ?></td>
                                <td><?= $t['judul'] ?? '-' ?></td>
                                <td><?= substr($t['deskripsi'] ?? '', 0, 50) ?>...</td>
                                <td>
                                    <?php if ($t['link_pengumpulan'] ?? '') : ?>
                                        <a href="<?= $t['link_pengumpulan'] ?>" target="_blank" class="btn btn-info btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Link
                                        </a>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Tidak ada link</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $deadline = $t['deadline'] ? strtotime($t['deadline']) : 0;
                                    $today = time();
                                    if ($deadline && $deadline < $today) {
                                        echo '<span class="badge bg-danger">' . date('d/m/Y', $deadline) . '</span>';
                                    } elseif ($deadline) {
                                        echo '<span class="badge bg-success">' . date('d/m/Y', $deadline) . '</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">-</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?= $t['semester'] ?? '-' ?></span>
                                </td>
                                <td><?= $t['tahun_ajaran'] ?? '-' ?></td>
                                <td>
                                    <a href="<?= base_url('admin/tugas/edit/' . ($t['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/tugas/delete/' . ($t['id'] ?? '')) ?>', '<?= $t['judul'] ?? 'Data' ?>')">
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