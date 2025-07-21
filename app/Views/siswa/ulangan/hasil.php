<?= $this->extend('siswa/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hasil Ulangan</h1>
        <a href="<?= base_url('siswa/ulangan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Ulangan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Judul Ulangan:</strong></td>
                            <td><?= $ulangan['judul_ulangan'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran:</strong></td>
                            <td><?= $ulangan['nama_mata_pelajaran'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nilai:</strong></td>
                            <td>
                                <h4 class="text-primary"><?= number_format($hasil['nilai'], 2) ?></h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Selesai:</strong></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($hasil['waktu_selesai'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jawaban Anda</h6>
        </div>
        <div class="card-body">
            <?php 
            $soalArray = json_decode($ulangan['soal_json'], true);
            $jawabanArray = json_decode($hasil['jawaban_json'], true);
            
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
                                        <input class="form-check-input" type="radio" disabled
                                               <?= (isset($jawabanArray[$index]) && $jawabanArray[$index] == $idx) ? 'checked' : '' ?>>
                                        <label class="form-check-label <?= $idx == $soal['jawaban_benar'] ? 'text-success font-weight-bold' : '' ?>">
                                            <?= chr(65 + $idx) ?>. <?= htmlspecialchars($pilihan) ?>
                                            <?php if ($idx == $soal['jawaban_benar']): ?>
                                                <i class="fas fa-check text-success"></i> (Jawaban Benar)
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="mt-2">
                                    <strong>Jawaban Anda:</strong> 
                                    <?php if (isset($jawabanArray[$index])): ?>
                                        <?= chr(65 + $jawabanArray[$index]) ?>
                                        <?php if ($jawabanArray[$index] == $soal['jawaban_benar']): ?>
                                            <span class="badge badge-success">Benar</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Salah</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak dijawab</span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p><strong>Jawaban Essay Anda:</strong></p>
                                <div class="border p-3 bg-light">
                                    <?= isset($jawabanArray[$index]) ? nl2br(htmlspecialchars($jawabanArray[$index])) : '<em>Tidak dijawab</em>' ?>
                                </div>
                                <small class="text-muted">Jawaban essay akan direview oleh guru</small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Tidak ada soal yang ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 