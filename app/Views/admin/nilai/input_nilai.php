<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Input Nilai - <?= $jadwal['mata_pelajaran'] ?></h1>
        <a href="<?= base_url('admin/nilai/mata-pelajaran/' . $jadwal['jurusan_id']) ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Input Nilai Siswa - <?= $jadwal['mata_pelajaran'] ?> (<?= $jadwal['nama_kelas'] ?>)
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/nilai/store') ?>" method="post" id="formNilai">
                <?= csrf_field() ?>
                <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                <input type="hidden" name="jurusan_id" value="<?= $jadwal['jurusan_id'] ?>">

                <!-- Controls untuk menambah kolom -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addTugasColumn()">
                            <i class="fas fa-plus fa-sm"></i> Tambah Tugas
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="addUlanganColumn()">
                            <i class="fas fa-plus fa-sm"></i> Tambah Ulangan
                        </button>
                        <a href="<?= base_url('admin/nilai/export/' . $jadwal['jurusan_id']) ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel fa-sm"></i> Export Excel
                        </a>
                    </div>
                    <div class="d-flex gap-3">
                        <span id="tugasCount" class="badge bg-primary">Tugas: 0</span>
                        <span id="ulanganCount" class="badge bg-warning">Ulangan: 0</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableNilai">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle">NIS</th>
                                <th rowspan="2" class="align-middle">Nama Siswa</th>
                                <th colspan="2" class="text-center" id="tugasHeader">Nilai Tugas</th>
                                <th colspan="2" class="text-center" id="ulanganHeader">Nilai Ulangan</th>
                                <th rowspan="2" class="align-middle">UTS</th>
                                <th rowspan="2" class="align-middle">UAS</th>
                            </tr>
                            <tr id="headerRow">
                                <th class="text-center tugas-header">Tugas 1</th>
                                <th class="text-center tugas-header">Tugas 2</th>
                                <th class="text-center ulangan-header">Ulangan 1</th>
                                <th class="text-center ulangan-header">Ulangan 2</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php foreach ($siswa as $index => $s): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $s['nis'] ?></td>
                                    <td><?= $s['nama_siswa'] ?></td>
                                    
                                    <!-- Kolom Tugas -->
                                    <td class="tugas-cell">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="nilai[<?= $s['id'] ?>][tugas][]" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100"
                                               value="<?= isset($nilai_existing[$s['id']]['tugas'][0]) ? $nilai_existing[$s['id']]['tugas'][0] : '' ?>">
                                    </td>
                                    <td class="tugas-cell">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="nilai[<?= $s['id'] ?>][tugas][]" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100"
                                               value="<?= isset($nilai_existing[$s['id']]['tugas'][1]) ? $nilai_existing[$s['id']]['tugas'][1] : '' ?>">
                                    </td>
                                    
                                    <!-- Kolom Ulangan -->
                                    <td class="ulangan-cell">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="nilai[<?= $s['id'] ?>][ulangan][]" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100"
                                               value="<?= isset($nilai_existing[$s['id']]['ulangan'][0]) ? $nilai_existing[$s['id']]['ulangan'][0] : '' ?>">
                                    </td>
                                    <td class="ulangan-cell">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="nilai[<?= $s['id'] ?>][ulangan][]" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100"
                                               value="<?= isset($nilai_existing[$s['id']]['ulangan'][1]) ? $nilai_existing[$s['id']]['ulangan'][1] : '' ?>">
                                    </td>
                                    
                                    <!-- UTS -->
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                               name="nilai[<?= $s['id'] ?>][uts]" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100"
                                               value="<?= isset($nilai_existing[$s['id']]['uts']) ? $nilai_existing[$s['id']]['uts'] : '' ?>">
                                    </td>
                                    
                                    <!-- UAS -->
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                               name="nilai[<?= $s['id'] ?>][uas]" 
                                               min="0" max="100" step="0.01" 
                                               placeholder="0-100"
                                               value="<?= isset($nilai_existing[$s['id']]['uas']) ? $nilai_existing[$s['id']]['uas'] : '' ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let tugasCount = 2;
let ulanganCount = 2;

function addTugasColumn() {
    tugasCount++;
    updateTable();
}

function addUlanganColumn() {
    ulanganCount++;
    updateTable();
}

function updateTable() {
    // Update header
    updateHeaders();
    
    // Update body
    updateBody();
    
    // Update counters
    updateCounters();
}

function updateHeaders() {
    const headerRow = document.getElementById('headerRow');
    
    // Clear existing headers
    headerRow.innerHTML = '';
    
    // Add tugas headers
    for (let i = 0; i < tugasCount; i++) {
        const th = document.createElement('th');
        th.className = 'text-center tugas-header';
        th.textContent = `Tugas ${i + 1}`;
        headerRow.appendChild(th);
    }
    
    // Add ulangan headers
    for (let i = 0; i < ulanganCount; i++) {
        const th = document.createElement('th');
        th.className = 'text-center ulangan-header';
        th.textContent = `Ulangan ${i + 1}`;
        headerRow.appendChild(th);
    }
    
    // Update main headers
    document.getElementById('tugasHeader').setAttribute('colspan', tugasCount);
    document.getElementById('ulanganHeader').setAttribute('colspan', ulanganCount);
}

function updateBody() {
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr');
    
    rows.forEach(row => {
        // Get existing data
        const no = row.cells[0].textContent;
        const nis = row.cells[1].textContent;
        const nama = row.cells[2].textContent;
        const utsInput = row.querySelector('input[name*="[uts]"]');
        const uasInput = row.querySelector('input[name*="[uas]"]');
        
        // Get existing nilai data
        const existingValue = <?= json_encode($nilai_existing) ?>;
        const siswaId = utsInput.name.match(/\[(\d+)\]/)[1];
        const existingTugas = existingValue[siswaId]?.tugas || [];
        const existingUlangan = existingValue[siswaId]?.ulangan || [];
        
        // Clear row content
        row.innerHTML = '';
        
        // Add basic cells
        row.innerHTML = `
            <td>${no}</td>
            <td>${nis}</td>
            <td>${nama}</td>
        `;
        
        // Add tugas cells
        for (let i = 0; i < tugasCount; i++) {
            const td = document.createElement('td');
            td.className = 'tugas-cell';
            td.innerHTML = `
                <input type="number" class="form-control form-control-sm" 
                       name="nilai[${siswaId}][tugas][]" 
                       min="0" max="100" step="0.01" 
                       placeholder="0-100"
                       value="${existingTugas[i] || ''}">
            `;
            row.appendChild(td);
        }
        
        // Add ulangan cells
        for (let i = 0; i < ulanganCount; i++) {
            const td = document.createElement('td');
            td.className = 'ulangan-cell';
            td.innerHTML = `
                <input type="number" class="form-control form-control-sm" 
                       name="nilai[${siswaId}][ulangan][]" 
                       min="0" max="100" step="0.01" 
                       placeholder="0-100"
                       value="${existingUlangan[i] || ''}">
            `;
            row.appendChild(td);
        }
        
        // Add UTS cell
        const utsTd = document.createElement('td');
        utsTd.innerHTML = `
            <input type="number" class="form-control form-control-sm" 
                   name="nilai[${siswaId}][uts]" 
                   min="0" max="100" step="0.01" 
                   placeholder="0-100"
                   value="${existingValue[siswaId]?.uts || ''}">
        `;
        row.appendChild(utsTd);
        
        // Add UAS cell
        const uasTd = document.createElement('td');
        uasTd.innerHTML = `
            <input type="number" class="form-control form-control-sm" 
                   name="nilai[${siswaId}][uas]" 
                   min="0" max="100" step="0.01" 
                   placeholder="0-100"
                   value="${existingValue[siswaId]?.uas || ''}">
        `;
        row.appendChild(uasTd);
    });
}

function updateCounters() {
    document.getElementById('tugasCount').textContent = `Tugas: ${tugasCount}`;
    document.getElementById('ulanganCount').textContent = `Ulangan: ${ulanganCount}`;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Hitung jumlah tugas dan ulangan yang sudah ada
    const existingTugas = <?= json_encode(array_values(array_filter(array_map(function($item) { 
        return count($item['tugas']); 
    }, $nilai_existing)))) ?>;
    const existingUlangan = <?= json_encode(array_values(array_filter(array_map(function($item) { 
        return count($item['ulangan']); 
    }, $nilai_existing)))) ?>;
    
    // Ambil jumlah maksimal
    const maxTugas = existingTugas.length > 0 ? Math.max(...existingTugas) : 2;
    const maxUlangan = existingUlangan.length > 0 ? Math.max(...existingUlangan) : 2;
    
    // Set minimal 2 kolom untuk masing-masing
    tugasCount = Math.max(maxTugas, 2);
    ulanganCount = Math.max(maxUlangan, 2);
    
    // Update tampilan
    updateTable();
});
</script>
<?= $this->endSection() ?>