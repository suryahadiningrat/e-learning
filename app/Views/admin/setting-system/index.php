<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php
// Function to extract hex color from gradient or return the color itself
function extractColorFromGradient($colorValue, $defaultColor) {
    if (empty($colorValue)) {
        return $defaultColor;
    }
    
    // If it's a gradient, extract the first color
    if (strpos($colorValue, 'linear-gradient') !== false) {
        // Extract color from gradient pattern like "linear-gradient(to bottom, #4e73df, #224abe)"
        preg_match('/#[a-fA-F0-9]{6}/', $colorValue, $matches);
        return !empty($matches) ? $matches[0] : $defaultColor;
    }
    
    // If it's already a hex color, return it
    if (preg_match('/^#[a-fA-F0-9]{6}$/', $colorValue)) {
        return $colorValue;
    }
    
    return $defaultColor;
}

// Extract colors for each role
$adminColor = extractColorFromGradient($settings['sidebar_color_admin'] ?? '', '#4e73df');
$guruColor = extractColorFromGradient($settings['sidebar_color_guru'] ?? '', '#1cc88a');
$siswaColor = extractColorFromGradient($settings['sidebar_color_siswa'] ?? '', '#f6c23e');
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Setting System</h1>
    </div>

    <!-- Logo Sekolah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-image me-2"></i>Logo Sekolah
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <?php if (isset($settings['logo_sekolah']) && $settings['logo_sekolah']): ?>
                            <img src="<?= base_url('uploads/logo/' . $settings['logo_sekolah']) ?>" 
                                 alt="Logo Sekolah" class="img-fluid border rounded" 
                                 style="max-height: 200px; max-width: 100%;">
                        <?php else: ?>
                            <div class="border rounded d-flex align-items-center justify-content-center" 
                                 style="height: 200px; background-color: #f8f9fa;">
                                <span class="text-muted">Logo belum diupload</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <form action="<?= base_url('admin/setting-system/update-logo') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Upload Logo Baru</label>
                            <input type="file" class="form-control <?= session('errors.logo') ? 'is-invalid' : '' ?>" 
                                   id="logo" name="logo" accept="image/*" required>
                            <div class="form-text">
                                Format: JPG, JPEG, PNG, GIF | Maksimal: 2MB
                            </div>
                            <?php if (session('errors.logo')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.logo') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Logo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tahun Ajaran -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar me-2"></i>Tahun Ajaran
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/setting-system/update-tahun-ajaran') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control <?= session('errors.tahun_ajaran') ? 'is-invalid' : '' ?>" 
                                   id="tahun_ajaran" name="tahun_ajaran" 
                                   value="<?= old('tahun_ajaran', $settings['tahun_ajaran'] ?? '') ?>" 
                                   placeholder="Contoh: 2024/2025" required>
                            <div class="form-text">
                                Format: 2024/2025 atau 2024-2025
                            </div>
                            <?php if (session('errors.tahun_ajaran')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tahun_ajaran') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Tahun Ajaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Background Login -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-desktop me-2"></i>Background Halaman Login
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Background Saat Ini</label>
                        <div class="border rounded p-3" style="height: 150px; background: <?= $settings['login_background_color'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' ?>; position: relative;">
                            <?php if (isset($settings['login_background_image']) && $settings['login_background_image']): ?>
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('<?= base_url('uploads/background/' . $settings['login_background_image']) ?>'); background-size: cover; background-position: center; border-radius: 0.375rem;"></div>
                            <?php endif; ?>
                            <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(255,255,255,0.8); padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                                Preview Login
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Background Color -->
                    <div class="mb-3">
                        <label for="loginBackgroundColor" class="form-label font-weight-bold">Warna Background</label>
                        <div class="input-group">
                            <input type="text" class="form-control color-picker-login" id="loginBackgroundColor" 
                                   value="<?= extractColorFromGradient($settings['login_background_color'] ?? '', '#667eea') ?>">
                            <button class="btn btn-outline-secondary" type="button" id="resetLoginColor">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                        <div class="form-text">Pilih warna untuk background login</div>
                    </div>
                    
                    <!-- Background Image -->
                    <div class="mb-3">
                        <label for="loginBackgroundImage" class="form-label font-weight-bold">Gambar Background (Opsional)</label>
                        <form action="<?= base_url('admin/setting-system/update-login-background-image') ?>" method="post" enctype="multipart/form-data" id="backgroundImageForm">
                            <?= csrf_field() ?>
                            <input type="file" class="form-control <?= session('errors.background_image') ? 'is-invalid' : '' ?>" 
                                   id="loginBackgroundImage" name="background_image" accept="image/*">
                            <div class="form-text">
                                Format: JPG, JPEG, PNG, GIF | Maksimal: 5MB | Kosongkan jika ingin menggunakan warna saja
                            </div>
                            <?php if (session('errors.background_image')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.background_image') ?>
                                </div>
                            <?php endif; ?>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload me-1"></i>Upload Gambar
                                </button>
                                <?php if (isset($settings['login_background_image']) && $settings['login_background_image']): ?>
                                    <button type="button" class="btn btn-danger btn-sm ms-2" id="removeBackgroundImage">
                                        <i class="fas fa-trash me-1"></i>Hapus Gambar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warna Sidebar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-paint-brush me-2"></i>Warna Sidebar
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Admin Color -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold mb-2">Warna Sidebar Admin</label>
                        <div class="input-group">
                            <input type="text" class="form-control color-picker" id="adminColor" 
                                   data-role="admin"
                                   value="<?= $adminColor ?>">
                        </div>
                        <div class="mt-2">
                            <div class="sidebar-preview rounded" style="height: 50px; background: <?= $settings['sidebar_color_admin'] ?? 'linear-gradient(to bottom, #4e73df, #224abe)' ?>"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Guru Color -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold mb-2">Warna Sidebar Guru</label>
                        <div class="input-group">
                            <input type="text" class="form-control color-picker" id="guruColor" 
                                   data-role="guru"
                                   value="<?= $guruColor ?>">
                        </div>
                        <div class="mt-2">
                            <div class="sidebar-preview rounded" style="height: 50px; background: <?= $settings['sidebar_color_guru'] ?? 'linear-gradient(to bottom, #1cc88a, #169b6b)' ?>"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Siswa Color -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold mb-2">Warna Sidebar Siswa</label>
                        <div class="input-group">
                            <input type="text" class="form-control color-picker" id="siswaColor" 
                                   data-role="siswa"
                                   value="<?= $siswaColor ?>">
                        </div>
                        <div class="mt-2">
                            <div class="sidebar-preview rounded" style="height: 50px; background: <?= $settings['sidebar_color_siswa'] ?? 'linear-gradient(to bottom, #f6c23e, #dda20a)' ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Sistem -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>Informasi Sistem
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Nama Sistem:</strong></td>
                            <td>E-Learning Management System</td>
                        </tr>
                        <tr>
                            <td><strong>Versi:</strong></td>
                            <td>1.0.0</td>
                        </tr>
                        <tr>
                            <td><strong>Framework:</strong></td>
                            <td>CodeIgniter 4</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>PHP Version:</strong></td>
                            <td><?= phpversion() ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database:</strong></td>
                            <td>MySQL</td>
                        </tr>
                        <tr>
                            <td><strong>Last Updated:</strong></td>
                            <td><?= date('d/m/Y H:i:s') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Preview image sebelum upload
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector('.col-md-4 img') || document.querySelector('.col-md-4 .border');
            if (preview) {
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-fluid border rounded" style="max-height: 200px; max-width: 100%;">`;
                }
            }
        };
        reader.readAsDataURL(file);
    }
});

// Load TinyColor for color manipulation first
const tinyColorScript = document.createElement('script');
tinyColorScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinycolor/1.4.2/tinycolor.min.js';
document.head.appendChild(tinyColorScript);

// Load Spectrum Color Picker
const spectrumCss = document.createElement('link');
spectrumCss.rel = 'stylesheet';
spectrumCss.href = 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css';
document.head.appendChild(spectrumCss);

const spectrumJs = document.createElement('script');
spectrumJs.src = 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js';
document.head.appendChild(spectrumJs);

// Initialize color pickers after both libraries are loaded
let scriptsLoaded = 0;
function checkScriptsLoaded() {
    scriptsLoaded++;
    if (scriptsLoaded === 2) {
        initializeColorPickers();
    }
}

tinyColorScript.onload = checkScriptsLoaded;
spectrumJs.onload = checkScriptsLoaded;

function initializeColorPickers() {
    // Initialize sidebar color pickers
    $('.color-picker').spectrum({
        showInput: true,
        showInitial: true,
        preferredFormat: "hex",
        showPalette: true,
        showSelectionPalette: true,
        showAlpha: false,
        allowEmpty: false,
        clickoutFiresChange: true,
        palette: [
            ['#4e73df', '#224abe'], // Admin default
            ['#1cc88a', '#169b6b'], // Guru default
            ['#f6c23e', '#dda20a']  // Siswa default
        ],
        change: function(color) {
            const role = $(this).data('role');
            const startColor = color.toHexString();
            
            // Update the input field value immediately
            $(this).val(startColor);
            
            // Create slightly darker color for gradient
            const endColor = tinycolor(startColor).darken(15).toHexString();
            const gradientValue = `linear-gradient(to bottom, ${startColor}, ${endColor})`;
            
            // Update preview immediately
            $(this).closest('.form-group').find('.sidebar-preview').css('background', gradientValue);
            
            // Save to database via AJAX
            $.ajax({
                url: '<?= base_url('admin/setting-system/update-sidebar-color') ?>',
                type: 'POST',
                data: {
                    role: role,
                    color: gradientValue,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            if (response.refresh) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan pengaturan'
                    });
                }
            });
        },
        move: function(color) {
            // Update preview during color selection (real-time preview)
            const startColor = color.toHexString();
            
            // Update the input field value during move
            $(this).val(startColor);
            
            const endColor = tinycolor(startColor).darken(15).toHexString();
            const gradientValue = `linear-gradient(to bottom, ${startColor}, ${endColor})`;
            
            // Update preview in real-time
            $(this).closest('.form-group').find('.sidebar-preview').css('background', gradientValue);
        }
    });
    
    // Initialize login background color picker
    $('.color-picker-login').spectrum({
        showInput: true,
        showInitial: true,
        preferredFormat: "hex",
        showPalette: true,
        showSelectionPalette: true,
        showAlpha: false,
        allowEmpty: false,
        clickoutFiresChange: true,
        palette: [
            ['#667eea', '#764ba2'], // Default login colors
            ['#4e73df', '#224abe'], // Admin colors
            ['#1cc88a', '#169b6b'], // Guru colors
            ['#f6c23e', '#dda20a']  // Siswa colors
        ],
        change: function(color) {
            const startColor = color.toHexString();
            const endColor = tinycolor(startColor).darken(15).toHexString();
            const gradientValue = `linear-gradient(135deg, ${startColor} 0%, ${endColor} 100%)`;
            
            // Update preview immediately
            updateLoginPreview(gradientValue);
            
            // Save to database via AJAX
            $.ajax({
                url: '<?= base_url('admin/setting-system/update-login-background-color') ?>',
                type: 'POST',
                data: {
                    color: gradientValue,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan pengaturan'
                    });
                }
            });
        },
        move: function(color) {
            const startColor = color.toHexString();
            const endColor = tinycolor(startColor).darken(15).toHexString();
            const gradientValue = `linear-gradient(135deg, ${startColor} 0%, ${endColor} 100%)`;
            
            // Update preview in real-time
            updateLoginPreview(gradientValue);
        }
    });
}

// Function to update login preview
function updateLoginPreview(backgroundColor, backgroundImage = null) {
    const previewDiv = $('.border.rounded.p-3').first();
    if (previewDiv.length) {
        previewDiv.css('background', backgroundColor);
        
        if (backgroundImage) {
            // Remove existing background image div if any
            previewDiv.find('div[style*="background-image"]').remove();
            
            // Add new background image div
            previewDiv.prepend(`<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('${backgroundImage}'); background-size: cover; background-position: center; border-radius: 0.375rem;"></div>`);
        }
    }
}

// Reset login background color
$('#resetLoginColor').click(function() {
    const defaultColor = '#667eea';
    const defaultGradient = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    
    $('.color-picker-login').spectrum('set', defaultColor);
    updateLoginPreview(defaultGradient);
    
    // Save to database
    $.ajax({
        url: '<?= base_url('admin/setting-system/update-login-background-color') ?>',
        type: 'POST',
        data: {
            color: defaultGradient,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Warna background direset ke default',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }
    });
});

// Remove background image
$('#removeBackgroundImage').click(function() {
    Swal.fire({
        title: 'Hapus Gambar Background?',
        text: 'Gambar background login akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin/setting-system/remove-login-background-image') ?>',
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus gambar'
                    });
                }
            });
        }
    });
});
</script>

<!-- SweetAlert2 is already included in the main layout -->

<?= $this->endSection() ?>