<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mengerjakan Ulangan</h1>
        <div class="d-flex gap-2">
            <div id="timer" class="badge badge-warning fs-6"></div>
            <a href="<?= base_url('siswa/ulangan') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= $ulangan['judul_ulangan'] ?></h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Mata Pelajaran:</strong></td>
                            <td><?= $ulangan['nama_mata_pelajaran'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Durasi:</strong></td>
                            <td><?= $ulangan['durasi_menit'] ?> menit</td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Mulai:</strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($ulangan['waktu_mulai'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Selesai:</strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($ulangan['waktu_selesai'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <form id="ulanganForm" action="<?= base_url('siswa/ulangan/submit-jawaban') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="ulangan_id" value="<?= $ulangan['id'] ?>">
                
                <?php 
                $soalArray = json_decode($ulangan['soal_json'], true);
                if (isset($soalArray['soal']) && is_array($soalArray['soal'])): 
                ?>
                    <?php foreach ($soalArray['soal'] as $index => $soal): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>Soal <?= $index + 1 ?></strong>
                                <span class="badge badge-info float-right">Bobot: <?= $soal['bobot'] ?></span>
                            </div>
                            <div class="card-body">
                                <p><strong>Pertanyaan:</strong></p>
                                <p><?= nl2br(htmlspecialchars($soal['pertanyaan'])) ?></p>
                                
                                <?php if ($soal['tipe'] == 'pilihan_ganda'): ?>
                                    <p><strong>Pilihan Jawaban:</strong></p>
                                    <?php foreach ($soal['pilihan'] as $idx => $pilihan): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="jawaban[<?= $index ?>]" 
                                                   id="soal_<?= $index ?>_<?= $idx ?>" 
                                                   value="<?= $idx ?>">
                                            <label class="form-check-label" for="soal_<?= $index ?>_<?= $idx ?>">
                                                <?= chr(65 + $idx) ?>. <?= htmlspecialchars($pilihan) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p><strong>Jawaban Essay:</strong></p>
                                    <textarea class="form-control" name="jawaban[<?= $index ?>]" 
                                              rows="4" placeholder="Tulis jawaban Anda di sini..."></textarea>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Tidak ada soal yang ditemukan.
                    </div>
                <?php endif; ?>

                <div class="text-center">
                    <button type="button" class="btn btn-warning btn-lg me-2" onclick="saveJawaban()">
                        <i class="fas fa-save"></i> Simpan Sementara
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Yakin ingin mengakhiri ulangan?')">
                        <i class="fas fa-check"></i> Selesai & Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Timer countdown
function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    var interval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = "Sisa Waktu: " + minutes + ":" + seconds;

        if (--timer < 0) {
            clearInterval(interval);
            display.textContent = "Waktu Habis!";
            document.getElementById('ulanganForm').submit();
        }
    }, 1000);
}

// Auto save jawaban every 30 seconds
function saveJawaban() {
    var formData = new FormData(document.getElementById('ulanganForm'));
    
    fetch('<?= base_url('siswa/ulangan/save-jawaban') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show temporary success message
            var alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alert.style.top = '20px';
            alert.style.right = '20px';
            alert.style.zIndex = '9999';
            alert.innerHTML = data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            document.body.appendChild(alert);
            
            setTimeout(function() {
                alert.remove();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Start timer when page loads
window.onload = function () {
    var duration = <?= $ulangan['durasi_menit'] * 60 ?>;
    var display = document.querySelector('#timer');
    startTimer(duration, display);
    
    // Auto save every 30 seconds
    setInterval(saveJawaban, 30000);
};
</script>
<?= $this->endSection() ?> 