<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Auth Routes
$routes->get('/', 'Auth::index');
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/processRegister', 'Auth::processRegister');
$routes->get('auth/logout', 'Auth::logout');

// Guru Routes
$routes->group('guru', ['filter' => 'auth:role:guru'], function($routes) {
    $routes->get('user-pengguna', 'Guru\UserPengguna::index');
    $routes->post('user-pengguna/update', 'Guru\UserPengguna::update');
});

// Siswa Routes
$routes->group('siswa', ['filter' => 'auth:role:siswa'], function($routes) {
    $routes->get('/', 'Siswa\Dashboard::index');
    $routes->get('dashboard', 'Siswa\Dashboard::index');
    
    // User
    $routes->get('user-pengguna', 'Siswa\UserPengguna::index');
    $routes->post('user-pengguna/update', 'Siswa\UserPengguna::update');
    
    // Jadwal Pelajaran
    $routes->get('jadwal', 'Siswa\Jadwal::index');
    
    // Presensi
    $routes->get('presensi', 'Siswa\Presensi::index');
    $routes->get('presensi/detail/(:any)', 'Siswa\Presensi::detail/$1');
    
    // Materi/Modul
    $routes->get('materi', 'Siswa\Materi::index');
    $routes->get('materi/download/(:num)', 'Siswa\Materi::download/$1');
    
    // Link Pengumpulan Tugas
    $routes->get('tugas', 'Siswa\Tugas::index');
    $routes->get('tugas/detail/(:num)', 'Siswa\Tugas::detail/$1');
    $routes->post('tugas/submit/(:num)', 'Siswa\Tugas::submit/$1');
    $routes->post('tugas/submit', 'Siswa\Tugas::submit');
    $routes->post('tugas/update', 'Siswa\Tugas::update');
    $routes->post('tugas/edit-submission/(:num)', 'Siswa\Tugas::editSubmission/$1');
    $routes->post('tugas/delete-submission/(:num)', 'Siswa\Tugas::deleteSubmission/$1');
    
    // Tugas/Soal Online
    $routes->get('ulangan', 'Siswa\Ulangan::index');
    $routes->get('ulangan/kerjakan/(:num)', 'Siswa\Ulangan::kerjakan/$1');
    $routes->post('ulangan/save-jawaban', 'Siswa\Ulangan::saveJawaban');
    $routes->post('ulangan/submit-jawaban', 'Siswa\Ulangan::submitJawaban');
    $routes->get('ulangan/hasil/(:num)', 'Siswa\Ulangan::hasil/$1');
    $routes->get('ulangan/riwayat', 'Siswa\Ulangan::riwayat');
    
    // Nilai
    $routes->get('nilai', 'Siswa\Nilai::index');
    $routes->get('nilai/export', 'Siswa\Nilai::export');
});

// Admin Routes
$routes->group('admin', ['filter' => 'auth:role:admin'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin\Dashboard::index');
    
    // User Management
    $routes->get('users', 'Admin\User::index');
    $routes->get('users/activate/(:num)', 'Admin\User::activate/$1');
    $routes->get('users/deactivate/(:num)', 'Admin\User::deactivate/$1');
    
    // User Activation
    $routes->get('user-pengguna', 'Admin\UserPengguna::index');
    $routes->get('user-pengguna/create', 'Admin\UserPengguna::create');
    $routes->post('user-pengguna/store', 'Admin\UserPengguna::store');
    $routes->get('user-pengguna/edit/(:num)', 'Admin\UserPengguna::edit/$1');
    $routes->post('user-pengguna/update/(:num)', 'Admin\UserPengguna::update/$1');
    $routes->get('user-pengguna/delete/(:num)', 'Admin\UserPengguna::delete/$1');
    $routes->get('user-pengguna/toggle-status/(:num)', 'Admin\UserPengguna::toggleStatus/$1');
    
    // Setting System
    $routes->get('setting-system', 'Admin\SettingSystem::index');
    $routes->post('setting-system/update-logo', 'Admin\SettingSystem::updateLogo');
    $routes->post('setting-system/update-background', 'Admin\SettingSystem::updateBackground');
    $routes->post('setting-system/update-tahun-ajaran', 'Admin\SettingSystem::updateTahunAjaran');
    $routes->post('setting-system/update-sidebar-color', 'Admin\SettingSystem::updateSidebarColor');
    $routes->post('setting-system/update-login-background-color', 'Admin\SettingSystem::updateLoginBackgroundColor');
    $routes->post('setting-system/update-login-background-image', 'Admin\SettingSystem::updateLoginBackgroundImage');
    $routes->post('setting-system/remove-login-background-image', 'Admin\SettingSystem::removeLoginBackgroundImage');
    
    // Data Guru
    $routes->get('guru', 'Admin\Guru::index');
    $routes->get('guru/create', 'Admin\Guru::create');
    $routes->post('guru/store', 'Admin\Guru::store');
    $routes->get('guru/edit/(:num)', 'Admin\Guru::edit/$1');
    $routes->post('guru/update/(:num)', 'Admin\Guru::update/$1');
    $routes->get('guru/delete/(:num)', 'Admin\Guru::delete/$1');
    $routes->get('guru/jadwal/(:num)', 'Admin\Guru::getJadwalGuru/$1');
    $routes->get('guru/print/(:num)', 'Admin\Guru::print/$1');
    
    // Data Siswa
    $routes->get('siswa', 'Admin\Siswa::index');
    $routes->get('siswa/jurusan/(:num)', 'Admin\Siswa::jurusan/$1');
    $routes->get('siswa/kelas/(:num)', 'Admin\Siswa::kelas/$1');
    $routes->get('siswa/create', 'Admin\Siswa::create');
    $routes->get('siswa/create/(:num)', 'Admin\Siswa::create/$1');
    $routes->post('siswa/store', 'Admin\Siswa::store');
    $routes->get('siswa/edit/(:num)', 'Admin\Siswa::edit/$1');
    $routes->post('siswa/update/(:num)', 'Admin\Siswa::update/$1');
    $routes->get('siswa/delete/(:num)', 'Admin\Siswa::delete/$1');
    $routes->get('siswa/export-jurusan/(:num)', 'Admin\Siswa::exportJurusan/$1');
    $routes->get('siswa/export-kelas/(:num)', 'Admin\Siswa::exportKelas/$1');
    
    // Data Jurusan
    $routes->get('jurusan', 'Admin\Jurusan::index');
    $routes->get('jurusan/create', 'Admin\Jurusan::create');
    $routes->post('jurusan/store', 'Admin\Jurusan::store');
    $routes->get('jurusan/edit/(:num)', 'Admin\Jurusan::edit/$1');
    $routes->post('jurusan/update/(:num)', 'Admin\Jurusan::update/$1');
    $routes->get('jurusan/delete/(:num)', 'Admin\Jurusan::delete/$1');
    
    // Data Kelas
    $routes->get('kelas', 'Admin\Kelas::index');
    $routes->get('kelas/create', 'Admin\Kelas::create');
    $routes->post('kelas/store', 'Admin\Kelas::store');
    $routes->get('kelas/edit/(:num)', 'Admin\Kelas::edit/$1');
    $routes->post('kelas/update/(:num)', 'Admin\Kelas::update/$1');
    $routes->get('kelas/delete/(:num)', 'Admin\Kelas::delete/$1');
    
    // Data Mata Pelajaran
    $routes->get('mata-pelajaran', 'Admin\MataPelajaran::index');
    $routes->get('mata-pelajaran/create', 'Admin\MataPelajaran::create');
    $routes->post('mata-pelajaran/store', 'Admin\MataPelajaran::store');
    $routes->get('mata-pelajaran/edit/(:num)', 'Admin\MataPelajaran::edit/$1');
    $routes->post('mata-pelajaran/update/(:num)', 'Admin\MataPelajaran::update/$1');
    $routes->get('mata-pelajaran/delete/(:num)', 'Admin\MataPelajaran::delete/$1');
    $routes->get('mata-pelajaran/toggle-status/(:num)', 'Admin\MataPelajaran::toggleStatus/$1');
    
    // Data Jadwal
    $routes->get('jadwal', 'Admin\Jadwal::index');
    $routes->get('jadwal/create', 'Admin\Jadwal::create');
    $routes->post('jadwal/store', 'Admin\Jadwal::store');
    $routes->get('jadwal/edit/(:num)', 'Admin\Jadwal::edit/$1');
    $routes->post('jadwal/update/(:num)', 'Admin\Jadwal::update/$1');
    $routes->get('jadwal/delete/(:num)', 'Admin\Jadwal::delete/$1');
    
    // Absensi
    $routes->get('absensi', 'Admin\Absensi::index');
    $routes->get('absensi/kelas/(:num)', 'Admin\Absensi::kelas/$1');
    $routes->get('absensi/jadwal/(:num)', 'Admin\Absensi::jadwal/$1');
    $routes->get('absensi/hari/(:num)', 'Admin\Absensi::hari/$1');
    $routes->get('absensi/input/(:num)', 'Admin\Absensi::input/$1');
    $routes->get('absensi/createHari/(:num)', 'Admin\Absensi::createHari/$1');
    $routes->post('absensi/storeHari', 'Admin\Absensi::storeHari');
    $routes->post('absensi/store-absensi', 'Admin\Absensi::storeAbsensi');
    $routes->get('absensi/exportHari/(:num)', 'Admin\Absensi::exportHari/$1');
    $routes->get('absensi/exportJadwal/(:num)', 'Admin\Absensi::exportJadwal/$1');
    $routes->get('absensi/exportKelas/(:num)', 'Admin\Absensi::exportKelas/$1');
    $routes->get('absensi/exportJurusan/(:num)', 'Admin\Absensi::exportJurusan/$1');
    
    // Legacy routes for backward compatibility
    $routes->get('absensi/create', 'Admin\Absensi::create');
    $routes->get('absensi/get-jadwal-by-kelas/(:num)', 'Admin\Absensi::getJadwalByKelas/$1');
    $routes->get('absensi/get-siswa-by-kelas/(:num)', 'Admin\Absensi::getSiswaByKelas/$1');
    $routes->get('absensi/export', 'Admin\Absensi::export');
    
    // Materi/Modul
    $routes->get('materi', 'Admin\Materi::index');
    $routes->get('materi/create', 'Admin\Materi::create');
    $routes->post('materi/store', 'Admin\Materi::store');
    $routes->get('materi/edit/(:num)', 'Admin\Materi::edit/$1');
    $routes->post('materi/update/(:num)', 'Admin\Materi::update/$1');
    $routes->get('materi/delete/(:num)', 'Admin\Materi::delete/$1');
    $routes->get('materi/download/(:num)', 'Admin\Materi::download/$1');
    
    // Link Pengumpulan Tugas
    $routes->get('tugas', 'Admin\Tugas::index');
    $routes->get('tugas/create', 'Admin\Tugas::create');
    $routes->post('tugas/store', 'Admin\Tugas::store');
    $routes->get('tugas/edit/(:num)', 'Admin\Tugas::edit/$1');
    $routes->post('tugas/update/(:num)', 'Admin\Tugas::update/$1');
    $routes->get('tugas/delete/(:num)', 'Admin\Tugas::delete/$1');
    $routes->post('tugas/delete/(:num)', 'Admin\Tugas::delete/$1');
    $routes->get('tugas/detail/(:num)', 'Admin\Tugas::detail/$1');
    
    // Tugas/Soal Online
    $routes->get('ulangan', 'Admin\Ulangan::index');
    $routes->get('ulangan/create', 'Admin\Ulangan::create');
    $routes->post('ulangan/store', 'Admin\Ulangan::store');
    $routes->get('ulangan/edit/(:num)', 'Admin\Ulangan::edit/$1');
    $routes->post('ulangan/update/(:num)', 'Admin\Ulangan::update/$1');
    $routes->get('ulangan/delete/(:num)', 'Admin\Ulangan::delete/$1');
    $routes->get('ulangan/publish/(:num)', 'Admin\Ulangan::publish/$1');
    $routes->get('ulangan/close/(:num)', 'Admin\Ulangan::close/$1');
    $routes->get('ulangan/preview/(:num)', 'Admin\Ulangan::preview/$1');
    $routes->get('ulangan/hasil/(:num)', 'Admin\Ulangan::hasil/$1');
    $routes->get('ulangan/detail-hasil/(:num)/(:num)', 'Admin\Ulangan::detailHasil/$1/$2');
    
    // Nilai
    $routes->get('nilai', 'Admin\Nilai::index');
    $routes->get('nilai/mata-pelajaran/(:num)', 'Admin\Nilai::mataPelajaran/$1');
    $routes->get('nilai/input/(:num)', 'Admin\Nilai::inputNilai/$1');
    $routes->post('nilai/store', 'Admin\Nilai::store');
    $routes->get('nilai/view/(:num)', 'Admin\Nilai::viewNilai/$1');
    $routes->get('nilai/export/(:num)', 'Admin\Nilai::export/$1');
    $routes->get('nilai/print/(:num)', 'Admin\Nilai::print/$1');
});

// Guru Routes
$routes->group('guru', ['filter' => 'auth:role:guru'], function($routes) {
    $routes->get('/', 'Guru\Dashboard::index');
    $routes->get('dashboard', 'Guru\Dashboard::index');
    
    // User Pengguna
    $routes->get('user-pengguna', 'Guru\UserPengguna::index');
    $routes->post('user-pengguna/update', 'Guru\UserPengguna::update');
    
    // Jadwal Mengajar
    $routes->get('jadwal', 'Guru\Jadwal::index');
    
    // Jadwal Pengajar - New menu item for guru
    $routes->get('jadwal-pengajar', 'Guru\JadwalPengajar::index');
    
    // Data Absensi (CRUD) - New Structured Flow
    $routes->get('absensi', 'Guru\Absensi::index');
    $routes->get('absensi/kelas/(:num)', 'Guru\Absensi::kelas/$1');
    $routes->get('absensi/jadwal/(:num)', 'Guru\Absensi::jadwal/$1');
    $routes->get('absensi/hari/(:num)', 'Guru\Absensi::hari/$1');
    $routes->get('absensi/input/(:num)', 'Guru\Absensi::inputAbsensi/$1');
    $routes->get('absensi/create-hari/(:num)', 'Guru\Absensi::createHari/$1');
    $routes->post('absensi/store-hari', 'Guru\Absensi::storeHari');
    $routes->post('absensi/store', 'Guru\Absensi::store');
    $routes->get('absensi/edit/(:num)', 'Guru\Absensi::edit/$1');
    $routes->post('absensi/update/(:num)', 'Guru\Absensi::update/$1');
    $routes->get('absensi/delete/(:num)', 'Guru\Absensi::delete/$1');
    $routes->get('absensi/export-hari/(:num)', 'Guru\Absensi::exportHari/$1');
    $routes->get('absensi/export-jadwal/(:num)', 'Guru\Absensi::exportJadwal/$1');
    $routes->get('absensi/export-kelas/(:num)', 'Guru\Absensi::exportKelas/$1');
    $routes->get('absensi/export-jurusan/(:num)', 'Guru\Absensi::exportJurusan/$1');
    
    // Legacy routes for backward compatibility
    $routes->get('absensi/create', 'Guru\Absensi::create');
    $routes->post('absensi/store-absensi', 'Guru\Absensi::storeAbsensi');
    $routes->get('absensi/get-jadwal-by-kelas/(:num)', 'Guru\Absensi::getJadwalByKelas/$1');
    $routes->get('absensi/get-siswa-by-kelas/(:num)', 'Guru\Absensi::getSiswaByKelas/$1');
    $routes->get('absensi/export', 'Guru\Absensi::export');
    
    // Data Nilai
    $routes->get('nilai', 'Guru\Nilai::index');
    $routes->get('nilai/mata-pelajaran/(:num)', 'Guru\Nilai::mataPelajaran/$1');
    $routes->get('nilai/input/(:num)', 'Guru\Nilai::inputNilai/$1');
    $routes->post('nilai/store', 'Guru\Nilai::store');
    $routes->get('nilai/view/(:num)', 'Guru\Nilai::viewNilai/$1');
    $routes->get('nilai/export/(:num)', 'Guru\Nilai::export/$1');
    
    // Materi/Modul
    $routes->get('materi', 'Guru\Materi::index');
    $routes->get('materi/create', 'Guru\Materi::create');
    $routes->post('materi/store', 'Guru\Materi::store');
    $routes->get('materi/edit/(:num)', 'Guru\Materi::edit/$1');
    $routes->post('materi/update/(:num)', 'Guru\Materi::update/$1');
    $routes->get('materi/delete/(:num)', 'Guru\Materi::delete/$1');
    $routes->get('materi/download/(:num)', 'Guru\Materi::download/$1');
    
    // Link Pengumpulan Tugas
    $routes->get('tugas', 'Guru\Tugas::index');
    $routes->get('tugas/create', 'Guru\Tugas::create');
    $routes->post('tugas/store', 'Guru\Tugas::store');
    $routes->get('tugas/edit/(:num)', 'Guru\Tugas::edit/$1');
    $routes->post('tugas/update/(:num)', 'Guru\Tugas::update/$1');
    $routes->get('tugas/delete/(:num)', 'Guru\Tugas::delete/$1');
    $routes->post('tugas/delete/(:num)', 'Guru\Tugas::delete/$1');
    $routes->get('tugas/detail/(:num)', 'Guru\Tugas::detail/$1');
    $routes->post('tugas/feedback', 'Guru\Tugas::feedback');
    $routes->post('tugas/reminder', 'Guru\Tugas::reminder');
    $routes->get('tugas/export/(:num)', 'Guru\Tugas::export/$1');
    
    // Tugas/Soal Online
    $routes->get('ulangan', 'Guru\Ulangan::index');
    $routes->get('ulangan/create', 'Guru\Ulangan::create');
    $routes->post('ulangan/store', 'Guru\Ulangan::store');
    $routes->get('ulangan/edit/(:num)', 'Guru\Ulangan::edit/$1');
    $routes->post('ulangan/update/(:num)', 'Guru\Ulangan::update/$1');
    $routes->get('ulangan/delete/(:num)', 'Guru\Ulangan::delete/$1');
    $routes->get('ulangan/publish/(:num)', 'Guru\Ulangan::publish/$1');
    $routes->get('ulangan/close/(:num)', 'Guru\Ulangan::close/$1');
    $routes->get('ulangan/preview/(:num)', 'Guru\Ulangan::preview/$1');
    $routes->get('ulangan/hasil/(:num)', 'Guru\Ulangan::hasil/$1');
    $routes->get('ulangan/detail-hasil/(:num)/(:num)', 'Guru\Ulangan::detailHasil/$1/$2');
});


