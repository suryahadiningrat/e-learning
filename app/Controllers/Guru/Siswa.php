<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\SiswaModel;
class Siswa extends BaseController {
    public function index() {
        $siswaModel = new SiswaModel();
        $siswas = $siswaModel->getSiswaWithRelations();
        return view('guru/siswa/index', ['siswas' => $siswas, 'title' => 'Data Siswa']);
    }
} 