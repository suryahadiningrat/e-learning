<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Pengguna</h1>
        <a href="<?= base_url('admin/user-pengguna/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah User
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar User Pengguna</h6>
        </div>
        <div class="card-body">
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
                            <th>Terakhir Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($users ?? [] as $user): ?>
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
                                        <span class="badge bg-warning">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['created_at'] ? date('d/m/Y H:i', strtotime($user['created_at'])) : '-' ?></td>
                                <td><?= $user['updated_at'] ? date('d/m/Y H:i', strtotime($user['updated_at'])) : '-' ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/user-pengguna/edit/' . ($user['id'] ?? '')) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (($user['id'] ?? '') != session()->get('user_id')): ?>
                                            <a href="javascript:void(0)" 
                                               class="btn btn-info btn-sm"
                                               onclick="confirmToggleStatus('<?= base_url('admin/user-pengguna/toggle-status/' . ($user['id'] ?? '')) ?>', '<?= $user['username'] ?? 'User' ?>', <?= $user['is_active'] ?? 0 ?>)">
                                                <i class="fas fa-toggle-<?= ($user['is_active'] ?? false) ? 'on' : 'off' ?>"></i>
                                            </a>
                                            <a href="javascript:void(0)" 
                                               class="btn btn-danger btn-sm"
                                               onclick="confirmDelete('<?= base_url('admin/user-pengguna/delete/' . ($user['id'] ?? '')) ?>', '<?= $user['username'] ?? 'User' ?>')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Akun Anda</span>
                                        <?php endif; ?>
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

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});

function confirmDelete(url, username) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Yakin ingin menghapus user "${username}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

function confirmToggleStatus(url, username, currentStatus) {
    const statusText = currentStatus ? 'menonaktifkan' : 'mengaktifkan';
    const statusIcon = currentStatus ? 'warning' : 'question';
    
    Swal.fire({
        title: 'Konfirmasi Ubah Status',
        text: `Yakin ingin ${statusText} user "${username}"?`,
        icon: statusIcon,
        showCancelButton: true,
        confirmButtonColor: currentStatus ? '#ffc107' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Ya, ${statusText}!`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
<?= $this->endSection() ?> 