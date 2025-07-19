<?= $this->extend('guru/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Siswa</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Data Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($siswas as $siswa): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= esc($siswa['nis']) ?></strong></td>
                            <td><?= esc($siswa['nisn']) ?></td>
                            <td><?= esc($siswa['full_name']) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= esc($siswa['nama_kelas']) ?></span>
                            </td>
                            <td><?= esc($siswa['nama_jurusan']) ?></td>
                            <td><?= esc($siswa['email']) ?></td>
                            <td>
                                <?php if($siswa['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 