<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Ulangan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/ulangan') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('admin/ulangan/store') ?>" method="post" id="ulanganForm">
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
                                            <option value="<?= $k['id'] ?>" <?= old('kelas_id') == $k['id'] ? 'selected' : '' ?>>
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
                        
                        <input type="hidden" name="soal_json" id="soal_json" value="[]">
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
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
let soalCounter = 0;
let soalArray = [];

function tambahSoal() {
    soalCounter++;
    const soalHtml = `
        <div class="card mb-3" id="soal_${soalCounter}">
            <div class="card-header">
                <h6 class="mb-0">Soal ${soalCounter}</h6>
                <button type="button" class="btn btn-danger btn-sm float-right" onclick="hapusSoal(${soalCounter})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Tipe Soal</label>
                    <select class="form-control tipe-soal" onchange="ubahTipeSoal(${soalCounter})">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <textarea class="form-control pertanyaan" rows="3" placeholder="Masukkan pertanyaan..."></textarea>
                </div>
                
                <div class="pilihan-ganda-container">
                    <div class="form-group">
                        <label>Pilihan Jawaban</label>
                        <div class="pilihan-container">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">A</span>
                                </div>
                                <input type="text" class="form-control pilihan" placeholder="Pilihan A">
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">B</span>
                                </div>
                                <input type="text" class="form-control pilihan" placeholder="Pilihan B">
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">C</span>
                                </div>
                                <input type="text" class="form-control pilihan" placeholder="Pilihan C">
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">D</span>
                                </div>
                                <input type="text" class="form-control pilihan" placeholder="Pilihan D">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Jawaban Benar</label>
                        <select class="form-control jawaban-benar">
                            <option value="0">A</option>
                            <option value="1">B</option>
                            <option value="2">C</option>
                            <option value="3">D</option>
                        </select>
                    </div>
                </div>
                
                <div class="essay-container" style="display: none;">
                    <div class="form-group">
                        <label>Jawaban Benar (Essay)</label>
                        <textarea class="form-control jawaban-essay" rows="3" placeholder="Masukkan jawaban yang benar..."></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Bobot Nilai</label>
                    <input type="number" class="form-control bobot" value="10" min="1">
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('soalContainer').insertAdjacentHTML('beforeend', soalHtml);
}

function hapusSoal(id) {
    document.getElementById(`soal_${id}`).remove();
    updateSoalJson();
}

function ubahTipeSoal(id) {
    const card = document.getElementById(`soal_${id}`);
    const tipe = card.querySelector('.tipe-soal').value;
    const pilihanContainer = card.querySelector('.pilihan-ganda-container');
    const essayContainer = card.querySelector('.essay-container');
    
    if (tipe === 'pilihan_ganda') {
        pilihanContainer.style.display = 'block';
        essayContainer.style.display = 'none';
    } else {
        pilihanContainer.style.display = 'none';
        essayContainer.style.display = 'block';
    }
}

function updateSoalJson() {
    soalArray = [];
    const soalCards = document.querySelectorAll('#soalContainer .card');
    
    soalCards.forEach((card, index) => {
        const tipe = card.querySelector('.tipe-soal').value;
        const pertanyaan = card.querySelector('.pertanyaan').value;
        const bobot = parseInt(card.querySelector('.bobot').value) || 10;
        
        const soal = {
            id: index + 1,
            tipe: tipe,
            pertanyaan: pertanyaan,
            bobot: bobot
        };
        
        if (tipe === 'pilihan_ganda') {
            const pilihan = Array.from(card.querySelectorAll('.pilihan')).map(input => input.value);
            const jawabanBenar = parseInt(card.querySelector('.jawaban-benar').value);
            
            soal.pilihan = pilihan;
            soal.jawaban_benar = jawabanBenar;
        } else {
            const jawabanEssay = card.querySelector('.jawaban-essay').value;
            soal.jawaban_benar = jawabanEssay;
        }
        
        soalArray.push(soal);
    });
    
    document.getElementById('soal_json').value = JSON.stringify({soal: soalArray});
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Tambah event listener untuk form submission
    document.getElementById('ulanganForm').addEventListener('submit', function(e) {
        updateSoalJson();
        
        if (soalArray.length === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 soal!');
            return false;
        }
    });
    
    // Event delegation untuk input changes
    document.getElementById('soalContainer').addEventListener('input', updateSoalJson);
    document.getElementById('soalContainer').addEventListener('change', updateSoalJson);
    
    // Tambah soal pertama
    tambahSoal();
});
</script>
<?= $this->endSection() ?> 