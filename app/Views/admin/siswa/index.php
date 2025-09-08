<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Siswa</h1>
        <a href="<?= base_url('admin/siswa/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Siswa
        </a>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filterKelas" class="form-label">Filter Kelas</label>
                    <select id="filterKelas" class="form-select form-select-sm">
                        <option value="">Semua Kelas</option>
                        <?php
                        $kelasOptions = [];
                        foreach ($siswa ?? [] as $siswa_item) {
                            if (!empty($siswa_item['nama_kelas']) && !in_array($siswa_item['nama_kelas'], $kelasOptions)) {
                                $kelasOptions[] = $siswa_item['nama_kelas'];
                            }
                        }
                        sort($kelasOptions);
                        foreach ($kelasOptions as $kelas): ?>
                            <option value="<?= $kelas ?>"><?= $kelas ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterJurusan" class="form-label">Filter Jurusan</label>
                    <select id="filterJurusan" class="form-select form-select-sm">
                        <option value="">Semua Jurusan</option>
                        <?php
                        $jurusanOptions = [];
                        foreach ($siswa ?? [] as $siswa_item) {
                            if (!empty($siswa_item['nama_jurusan']) && !in_array($siswa_item['nama_jurusan'], $jurusanOptions)) {
                                $jurusanOptions[] = $siswa_item['nama_jurusan'];
                            }
                        }
                        sort($jurusanOptions);
                        foreach ($jurusanOptions as $jurusan): ?>
                            <option value="<?= $jurusan ?>"><?= $jurusan ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterJenisKelamin" class="form-label">Filter Jenis Kelamin</label>
                    <select id="filterJenisKelamin" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Filter Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-secondary btn-sm" id="resetFilter">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($siswa ?? [] as $siswa_item): ?>
                            <tr data-jenis-kelamin="<?= $siswa_item['jenis_kelamin'] ?? '' ?>" 
                                data-status="<?= ($siswa_item['is_active'] ?? false) ? '1' : '0' ?>">
                                <td><?= $no++ ?></td>
                                <td><?= $siswa_item['nis'] ?? '' ?></td>
                                <td><?= $siswa_item['full_name'] ?? '' ?></td>
                                <td><?= $siswa_item['email'] ?? '' ?></td>
                                <td>
                                    <?php if (($siswa_item['jenis_kelamin'] ?? '') == 'L'): ?>
                                        <span class="badge bg-primary">Laki-laki</span>
                                    <?php else: ?>
                                        <span class="badge bg-pink">Perempuan</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $siswa_item['nama_kelas'] ?? '' ?></td>
                                <td><?= $siswa_item['nama_jurusan'] ?? '' ?></td>
                                <td>
                                    <?php if (($siswa_item['is_active'] ?? false)): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/siswa/edit/' . ($siswa_item['id'] ?? '')) ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteSiswa(<?= $siswa_item['id'] ?? '' ?>, '<?= $siswa_item['full_name'] ?? '' ?>')" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        columnDefs: [
            {
                targets: -1,
                orderable: false,
                searchable: false
            }
        ]
    });

    // Filter functions
    $('#filterKelas').on('change', function() {
        var selectedKelas = this.value;
        if (selectedKelas === '') {
            table.column(5).search('').draw();
        } else {
            table.column(5).search('^' + selectedKelas + '$', true, false).draw();
        }
    });

    $('#filterJurusan').on('change', function() {
        var selectedJurusan = this.value;
        if (selectedJurusan === '') {
            table.column(6).search('').draw();
        } else {
            table.column(6).search('^' + selectedJurusan + '$', true, false).draw();
        }
    });

    $('#filterJenisKelamin').on('change', function() {
        var selectedJK = this.value;
        if (selectedJK === '') {
            table.column(4).search('').draw();
        } else {
            var searchText = selectedJK === 'L' ? 'Laki-laki' : 'Perempuan';
            table.column(4).search(searchText).draw();
        }
    });

    $('#filterStatus').on('change', function() {
        var selectedStatus = this.value;
        if (selectedStatus === '') {
            table.column(7).search('').draw();
        } else {
            var searchText = selectedStatus === '1' ? 'Aktif' : 'Nonaktif';
            table.column(7).search(searchText).draw();
        }
    });

    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#filterKelas').val('');
        $('#filterJurusan').val('');
        $('#filterJenisKelamin').val('');
        $('#filterStatus').val('');
        
        table.search('').columns().search('').draw();
    });
});

function deleteSiswa(id, nama) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus siswa "${nama}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= base_url('admin/siswa/delete') ?>/${id}`;
        }
    });
}
</script>
<?= $this->endSection() ?>