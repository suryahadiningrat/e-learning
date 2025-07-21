<?= $this->extend('guru/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Preview Ulangan</h1>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru/ulangan') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
            <?php if ($ulangan['status'] == 'draft'): ?>
                <a href="<?= base_url('guru/ulangan/publish/' . $ulangan['id']) ?>" 
                   class="btn btn-success btn-sm" onclick="return confirm('Yakin ingin mempublish ulangan ini?')">
                    <i class="fas fa-check fa-sm"></i> Publish
                </a>
            <?php endif; ?>
        </div>
    </div>

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
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <?php if ($ulangan['status'] == 'draft'): ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php elseif ($ulangan['status'] == 'published'): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Closed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat Oleh:</strong></td>
                            <td><?= $ulangan['nama_creator'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <h5>Daftar Soal</h5>
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
                                <ol type="A">
                                    <?php foreach ($soal['pilihan'] as $idx => $pilihan): ?>
                                        <li class="<?= $idx == $soal['jawaban_benar'] ? 'text-success font-weight-bold' : '' ?>">
                                            <?= htmlspecialchars($pilihan) ?>
                                            <?php if ($idx == $soal['jawaban_benar']): ?>
                                                <i class="fas fa-check text-success"></i> (Jawaban Benar)
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p><strong>Tipe Soal:</strong> Essay</p>
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