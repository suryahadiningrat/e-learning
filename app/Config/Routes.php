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
    $routes->get('user-pengguna', 'Siswa\UserPengguna::index');
    $routes->post('user-pengguna/update', 'Siswa\UserPengguna::update');
});

// Admin Routes
$routes->group('admin', ['filter' => 'auth:role:admin'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('users', 'Admin\User::index');
    $routes->get('users/activate/(:num)', 'Admin\User::activate/$1');
    $routes->get('users/deactivate/(:num)', 'Admin\User::deactivate/$1');
    
    // User Pengguna routes
    $routes->get('user-pengguna', 'Admin\UserPengguna::index');
    $routes->get('user-pengguna/create', 'Admin\UserPengguna::create');
    $routes->post('user-pengguna/store', 'Admin\UserPengguna::store');
    $routes->get('user-pengguna/edit/(:num)', 'Admin\UserPengguna::edit/$1');
    $routes->post('user-pengguna/update/(:num)', 'Admin\UserPengguna::update/$1');
    $routes->get('user-pengguna/delete/(:num)', 'Admin\UserPengguna::delete/$1');
    $routes->get('user-pengguna/toggle-status/(:num)', 'Admin\UserPengguna::toggleStatus/$1');
    
    // Data Master routes
    $routes->get('jurusan', 'Admin\Jurusan::index');
    $routes->get('jurusan/create', 'Admin\Jurusan::create');
    $routes->post('jurusan/store', 'Admin\Jurusan::store');
    $routes->get('jurusan/edit/(:num)', 'Admin\Jurusan::edit/$1');
    $routes->post('jurusan/update/(:num)', 'Admin\Jurusan::update/$1');
    $routes->get('jurusan/delete/(:num)', 'Admin\Jurusan::delete/$1');
    
    // Mata Pelajaran routes
    $routes->get('mata-pelajaran', 'Admin\MataPelajaran::index');
    $routes->get('mata-pelajaran/create', 'Admin\MataPelajaran::create');
    $routes->post('mata-pelajaran/store', 'Admin\MataPelajaran::store');
    $routes->get('mata-pelajaran/edit/(:num)', 'Admin\MataPelajaran::edit/$1');
    $routes->post('mata-pelajaran/update/(:num)', 'Admin\MataPelajaran::update/$1');
    $routes->get('mata-pelajaran/delete/(:num)', 'Admin\MataPelajaran::delete/$1');
    $routes->get('mata-pelajaran/toggle-status/(:num)', 'Admin\MataPelajaran::toggleStatus/$1');
    
    $routes->get('siswa', 'Admin\Siswa::index');
    $routes->get('siswa/create', 'Admin\Siswa::create');
    $routes->post('siswa/store', 'Admin\Siswa::store');
    $routes->get('siswa/edit/(:num)', 'Admin\Siswa::edit/$1');
    $routes->post('siswa/update/(:num)', 'Admin\Siswa::update/$1');
    $routes->get('siswa/delete/(:num)', 'Admin\Siswa::delete/$1');
    
    $routes->get('guru', 'Admin\Guru::index');
    $routes->get('guru/create', 'Admin\Guru::create');
    $routes->post('guru/store', 'Admin\Guru::store');
    $routes->get('guru/edit/(:num)', 'Admin\Guru::edit/$1');
    $routes->post('guru/update/(:num)', 'Admin\Guru::update/$1');
    $routes->get('guru/delete/(:num)', 'Admin\Guru::delete/$1');
    
    $routes->get('kelas', 'Admin\Kelas::index');
    $routes->get('kelas/create', 'Admin\Kelas::create');
    $routes->post('kelas/store', 'Admin\Kelas::store');
    $routes->get('kelas/edit/(:num)', 'Admin\Kelas::edit/$1');
    $routes->post('kelas/update/(:num)', 'Admin\Kelas::update/$1');
    $routes->get('kelas/delete/(:num)', 'Admin\Kelas::delete/$1');
    
    $routes->get('jadwal', 'Admin\Jadwal::index');
    $routes->get('jadwal/create', 'Admin\Jadwal::create');
    $routes->post('jadwal/store', 'Admin\Jadwal::store');
    $routes->get('jadwal/edit/(:num)', 'Admin\Jadwal::edit/$1');
    $routes->post('jadwal/update/(:num)', 'Admin\Jadwal::update/$1');
    $routes->get('jadwal/delete/(:num)', 'Admin\Jadwal::delete/$1');
    
    $routes->get('absensi', 'Admin\Absensi::index');
    $routes->get('absensi/create', 'Admin\Absensi::create');
    $routes->post('absensi/store', 'Admin\Absensi::store');
    $routes->get('absensi/edit/(:num)', 'Admin\Absensi::edit/$1');
    $routes->post('absensi/update/(:num)', 'Admin\Absensi::update/$1');
    $routes->get('absensi/delete/(:num)', 'Admin\Absensi::delete/$1');
    $routes->get('absensi/get-jadwal-by-kelas/(:num)', 'Admin\Absensi::getJadwalByKelas/$1');
    $routes->get('absensi/get-siswa-by-kelas/(:num)', 'Admin\Absensi::getSiswaByKelas/$1');
    $routes->get('absensi/export', 'Admin\Absensi::export');
    
    $routes->get('nilai', 'Admin\Nilai::index');
    $routes->get('nilai/mata-pelajaran/(:num)', 'Admin\Nilai::mataPelajaran/$1');
    $routes->get('nilai/input/(:num)', 'Admin\Nilai::inputNilai/$1');
    $routes->post('nilai/store', 'Admin\Nilai::store');
    $routes->get('nilai/view/(:num)', 'Admin\Nilai::viewNilai/$1');
    $routes->get('nilai/export/(:num)', 'Admin\Nilai::export/$1');
    
    $routes->get('materi', 'Admin\Materi::index');
    $routes->get('materi/create', 'Admin\Materi::create');
    $routes->post('materi/store', 'Admin\Materi::store');
    $routes->get('materi/edit/(:num)', 'Admin\Materi::edit/$1');
    $routes->post('materi/update/(:num)', 'Admin\Materi::update/$1');
    $routes->get('materi/delete/(:num)', 'Admin\Materi::delete/$1');
    $routes->get('materi/download/(:num)', 'Admin\Materi::download/$1');
    
    $routes->get('tugas', 'Admin\Tugas::index');
    $routes->get('tugas/create', 'Admin\Tugas::create');
    $routes->post('tugas/store', 'Admin\Tugas::store');
    $routes->get('tugas/edit/(:num)', 'Admin\Tugas::edit/$1');
    $routes->post('tugas/update/(:num)', 'Admin\Tugas::update/$1');
    $routes->get('tugas/delete/(:num)', 'Admin\Tugas::delete/$1');
    
    // Ulangan routes
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
    
    // Setting System routes
    $routes->get('setting-system', 'Admin\SettingSystem::index');
    $routes->post('setting-system/update-logo', 'Admin\SettingSystem::updateLogo');
    $routes->post('setting-system/update-background', 'Admin\SettingSystem::updateBackground');
    $routes->post('setting-system/update-tahun-ajaran', 'Admin\SettingSystem::updateTahunAjaran');
    $routes->post('setting-system/update-sidebar-color', 'Admin\SettingSystem::updateSidebarColor');
});

// Guru Routes
$routes->group('guru', ['filter' => 'auth:role:guru'], function($routes) {
    $routes->get('dashboard', 'Guru\Dashboard::index');
    // User Pengguna (Read Only)
    $routes->get('user-pengguna', 'Guru\UserPengguna::index');
    // Data Siswa (Read Only)
    $routes->get('siswa', 'Guru\Siswa::index');
    // Data Jurusan/Kelas (Read Only)
    $routes->get('kelas', 'Guru\Kelas::index');
    $routes->get('jurusan', 'Guru\Jurusan::index');
    // Data Absensi (CRUD)
    $routes->get('absensi', 'Guru\Absensi::index');
    $routes->get('absensi/create', 'Guru\Absensi::create');
    $routes->post('absensi/store', 'Guru\Absensi::store');
    $routes->get('absensi/edit/(:num)', 'Guru\Absensi::edit/$1');
    $routes->post('absensi/update/(:num)', 'Guru\Absensi::update/$1');
    $routes->get('absensi/delete/(:num)', 'Guru\Absensi::delete/$1');
    $routes->get('absensi/get-jadwal-by-kelas/(:num)', 'Guru\Absensi::getJadwalByKelas/$1');
    $routes->get('absensi/get-siswa-by-kelas/(:num)', 'Guru\Absensi::getSiswaByKelas/$1');
    $routes->get('absensi/export', 'Guru\Absensi::export');
    
    $routes->get('nilai', 'Guru\Nilai::index');
    $routes->get('nilai/mata-pelajaran/(:num)', 'Guru\Nilai::mataPelajaran/$1');
    $routes->get('nilai/input/(:num)', 'Guru\Nilai::inputNilai/$1');
    $routes->post('nilai/store', 'Guru\Nilai::store');
    $routes->get('nilai/view/(:num)', 'Guru\Nilai::viewNilai/$1');
    $routes->get('nilai/export/(:num)', 'Guru\Nilai::export/$1');
    
    $routes->get('materi', 'Guru\Materi::index');
    $routes->get('materi/create', 'Guru\Materi::create');
    $routes->post('materi/store', 'Guru\Materi::store');
    $routes->get('materi/edit/(:num)', 'Guru\Materi::edit/$1');
    $routes->post('materi/update/(:num)', 'Guru\Materi::update/$1');
    $routes->get('materi/delete/(:num)', 'Guru\Materi::delete/$1');
    $routes->get('materi/download/(:num)', 'Guru\Materi::download/$1');
    
    // Ulangan routes untuk Guru
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

// Routes untuk Guru
$routes->group('guru', ['filter' => 'auth:guru'], function($routes) {
    $routes->get('/', 'Guru\Dashboard::index');
    
    // Data Nilai untuk Guru
    $routes->get('nilai', 'Guru\Nilai::index');
    $routes->get('nilai/mata-pelajaran/(:num)', 'Guru\Nilai::mataPelajaran/$1');
    $routes->get('nilai/input/(:num)', 'Guru\Nilai::inputNilai/$1');
    $routes->post('nilai/store', 'Guru\Nilai::store');
    $routes->get('nilai/export/(:num)', 'Guru\Nilai::export/$1');
});

// Siswa Routes
$routes->group('siswa', ['filter' => 'auth:siswa'], function($routes) {
    $routes->get('/', 'Siswa\Dashboard::index');
    $routes->get('dashboard', 'Siswa\Dashboard::index');
    
    // Data Jadwal untuk Siswa
    $routes->get('jadwal', 'Siswa\Jadwal::index');
    
    // Data Nilai untuk Siswa
    $routes->get('nilai', 'Siswa\Nilai::index');
    $routes->get('nilai/export', 'Siswa\Nilai::export');
    
    // Data Materi untuk Siswa
    $routes->get('materi', 'Siswa\Materi::index');
    $routes->get('materi/download/(:num)', 'Siswa\Materi::download/$1');
    
    // Data Ulangan untuk Siswa
    $routes->get('ulangan', 'Siswa\Ulangan::index');
    $routes->get('ulangan/kerjakan/(:num)', 'Siswa\Ulangan::kerjakan/$1');
    $routes->post('ulangan/save-jawaban', 'Siswa\Ulangan::saveJawaban');
    $routes->post('ulangan/submit-jawaban', 'Siswa\Ulangan::submitJawaban');
    $routes->get('ulangan/hasil/(:num)', 'Siswa\Ulangan::hasil/$1');
    $routes->get('ulangan/riwayat', 'Siswa\Ulangan::riwayat');
});
