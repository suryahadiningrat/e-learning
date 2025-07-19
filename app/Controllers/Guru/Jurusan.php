<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\JurusanModel;
class Jurusan extends BaseController {
    public function index() {
        $jurusanModel = new JurusanModel();
        $jurusan = $jurusanModel->findAll();
        return view('guru/jurusan/index', ['jurusan' => $jurusan, 'title' => 'Data Jurusan']);
    }
} 