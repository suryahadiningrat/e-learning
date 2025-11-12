<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $kelasModel;
    protected $mataPelajaranModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
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
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'mata_pelajaran' => $this->mataPelajaranModel->getAktifMataPelajaran()
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
            log_message('warning', 'Admin Jadwal Store - Konflik jadwal guru detected. Guru ID: ' . $guruId . ', Hari: ' . $hari . ', Jam: ' . $jamMulai . '-' . $jamSelesai);
            return redirect()->back()->withInput()->with('error', 'Guru sudah memiliki jadwal pada waktu tersebut');
        }
        
        // Cek konflik jadwal kelas
        $konflikKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai);
        if ($konflikKelas) {
            log_message('warning', 'Admin Jadwal Store - Konflik jadwal kelas detected. Kelas ID: ' . $kelasId . ', Hari: ' . $hari . ', Jam: ' . $jamMulai . '-' . $jamSelesai);
            return redirect()->back()->withInput()->with('error', 'Kelas sudah memiliki jadwal pada waktu tersebut');
        }

        // Validasi input lainnya
        $rules = [
            'guru_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'mata_pelajaran_id' => 'required|numeric',
            'hari' => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat]',
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
            'mata_pelajaran_id' => [
                'required' => 'Mata pelajaran harus dipilih',
                'numeric' => 'Mata pelajaran tidak valid'
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
            log_message('warning', 'Admin Jadwal Store - Invalid time range. Jam mulai: ' . $jamMulai . ', Jam selesai: ' . $jamSelesai);
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
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'hari' => $hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'semester' => $this->request->getPost('semester'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Log data yang akan diinsert
            log_message('debug', 'Admin Jadwal Store - Data to insert: ' . json_encode($jadwalData));

            $result = $db->table('jadwal')->insert($jadwalData);

            // Log hasil insert
            if ($result) {
                $insertId = $db->insertID();
                log_message('debug', 'Admin Jadwal Store - Insert successful, ID: ' . $insertId);
            } else {
                log_message('error', 'Admin Jadwal Store - Insert failed, no result returned');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Admin Jadwal Store - Transaction failed. Error: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal. Error: ' . ($error['message'] ?? 'Unknown error'));
            }

            log_message('info', 'Admin Jadwal Store - Jadwal berhasil ditambahkan untuk Guru ID: ' . $guruId . ', Kelas ID: ' . $kelasId);
            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Admin Jadwal Store - Exception occurred: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            log_message('error', 'Admin Jadwal Store - Stack trace: ' . $e->getTraceAsString());
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
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'mata_pelajaran' => $this->mataPelajaranModel->getAktifMataPelajaran()
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
            log_message('warning', 'Admin Jadwal Update - Konflik jadwal guru detected. Guru ID: ' . $guruId . ', Hari: ' . $hari . ', Jam: ' . $jamMulai . '-' . $jamSelesai . ', Jadwal ID: ' . $id);
            return redirect()->back()->withInput()->with('error', 'Guru sudah memiliki jadwal pada waktu tersebut');
        }
        
        // Cek konflik jadwal kelas (kecuali jadwal yang sedang diedit)
        $konflikKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $id);
        if ($konflikKelas) {
            log_message('warning', 'Admin Jadwal Update - Konflik jadwal kelas detected. Kelas ID: ' . $kelasId . ', Hari: ' . $hari . ', Jam: ' . $jamMulai . '-' . $jamSelesai . ', Jadwal ID: ' . $id);
            return redirect()->back()->withInput()->with('error', 'Kelas sudah memiliki jadwal pada waktu tersebut');
        }

        // Validasi input lainnya
        $rules = [
            'guru_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'mata_pelajaran_id' => 'required|numeric',
            'hari' => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat]',
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
            'mata_pelajaran_id' => [
                'required' => 'Mata pelajaran harus dipilih',
                'numeric' => 'Mata pelajaran tidak valid'
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
            log_message('warning', 'Admin Jadwal Update - Invalid time range. Jam mulai: ' . $jamMulai . ', Jam selesai: ' . $jamSelesai . ', Jadwal ID: ' . $id);
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
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
                'hari' => $hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'semester' => $this->request->getPost('semester'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Log data yang akan diupdate
            log_message('debug', 'Admin Jadwal Update - Data to update for ID ' . $id . ': ' . json_encode($jadwalData));

            $result = $db->table('jadwal')->where('id', $id)->update($jadwalData);

            // Log hasil update
            if ($result) {
                $affectedRows = $db->affectedRows();
                log_message('debug', 'Admin Jadwal Update - Update successful, affected rows: ' . $affectedRows);
            } else {
                log_message('error', 'Admin Jadwal Update - Update failed, no result returned');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Admin Jadwal Update - Transaction failed. Error: ' . json_encode($error));
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jadwal. Error: ' . ($error['message'] ?? 'Unknown error'));
            }

            log_message('info', 'Admin Jadwal Update - Jadwal ID ' . $id . ' berhasil diperbarui untuk Guru ID: ' . $guruId . ', Kelas ID: ' . $kelasId);
            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Admin Jadwal Update - Exception occurred: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            log_message('error', 'Admin Jadwal Update - Stack trace: ' . $e->getTraceAsString());
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
            // Log jadwal yang akan dihapus
            log_message('debug', 'Admin Jadwal Delete - Attempting to delete jadwal ID: ' . $id);

            // Hapus data jadwal
            $result = $db->table('jadwal')->where('id', $id)->delete();

            // Log hasil delete
            if ($result) {
                $affectedRows = $db->affectedRows();
                log_message('debug', 'Admin Jadwal Delete - Delete successful, affected rows: ' . $affectedRows);
            } else {
                log_message('error', 'Admin Jadwal Delete - Delete failed, no result returned');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', 'Admin Jadwal Delete - Transaction failed. Error: ' . json_encode($error));
                return redirect()->to('admin/jadwal')->with('error', 'Gagal menghapus jadwal. Error: ' . ($error['message'] ?? 'Unknown error'));
            }

            log_message('info', 'Admin Jadwal Delete - Jadwal ID ' . $id . ' berhasil dihapus');
            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Admin Jadwal Delete - Exception occurred: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            log_message('error', 'Admin Jadwal Delete - Stack trace: ' . $e->getTraceAsString());
            return redirect()->to('admin/jadwal')->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
} 