<?php

namespace Config;

class MenuConfig
{
    /**
     * Menu configuration for different roles
     */
    public static function getMenuByRole($role)
    {
        $menus = [
            'admin' => [
                [
                    'title' => 'Panel Admin',
                    'type' => 'header'
                ],
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => 'admin/dashboard',
                    'active_pattern' => 'admin/dashboard'
                ],
                [
                    'title' => 'User',
                    'icon' => 'fas fa-user-cog',
                    'url' => 'admin/user-pengguna',
                    'active_pattern' => 'admin/user-pengguna'
                ],
                [
                    'title' => 'User Activation',
                    'icon' => 'fas fa-users',
                    'url' => 'admin/users',
                    'active_pattern' => 'admin/users'
                ],
                [
                    'title' => 'Setting System',
                    'icon' => 'fas fa-cogs',
                    'url' => 'admin/setting-system',
                    'active_pattern' => 'admin/setting-system'
                ],
                [
                    'title' => 'Data Master',
                    'type' => 'header'
                ],
                [
                    'title' => 'Data Guru',
                    'icon' => 'fas fa-chalkboard-teacher',
                    'url' => 'admin/guru',
                    'active_pattern' => 'admin/guru'
                ],
                [
                    'title' => 'Data Siswa',
                    'icon' => 'fas fa-user-graduate',
                    'url' => 'admin/siswa',
                    'active_pattern' => 'admin/siswa'
                ],
                [
                    'title' => 'Data Jurusan',
                    'icon' => 'fas fa-building',
                    'url' => 'admin/jurusan',
                    'active_pattern' => 'admin/jurusan'
                ],
                [
                    'title' => 'Data Kelas',
                    'icon' => 'fas fa-door-open',
                    'url' => 'admin/kelas',
                    'active_pattern' => 'admin/kelas'
                ],
                [
                    'title' => 'Data Mata Pelajaran',
                    'icon' => 'fas fa-book-open',
                    'url' => 'admin/mata-pelajaran',
                    'active_pattern' => 'admin/mata-pelajaran'
                ],
                [
                    'title' => 'Data Jadwal',
                    'icon' => 'fas fa-calendar-alt',
                    'url' => 'admin/jadwal',
                    'active_pattern' => 'admin/jadwal'
                ],
                [
                    'title' => 'Pembelajaran',
                    'type' => 'header'
                ],
                [
                    'title' => 'Absensi',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => 'admin/absensi',
                    'active_pattern' => 'admin/absensi'
                ],
                [
                    'title' => 'Materi/Modul',
                    'icon' => 'fas fa-book',
                    'url' => 'admin/materi',
                    'active_pattern' => 'admin/materi'
                ],
                [
                    'title' => 'Link Pengumpulan Tugas',
                    'icon' => 'fas fa-link',
                    'url' => 'admin/tugas',
                    'active_pattern' => 'admin/tugas'
                ],
                [
                    'title' => 'Tugas/Soal Online',
                    'icon' => 'fas fa-file-alt',
                    'url' => 'admin/ulangan',
                    'active_pattern' => 'admin/ulangan'
                ],
                [
                    'title' => 'Nilai',
                    'icon' => 'fas fa-chart-line',
                    'url' => 'admin/nilai',
                    'active_pattern' => 'admin/nilai'
                ]
            ],
            'guru' => [
                [
                    'title' => 'Panel Guru',
                    'type' => 'header'
                ],
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => 'guru/dashboard',
                    'active_pattern' => 'guru/dashboard'
                ],
                [
                    'title' => 'User',
                    'icon' => 'fas fa-user-cog',
                    'url' => 'guru/user-pengguna',
                    'active_pattern' => 'guru/user-pengguna'
                ],
                [
                    'title' => 'Jadwal Pengajar',
                    'icon' => 'fas fa-calendar-check',
                    'url' => 'guru/jadwal',
                    'active_pattern' => 'guru/jadwal'
                ],
                [
                    'title' => 'Pembelajaran',
                    'type' => 'header'
                ],
                [
                    'title' => 'Absensi',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => 'guru/absensi',
                    'active_pattern' => 'guru/absensi'
                ],
                [
                    'title' => 'Materi/Modul',
                    'icon' => 'fas fa-book',
                    'url' => 'guru/materi',
                    'active_pattern' => 'guru/materi'
                ],
                [
                    'title' => 'Link Pengumpulan Tugas',
                    'icon' => 'fas fa-link',
                    'url' => 'guru/tugas',
                    'active_pattern' => 'guru/tugas'
                ],
                [
                    'title' => 'Tugas/Soal Online',
                    'icon' => 'fas fa-clipboard-list',
                    'url' => 'guru/ulangan',
                    'active_pattern' => 'guru/ulangan'
                ],
                [
                    'title' => 'Nilai',
                    'icon' => 'fas fa-chart-line',
                    'url' => 'guru/nilai',
                    'active_pattern' => 'guru/nilai'
                ]
            ],
            'siswa' => [
                [
                    'title' => 'Panel Siswa',
                    'type' => 'header'
                ],
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => 'siswa/dashboard',
                    'active_pattern' => 'siswa/dashboard'
                ],
                [
                    'title' => 'User',
                    'icon' => 'fas fa-user-cog',
                    'url' => 'siswa/user-pengguna',
                    'active_pattern' => 'siswa/user-pengguna'
                ],
                [
                    'title' => 'Jadwal Pelajaran',
                    'icon' => 'fas fa-calendar-alt',
                    'url' => 'siswa/jadwal',
                    'active_pattern' => 'siswa/jadwal'
                ],
                [
                    'title' => 'Pembelajaran',
                    'type' => 'header'
                ],
                [
                    'title' => 'Materi/Modul',
                    'icon' => 'fas fa-book',
                    'url' => 'siswa/materi',
                    'active_pattern' => 'siswa/materi'
                ],
                [
                    'title' => 'Link Pengumpulan Tugas',
                    'icon' => 'fas fa-link',
                    'url' => 'siswa/tugas',
                    'active_pattern' => 'siswa/tugas'
                ],
                [
                    'title' => 'Tugas/Soal Online',
                    'icon' => 'fas fa-clipboard-list',
                    'url' => 'siswa/ulangan',
                    'active_pattern' => 'siswa/ulangan'
                ],
                [
                    'title' => 'Nilai',
                    'icon' => 'fas fa-chart-line',
                    'url' => 'siswa/nilai',
                    'active_pattern' => 'siswa/nilai'
                ]
            ]
        ];

        return $menus[$role] ?? [];
    }

    /**
     * Get sidebar color by role
     */
    public static function getSidebarColorByRole($role)
    {
        $colors = [
            'admin' => 'linear-gradient(to bottom, #4e73df, #224abe)',
            'guru' => 'linear-gradient(to bottom, #1cc88a, #169b6b)',
            'siswa' => 'linear-gradient(to bottom, #f6c23e, #dda20a)'
        ];

        return $colors[$role] ?? $colors['admin'];
    }

    /**
     * Get panel title by role
     */
    public static function getPanelTitleByRole($role)
    {
        $titles = [
            'admin' => 'Panel Admin',
            'guru' => 'Panel Guru',
            'siswa' => 'Panel Siswa'
        ];

        return $titles[$role] ?? 'Panel';
    }
}