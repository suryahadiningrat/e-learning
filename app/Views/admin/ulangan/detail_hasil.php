<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Hasil Ulangan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/ulangan/hasil/' . $ulangan['id']) ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informasi Ulangan</h5>
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
                                    <td><strong>Kelas:</strong></td>
                                    <td><?= $ulangan['nama_kelas'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Mulai:</strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($ulangan['waktu_mulai'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Selesai:</strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($ulangan['waktu_selesai'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Durasi:</strong></td>
                                    <td><?= $ulangan['durasi_menit'] ?> menit</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informasi Siswa</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Siswa:</strong></td>
                                    <td><?= $siswa['nama_siswa'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIS:</strong></td>
                                    <td><?= $siswa['nis'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas:</strong></td>
                                    <td><?= $siswa['nama_kelas'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Selesai:</strong></td>
                                    <td><?= $hasil['waktu_selesai'] ? date('d/m/Y H:i:s', strtotime($hasil['waktu_selesai'])) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Hasil Ulangan</h5>
                            <div class="alert alert-info">
                                <h6><strong>Nilai Akhir: <?= number_format($hasil['nilai'], 2) ?></strong></h6>
                                <?php 
                                $soalJson = json_decode($ulangan['soal_json'], true);
                                $totalSoal = isset($soalJson['soal']) ? count($soalJson['soal']) : 0;
                                $totalBobot = 0;
                                if (isset($soalJson['soal'])) {
                                    foreach ($soalJson['soal'] as $soal) {
                                        $totalBobot += $soal['bobot'];
                                    }
                                }
                                $persentase = $totalBobot > 0 ? ($hasil['nilai'] / $totalBobot) * 100 : 0;
                                ?>
                                <p>Total Soal: <?= $totalSoal ?> | Total Bobot: <?= $totalBobot ?> | Persentase: <?= number_format($persentase, 2) ?>%</p>
                            </div>

                            <?php if ($hasil['jawaban_json']): ?>
                                <h6>Detail Jawaban:</h6>
                                <?php 
                                $jawabanJson = json_decode($hasil['jawaban_json'], true);
                                if (isset($soalJson['soal'])):
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
                                            <p><strong>Pertanyaan:</strong> <?= $soal['pertanyaan'] ?></p>
                                            
                                            <?php if ($soal['tipe'] == 'pilihan_ganda'): ?>
                                                <p><strong>Jawaban Siswa:</strong> 
                                                    <?php if ($jawaban !== null && $jawaban !== ''): ?>
                                                        <?= isset($soal['pilihan'][$jawaban]) ? $soal['pilihan'][$jawaban] : 'Jawaban tidak valid' ?>
                                                        <?php if ($jawaban == $soal['jawaban_benar']): ?>
                                                            <span class="badge badge-success">Benar</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Salah</span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak dijawab</span>
                                                    <?php endif; ?>
                                                </p>
                                                <p><strong>Jawaban Benar:</strong> <?= $soal['pilihan'][$soal['jawaban_benar']] ?></p>
                                                <p><strong>Pilihan Jawaban:</strong></p>
                                                <ul>
                                                    <?php foreach ($soal['pilihan'] as $idx => $pilihan): ?>
                                                        <li><?= chr(65 + $idx) ?>. <?= $pilihan ?> <?= $idx == $soal['jawaban_benar'] ? ' <span class="badge badge-success">Benar</span>' : '' ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p><strong>Jawaban Siswa:</strong> 
                                                    <?= $jawaban ?: 'Tidak dijawab' ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <p><strong>Bobot:</strong> <?= $soal['bobot'] ?></p>
                                        </div>
                                    </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Format soal tidak valid
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Detail jawaban tidak tersedia
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 