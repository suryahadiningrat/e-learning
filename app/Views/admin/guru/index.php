<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Guru</h1>
        <a href="<?= base_url('admin/guru/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Guru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Guru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Bidang Studi</th>
                            <th>Jenis Kelamin</th>
                            <th>No. Telepon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($guru ?? [] as $g) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $g['nip'] ?? '-' ?></td>
                                <td><?= $g['full_name'] ?? '-' ?></td>
                                <td><?= $g['email'] ?? '-' ?></td>
                                <td><?= $g['bidang_studi'] ?? '-' ?></td>
                                <td>
                                    <?php if (($g['jenis_kelamin'] ?? '') == 'L') : ?>
                                        <span class="badge bg-primary">Laki-laki</span>
                                    <?php else : ?>
                                        <span class="badge bg-pink">Perempuan</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $g['no_telp'] ?? '-' ?></td>
                                <td>
                                    <?php if (($g['is_active'] ?? 0) == 1) : ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/guru/edit/' . ($g['id'] ?? '')) ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/guru/print/' . ($g['id'] ?? '')) ?>" class="btn btn-success btn-sm" target="_blank" title="Print Data">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-info btn-sm" onclick="showJadwalModal(<?= $g['id'] ?? 0 ?>, '<?= addslashes($g['full_name'] ?? '') ?>')" title="Lihat Jadwal">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete('<?= base_url('admin/guru/delete/' . ($g['id'] ?? '')) ?>', '<?= $g['full_name'] ?? 'Data' ?>')" title="Hapus">
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

<!-- Modal Jadwal Guru -->
<div class="modal fade" id="jadwalModal" tabindex="-1" aria-labelledby="jadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jadwalModalLabel">Jadwal Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="jadwalContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat jadwal...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        }
    });
});

function confirmDelete(url, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus guru "${name}"?`,
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

function showJadwalModal(guruId, namaGuru) {
    // Set modal title
    document.getElementById('jadwalModalLabel').textContent = `Jadwal Mengajar - ${namaGuru}`;
    
    // Show loading
    document.getElementById('jadwalContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat jadwal...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('jadwalModal'));
    modal.show();
    
    // Fetch jadwal data
    fetch(`<?= base_url('admin/guru/jadwal/') ?>${guruId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayJadwal(data.jadwal);
            } else {
                document.getElementById('jadwalContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${data.error || 'Gagal memuat jadwal'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('jadwalContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Terjadi kesalahan saat memuat jadwal
                </div>
            `;
        });
}

function displayJadwal(jadwalList) {
    if (!jadwalList || jadwalList.length === 0) {
        document.getElementById('jadwalContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Guru ini belum memiliki jadwal mengajar
            </div>
        `;
        return;
    }
    
    // Group jadwal by hari
    const jadwalByHari = {};
    const hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    
    jadwalList.forEach(jadwal => {
        if (!jadwalByHari[jadwal.hari]) {
            jadwalByHari[jadwal.hari] = [];
        }
        jadwalByHari[jadwal.hari].push(jadwal);
    });
    
    let html = '<div class="row">';
    
    hariOrder.forEach(hari => {
        if (jadwalByHari[hari]) {
            html += `
                <div class="col-12 mb-3">
                    <h6 class="text-primary border-bottom pb-2">
                        <i class="fas fa-calendar-day"></i> ${hari}
                    </h6>
                    <div class="row">
            `;
            
            jadwalByHari[hari].forEach(jadwal => {
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="card border-left-primary h-100">
                            <div class="card-body py-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            ${jadwal.nama_mata_pelajaran}
                                        </div>
                                        <div class="text-sm mb-1">
                                            <i class="fas fa-users"></i> ${jadwal.nama_kelas}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <i class="fas fa-clock"></i> ${jadwal.jam_mulai} - ${jadwal.jam_selesai}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ${jadwal.semester} | ${jadwal.tahun_ajaran}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
    });
    
    html += '</div>';
    
    document.getElementById('jadwalContent').innerHTML = html;
}
</script>
<?= $this->endSection() ?>