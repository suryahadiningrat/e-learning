<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                </div>
                <div class="card-body">
                    <?php if (empty($jadwal)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada jadwal mengajar yang tersedia.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="jadwalTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Semester</th>
                                        <th>Tahun Ajaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($jadwal as $j): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $j['hari'] ?></td>
                                            <td><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></td>
                                            <td><?= $j['nama_mata_pelajaran'] ?></td>
                                            <td><?= $j['nama_kelas'] ?></td>
                                            <td><?= $j['nama_jurusan'] ?></td>
                                            <td><?= $j['semester'] ?></td>
                                            <td><?= $j['tahun_ajaran'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#jadwalTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [
            [1, 'asc'], // Sort by hari
            [2, 'asc']  // Then by jam
        ]
    });
});
</script>
<?= $this->endSection() ?>