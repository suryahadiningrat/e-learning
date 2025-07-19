<?= $this->extend('siswa/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jadwal Saya</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Ruang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($jadwal as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['mata_pelajaran']) ?></td>
                            <td><?= esc($item['nama_guru']) ?></td>
                            <td><?= esc($item['hari']) ?></td>
                            <td><?= esc($item['jam_mulai']) ?> - <?= esc($item['jam_selesai']) ?></td>
                            <td><?= esc($item['ruang'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 