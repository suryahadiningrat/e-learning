<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Jurusan</h1>
        <a href="<?= base_url('admin/jurusan/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Jurusan
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jurusan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Jurusan</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($jurusan ?? [] as $j) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $j['kode_jurusan'] ?? '-' ?></td>
                                <td><?= $j['nama_jurusan'] ?? '-' ?></td>
                                <td><?= $j['deskripsi'] ?? '-' ?></td>
                                <td>
                                    <span class="badge bg-info"><?= $j['jumlah_kelas'] ?? 0 ?> Kelas</span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/jurusan/edit/' . ($j['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/jurusan/delete/' . ($j['id'] ?? '')) ?>', '<?= $j['nama_jurusan'] ?? 'Data' ?>')">
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