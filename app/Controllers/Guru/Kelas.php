<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\KelasModel;
class Kelas extends BaseController {
    public function index() {
        $kelasModel = new KelasModel();
        $kelas = $kelasModel->getKelasWithRelations();
        return view('guru/kelas/index', ['kelas' => $kelas, 'title' => 'Data Kelas']);
    }
} 