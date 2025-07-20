-- Drop table if exists
DROP TABLE IF EXISTS `nilai`;

-- Create table nilai dengan struktur yang benar (tanpa foreign key constraint)
CREATE TABLE `nilai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `jadwal_id` int(11) NOT NULL,
  `nilai_tugas` JSON NULL COMMENT 'Array nilai tugas multiple',
  `nilai_ulangan` JSON NULL COMMENT 'Array nilai ulangan multiple',
  `nilai_uts` decimal(5,2) NULL,
  `nilai_uas` decimal(5,2) NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `idx_siswa_jadwal` (`siswa_id`, `jadwal_id`),
  KEY `idx_jadwal` (`jadwal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci; 