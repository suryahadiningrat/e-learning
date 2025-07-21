<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jadwal Pelajaran</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Jadwal Kelas <?= $siswa['nama_kelas'] ?> - <?= $siswa['nama_jurusan'] ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="jadwalTable">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                foreach ($hariOrder as $hari): 
                                    $jadwalHari = array_filter($jadwal, function($j) use ($hari) {
                                        return $j['hari'] === $hari;
                                    });
                                ?>
                                    <tr>
                                        <td rowspan="<?= count($jadwalHari) ?: 1 ?>" class="align-middle">
                                            <strong><?= $hari ?></strong>
                                        </td>
                                        <?php if (empty($jadwalHari)): ?>
                                            <td colspan="4" class="text-center text-muted">
                                                <em>Tidak ada jadwal</em>
                                            </td>
                                        <?php else: ?>
                                            <?php $first = true; ?>
                                            <?php foreach ($jadwalHari as $j): ?>
                                                <?php if (!$first): ?>
                                                    <tr>
                                                <?php endif; ?>
                                                <td>
                                                    <strong><?= $j['jam_mulai'] ?></strong> - <strong><?= $j['jam_selesai'] ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?= $j['nama_mata_pelajaran'] ?></strong>
                                                </td>
                                                <td>
                                                    <?= $j['nama_guru'] ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $mulai = strtotime($j['jam_mulai']);
                                                    $selesai = strtotime($j['jam_selesai']);
                                                    $durasi = round(($selesai - $mulai) / 3600, 1);
                                                    ?>
                                                    <span class="badge bg-info"><?= $durasi ?> jam</span>
                                                </td>
                                                <?php if (!$first): ?>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php $first = false; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#jadwalTable').DataTable({
        "responsive": true,
        "ordering": false,
        "searching": false,
        "paging": false,
        "info": false
    });
});
</script>
<?= $this->endSection() ?> 