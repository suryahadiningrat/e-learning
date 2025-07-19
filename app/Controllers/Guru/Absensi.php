<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JadwalModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Absensi extends BaseController {
    protected $absensiModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $jadwalModel;
    public function __construct() {
        $this->absensiModel = new AbsensiModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jadwalModel = new JadwalModel();
    }
    public function index() {
        $data = [
            'title' => 'Data Absensi',
            'absensi' => $this->absensiModel->getAbsensiWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations()
        ];
        return view('guru/absensi/index', $data);
    }
    public function create() {
        $data = [
            'title' => 'Tambah Absensi',
            'siswa' => $this->siswaModel->getSiswaWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];
        return view('guru/absensi/create', $data);
    }
    public function store() {
        $siswaId = $this->request->getPost('siswa_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        $existingAbsensi = $this->absensiModel->checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal);
        if ($existingAbsensi) {
            return redirect()->back()->withInput()->with('error', 'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada');
        }
        $rules = [
            'siswa_id' => 'required|numeric',
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
            'keterangan' => 'permit_empty|max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $absensiData = [
            'siswa_id' => $siswaId,
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->absensiModel->insert($absensiData);
        return redirect()->to('guru/absensi')->with('success', 'Absensi berhasil ditambahkan');
    }
    public function edit($id = null) {
        $absensi = $this->absensiModel->getAbsensiWithRelations($id);
        if (!$absensi) {
            return redirect()->to('guru/absensi')->with('error', 'Absensi tidak ditemukan');
        }
        $data = [
            'title' => 'Edit Absensi',
            'absensi' => $absensi,
            'siswa' => $this->siswaModel->getSiswaWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];
        return view('guru/absensi/edit', $data);
    }
    public function update($id = null) {
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to('guru/absensi')->with('error', 'Absensi tidak ditemukan');
        }
        $siswaId = $this->request->getPost('siswa_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        $existingAbsensi = $this->absensiModel->checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal, $id);
        if ($existingAbsensi) {
            return redirect()->back()->withInput()->with('error', 'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada');
        }
        $rules = [
            'siswa_id' => 'required|numeric',
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
            'keterangan' => 'permit_empty|max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $absensiData = [
            'siswa_id' => $siswaId,
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->absensiModel->update($id, $absensiData);
        return redirect()->to('guru/absensi')->with('success', 'Absensi berhasil diperbarui');
    }
    public function delete($id = null) {
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to('guru/absensi')->with('error', 'Absensi tidak ditemukan');
        }
        $this->absensiModel->delete($id);
        return redirect()->to('guru/absensi')->with('success', 'Absensi berhasil dihapus');
    }
    public function export()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kelasId = $this->request->getGet('kelas_id');
        $absensiList = $this->absensiModel->getAbsensiWithRelationsFiltered($startDate, $endDate, $kelasId);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Nama Siswa');
        $sheet->setCellValue('D1', 'NIS');
        $sheet->setCellValue('E1', 'Kelas');
        $sheet->setCellValue('F1', 'Jurusan');
        $sheet->setCellValue('G1', 'Mata Pelajaran');
        $sheet->setCellValue('H1', 'Guru');
        $sheet->setCellValue('I1', 'Hari');
        $sheet->setCellValue('J1', 'Jam');
        $sheet->setCellValue('K1', 'Status');
        $sheet->setCellValue('L1', 'Keterangan');
        $row = 2;
        $no = 1;
        foreach ($absensiList as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['tanggal']);
            $sheet->setCellValue('C' . $row, $item['nama_siswa']);
            $sheet->setCellValue('D' . $row, $item['nis']);
            $sheet->setCellValue('E' . $row, $item['nama_kelas']);
            $sheet->setCellValue('F' . $row, $item['nama_jurusan']);
            $sheet->setCellValue('G' . $row, $item['mata_pelajaran']);
            $sheet->setCellValue('H' . $row, $item['nama_guru']);
            $sheet->setCellValue('I' . $row, $item['hari']);
            $sheet->setCellValue('J' . $row, $item['jam_mulai'] . ' - ' . $item['jam_selesai']);
            $sheet->setCellValue('K' . $row, $item['status']);
            $sheet->setCellValue('L' . $row, $item['keterangan']);
            $row++;
        }
        $filename = 'absensi_guru_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    public function getJadwalByKelas($kelasId) {
        $jadwal = $this->jadwalModel->getJadwalByKelas($kelasId);
        return $this->response->setJSON($jadwal);
    }
    public function getSiswaByKelas($kelasId) {
        $siswa = $this->siswaModel->getSiswaByKelas($kelasId);
        return $this->response->setJSON($siswa);
    }
} 