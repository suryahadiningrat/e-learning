<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - <?= $jadwal['nama_mata_pelajaran'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section table {
            width: 100%;
            max-width: 500px;
        }
        
        .info-section td {
            padding: 3px 0;
        }
        
        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .nilai-table th,
        .nilai-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        .nilai-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .nilai-table td:nth-child(2),
        .nilai-table td:nth-child(3) {
            text-align: left;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            margin-top: 70px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP NILAI SISWA</h1>
        <h2>SMK NEGERI / SWASTA</h2>
    </div>
    
    <div class="info-section">
        <table>
            <tr>
                <td>Mata Pelajaran</td>
                <td>: <?= $jadwal['nama_mata_pelajaran'] ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= $jadwal['nama_kelas'] ?></td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>: <?= $jadwal['nama_jurusan'] ?></td>
            </tr>
            <tr>
                <td>Guru Pengajar</td>
                <td>: <?= $jadwal['nama_guru'] ?></td>
            </tr>
            <tr>
                <td>Hari / Jam</td>
                <td>: <?= $jadwal['hari'] ?> / <?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
            </tr>
        </table>
    </div>
    
    <table class="nilai-table">
        <thead>
            <tr>
                <th rowspan="3">No</th>
                <th rowspan="3">NIS</th>
                <th rowspan="3">Nama Siswa</th>
                <?php if ($maxTugas > 0): ?>
                    <th colspan="<?= $maxTugas ?>" rowspan="2">Nilai Tugas</th>
                <?php endif; ?>
                <?php if ($maxUlangan > 0): ?>
                    <th colspan="<?= $maxUlangan ?>" rowspan="2">Nilai Ulangan</th>
                <?php endif; ?>
                <th colspan="2">Semester 1</th>
                <th colspan="2">Semester 2</th>
                <th rowspan="3">Rata-rata</th>
            </tr>
            <tr>
                <th>UTS</th>
                <th>UAS</th>
                <th>UTS</th>
                <th>UAS</th>
            </tr>
            <tr>
                <?php for ($i = 1; $i <= $maxTugas; $i++): ?>
                    <th>T<?= $i ?></th>
                <?php endfor; ?>
                <?php for ($i = 1; $i <= $maxUlangan; $i++): ?>
                    <th>U<?= $i ?></th>
                <?php endfor; ?>
            </tr>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($siswa as $index => $s): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $s['nis'] ?></td>
                    <td><?= $s['nama_siswa'] ?></td>
                    
                    <!-- Nilai Tugas -->
                    <?php 
                    $nilaiSiswa = isset($nilai[$s['id']]) ? $nilai[$s['id']] : [
                        'tugas' => [], 
                        'ulangan' => [],
                        'uts_sem1' => null,
                        'uas_sem1' => null,
                        'uts_sem2' => null,
                        'uas_sem2' => null
                    ];
                    ?>
                    <?php for ($i = 0; $i < $maxTugas; $i++): ?>
                        <td>
                            <?= isset($nilaiSiswa['tugas'][$i]) && $nilaiSiswa['tugas'][$i] !== '' ? $nilaiSiswa['tugas'][$i] : '-' ?>
                        </td>
                    <?php endfor; ?>
                    
                    <!-- Nilai Ulangan -->
                    <?php for ($i = 0; $i < $maxUlangan; $i++): ?>
                        <td>
                            <?= isset($nilaiSiswa['ulangan'][$i]) && $nilaiSiswa['ulangan'][$i] !== '' ? $nilaiSiswa['ulangan'][$i] : '-' ?>
                        </td>
                    <?php endfor; ?>
                    
                    <!-- Semester 1 -->
                    <td><?= isset($nilaiSiswa['uts_sem1']) && $nilaiSiswa['uts_sem1'] !== null ? $nilaiSiswa['uts_sem1'] : '-' ?></td>
                    <td><?= isset($nilaiSiswa['uas_sem1']) && $nilaiSiswa['uas_sem1'] !== null ? $nilaiSiswa['uas_sem1'] : '-' ?></td>
                    
                    <!-- Semester 2 -->
                    <td><?= isset($nilaiSiswa['uts_sem2']) && $nilaiSiswa['uts_sem2'] !== null ? $nilaiSiswa['uts_sem2'] : '-' ?></td>
                    <td><?= isset($nilaiSiswa['uas_sem2']) && $nilaiSiswa['uas_sem2'] !== null ? $nilaiSiswa['uas_sem2'] : '-' ?></td>
                    
                    <!-- Rata-rata -->
                    <td>
                        <?php 
                        $allNilai = array_merge(
                            $nilaiSiswa['tugas'],
                            $nilaiSiswa['ulangan']
                        );
                        if (isset($nilaiSiswa['uts_sem1']) && $nilaiSiswa['uts_sem1'] !== null) $allNilai[] = $nilaiSiswa['uts_sem1'];
                        if (isset($nilaiSiswa['uas_sem1']) && $nilaiSiswa['uas_sem1'] !== null) $allNilai[] = $nilaiSiswa['uas_sem1'];
                        if (isset($nilaiSiswa['uts_sem2']) && $nilaiSiswa['uts_sem2'] !== null) $allNilai[] = $nilaiSiswa['uts_sem2'];
                        if (isset($nilaiSiswa['uas_sem2']) && $nilaiSiswa['uas_sem2'] !== null) $allNilai[] = $nilaiSiswa['uas_sem2'];
                        
                        $validNilai = array_filter($allNilai, function($v) { return $v !== '' && $v !== null; });
                        if (count($validNilai) > 0) {
                            echo number_format(array_sum($validNilai) / count($validNilai), 2);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="signature-section">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div>Kepala Sekolah</div>
            <div class="signature-line">
                (_________________)
            </div>
        </div>
        <div class="signature-box">
            <div>Tanggal: <?= date('d/m/Y') ?></div>
            <div>Guru Mata Pelajaran</div>
            <div class="signature-line">
                <?= $jadwal['nama_guru'] ?>
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
