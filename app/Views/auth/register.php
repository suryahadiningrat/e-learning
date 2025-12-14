<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Informasi SMK Negeri 1 Pleret</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .register-container::-webkit-scrollbar {
            width: 8px;
        }
        .register-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .register-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .register-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .register-body {
            padding: 40px 30px;
        }
        .section-title {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 20px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .login-link {
            text-align: center;
            color: #6c757d;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: transparent;
            border-right: none;
        }
        .form-control, .form-select {
            border-left: none;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        .data-pribadi-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .data-pribadi-section.show {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .required-star {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2><i class="fas fa-user-plus me-2"></i>Register</h2>
            <p class="mb-0 mt-2">Daftar akun baru</p>
        </div>
        
        <div class="register-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->get('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/processRegister') ?>" method="post" id="registerForm">
                <!-- Data Akun -->
                <h6 class="section-title"><i class="fas fa-user-circle me-2"></i>Data Akun</h6>
                
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control" name="username" placeholder="Username *" value="<?= old('username') ?>" required>
                </div>
                
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control" name="email" placeholder="Email *" value="<?= old('email') ?>" required>
                </div>
                
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" class="form-control" name="full_name" placeholder="Nama Lengkap *" value="<?= old('full_name') ?>" required>
                </div>
                
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-users"></i>
                    </span>
                    <select class="form-select" name="role" id="roleSelect" required>
                        <option value="">Pilih Role *</option>
                        <option value="guru" <?= old('role') == 'guru' ? 'selected' : '' ?>>Guru</option>
                        <option value="siswa" <?= old('role') == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                    </select>
                </div>
                
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" name="password" placeholder="Password *" required>
                </div>
                
                <div class="input-group mb-4">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Konfirmasi Password *" required>
                </div>
                
                <!-- Data Pribadi Guru -->
                <div id="guruSection" class="data-pribadi-section">
                    <h6 class="section-title"><i class="fas fa-chalkboard-teacher me-2"></i>Data Pribadi Guru</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                <input type="text" class="form-control" name="nip" placeholder="Masukkan NIP" value="<?= old('nip') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bidang Studi <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-book"></i></span>
                                <input type="text" class="form-control" name="bidang_studi" placeholder="Bidang Studi" value="<?= old('bidang_studi') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select class="form-select" name="jenis_kelamin_guru">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" <?= old('jenis_kelamin_guru') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin_guru') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" name="no_telp_guru" placeholder="No. Telepon" value="<?= old('no_telp_guru') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" name="tempat_lahir_guru" placeholder="Tempat Lahir" value="<?= old('tempat_lahir_guru') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="tanggal_lahir_guru" value="<?= old('tanggal_lahir_guru') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                            <textarea class="form-control" name="alamat_guru" rows="2" placeholder="Alamat lengkap"><?= old('alamat_guru') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Data Pribadi Siswa -->
                <div id="siswaSection" class="data-pribadi-section">
                    <h6 class="section-title"><i class="fas fa-user-graduate me-2"></i>Data Pribadi Siswa</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                <input type="text" class="form-control" name="nis" placeholder="Masukkan NIS" value="<?= old('nis') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select class="form-select" name="jenis_kelamin_siswa">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" <?= old('jenis_kelamin_siswa') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin_siswa') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" name="no_telp_siswa" placeholder="No. Telepon" value="<?= old('no_telp_siswa') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-school"></i></span>
                                <select class="form-select" name="kelas_id">
                                    <option value="">Pilih Kelas</option>
                                    <?php 
                                    $db = \Config\Database::connect();
                                    $kelasList = $db->table('kelas k')
                                        ->select('k.id, k.tingkat, k.kode_jurusan, k.paralel, j.nama_jurusan')
                                        ->join('jurusan j', 'j.id = k.jurusan_id')
                                        ->orderBy('k.tingkat, k.kode_jurusan, k.paralel')
                                        ->get()->getResultArray();
                                    foreach ($kelasList as $kelas): ?>
                                        <option value="<?= $kelas['id'] ?>" <?= old('kelas_id') == $kelas['id'] ? 'selected' : '' ?>>
                                            <?= $kelas['tingkat'] . ' ' . $kelas['kode_jurusan'] . ' ' . $kelas['paralel'] ?> - <?= $kelas['nama_jurusan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" name="tempat_lahir_siswa" placeholder="Tempat Lahir" value="<?= old('tempat_lahir_siswa') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="tanggal_lahir_siswa" value="<?= old('tanggal_lahir_siswa') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                            <textarea class="form-control" name="alamat_siswa" rows="2" placeholder="Alamat lengkap"><?= old('alamat_siswa') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-register">
                    <i class="fas fa-user-plus me-2"></i>Register
                </button>
            </form>
            
            <div class="login-link">
                Sudah punya akun? <a href="<?= base_url('auth') ?>">Login disini</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('roleSelect').addEventListener('change', function() {
            const guruSection = document.getElementById('guruSection');
            const siswaSection = document.getElementById('siswaSection');
            
            // Hide all sections first
            guruSection.classList.remove('show');
            siswaSection.classList.remove('show');
            
            // Disable all inputs in hidden sections
            guruSection.querySelectorAll('input, select, textarea').forEach(el => el.required = false);
            siswaSection.querySelectorAll('input, select, textarea').forEach(el => el.required = false);
            
            if (this.value === 'guru') {
                guruSection.classList.add('show');
                // Enable required for guru fields
                guruSection.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.name !== '') el.required = true;
                });
            } else if (this.value === 'siswa') {
                siswaSection.classList.add('show');
                // Enable required for siswa fields
                siswaSection.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.name !== '') el.required = true;
                });
            }
        });
        
        // Trigger change on page load if role is already selected
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>
