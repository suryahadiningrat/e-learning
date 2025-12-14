<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Data Guru - <?= $guru['full_name'] ?? '' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 8px 10px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 180px;
            font-weight: 600;
        }
        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .jadwal-table th,
        .jadwal-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .jadwal-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 80px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <div class="header">
        <h2>SISTEM INFORMASI SMK NEGERI 1 PLERET</h2>
        <p>Jl. Raya Pleret, Bantul, Yogyakarta</p>
        <p>RINCIAN DATA GURU</p>
        <p style="font-size: 12px;">Tanggal Cetak: <?= date('d F Y, H:i') ?> WIB</p>
    </div>

    <div class="info-section">
        <h5 class="section-title">Data Pribadi</h5>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>: <?= $guru['full_name'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>: <?= $guru['nip'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: <?= ($guru['jenis_kelamin'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>: <?= $guru['tempat_lahir'] ?? '-' ?>, <?= date('d F Y', strtotime($guru['tanggal_lahir'] ?? 'now')) ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: <?= $guru['alamat'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>No. Telepon</td>
                <td>: <?= $guru['no_telp'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: <?= $guru['email'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Bidang Studi</td>
                <td>: <?= $guru['bidang_studi'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Username</td>
                <td>: <?= $guru['username'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Status Akun</td>
                <td>: <?= ($guru['is_active'] ?? 0) == 1 ? 'Aktif' : 'Tidak Aktif' ?></td>
            </tr>
        </table>
    </div>

    <?php if (!empty($jadwal)): ?>
    <div class="jadwal-section">
        <h5 class="section-title">Jadwal Mengajar</h5>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Hari</th>
                    <th style="width: 25%;">Mata Pelajaran</th>
                    <th style="width: 20%;">Kelas</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 20%;">Semester/TA</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                
                // Sort jadwal by day order
                usort($jadwal, function($a, $b) use ($hariOrder) {
                    $aIdx = array_search($a['hari'], $hariOrder);
                    $bIdx = array_search($b['hari'], $hariOrder);
                    if ($aIdx == $bIdx) {
                        return strcmp($a['jam_mulai'], $b['jam_mulai']);
                    }
                    return $aIdx - $bIdx;
                });
                
                foreach ($jadwal as $j): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $j['hari'] ?></td>
                    <td><?= $j['nama_mata_pelajaran'] ?></td>
                    <td><?= $j['nama_kelas'] ?></td>
                    <td><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></td>
                    <td><?= $j['semester'] ?> / <?= $j['tahun_ajaran'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="info-section">
        <h5 class="section-title">Jadwal Mengajar</h5>
        <p class="text-muted"><em>Guru ini belum memiliki jadwal mengajar</em></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Mengetahui,</p>
        <div class="signature">
            <div class="signature-line"></div>
            <p><strong>Kepala Sekolah</strong></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
