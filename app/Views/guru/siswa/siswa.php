<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Siswa - <?= $kelas['nama_kelas'] ?></h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru/siswa/export-kelas/' . $kelas['id']) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            <a href="<?= base_url('guru/siswa/jurusan/' . $kelas['jurusan_id']) ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
        </div>
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa - <?= $kelas['nama_kelas'] ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat/Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th>No. Telepon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($siswa ?? [] as $siswa_item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $siswa_item['nis'] ?? '' ?></td>
                                <td><?= $siswa_item['full_name'] ?? '' ?></td>
                                <td><?= $siswa_item['email'] ?? '' ?></td>
                                <td>
                                    <?php if (($siswa_item['jenis_kelamin'] ?? '') == 'L'): ?>
                                        <span class="badge bg-primary">Laki-laki</span>
                                    <?php else: ?>
                                        <span class="badge bg-pink">Perempuan</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $siswa_item['tempat_lahir'] ?? '' ?>, <?= isset($siswa_item['tanggal_lahir']) ? date('d/m/Y', strtotime($siswa_item['tanggal_lahir'])) : '' ?></td>
                                <td><?= $siswa_item['alamat'] ?? '' ?></td>
                                <td><?= $siswa_item['no_telp'] ?? '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection() ?>
