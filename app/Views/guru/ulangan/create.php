<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Ulangan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('guru/ulangan') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('guru/ulangan/store') ?>" method="post" id="ulanganForm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="judul_ulangan">Judul Ulangan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul_ulangan" name="judul_ulangan" 
                                           value="<?= old('judul_ulangan') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="mata_pelajaran_id">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select class="form-control" id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <?php foreach ($mata_pelajaran as $mp): ?>
                                            <option value="<?= $mp['id'] ?>" <?= old('mata_pelajaran_id') == $mp['id'] ? 'selected' : '' ?>>
                                                <?= $mp['nama'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="kelas_id" name="kelas_id" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($kelas as $k): ?>
                                            <option value="<?= $k['id'] ?>" <?= old('kelas_id') == $k['id'] ? 'selected' : '' ?>>
                                                <?= $k['nama_kelas'] ?> <?= isset($k['nama_jurusan']) && $k['nama_jurusan'] ? ' - ' . $k['nama_jurusan'] : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_mulai">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="waktu_mulai" name="waktu_mulai" 
                                           value="<?= old('waktu_mulai') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="waktu_selesai">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="waktu_selesai" name="waktu_selesai" 
                                           value="<?= old('waktu_selesai') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="durasi_menit">Durasi (Menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="durasi_menit" name="durasi_menit" 
                                           value="<?= old('durasi_menit', 60) ?>" min="1" required>
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
                        
                        <input type="hidden" name="soal_json" id="soal_json" value='{"soal":[]}'>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="<?= base_url('guru/ulangan') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let soalCount = 0;

function tambahSoal() {
    soalCount++;
    const container = document.getElementById('soalContainer');
    
    const soalDiv = document.createElement('div');
    soalDiv.className = 'soal-item border rounded p-3 mb-3';
    soalDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Soal ${soalCount}</h6>
            <button type="button" class="btn btn-danger btn-sm" onclick="hapusSoal(this)">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
        
        <div class="form-group">
            <label>Pertanyaan <span class="text-danger">*</span></label>
            <textarea class="form-control soal-pertanyaan" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Tipe Soal <span class="text-danger">*</span></label>
            <select class="form-control soal-tipe" onchange="togglePilihan(this, ${soalCount})" required>
                <option value="">Pilih Tipe</option>
                <option value="pilihan_ganda">Pilihan Ganda</option>
                <option value="essay">Essay</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Bobot (nilai) <span class="text-danger">*</span></label>
            <input type="number" class="form-control soal-bobot" value="10" min="1" required>
        </div>
        
        <div id="pilihan_container_${soalCount}" class="pilihan-container" style="display: none;">
            <label>Pilihan Jawaban:</label>
            <div class="pilihan-list">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="jawaban_benar_${soalCount}" value="0">
                        </div>
                    </div>
                    <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan A">
                </div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="jawaban_benar_${soalCount}" value="1">
                        </div>
                    </div>
                    <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan B">
                </div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="jawaban_benar_${soalCount}" value="2">
                        </div>
                    </div>
                    <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan C">
                </div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="jawaban_benar_${soalCount}" value="3">
                        </div>
                    </div>
                    <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan D">
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(soalDiv);
}

function hapusSoal(button) {
    button.closest('.soal-item').remove();
    updateSoalNumbers();
}

function updateSoalNumbers() {
    const soalItems = document.querySelectorAll('.soal-item');
    soalItems.forEach((item, index) => {
        const title = item.querySelector('h6');
        title.textContent = `Soal ${index + 1}`;
    });
}

function togglePilihan(select, soalIndex) {
    const container = document.getElementById(`pilihan_container_${soalIndex}`);
    const radioButtons = container.querySelectorAll('input[type="radio"]');
    const pilihanInputs = container.querySelectorAll('.pilihan-jawaban');
    
    if (select.value === 'pilihan_ganda') {
        container.style.display = 'block';
        // Add required attribute for pilihan ganda
        radioButtons.forEach(radio => radio.setAttribute('required', 'required'));
        pilihanInputs.forEach(input => input.setAttribute('required', 'required'));
    } else {
        container.style.display = 'none';
        // Remove required attribute for essay
        radioButtons.forEach(radio => radio.removeAttribute('required'));
        pilihanInputs.forEach(input => input.removeAttribute('required'));
    }
}

// Auto-save soal ke JSON
function updateSoalJson() {
    const soalItems = document.querySelectorAll('.soal-item');
    const soal = [];
    
    soalItems.forEach((item, index) => {
        const pertanyaan = item.querySelector('.soal-pertanyaan').value;
        const tipe = item.querySelector('.soal-tipe').value;
        const bobot = parseInt(item.querySelector('.soal-bobot').value);
        
        const soalItem = {
            pertanyaan: pertanyaan,
            tipe: tipe,
            bobot: bobot
        };
        
        if (tipe === 'pilihan_ganda') {
            const pilihan = [];
            const pilihanInputs = item.querySelectorAll('.pilihan-jawaban');
            pilihanInputs.forEach(input => {
                pilihan.push(input.value);
            });
            const jawabanBenar = item.querySelector('input[type="radio"]:checked');
            soalItem.pilihan = pilihan;
            soalItem.jawaban_benar = jawabanBenar ? parseInt(jawabanBenar.value) : 0;
        }
        
        soal.push(soalItem);
    });
    
    document.getElementById('soal_json').value = JSON.stringify({ soal: soal });
    console.log('Soal JSON updated:', document.getElementById('soal_json').value);
}

// Update JSON saat form berubah
document.addEventListener('input', updateSoalJson);
document.addEventListener('change', updateSoalJson);

// Update JSON sebelum submit
document.getElementById('ulanganForm').addEventListener('submit', function(e) {
    // Validate each question first
    const soalItems = document.querySelectorAll('.soal-item');
    let isValid = true;
    let errorMessages = [];
    
    soalItems.forEach((item, index) => {
        const pertanyaan = item.querySelector('.soal-pertanyaan').value.trim();
        const tipe = item.querySelector('.soal-tipe').value;
        const bobot = item.querySelector('.soal-bobot').value;
        
        if (!pertanyaan) {
            isValid = false;
            errorMessages.push(`Soal ${index + 1}: Pertanyaan tidak boleh kosong`);
        }
        
        if (!tipe) {
            isValid = false;
            errorMessages.push(`Soal ${index + 1}: Tipe soal harus dipilih`);
        }
        
        if (!bobot || bobot < 1) {
            isValid = false;
            errorMessages.push(`Soal ${index + 1}: Bobot nilai harus lebih dari 0`);
        }
        
        if (tipe === 'pilihan_ganda') {
            const pilihanInputs = item.querySelectorAll('.pilihan-jawaban');
            const jawabanBenar = item.querySelector('input[type="radio"]:checked');
            
            let allPilihanFilled = true;
            pilihanInputs.forEach((input, idx) => {
                if (!input.value.trim()) {
                    allPilihanFilled = false;
                }
            });
            
            if (!allPilihanFilled) {
                isValid = false;
                errorMessages.push(`Soal ${index + 1}: Semua pilihan jawaban harus diisi`);
            }
            
            if (!jawabanBenar) {
                isValid = false;
                errorMessages.push(`Soal ${index + 1}: Pilih jawaban yang benar`);
            }
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Terdapat kesalahan:\n\n' + errorMessages.join('\n'));
        return false;
    }
    
    updateSoalJson();
    const soalJson = document.getElementById('soal_json').value;
    console.log('Submitting with soal JSON:', soalJson);
    
    if (!soalJson || soalJson === '{"soal":[]}') {
        e.preventDefault();
        alert('Harap tambahkan minimal 1 soal!');
        return false;
    }
});
</script>

<?= $this->endSection() ?> 