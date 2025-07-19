<?= $this->extend('guru/layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Pengguna</h1>
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
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($users as $user): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= esc($user['username']) ?></strong></td>
                            <td><?= esc($user['full_name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <?php 
                                $roleClass = '';
                                switch($user['role']) {
                                    case 'admin': $roleClass = 'bg-danger'; break;
                                    case 'guru': $roleClass = 'bg-primary'; break;
                                    case 'siswa': $roleClass = 'bg-success'; break;
                                    default: $roleClass = 'bg-secondary';
                                }
                                ?>
                                <span class="badge <?= $roleClass ?>"><?= ucfirst(esc($user['role'])) ?></span>
                            </td>
                            <td>
                                <?php if($user['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 