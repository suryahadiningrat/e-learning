<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Ulangan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/ulangan') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('admin/ulangan/update/' . $ulangan['id']) ?>" method="post" id="ulanganForm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="judul_ulangan">Judul Ulangan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul_ulangan" name="judul_ulangan" 
                                           value="<?= old('judul_ulangan', $ulangan['judul_ulangan']) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="mata_pelajaran_id">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select class="form-control" id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <?php foreach ($mata_pelajaran as $mp): ?>
                                            <option value="<?= $mp['id'] ?>" <?= old('mata_pelajaran_id', $ulangan['mata_pelajaran_id']) == $mp['id'] ? 'selected' : '' ?>>
                                                <?= $mp['nama'] ?> (<?= $mp['kode'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="kelas_id" name="kelas_id" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($kelas as $k): ?>
                                            <option value="<?= $k['id'] ?>" <?= old('kelas_id', $ulangan['kelas_id']) == $k['id'] ? 'selected' : '' ?>>
                                                <?= $k['nama_kelas'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_mulai">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="waktu_mulai" name="waktu_mulai" 
                                           value="<?= old('waktu_mulai', date('Y-m-d\TH:i', strtotime($ulangan['waktu_mulai']))) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="waktu_selesai">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="waktu_selesai" name="waktu_selesai" 
                                           value="<?= old('waktu_selesai', date('Y-m-d\TH:i', strtotime($ulangan['waktu_selesai']))) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="durasi_menit">Durasi (Menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="durasi_menit" name="durasi_menit" 
                                           value="<?= old('durasi_menit', $ulangan['durasi_menit']) ?>" min="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <h5>Soal Ulangan</h5>
                                <div id="soalContainer">
                                    <!-- Soal akan ditambahkan di sini -->
                                </div>
                                <button type="button" class="btn btn-success" onclick="tambahSoal()">
                                    <i class="fas fa-plus"></i> Tambah Soal
                                </button>
                            </div>
                        </div>
                        
                        <input type="hidden" name="soal_json" id="soal_json" value="<?= htmlspecialchars($ulangan['soal_json']) ?>">
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('admin/ulangan') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Tunggu sampai document ready dan jQuery tersedia
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan jQuery tersedia
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    
    // Load soal yang sudah ada
    try {
        var soalJson = JSON.parse('<?= addslashes($ulangan['soal_json']) ?>');
        console.log('Soal JSON:', soalJson); // Debug
        
        if (soalJson && soalJson.soal && soalJson.soal.length > 0) {
            console.log('Loading', soalJson.soal.length, 'soal'); // Debug
            soalJson.soal.forEach(function(soal, index) {
                console.log('Loading soal:', soal); // Debug
                tambahSoal(soal);
            });
        } else {
            console.log('No soal found or invalid format'); // Debug
        }
    } catch (e) {
        console.error('Error parsing soal JSON:', e);
    }
});

// Fungsi untuk menambah soal
function tambahSoal(soalData = null) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    
    var soalIndex = jQuery('.soal-item').length;
    var soalHtml = `
        <div class="soal-item border p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>Soal ${soalIndex + 1}</h6>
                <button type="button" class="btn btn-danger btn-sm" onclick="hapusSoal(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="form-group">
                <label>Pertanyaan:</label>
                <textarea class="form-control soal-pertanyaan" rows="3" placeholder="Masukkan pertanyaan...">${soalData ? soalData.pertanyaan : ''}</textarea>
            </div>
            <div class="form-group">
                <label>Tipe Soal:</label>
                <select class="form-control soal-tipe" onchange="toggleJawaban(this)">
                    <option value="pilihan_ganda" ${soalData && soalData.tipe === 'pilihan_ganda' ? 'selected' : ''}>Pilihan Ganda</option>
                    <option value="essay" ${soalData && soalData.tipe === 'essay' ? 'selected' : ''}>Essay</option>
                </select>
            </div>
            <div class="soal-jawaban" style="display: ${soalData && soalData.tipe === 'pilihan_ganda' ? 'block' : 'none'};">
                <div class="form-group">
                    <label>Pilihan Jawaban:</label>
                    <div class="pilihan-container">
                        ${soalData && soalData.pilihan ? soalData.pilihan.map((pilihan, idx) => `
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">${String.fromCharCode(65 + idx)}</span>
                                </div>
                                <input type="text" class="form-control pilihan-jawaban" value="${pilihan}" placeholder="Pilihan ${String.fromCharCode(65 + idx)}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="radio" name="jawaban_benar_${soalIndex}" value="${idx}" ${soalData.jawaban_benar == idx ? 'checked' : ''}>
                                    </div>
                                </div>
                            </div>
                        `).join('') : `
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">A</span>
                                </div>
                                <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan A">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="radio" name="jawaban_benar_${soalIndex}" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">B</span>
                                </div>
                                <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan B">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="radio" name="jawaban_benar_${soalIndex}" value="1">
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">C</span>
                                </div>
                                <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan C">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="radio" name="jawaban_benar_${soalIndex}" value="2">
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">D</span>
                                </div>
                                <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan D">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="radio" name="jawaban_benar_${soalIndex}" value="3">
                                    </div>
                                </div>
                            </div>
                        `}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Bobot Nilai:</label>
                <input type="number" class="form-control soal-bobot" value="${soalData ? soalData.bobot : 1}" min="1" max="100">
            </div>
        </div>
    `;
    jQuery('#soalContainer').append(soalHtml);
}

function hapusSoal(button) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    jQuery(button).closest('.soal-item').remove();
    updateSoalIndex();
}

function toggleJawaban(select) {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    var jawabanDiv = jQuery(select).closest('.soal-item').find('.soal-jawaban');
    if (jQuery(select).val() === 'pilihan_ganda') {
        jawabanDiv.show();
    } else {
        jawabanDiv.hide();
    }
}

function updateSoalIndex() {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    jQuery('.soal-item').each(function(index) {
        jQuery(this).find('h6').text('Soal ' + (index + 1));
    });
}

// Update soal JSON sebelum submit
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not available');
        return;
    }
    
    jQuery('#ulanganForm').on('submit', function() {
        var soalArray = [];
        jQuery('.soal-item').each(function(index) {
            var soal = {
                pertanyaan: jQuery(this).find('.soal-pertanyaan').val(),
                tipe: jQuery(this).find('.soal-tipe').val(),
                bobot: parseInt(jQuery(this).find('.soal-bobot').val())
            };
            
            if (soal.tipe === 'pilihan_ganda') {
                soal.pilihan = [];
                jQuery(this).find('.pilihan-jawaban').each(function() {
                    soal.pilihan.push(jQuery(this).val());
                });
                soal.jawaban_benar = parseInt(jQuery(this).find('input[type="radio"]:checked').val());
            }
            
            soalArray.push(soal);
        });
        
        jQuery('#soal_json').val(JSON.stringify({soal: soalArray}));
    });
});
</script>
<?= $this->endSection() ?> 