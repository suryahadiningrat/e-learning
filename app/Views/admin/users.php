<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Aktivasi</h1>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="alert alert-info text-center">
                    <h4 class="alert-heading">Data User Aktivasi Tidak Ada</h4>
                    <p class="mb-0">Saat ini tidak ada user yang memerlukan aktivasi.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= $user['username'] ?? '-' ?></strong>
                                </td>
                                <td><?= $user['full_name'] ?? '-' ?></td>
                                <td><?= $user['email'] ?? '-' ?></td>
                                <td>
                                    <?php
                                    $roleClass = '';
                                    $roleText = '';
                                    switch ($user['role'] ?? '') {
                                        case 'admin':
                                            $roleClass = 'bg-danger';
                                            $roleText = 'Admin';
                                            break;
                                        case 'guru':
                                            $roleClass = 'bg-primary';
                                            $roleText = 'Guru';
                                            break;
                                        case 'siswa':
                                            $roleClass = 'bg-success';
                                            $roleText = 'Siswa';
                                            break;
                                        default:
                                            $roleClass = 'bg-secondary';
                                            $roleText = 'Unknown';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $roleClass ?>"><?= $roleText ?></span>
                                </td>
                                <td>
                                    <?php if ($user['is_active'] ?? false): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['created_at'] ? date('d/m/Y H:i', strtotime($user['created_at'])) : '-' ?></td>
                                <td>
                                    <?php if ($user['is_active'] ?? false): ?>
                                        <a href="javascript:void(0)" 
                                           class="btn btn-warning btn-sm"
                                           onclick="confirmDeactivate('<?= base_url('admin/users/deactivate/' . ($user['id'] ?? '')) ?>', '<?= $user['username'] ?? 'User' ?>')">
                                            <i class="fas fa-ban"></i> Nonaktifkan
                                        </a>
                                    <?php else: ?>
                                        <a href="javascript:void(0)" 
                                           class="btn btn-success btn-sm"
                                           onclick="confirmActivate('<?= base_url('admin/users/activate/' . ($user['id'] ?? '')) ?>', '<?= $user['username'] ?? 'User' ?>')">
                                            <i class="fas fa-check"></i> Aktivasi
                                        </a>
                                    <?php endif; ?>
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

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});

function confirmActivate(url, username) {
    Swal.fire({
        title: 'Konfirmasi Aktivasi',
        text: `Yakin ingin mengaktivasi user "${username}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Aktivasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

function confirmDeactivate(url, username) {
    Swal.fire({
        title: 'Konfirmasi Nonaktifkan',
        text: `Yakin ingin menonaktifkan user "${username}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
<?= $this->endSection() ?> 