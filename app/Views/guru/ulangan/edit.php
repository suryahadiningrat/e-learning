<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Ulangan</h1>
        <a href="<?= base_url('guru/ulangan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form id="ulanganForm" action="<?= base_url('guru/ulangan/update/' . $ulangan['id']) ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Ulangan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="judul_ulangan">Judul Ulangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="judul_ulangan" name="judul_ulangan" 
                                   value="<?= old('judul_ulangan', $ulangan['judul_ulangan']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mata_pelajaran_id">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-control" id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php foreach ($mata_pelajaran as $mp): ?>
                                    <option value="<?= $mp['id'] ?>" <?= old('mata_pelajaran_id', $ulangan['mata_pelajaran_id']) == $mp['id'] ? 'selected' : '' ?>>
                                        <?= $mp['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                            <select class="form-control" id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= old('kelas_id', $ulangan['kelas_id']) == $k['id'] ? 'selected' : '' ?>>
                                        <?= $k['nama_kelas'] ?> <?= $k['nama_jurusan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="durasi_menit">Durasi (menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="durasi_menit" name="durasi_menit" 
                                   value="<?= old('durasi_menit', $ulangan['durasi_menit']) ?>" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="waktu_mulai">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="waktu_mulai" name="waktu_mulai" 
                                   value="<?= old('waktu_mulai', date('Y-m-d\TH:i', strtotime($ulangan['waktu_mulai']))) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="waktu_selesai">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="waktu_selesai" name="waktu_selesai" 
                                   value="<?= old('waktu_selesai', date('Y-m-d\TH:i', strtotime($ulangan['waktu_selesai']))) ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Soal</h6>
                <button type="button" class="btn btn-success btn-sm" onclick="tambahSoal()">
                    <i class="fas fa-plus"></i> Tambah Soal
                </button>
            </div>
            <div class="card-body">
                <div id="soalContainer">
                    <!-- Soal akan ditambahkan di sini -->
                </div>
                
                <input type="hidden" id="soal_json" name="soal_json" value="<?= htmlspecialchars($ulangan['soal_json']) ?>">
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Ulangan
                    </button>
                    <a href="<?= base_url('guru/ulangan') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let soalCount = 0;

// Load soal yang sudah ada saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const soalJson = document.getElementById('soal_json').value;
    console.log('Loading soal JSON:', soalJson);
    
    if (soalJson) {
        try {
            const soal = JSON.parse(soalJson);
            if (soal.soal && Array.isArray(soal.soal)) {
                soal.soal.forEach((s, index) => {
                    tambahSoal(s);
                });
            }
        } catch (e) {
            console.error('Error parsing soal JSON:', e);
        }
    }
});

function tambahSoal(soalData = null) {
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
            <textarea class="form-control soal-pertanyaan" rows="3" required>${soalData ? soalData.pertanyaan : ''}</textarea>
        </div>
        
        <div class="form-group">
            <label>Tipe Soal <span class="text-danger">*</span></label>
            <select class="form-control soal-tipe" onchange="togglePilihan(this, ${soalCount})" required>
                <option value="">Pilih Tipe</option>
                <option value="pilihan_ganda" ${soalData && soalData.tipe === 'pilihan_ganda' ? 'selected' : ''}>Pilihan Ganda</option>
                <option value="essay" ${soalData && soalData.tipe === 'essay' ? 'selected' : ''}>Essay</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Bobot (nilai) <span class="text-danger">*</span></label>
            <input type="number" class="form-control soal-bobot" value="${soalData ? soalData.bobot : 10}" min="1" required>
        </div>
        
        <div id="pilihan_container_${soalCount}" class="pilihan-container" style="display: ${soalData && soalData.tipe === 'pilihan_ganda' ? 'block' : 'none'};">
            <label>Pilihan Jawaban:</label>
            <div class="pilihan-list">
                ${soalData && soalData.pilihan ? soalData.pilihan.map((p, i) => `
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="jawaban_benar_${soalCount}" value="${i}" ${soalData.jawaban_benar == i ? 'checked' : ''} required>
                            </div>
                        </div>
                        <input type="text" class="form-control pilihan-jawaban" value="${p}" placeholder="Pilihan ${String.fromCharCode(65 + i)}" required>
                    </div>
                `).join('') : `
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="jawaban_benar_${soalCount}" value="0" required>
                            </div>
                        </div>
                        <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan A" required>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="jawaban_benar_${soalCount}" value="1" required>
                            </div>
                        </div>
                        <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan B" required>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="jawaban_benar_${soalCount}" value="2" required>
                            </div>
                        </div>
                        <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan C" required>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="jawaban_benar_${soalCount}" value="3" required>
                            </div>
                        </div>
                        <input type="text" class="form-control pilihan-jawaban" placeholder="Pilihan D" required>
                    </div>
                `}
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
    if (select.value === 'pilihan_ganda') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
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