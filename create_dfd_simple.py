import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle
import numpy as np

# Create figure and axis
fig, ax = plt.subplots(1, 1, figsize=(16, 12))
ax.set_xlim(0, 16)
ax.set_ylim(0, 12)
ax.set_aspect('equal')
ax.axis('off')

# Colors
boundary_color = '#E8E8E8'
entity_color = 'white'
process_color = 'white'
datastore_color = 'white'
line_color = 'black'

# Title
ax.text(8, 11.5, 'DFD Level 1 - Sistem E-Learning SMK', 
        fontsize=14, fontweight='bold', ha='center')

# Main system boundary
main_boundary = FancyBboxPatch((0.5, 1), 15, 9.5, 
                               boxstyle="round,pad=0.1", 
                               facecolor=boundary_color, 
                               edgecolor='black', 
                               linewidth=1.5)
ax.add_patch(main_boundary)

# External Entities (left side)
# Admin
admin_rect = FancyBboxPatch((0.8, 8.5), 2, 1, 
                           boxstyle="round,pad=0.05", 
                           facecolor=entity_color, 
                           edgecolor='black', 
                           linewidth=1)
ax.add_patch(admin_rect)
ax.text(1.8, 9, 'Admin', fontsize=10, ha='center', va='center', fontweight='bold')

# Guru
guru_rect = FancyBboxPatch((0.8, 6.5), 2, 1, 
                          boxstyle="round,pad=0.05", 
                          facecolor=entity_color, 
                          edgecolor='black', 
                          linewidth=1)
ax.add_patch(guru_rect)
ax.text(1.8, 7, 'Guru', fontsize=10, ha='center', va='center', fontweight='bold')

# Siswa
siswa_rect = FancyBboxPatch((0.8, 4.5), 2, 1, 
                           boxstyle="round,pad=0.05", 
                           facecolor=entity_color, 
                           edgecolor='black', 
                           linewidth=1)
ax.add_patch(siswa_rect)
ax.text(1.8, 5, 'Siswa', fontsize=10, ha='center', va='center', fontweight='bold')

# Processes (circles)
# 1. Master
master_circle = Circle((5, 8.5), 0.8, facecolor=process_color, edgecolor='black', linewidth=1.5)
ax.add_patch(master_circle)
ax.text(5, 8.7, '1', fontsize=12, ha='center', va='center', fontweight='bold')
ax.text(5, 8.3, 'Master', fontsize=10, ha='center', va='center', fontweight='bold')

# 2. Transaksi
transaksi_circle = Circle((5, 6), 0.8, facecolor=process_color, edgecolor='black', linewidth=1.5)
ax.add_patch(transaksi_circle)
ax.text(5, 6.2, '2', fontsize=12, ha='center', va='center', fontweight='bold')
ax.text(5, 5.8, 'Transaksi', fontsize=10, ha='center', va='center', fontweight='bold')

# 3. Laporan
laporan_circle = Circle((5, 3.5), 0.8, facecolor=process_color, edgecolor='black', linewidth=1.5)
ax.add_patch(laporan_circle)
ax.text(5, 3.7, '3', fontsize=12, ha='center', va='center', fontweight='bold')
ax.text(5, 3.3, 'Laporan', fontsize=10, ha='center', va='center', fontweight='bold')

# Data Stores (right side - vertically aligned)
datastores = [
    ('T1. Data Guru', 10.5, 9.5),
    ('T2. Data Siswa', 10.5, 9),
    ('T3. Data Jurusan', 10.5, 8.5),
    ('T4. Data Kelas', 10.5, 8),
    ('T5. Data Tahun Akademik', 10.5, 7.5),
    ('T6. Data Mata Pelajaran', 10.5, 7),
    ('T7. Data Materi', 10.5, 6.5),
    ('T8. Data Penilaian Siswa', 10.5, 6),
    ('T9. Data Tugas', 10.5, 5.5),
    ('T10. Data Jawaban', 10.5, 5),
    ('T11. Data Nilai', 10.5, 4.5),
    ('T12. Data Kehadiran Siswa', 10.5, 4),
    ('T13. Data Pengumuman', 10.5, 3.5),
    ('T14. Data Penjadwalan Akademik', 10.5, 3)
]

# Draw data stores
for name, x, y in datastores:
    ds_rect = FancyBboxPatch((x, y-0.2), 4.5, 0.4, 
                            boxstyle="round,pad=0.02", 
                            facecolor=datastore_color, 
                            edgecolor='black', 
                            linewidth=1)
    ax.add_patch(ds_rect)
    ax.text(x+0.1, y, name, fontsize=8, ha='left', va='center')

# Data Flow Lines
# From Admin to Master process
ax.annotate('', xy=(4.2, 8.5), xytext=(2.8, 9),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(3.5, 8.8, 'Data Guru\nData Kelas\nData Tahun Akademik\nData Mata Pelajaran', 
        fontsize=7, ha='center', va='center')

# From Admin to all data stores (horizontal lines)
admin_flows = [
    ('Data Guru', 9, 9.5),
    ('Data Siswa', 9, 9),
    ('Data Jurusan', 9, 8.5),
    ('Data Kelas', 9, 8),
    ('Data Tahun Akademik', 9, 7.5),
    ('Data Mata Pelajaran', 9, 7)
]

for label, x, y in admin_flows:
    # Horizontal line from admin area
    ax.plot([2.8, x], [9, y], color=line_color, linewidth=1)
    # Arrow to datastore
    ax.annotate('', xy=(10.5, y), xytext=(x, y),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
    # Label
    ax.text((2.8 + x)/2, y + 0.1, label, fontsize=7, ha='center', va='bottom')

# From Guru to Transaksi
ax.annotate('', xy=(4.2, 6.2), xytext=(2.8, 7),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(3.5, 6.6, 'Data Materi\nData Penilaian Siswa', 
        fontsize=7, ha='center', va='center')

# From Guru to data stores
guru_flows = [
    ('Data Materi', 8.5, 6.5),
    ('Data Tugas', 8.5, 5.5),
    ('Data Jawaban', 8.5, 5),
    ('Data Nilai', 8.5, 4.5)
]

for label, x, y in guru_flows:
    ax.plot([2.8, x], [7, y], color=line_color, linewidth=1)
    ax.annotate('', xy=(10.5, y), xytext=(x, y),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
    ax.text((2.8 + x)/2, y + 0.1, label, fontsize=7, ha='center', va='bottom')

# From Siswa to Transaksi
ax.annotate('', xy=(4.2, 5.8), xytext=(2.8, 5),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(3.5, 5.4, 'Data Diri\nMateri\nPengerjaan', 
        fontsize=7, ha='center', va='center')

# From Siswa to Laporan
ax.annotate('', xy=(4.2, 3.7), xytext=(2.8, 4.8),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(3.5, 4.2, 'Melihat\nPencapaian Siswa', 
        fontsize=7, ha='center', va='center')

# Between processes
# Master to Transaksi
ax.annotate('', xy=(5, 6.8), xytext=(5, 7.7),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(5.5, 7.2, 'Data Pengaturan Akademik', 
        fontsize=7, ha='left', va='center')

# Transaksi to Laporan
ax.annotate('', xy=(5, 4.3), xytext=(5, 5.2),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(5.5, 4.8, 'Data Mata Pelajaran\nData Tahun Akademik\nData Jurusan\nData Kelas\nData Guru', 
        fontsize=7, ha='left', va='center')

# From processes to data stores
# Master to data stores
master_to_ds = [
    (6.5, 6.5),  # to Data Materi
    (7, 6),      # to Data Penilaian Siswa
    (7.5, 5.5),  # to Data Tugas
    (8, 5),      # to Data Jawaban
    (8.5, 4.5)   # to Data Nilai
]

for x, y in master_to_ds:
    ax.plot([5.8, x], [8.5, y], color=line_color, linewidth=1)
    ax.annotate('', xy=(10.5, y), xytext=(x, y),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Laporan to bottom data stores
ax.plot([5.8, 9], [3.5, 3.5], color=line_color, linewidth=1)
ax.annotate('', xy=(10.5, 3.5), xytext=(9, 3.5),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(7.5, 3.7, 'Data Pengumuman', fontsize=7, ha='center', va='bottom')

ax.plot([5.8, 9], [3.3, 3], color=line_color, linewidth=1)
ax.annotate('', xy=(10.5, 3), xytext=(9, 3),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(7.5, 3.2, 'Data Penjadwalan Akademik', fontsize=7, ha='center', va='bottom')

# Return flows (from data stores back to processes/entities)
# From data stores to Siswa (bottom right)
ax.plot([12.5, 14], [4, 2], color=line_color, linewidth=1)
ax.plot([14, 14], [2, 1.5], color=line_color, linewidth=1)
ax.plot([14, 2.8], [1.5, 1.5], color=line_color, linewidth=1)
ax.plot([2.8, 2.8], [1.5, 4.5], color=line_color, linewidth=1)
ax.annotate('', xy=(2.8, 4.5), xytext=(2.8, 1.8),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(8.5, 1.2, 'Melihat\nPencapaian Siswa', fontsize=7, ha='center', va='center')

plt.tight_layout()
plt.savefig('dfd_level1_simple.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('dfd_level1_simple.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.show()

print("DFD Level 1 berhasil dibuat dengan pendekatan yang lebih sederhana!")
print("File disimpan sebagai: dfd_level1_simple.png dan dfd_level1_simple.pdf")