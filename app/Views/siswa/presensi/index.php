<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
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

    <!-- Ringkasan Presensi -->
    <div class="row">
        <?php if (!empty($summary)): ?>
            <?php foreach ($summary as $mapel => $stat): ?>
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="h5 mb-2 font-weight-bold text-primary">
                                        <?= esc($mapel) ?>
                                    </div>
                                    <div class="row text-xs">
                                        <div class="col-md-3">
                                            <div class="text-success">
                                                <i class="fas fa-check-circle"></i> Hadir: <?= $stat['hadir'] ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-warning">
                                                <i class="fas fa-exclamation-circle"></i> Izin: <?= $stat['izin'] ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-info">
                                                <i class="fas fa-notes-medical"></i> Sakit: <?= $stat['sakit'] ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-danger">
                                                <i class="fas fa-times-circle"></i> Alpha: <?= $stat['alpha'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <strong>Total Pertemuan: <?= $stat['total'] ?></strong>
                                        <?php 
                                        $persenHadir = $stat['total'] > 0 ? round(($stat['hadir'] / $stat['total']) * 100, 1) : 0;
                                        ?>
                                        <span class="badge bg-<?= $persenHadir >= 75 ? 'success' : 'danger' ?>">
                                            <?= $persenHadir ?>% Kehadiran
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="<?= base_url('siswa/presensi/detail/' . urlencode($mapel)) ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Belum ada data presensi untuk Anda.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Riwayat Presensi Terbaru -->
    <?php if (!empty($absensi)): ?>
    <div class="card shadow mb-4 mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Presensi Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th>Pertemuan Ke</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php 
                        // Sort by date descending
                        usort($absensi, function($a, $b) {
                            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
                        });
                        
                        // Show only 20 latest
                        $absensiTerbaru = array_slice($absensi, 0, 20);
                        ?>
                        <?php foreach ($absensiTerbaru as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                <td><?= esc($item['nama_mata_pelajaran'] ?? '-') ?></td>
                                <td><?= esc($item['nama_guru'] ?? '-') ?></td>
                                <td><?= $item['pertemuan_ke'] ?? '-' ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($item['status'] ?? '');
                                    $badgeClass = 'secondary';
                                    $icon = 'question-circle';
                                    
                                    if ($status == 'hadir') {
                                        $badgeClass = 'success';
                                        $icon = 'check-circle';
                                    } elseif ($status == 'izin') {
                                        $badgeClass = 'warning';
                                        $icon = 'exclamation-circle';
                                    } elseif ($status == 'sakit') {
                                        $badgeClass = 'info';
                                        $icon = 'notes-medical';
                                    } elseif ($status == 'alpha') {
                                        $badgeClass = 'danger';
                                        $icon = 'times-circle';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <i class="fas fa-<?= $icon ?>"></i>
                                        <?= ucfirst($item['status'] ?? '-') ?>
                                    </span>
                                </td>
                                <td><?= esc($item['keterangan'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        order: [[1, 'desc']] // Sort by tanggal descending
    });
});
</script>
<?= $this->endSection() ?>
