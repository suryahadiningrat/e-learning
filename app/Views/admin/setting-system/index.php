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
            
            // Create slightly darker color for gradient - ensure tinycolor is available
            const endColor = typeof tinycolor !== 'undefined' ? 
                tinycolor(startColor).darken(15).toHexString() : 
                startColor;
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
            const role = $(this).data('role');
            const startColor = color.toHexString();
            
            // Update the input field value during move
            $(this).val(startColor);
            
            const endColor = typeof tinycolor !== 'undefined' ? 
                tinycolor(startColor).darken(15).toHexString() : 
                startColor;
            const gradientValue = `linear-gradient(to bottom, ${startColor}, ${endColor})`;
            
            // Update preview in real-time
            $(this).closest('.form-group').find('.sidebar-preview').css('background', gradientValue);
        },
        show: function(color) {
            // Ensure the input shows the correct color when opened
            $(this).val(color.toHexString());
        }
    });
}
</script>

<!-- SweetAlert2 is already included in the main layout -->

<?= $this->endSection() ?> 