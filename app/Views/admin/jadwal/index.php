<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Jadwal</h1>
        <a href="<?= base_url('admin/jadwal/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Jadwal
        </a>
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
                            <th>Guru</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Durasi</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($jadwal ?? [] as $j) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= $j['nama_guru'] ?? '-' ?></strong><br>
                                    <small class="text-muted"><?= $j['bidang_studi'] ?? '-' ?></small>
                                </td>
                                <td>
                                    <strong><?= $j['nama_kelas'] ?? '-' ?></strong><br>
                                    <small class="text-muted"><?= $j['nama_jurusan'] ?? '-' ?> (<?= $j['tingkat'] ?? '-' ?>)</small>
                                </td>
                                <td><strong><?= $j['nama_mata_pelajaran'] ?? '-' ?></strong></td>
                                <td>
                                    <?php 
                                    $hariColors = [
                                        'Senin' => 'primary',
                                        'Selasa' => 'success', 
                                        'Rabu' => 'warning',
                                        'Kamis' => 'info',
                                        'Jumat' => 'danger',
                                        'Sabtu' => 'secondary'
                                    ];
                                    $color = $hariColors[$j['hari'] ?? ''] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= $j['hari'] ?? '-' ?></span>
                                </td>
                                <td>
                                    <strong><?= $j['jam_mulai'] ?? '-' ?></strong> - <strong><?= $j['jam_selesai'] ?? '-' ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    if (isset($j['jam_mulai']) && isset($j['jam_selesai'])) {
                                        $mulai = strtotime($j['jam_mulai']);
                                        $selesai = strtotime($j['jam_selesai']);
                                        $durasi = round(($selesai - $mulai) / 3600, 1);
                                        echo '<span class="badge bg-info">' . $durasi . ' jam</span>';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $semesterColor = ($j['semester'] ?? '') == 'Ganjil' ? 'warning' : 'success';
                                    ?>
                                    <span class="badge bg-<?= $semesterColor ?>"><?= $j['semester'] ?? '-' ?></span>
                                </td>
                                <td><?= $j['tahun_ajaran'] ?? '-' ?></td>
                                <td>
                                    <a href="<?= base_url('admin/jadwal/edit/' . ($j['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/jadwal/delete/' . ($j['id'] ?? '')) ?>', '<?= $j['mata_pelajaran'] ?? 'Data' ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[4, 'asc'], [5, 'asc']], // Sort by hari, then jam_mulai
        columnDefs: [
            {
                targets: [0, 9], // No and Aksi columns
                orderable: false
            }
        ]
    });
});

function confirmDelete(url, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus jadwal "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
<?= $this->endSection() ?> 