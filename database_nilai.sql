-- Tabel untuk menyimpan data nilai siswa
CREATE TABLE nilai (
    id INT PRIMARY KEY AUTO_INCREMENT,
    siswa_id INT NOT NULL,
    jadwal_id INT NOT NULL,
    nilai_tugas JSON, -- Multiple tugas: [85, 90, 88]
    nilai_ulangan JSON, -- Multiple ulangan: [92, 87, 95]
    nilai_uts DECIMAL(5,2) NULL,
    nilai_uas DECIMAL(5,2) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (jadwal_id) REFERENCES jadwal(id) ON DELETE CASCADE,
    
    -- Pastikan satu siswa hanya punya satu nilai per jadwal
    UNIQUE KEY unique_siswa_jadwal (siswa_id, jadwal_id)
);

-- Index untuk optimasi query
CREATE INDEX idx_nilai_siswa ON nilai(siswa_id);
CREATE INDEX idx_nilai_jadwal ON nilai(jadwal_id);
CREATE INDEX idx_nilai_created ON nilai(created_at); 