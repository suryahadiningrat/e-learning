<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<style>
    .table-responsive {
        overflow-x: auto;
    }
    
    .table th {
        white-space: nowrap;
        vertical-align: middle;
        font-size: 0.9rem;
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    
    .table-dark th {
        background-color: #343a40;
        border-color: #454d55;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Nilai</h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('siswa/nilai/export-pdf') ?>" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf fa-sm"></i> Export PDF
            </a>
            <a href="<?= base_url('siswa/nilai/export') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
        </div>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Nilai Siswa - <?= $siswa['full_name'] ?> (<?= $siswa['nis'] ?>)
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($nilai)): ?>
                <?php
                // Hitung jumlah maksimal tugas dan ulangan
                $maxTugas = 0;
                $maxUlangan = 0;
                foreach ($nilai as $item) {
                    $tugasArray = json_decode($item['nilai_tugas'], true) ?: [];
                    $ulanganArray = json_decode($item['nilai_ulangan'], true) ?: [];
                    $maxTugas = max($maxTugas, count($tugasArray));
                    $maxUlangan = max($maxUlangan, count($ulanganArray));
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Mata Pelajaran</th>
                                <th rowspan="2">Kelas</th>
                                <th colspan="<?= $maxTugas ?>" class="text-center">Nilai Tugas</th>
                                <th colspan="<?= $maxUlangan ?>" class="text-center">Nilai Ulangan</th>
                            </tr>
                            <tr>
                                <?php for ($i = 1; $i <= $maxTugas; $i++): ?>
                                    <th class="text-center">Tugas <?= $i ?></th>
                                <?php endfor; ?>
                                <?php for ($i = 1; $i <= $maxUlangan; $i++): ?>
                                    <th class="text-center">Ulangan <?= $i ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nilai as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $item['nama_mata_pelajaran'] ?></td>
                                    <td><?= $item['nama_kelas'] ?></td>
                                    
                                    <?php 
                                    $nilaiTugas = json_decode($item['nilai_tugas'], true) ?: [];
                                    for ($i = 0; $i < $maxTugas; $i++): 
                                    ?>
                                        <td class="text-center">
                                            <?php if (isset($nilaiTugas[$i]) && $nilaiTugas[$i] !== ''): ?>
                                                <span class="badge bg-primary"><?= $nilaiTugas[$i] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>
                                    
                                    <?php 
                                    $nilaiUlangan = json_decode($item['nilai_ulangan'], true) ?: [];
                                    for ($i = 0; $i < $maxUlangan; $i++): 
                                    ?>
                                        <td class="text-center">
                                            <?php if (isset($nilaiUlangan[$i]) && $nilaiUlangan[$i] !== ''): ?>
                                                <span class="badge bg-success"><?= $nilaiUlangan[$i] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada data nilai</h5>
                    <p class="text-gray-400">Nilai akan muncul setelah guru menginput nilai untuk mata pelajaran Anda</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }
    
    // Initialize DataTable with specific configuration for nilai table
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            scrollX: true,
            scrollCollapse: true,
            fixedHeader: true,
            columnDefs: [
                {
                    targets: '_all',
                    className: 'text-center'
                }
            ]
        });
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 