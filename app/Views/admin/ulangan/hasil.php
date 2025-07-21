<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hasil Ulangan</h1>
        <a href="<?= base_url('admin/ulangan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Ulangan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Judul Ulangan:</strong></td>
                            <td><?= $ulangan['judul_ulangan'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran:</strong></td>
                            <td><?= $ulangan['nama_mata_pelajaran'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas:</strong></td>
                            <td><?= $ulangan['nama_kelas'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total Soal:</strong></td>
                            <td><?= $totalSoal ?> soal</td>
                        </tr>
                        <tr>
                            <td><strong>Total Bobot:</strong></td>
                            <td><?= $totalBobot ?> poin</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Siswa</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($hasil)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="hasilTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Nilai</th>
                                <th>Waktu Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($hasil as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $item['nama_siswa'] ?></td>
                                    <td>
                                        <strong><?= number_format($item['nilai'], 2) ?></strong> / <?= $totalBobot ?>
                                    </td>
                                    <td>
                                        <?= $item['waktu_selesai'] ? date('d/m/Y H:i', strtotime($item['waktu_selesai'])) : '-' ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/ulangan/detail-hasil/' . $ulangan['id'] . '/' . $item['siswa_id']) ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Statistik -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Peserta</h5>
                                <h3><?= count($hasil) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Rata-rata Nilai</h5>
                                <h3><?= number_format($rataRata, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Nilai Tertinggi</h5>
                                <h3><?= number_format($nilaiTertinggi, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Nilai Terendah</h5>
                                <h3><?= number_format($nilaiTerendah, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada hasil ulangan</h5>
                    <p class="text-gray-400">Siswa belum mengerjakan ulangan ini</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#hasilTable').DataTable({
        "responsive": true,
        "order": [[2, "desc"]]
    });
});
</script>
<?= $this->endSection() ?> 