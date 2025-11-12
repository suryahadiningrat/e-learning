<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <a href="<?= base_url('siswa/presensi') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
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

    <?php if (!empty($absensi)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Detail Presensi - <?= esc($mataPelajaran) ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pertemuan</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Guru</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($absensi as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d F Y', strtotime($item['tanggal'])) ?></td>
                                <td><?= $item['pertemuan_ke'] ?? '-' ?></td>
                                <td><?= esc($item['hari'] ?? '-') ?></td>
                                <td><?= $item['jam_mulai'] ?? '-' ?> - <?= $item['jam_selesai'] ?? '-' ?></td>
                                <td><?= esc($item['nama_guru'] ?? '-') ?></td>
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
    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Belum ada data presensi untuk mata pelajaran ini.
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
