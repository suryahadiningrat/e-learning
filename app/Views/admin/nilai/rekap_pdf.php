<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Siswa</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header h2 { margin: 5px 0; font-size: 16px; }
        .header p { margin: 0; font-size: 12px; }
        .info-table { width: 100%; margin-bottom: 20px; font-size: 12px; }
        .info-table td { padding: 3px; }
        .nilai-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .nilai-table th, .nilai-table td { border: 1px solid #000; padding: 5px; }
        .nilai-table th { background-color: #f0f0f0; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $sekolah['nama'] ?></h1>
        <p><?= $sekolah['alamat'] ?></p>
        <hr>
        <h2>REKAP HASIL BELAJAR SISWA</h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Siswa</td>
            <td width="1%">:</td>
            <td width="34%"><?= $siswa['full_name'] ?></td>
            <td width="15%">Kelas</td>
            <td width="1%">:</td>
            <td width="34%"><?= $siswa['nama_kelas'] ?></td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>:</td>
            <td><?= $siswa['nis'] ?></td>
            <td>Jurusan</td>
            <td>:</td>
            <td><?= $siswa['nama_jurusan'] ?></td>
        </tr>
    </table>

    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Mata Pelajaran</th>
                <th width="15%">Nilai Tugas (Avg)</th>
                <th width="15%">Nilai Ulangan (Avg)</th>
                <th width="10%">UTS</th>
                <th width="10%">UAS</th>
                <th width="10%">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($nilai_list as $nilai): 
                $tugas = json_decode($nilai['nilai_tugas'], true) ?: [];
                $ulangan = json_decode($nilai['nilai_ulangan'], true) ?: [];
                $avgTugas = !empty($tugas) ? array_sum($tugas) / count($tugas) : 0;
                $avgUlangan = !empty($ulangan) ? array_sum($ulangan) / count($ulangan) : 0;
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $nilai['nama_mata_pelajaran'] ?></td>
                <td class="text-center"><?= number_format($avgTugas, 2) ?></td>
                <td class="text-center"><?= number_format($avgUlangan, 2) ?></td>
                <td class="text-center"><?= $nilai['nilai_uts_sem1'] ? number_format($nilai['nilai_uts_sem1'], 2) : '-' ?></td>
                <td class="text-center"><?= $nilai['nilai_uas_sem1'] ? number_format($nilai['nilai_uas_sem1'], 2) : '-' ?></td>
                <td class="text-center"><strong><?= number_format($nilai['rata_rata'], 2) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Bantul, <?= date('d F Y') ?></p>
        <br><br><br>
        <p>Kepala Sekolah</p>
    </div>
</body>
</html>
