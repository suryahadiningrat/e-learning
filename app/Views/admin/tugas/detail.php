<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0"><?= $title ?></h4>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('admin/tugas') ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <a href="<?= base_url('admin/tugas/edit/' . $tugas['id']) ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h3 class="mb-3"><?= esc($tugas['nama_tugas']) ?></h3>
                                
                                <?php if (!empty($tugas['deskripsi'])): ?>
                                    <div class="alert alert-light">
                                        <h6>Deskripsi:</h6>
                                        <p class="mb-0"><?= nl2br(esc($tugas['deskripsi'])) ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light">
                                        <em>Tidak ada deskripsi tugas</em>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Jadwal Info -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar"></i> Informasi Jadwal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Mata Pelajaran:</strong></td>
                                                    <td><?= esc($tugas['nama_mata_pelajaran']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Kelas:</strong></td>
                                                    <td><?= esc($tugas['nama_kelas']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Guru:</strong></td>
                                                    <td><?= esc($tugas['nama_guru']) ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Hari:</strong></td>
                                                    <td><?= esc($tugas['hari']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Waktu:</strong></td>
                                                    <td><?= date('H:i', strtotime($tugas['jam_mulai'])) ?> - <?= date('H:i', strtotime($tugas['jam_selesai'])) ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Status Card -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Status Tugas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Deadline:</strong>
                                        <?php if ($tugas['deadline']): ?>
                                            <span class="d-block text-<?= (strtotime($tugas['deadline']) < time()) ? 'danger' : 'success' ?>">
                                                <?= date('d M Y H:i', strtotime($tugas['deadline'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted d-block">Tidak ada deadline</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Total Pengumpulan:</strong>
                                        <span class="badge bg-primary ms-2"><?= count($pengumpulan ?? []) ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Selesai:</strong>
                                        <span class="badge bg-success ms-2"><?= count(array_filter($pengumpulan ?? [], fn($p) => $p['status'] == 'selesai')) ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Terlambat:</strong>
                                        <span class="badge bg-danger ms-2"><?= count(array_filter($pengumpulan ?? [], fn($p) => $p['status'] == 'terlambat')) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Info -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Detail Tugas</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Dibuat:</strong><br>
                                            <small><?= date('d M Y H:i', strtotime($tugas['created_at'])) ?></small>
                                        </li>
                                        <li class="mt-2"><strong>Diupdate:</strong><br>
                                            <small><?= date('d M Y H:i', strtotime($tugas['updated_at'])) ?></small>
                                        </li>
                                        <li class="mt-2"><strong>Pembuat:</strong><br>
                                            <small><?= esc($tugas['nama_pembuat']) ?></small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pengumpulan Tugas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload"></i> Daftar Pengumpulan Tugas
                </h5>
                <div>
                    <button class="btn btn-sm btn-success" onclick="exportData()">
                        <i class="fas fa-download"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($pengumpulan)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada pengumpulan tugas</h5>
                        <p class="text-muted">Siswa belum mengumpulkan tugas ini</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped" id="pengumpulanTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Link Tugas</th>
                                    <th>Status</th>
                                    <th>Tgl Pengumpulan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengumpulan as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <?= strtoupper(substr($item['nama_siswa'], 0, 1)) ?>
                                                </div>
                                                <strong><?= esc($item['nama_siswa']) ?></strong>
                                            </div>
                                        </td>
                                        <td><?= esc($item['nis']) ?></td>
                                        <td><?= esc($tugas['nama_kelas']) ?></td>
                                        <td>
                                            <a href="<?= esc($item['link_tugas']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> Lihat Link
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $item['status'] == 'selesai' ? 'success' : ($item['status'] == 'terlambat' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($item['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($item['submitted_at'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['submitted_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($tugas['deadline'] && strtotime($item['submitted_at']) > strtotime($tugas['deadline'])): ?>
                                                <small class="text-danger">
                                                    <i class="fas fa-clock"></i> 
                                                    Terlambat <?= time_diff($tugas['deadline'], $item['submitted_at']) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Tepat waktu
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= esc($item['link_tugas']) ?>" target="_blank" class="btn btn-sm btn-primary" title="Lihat Tugas">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-info" onclick="showDetail('<?= esc($item['nama_siswa']) ?>', '<?= esc($item['link_tugas']) ?>', '<?= $item['submitted_at'] ?>')" title="Detail">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengumpulan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    <!-- Content will be loaded here -->
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
    // DataTable for pengumpulan
    $('#pengumpulanTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[6, 'desc']], // Sort by tanggal pengumpulan desc
        columnDefs: [
            { targets: [8], orderable: false }
        ],
        pageLength: 25
    });
});

function showDetail(nama, link, tanggal) {
    const content = `
        <div class="mb-3">
            <strong>Nama Siswa:</strong><br>
            ${nama}
        </div>
        <div class="mb-3">
            <strong>Link Tugas:</strong><br>
            <a href="${link}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fas fa-external-link-alt"></i> Buka Link
            </a>
        </div>
        <div class="mb-3">
            <strong>Tanggal Pengumpulan:</strong><br>
            ${new Date(tanggal).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

function exportData() {
    // Simple export functionality
    const table = document.getElementById('pengumpulanTable');
    let csv = 'No,Nama Siswa,NIS,Kelas,Link Tugas,Status,Tanggal Pengumpulan\n';
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        csv += `${index + 1},"${cells[1].textContent.trim()}","${cells[2].textContent}","${cells[3].textContent}","${cells[4].querySelector('a').href}","${cells[5].textContent}","${cells[6].textContent}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', `pengumpulan_tugas_<?= esc($tugas['nama_tugas']) ?>_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

<?php if (!function_exists('time_diff')): ?>
<?php 
function time_diff($datetime1, $datetime2) {
    $interval = date_diff(date_create($datetime1), date_create($datetime2));
    
    if ($interval->days > 0) {
        return $interval->days . ' hari';
    } elseif ($interval->h > 0) {
        return $interval->h . ' jam';
    } else {
        return $interval->i . ' menit';
    }
}
?>
<?php endif; ?>
</script>
<?= $this->endSection() ?>
