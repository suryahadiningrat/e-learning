<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Ulangan</h1>
        <a href="<?= base_url('siswa/ulangan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Ulangan yang Telah Dikerjakan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="riwayatTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Judul Ulangan</th>
                            <th>Mata Pelajaran</th>
                            <th>Nilai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($riwayat as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                    </span>
                                </td>
                                <td><strong><?= $item['judul_ulangan'] ?></strong></td>
                                <td><?= $item['nama_mata_pelajaran'] ?></td>
                                <td>
                                    <strong><?= number_format($item['nilai'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $persentase = ($item['nilai'] / 100) * 100; // Assuming max score is 100
                                    if ($persentase >= 80) {
                                        echo '<span class="badge bg-success">Sangat Baik</span>';
                                    } elseif ($persentase >= 70) {
                                        echo '<span class="badge bg-warning">Baik</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Kurang</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('siswa/ulangan/hasil/' . $item['ulangan_id']) ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (empty($riwayat)): ?>
        <div class="text-center py-4">
            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-500">Belum ada riwayat ulangan</h5>
            <p class="text-gray-400">Anda belum mengerjakan ulangan apapun</p>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('#riwayatTable').DataTable({
        "responsive": true,
        "order": [[1, "desc"]]
    });
});
</script>
<?= $this->endSection() ?> 