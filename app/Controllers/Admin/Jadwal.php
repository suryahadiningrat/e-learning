<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Jadwal',
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];

        return view('admin/jadwal/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Jadwal',
            'guru' => $this->guruModel->getGuruWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations()
        ];

        return view('admin/jadwal/create', $data);
    }

    public function store()
    {
        // Validasi input dengan pengecekan manual untuk konflik jadwal
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');
        
        // Cek konflik jadwal guru
        $konflikGuru = $this->jadwalModel->checkGuruConflict($guruId, $hari, $jamMulai, $jamSelesai);
        if ($konflikGuru) {
            return redirect()->back()->withInput()->with('error', 'Guru sudah memiliki jadwal pada waktu tersebut');
        }
        
        // Cek konflik jadwal kelas
        $konflikKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai);
        if ($konflikKelas) {
            return redirect()->back()->withInput()->with('error', 'Kelas sudah memiliki jadwal pada waktu tersebut');
        }

        // Validasi input lainnya
        $rules = [
            'guru_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'mata_pelajaran' => 'required|min_length[2]|max_length[100]',
            'hari' => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
            'jam_mulai' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'jam_selesai' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'semester' => 'required|in_list[Ganjil,Genap]',
            'tahun_ajaran' => 'required|min_length[4]|max_length[9]'
        ];

        $messages = [
            'guru_id' => [
                'required' => 'Guru harus dipilih',
                'numeric' => 'Guru tidak valid'
            ],
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas tidak valid'
            ],
            'mata_pelajaran' => [
                'required' => 'Mata pelajaran harus diisi',
                'min_length' => 'Mata pelajaran minimal 2 karakter',
                'max_length' => 'Mata pelajaran maksimal 100 karakter'
            ],
            'hari' => [
                'required' => 'Hari harus dipilih',
                'in_list' => 'Hari tidak valid'
            ],
            'jam_mulai' => [
                'required' => 'Jam mulai harus diisi',
                'regex_match' => 'Format jam mulai tidak valid (HH:MM)'
            ],
            'jam_selesai' => [
                'required' => 'Jam selesai harus diisi',
                'regex_match' => 'Format jam selesai tidak valid (HH:MM)'
            ],
            'semester' => [
                'required' => 'Semester harus dipilih',
                'in_list' => 'Semester tidak valid'
            ],
            'tahun_ajaran' => [
                'required' => 'Tahun ajaran harus diisi',
                'min_length' => 'Tahun ajaran minimal 4 karakter',
                'max_length' => 'Tahun ajaran maksimal 9 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi jam selesai harus lebih besar dari jam mulai
        if (strtotime($jamSelesai) <= strtotime($jamMulai)) {
            return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih besar dari jam mulai');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Buat data jadwal
            $jadwalData = [
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
                'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
                'hari' => $hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'semester' => $this->request->getPost('semester'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('jadwal')->insert($jadwalData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal');
            }

            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $jadwal = $this->jadwalModel->getJadwalWithRelations($id);
        
        if (!$jadwal) {
            return redirect()->to('admin/jadwal')->with('error', 'Jadwal tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Jadwal',
            'jadwal' => $jadwal,
            'guru' => $this->guruModel->getGuruWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations()
        ];

        return view('admin/jadwal/edit', $data);
    }

    public function update($id = null)
    {
        $jadwal = $this->jadwalModel->find($id);
        
        if (!$jadwal) {
            return redirect()->to('admin/jadwal')->with('error', 'Jadwal tidak ditemukan');
        }

        // Validasi input dengan pengecekan manual untuk konflik jadwal
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');
        
        // Cek konflik jadwal guru (kecuali jadwal yang sedang diedit)
        $konflikGuru = $this->jadwalModel->checkGuruConflict($guruId, $hari, $jamMulai, $jamSelesai, $id);
        if ($konflikGuru) {
            return redirect()->back()->withInput()->with('error', 'Guru sudah memiliki jadwal pada waktu tersebut');
        }
        
        // Cek konflik jadwal kelas (kecuali jadwal yang sedang diedit)
        $konflikKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $id);
        if ($konflikKelas) {
            return redirect()->back()->withInput()->with('error', 'Kelas sudah memiliki jadwal pada waktu tersebut');
        }

        // Validasi input lainnya
        $rules = [
            'guru_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'mata_pelajaran' => 'required|min_length[2]|max_length[100]',
            'hari' => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
            'jam_mulai' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'jam_selesai' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'semester' => 'required|in_list[Ganjil,Genap]',
            'tahun_ajaran' => 'required|min_length[4]|max_length[9]'
        ];

        $messages = [
            'guru_id' => [
                'required' => 'Guru harus dipilih',
                'numeric' => 'Guru tidak valid'
            ],
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas tidak valid'
            ],
            'mata_pelajaran' => [
                'required' => 'Mata pelajaran harus diisi',
                'min_length' => 'Mata pelajaran minimal 2 karakter',
                'max_length' => 'Mata pelajaran maksimal 100 karakter'
            ],
            'hari' => [
                'required' => 'Hari harus dipilih',
                'in_list' => 'Hari tidak valid'
            ],
            'jam_mulai' => [
                'required' => 'Jam mulai harus diisi',
                'regex_match' => 'Format jam mulai tidak valid (HH:MM)'
            ],
            'jam_selesai' => [
                'required' => 'Jam selesai harus diisi',
                'regex_match' => 'Format jam selesai tidak valid (HH:MM)'
            ],
            'semester' => [
                'required' => 'Semester harus dipilih',
                'in_list' => 'Semester tidak valid'
            ],
            'tahun_ajaran' => [
                'required' => 'Tahun ajaran harus diisi',
                'min_length' => 'Tahun ajaran minimal 4 karakter',
                'max_length' => 'Tahun ajaran maksimal 9 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi jam selesai harus lebih besar dari jam mulai
        if (strtotime($jamSelesai) <= strtotime($jamMulai)) {
            return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih besar dari jam mulai');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update data jadwal
            $jadwalData = [
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
                'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
                'hari' => $hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'semester' => $this->request->getPost('semester'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('jadwal')->where('id', $id)->update($jadwalData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jadwal');
            }

            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $jadwal = $this->jadwalModel->find($id);
        
        if (!$jadwal) {
            return redirect()->to('admin/jadwal')->with('error', 'Jadwal tidak ditemukan');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus data jadwal
            $db->table('jadwal')->where('id', $id)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('admin/jadwal')->with('error', 'Gagal menghapus jadwal');
            }

            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/jadwal')->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
} 