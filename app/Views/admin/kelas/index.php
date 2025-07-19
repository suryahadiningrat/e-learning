<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Kelas</h1>
        <a href="<?= base_url('admin/kelas/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Kelas
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th>Tingkat</th>
                            <th>Kapasitas</th>
                            <th>Jumlah Siswa</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($kelas ?? [] as $k) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $k['nama_kelas'] ?? '-' ?></strong></td>
                                <td><?= $k['nama_jurusan'] ?? '-' ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $k['tingkat'] ?? '-' ?></span>
                                </td>
                                <td><?= $k['kapasitas'] ?? 0 ?> siswa</td>
                                <td>
                                    <span class="badge bg-info"><?= $k['jumlah_siswa'] ?? 0 ?> siswa</span>
                                </td>
                                <td>
                                    <?php 
                                    $sisaKuota = ($k['kapasitas'] ?? 0) - ($k['jumlah_siswa'] ?? 0);
                                    if ($sisaKuota > 0): ?>
                                        <span class="badge bg-success">Tersedia (<?= $sisaKuota ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Penuh</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/kelas/edit/' . ($k['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/kelas/delete/' . ($k['id'] ?? '')) ?>', '<?= $k['nama_kelas'] ?? 'Data' ?>')">
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
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

function confirmDelete(url, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus kelas "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
<?= $this->endSection() ?> 