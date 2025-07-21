<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Mata Pelajaran</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/mata-pelajaran/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="mataPelajaranTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Mata Pelajaran</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($mata_pelajaran as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $item['kode'] ?></strong></td>
                                <td><?= $item['nama'] ?></td>
                                <td><?= $item['deskripsi'] ?: '-' ?></td>
                                <td>
                                    <?php if ($item['status'] == 1): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/mata-pelajaran/edit/' . $item['id']) ?>" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($item['status'] == 1): ?>
                                            <a href="<?= base_url('admin/mata-pelajaran/toggle-status/' . $item['id']) ?>" 
                                               class="btn btn-secondary btn-sm" title="Nonaktifkan"
                                               onclick="return confirm('Yakin ingin menonaktifkan mata pelajaran ini?')">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/mata-pelajaran/toggle-status/' . $item['id']) ?>" 
                                               class="btn btn-success btn-sm" title="Aktifkan"
                                               onclick="return confirm('Yakin ingin mengaktifkan mata pelajaran ini?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/mata-pelajaran/delete/' . $item['id']) ?>" 
                                           class="btn btn-danger btn-sm" title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
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
</div>

<script>
$(document).ready(function() {
    $('#mataPelajaranTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#mataPelajaranTable_wrapper .col-md-6:eq(0)');
});
</script>
<?= $this->endSection() ?> 