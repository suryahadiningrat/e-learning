<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Ulangan</h1>
        <a href="<?= base_url('guru/ulangan/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Ulangan
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Ulangan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Ulangan</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Waktu</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($ulangan ?? [] as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $item['judul_ulangan'] ?></strong></td>
                                <td><?= $item['nama_mata_pelajaran'] ?></td>
                                <td><?= $item['nama_kelas'] ?> <?= isset($item['nama_jurusan']) && $item['nama_jurusan'] ? ' - ' . $item['nama_jurusan'] : '' ?></td>
                                <td>
                                    <div><strong>Mulai:</strong> <?= date('d/m/Y H:i', strtotime($item['waktu_mulai'])) ?></div>
                                    <div><strong>Selesai:</strong> <?= date('d/m/Y H:i', strtotime($item['waktu_selesai'])) ?></div>
                                </td>
                                <td><span class="badge bg-info"><?= $item['durasi_menit'] ?> menit</span></td>
                                <td>
                                    <?php 
                                    $statusClass = '';
                                    switch($item['status']) {
                                        case 'draft': $statusClass = 'bg-secondary'; break;
                                        case 'published': $statusClass = 'bg-success'; break;
                                        case 'closed': $statusClass = 'bg-danger'; break;
                                        default: $statusClass = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($item['status']) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('guru/ulangan/preview/' . $item['id']) ?>" 
                                           class="btn btn-info btn-sm" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($item['status'] == 'draft'): ?>
                                            <a href="<?= base_url('guru/ulangan/edit/' . $item['id']) ?>" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('guru/ulangan/publish/' . $item['id']) ?>" 
                                               class="btn btn-success btn-sm" title="Publish"
                                               onclick="return confirm('Yakin ingin mempublish ulangan ini?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($item['status'] == 'published'): ?>
                                            <a href="<?= base_url('guru/ulangan/hasil/' . $item['id']) ?>" 
                                               class="btn btn-primary btn-sm" title="Hasil">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete('<?= base_url('guru/ulangan/delete/' . $item['id']) ?>', '<?= $item['judul_ulangan'] ?>')" 
                                           class="btn btn-danger btn-sm" title="Hapus">
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

<script>
function confirmDelete(url, name) {
    if (confirm('Yakin ingin menghapus ulangan "' + name + '"?')) {
        window.location.href = url;
    }
}

// DataTable sudah diinisialisasi di layout.php, jadi hapus yang ini
// untuk menghindari konflik
</script>
<?= $this->endSection() ?> 