<?php if (isset($tugas)): ?>
    <div class="mb-3">
        <label for="edit_nama_tugas" class="form-label">Nama Tugas <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="edit_nama_tugas" name="nama_tugas" 
               value="<?= esc($tugas['nama_tugas']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="edit_jadwal_id" class="form-label">Jadwal <span class="text-danger">*</span></label>
        <select class="form-select" id="edit_jadwal_id" name="jadwal_id" required>
            <option value="">Pilih Jadwal</option>
            <?php foreach ($jadwal as $item): ?>
                <option value="<?= $item['jadwal_id'] ?>" <?= $tugas['jadwal_id'] == $item['jadwal_id'] ? 'selected' : '' ?>>
                    <?= esc($item['nama_mata_pelajaran']) ?> - <?= esc($item['nama_kelas']) ?> 
                    (<?= esc($item['hari']) ?> <?= date('H:i', strtotime($item['jam_mulai'])) ?>-<?= date('H:i', strtotime($item['jam_selesai'])) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Hanya jadwal mata pelajaran yang Anda ampu</div>
    </div>

    <div class="mb-3">
        <label for="edit_deskripsi" class="form-label">Deskripsi Tugas</label>
        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"><?= esc($tugas['deskripsi']) ?></textarea>
    </div>

    <div class="mb-3">
        <label for="edit_deadline" class="form-label">Deadline</label>
        <input type="datetime-local" class="form-control" id="edit_deadline" name="deadline" 
               value="<?= $tugas['deadline'] ? date('Y-m-d\TH:i', strtotime($tugas['deadline'])) : '' ?>">
        <div class="form-text">Opsional - kosongkan jika tidak ada deadline</div>
    </div>

    <div class="alert alert-info">
        <small>
            <i class="fas fa-info-circle"></i> 
            <strong>Perhatian:</strong> Mengubah jadwal akan mempengaruhi akses siswa untuk melihat tugas ini.
        </small>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> 
        Terjadi kesalahan saat memuat data tugas.
    </div>
<?php endif; ?>
