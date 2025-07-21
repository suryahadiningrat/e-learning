<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Hasil Ulangan</h1>
        <a href="<?= base_url('guru/ulangan/hasil/' . $ulangan['id']) ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Siswa:</strong></td>
                                    <td><?= isset($siswa['nama']) ? $siswa['nama'] : 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIS:</strong></td>
                                    <td><?= isset($siswa['nis']) ? $siswa['nis'] : 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas:</strong></td>
                                    <td><?= isset($siswa['nama_kelas']) ? $siswa['nama_kelas'] : 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Selesai:</strong></td>
                                    <td><?= isset($hasil['waktu_selesai']) && $hasil['waktu_selesai'] ? date('d/m/Y H:i:s', strtotime($hasil['waktu_selesai'])) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <h5>Hasil Ulangan</h5>
                    <div class="alert alert-info">
                        <h6><strong>Nilai Akhir: <?= isset($hasil['nilai']) ? number_format($hasil['nilai'], 2) : '0.00' ?></strong></h6>
                        <?php 
                        $soalJson = json_decode($ulangan['soal_json'], true);
                        $totalSoal = isset($soalJson['soal']) ? count($soalJson['soal']) : 0;
                        $totalBobot = 0;
                        if (isset($soalJson['soal'])) {
                            foreach ($soalJson['soal'] as $soal) {
                                $totalBobot += isset($soal['bobot']) ? $soal['bobot'] : 10;
                            }
                        }
                        $nilai = isset($hasil['nilai']) ? $hasil['nilai'] : 0;
                        $persentase = $totalBobot > 0 ? ($nilai / $totalBobot) * 100 : 0;
                        ?>
                        <p>Total Soal: <?= $totalSoal ?> | Total Bobot: <?= $totalBobot ?> | Persentase: <?= number_format($persentase, 2) ?>%</p>
                    </div>

                    <?php if (isset($hasil['jawaban_json']) && $hasil['jawaban_json']): ?>
                        <h6>Detail Jawaban:</h6>
                        <?php 
                        $jawabanJson = json_decode($hasil['jawaban_json'], true);
                        if (isset($soalJson['soal']) && is_array($soalJson['soal'])):
                            foreach ($soalJson['soal'] as $index => $soal):
                                // Handle berbagai format jawaban
                                $jawaban = null;
                                if (isset($jawabanJson['jawaban']) && isset($jawabanJson['jawaban'][$index])) {
                                    $jawaban = $jawabanJson['jawaban'][$index];
                                } elseif (isset($jawabanJson[$index])) {
                                    $jawaban = $jawabanJson[$index];
                                }
                        ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>Soal <?= $index + 1 ?></strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Pertanyaan:</strong> <?= isset($soal['pertanyaan']) ? $soal['pertanyaan'] : 'Pertanyaan tidak tersedia' ?></p>
                                    
                                    <?php if (isset($soal['tipe']) && $soal['tipe'] == 'pilihan_ganda'): ?>
                                        <p><strong>Jawaban Siswa:</strong> 
                                            <?php if ($jawaban !== null && $jawaban !== ''): ?>
                                                <?= isset($soal['pilihan'][$jawaban]) ? $soal['pilihan'][$jawaban] : 'Jawaban tidak valid' ?>
                                                <?php if (isset($soal['jawaban_benar']) && $jawaban == $soal['jawaban_benar']): ?>
                                                    <span class="badge badge-success">Benar</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Salah</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak dijawab</span>
                                            <?php endif; ?>
                                        </p>
                                        <p><strong>Jawaban Benar:</strong> 
                                            <?= isset($soal['pilihan'][$soal['jawaban_benar']]) ? $soal['pilihan'][$soal['jawaban_benar']] : 'Jawaban tidak tersedia' ?>
                                        </p>
                                        <p><strong>Pilihan Jawaban:</strong></p>
                                        <ul>
                                            <?php if (isset($soal['pilihan']) && is_array($soal['pilihan'])): ?>
                                                <?php foreach ($soal['pilihan'] as $key => $pilihan): ?>
                                                    <li><?= chr(65 + $key) ?>. <?= $pilihan ?></li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li>Pilihan tidak tersedia</li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php elseif (isset($soal['tipe']) && $soal['tipe'] == 'essay'): ?>
                                        <p><strong>Jawaban Siswa:</strong></p>
                                        <div class="border p-3 bg-light">
                                            <?= $jawaban ? nl2br(htmlspecialchars($jawaban)) : '<em>Tidak dijawab</em>' ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <p><strong>Bobot:</strong> <?= isset($soal['bobot']) ? $soal['bobot'] : 10 ?> poin</p>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <div class="alert alert-warning">
                                <p>Tidak ada soal yang tersedia atau format soal tidak valid.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <p>Detail jawaban tidak tersedia.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?> 