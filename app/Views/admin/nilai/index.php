<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Nilai</h1>
        <a href="<?= base_url('admin/nilai/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Nilai
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Nilai</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Mata Pelajaran</th>
                            <th>Nilai Tugas</th>
                            <th>Nilai UTS</th>
                            <th>Nilai UAS</th>
                            <th>Nilai Akhir</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($nilai ?? [] as $n) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $n['nama_siswa'] ?? '-' ?></td>
                                <td><?= $n['mata_pelajaran'] ?? '-' ?></td>
                                <td>
                                    <span class="badge bg-info"><?= $n['nilai_tugas'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?= $n['nilai_uts'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-danger"><?= $n['nilai_uas'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $nilai_akhir = $n['nilai_akhir'] ?? 0;
                                    if ($nilai_akhir >= 85) {
                                        echo '<span class="badge bg-success">' . $nilai_akhir . '</span>';
                                    } elseif ($nilai_akhir >= 75) {
                                        echo '<span class="badge bg-info">' . $nilai_akhir . '</span>';
                                    } elseif ($nilai_akhir >= 65) {
                                        echo '<span class="badge bg-warning">' . $nilai_akhir . '</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">' . $nilai_akhir . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $n['semester'] ?? '-' ?></span>
                                </td>
                                <td><?= $n['tahun_ajaran'] ?? '-' ?></td>
                                <td>
                                    <a href="<?= base_url('admin/nilai/edit/' . ($n['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/nilai/delete/' . ($n['id'] ?? '')) ?>', '<?= $n['nama_siswa'] ?? 'Data' ?>')">
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