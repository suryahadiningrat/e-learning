<?= $this->extend('guru/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Jurusan</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Data Jurusan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jurusan</th>
                            <th>Kode Jurusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($jurusan as $j): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= esc($j['nama_jurusan']) ?></strong></td>
                            <td>
                                <span class="badge bg-secondary"><?= esc($j['kode_jurusan']) ?></span>
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