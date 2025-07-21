<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Ulangan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/ulangan/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Ulangan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="ulanganTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Ulangan</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($ulangan as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['judul_ulangan'] ?></td>
                                <td><?= $item['nama_mata_pelajaran'] ?></td>
                                <td><?= $item['nama_kelas'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item['waktu_mulai'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item['waktu_selesai'])) ?></td>
                                <td><?= $item['durasi_menit'] ?> menit</td>
                                <td>
                                    <?php if ($item['status'] == 'draft'): ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php elseif ($item['status'] == 'published'): ?>
                                        <span class="badge badge-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Closed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $item['nama_creator'] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/ulangan/preview/' . $item['id']) ?>" 
                                           class="btn btn-info btn-sm" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($item['status'] == 'draft'): ?>
                                            <a href="<?= base_url('admin/ulangan/publish/' . $item['id']) ?>" 
                                               class="btn btn-success btn-sm" title="Publish"
                                               onclick="return confirm('Yakin ingin mempublish ulangan ini?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php elseif ($item['status'] == 'published'): ?>
                                            <a href="<?= base_url('admin/ulangan/close/' . $item['id']) ?>" 
                                               class="btn btn-warning btn-sm" title="Close"
                                               onclick="return confirm('Yakin ingin menutup ulangan ini?')">
                                                <i class="fas fa-lock"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/ulangan/hasil/' . $item['id']) ?>" 
                                           class="btn btn-primary btn-sm" title="Hasil">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="<?= base_url('admin/ulangan/edit/' . $item['id']) ?>" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/ulangan/delete/' . $item['id']) ?>" 
                                           class="btn btn-danger btn-sm" title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus ulangan ini?')">
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
    $('#ulanganTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#ulanganTable_wrapper .col-md-6:eq(0)');
});
</script>
<?= $this->endSection() ?> 