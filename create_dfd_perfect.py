import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Rectangle
import numpy as np

# Create figure with exact proportions
fig, ax = plt.subplots(1, 1, figsize=(20, 14))
ax.set_xlim(0, 20)
ax.set_ylim(0, 14)
ax.set_aspect('equal')
ax.axis('off')

# Colors matching reference
boundary_color = '#F5F5F5'
entity_color = 'white'
process_color = 'white'
datastore_color = 'white'
line_color = 'black'

# Title
ax.text(10, 13.2, 'DFD Level 1 - Sistem E-Learning SMK', 
        fontsize=16, fontweight='bold', ha='center')

# Main system boundary (large rectangle)
main_boundary = Rectangle((1, 1.5), 17.5, 11, 
                         facecolor=boundary_color, 
                         edgecolor='black', 
                         linewidth=2)
ax.add_patch(main_boundary)

# External Entities (left side, outside boundary)
# Admin
admin_rect = Rectangle((0.2, 10), 2.5, 1.2, 
                      facecolor=entity_color, 
                      edgecolor='black', 
                      linewidth=1.5)
ax.add_patch(admin_rect)
ax.text(1.45, 10.6, 'Admin', fontsize=12, ha='center', va='center', fontweight='bold')

# Guru  
guru_rect = Rectangle((0.2, 7.5), 2.5, 1.2, 
                     facecolor=entity_color, 
                     edgecolor='black', 
                     linewidth=1.5)
ax.add_patch(guru_rect)
ax.text(1.45, 8.1, 'Guru', fontsize=12, ha='center', va='center', fontweight='bold')

# Siswa
siswa_rect = Rectangle((0.2, 5), 2.5, 1.2, 
                      facecolor=entity_color, 
                      edgecolor='black', 
                      linewidth=1.5)
ax.add_patch(siswa_rect)
ax.text(1.45, 5.6, 'Siswa', fontsize=12, ha='center', va='center', fontweight='bold')

# Processes (circles inside boundary)
# 1. Master
master_circle = Circle((5.5, 10), 1, facecolor=process_color, edgecolor='black', linewidth=2)
ax.add_patch(master_circle)
ax.text(5.5, 10.2, '1', fontsize=14, ha='center', va='center', fontweight='bold')
ax.text(5.5, 9.8, 'Master', fontsize=11, ha='center', va='center', fontweight='bold')

# 2. Transaksi
transaksi_circle = Circle((5.5, 7), 1, facecolor=process_color, edgecolor='black', linewidth=2)
ax.add_patch(transaksi_circle)
ax.text(5.5, 7.2, '2', fontsize=14, ha='center', va='center', fontweight='bold')
ax.text(5.5, 6.8, 'Transaksi', fontsize=11, ha='center', va='center', fontweight='bold')

# 3. Laporan
laporan_circle = Circle((5.5, 4), 1, facecolor=process_color, edgecolor='black', linewidth=2)
ax.add_patch(laporan_circle)
ax.text(5.5, 4.2, '3', fontsize=14, ha='center', va='center', fontweight='bold')
ax.text(5.5, 3.8, 'Laporan', fontsize=11, ha='center', va='center', fontweight='bold')

# Data Stores (right side, inside boundary, vertically aligned)
datastores = [
    ('T1. Data Guru', 12, 11.5),
    ('T2. Data Siswa', 12, 11),
    ('T3. Data Jurusan', 12, 10.5),
    ('T4. Data Kelas', 12, 10),
    ('T5. Data Tahun Akademik', 12, 9.5),
    ('T6. Data Mata Pelajaran', 12, 9),
    ('T7. Data Materi', 12, 8.5),
    ('T8. Data Penilaian Siswa', 12, 8),
    ('T9. Data Tugas', 12, 7.5),
    ('T10. Data Jawaban', 12, 7),
    ('T11. Data Nilai', 12, 6.5),
    ('T12. Data Kehadiran Siswa', 12, 6),
    ('T13. Data Pengumuman', 12, 5.5),
    ('T14. Data Penjadwalan Akademik', 12, 5)
]

# Draw data stores as open rectangles
for name, x, y in datastores:
    # Left line
    ax.plot([x, x], [y-0.15, y+0.15], color='black', linewidth=1.5)
    # Top line
    ax.plot([x, x+5.5], [y+0.15, y+0.15], color='black', linewidth=1.5)
    # Bottom line  
    ax.plot([x, x+5.5], [y-0.15, y-0.15], color='black', linewidth=1.5)
    # Text
    ax.text(x+0.2, y, name, fontsize=10, ha='left', va='center')

# Data Flow Lines - Positioned below symbols for visibility

# 1. From Admin - Multiple horizontal lines to data stores
admin_y = 10.2  # Lower position to avoid text overlap
admin_x_end = 2.7

# Admin flows (horizontal lines from admin to data stores)
admin_targets = [
    ('Data Guru', 11.5),
    ('Data Siswa', 11),
    ('Data Jurusan', 10.5), 
    ('Data Kelas', 10),
    ('Data Tahun Akademik', 9.5),
    ('Data Mata Pelajaran', 9)
]

for label, target_y in admin_targets:
    # Horizontal line from admin (lower position)
    ax.plot([admin_x_end, 12], [admin_y, target_y], color=line_color, linewidth=1)
    # Arrow at end
    ax.annotate('', xy=(12, target_y), xytext=(11.8, target_y),
                arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
    # Label on line (positioned below the line)
    mid_x = (admin_x_end + 12) / 2
    mid_y = (admin_y + target_y) / 2
    ax.text(mid_x, mid_y - 0.15, label, fontsize=8, ha='center', va='top', 
            bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# 2. From Admin to Master process (line below the circle)
ax.plot([admin_x_end, 4.5], [admin_y, 9.2], color=line_color, linewidth=1)
ax.annotate('', xy=(4.5, 9.2), xytext=(4.3, 9.2),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(3.6, 9.6, 'Data Guru\nData Kelas\nData Tahun Akademik\nData Mata Pelajaran', 
        fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# 3. From Guru - Lines to Transaksi and data stores
guru_y = 7.7  # Lower position to avoid text overlap
guru_x_end = 2.7

# Guru to Transaksi (line below the circle)
ax.plot([guru_x_end, 4.5], [guru_y, 6.2], color=line_color, linewidth=1)
ax.annotate('', xy=(4.5, 6.2), xytext=(4.3, 6.2),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(3.6, 6.9, 'Data Materi\nData Penilaian Siswa', 
        fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# Guru flows to data stores
guru_targets = [
    ('Data Materi', 8.5),
    ('Data Tugas', 7.5),
    ('Data Jawaban', 7),
    ('Data Nilai', 6.5)
]

for label, target_y in guru_targets:
    ax.plot([guru_x_end, 12], [guru_y, target_y], color=line_color, linewidth=1)
    ax.annotate('', xy=(12, target_y), xytext=(11.8, target_y),
                arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
    mid_x = (guru_x_end + 12) / 2
    mid_y = (guru_y + target_y) / 2
    ax.text(mid_x, mid_y - 0.15, label, fontsize=8, ha='center', va='top',
            bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# 4. From Siswa - Lines to Transaksi and Laporan
siswa_y = 5.2  # Lower position to avoid text overlap
siswa_x_end = 2.7

# Siswa to Transaksi (line below the circle)
ax.plot([siswa_x_end, 4.5], [siswa_y, 6.0], color=line_color, linewidth=1)
ax.annotate('', xy=(4.5, 6.0), xytext=(4.3, 6.0),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(3.6, 5.6, 'Data Diri\nMateri\nPengerjaan', 
        fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# Siswa to Laporan (line below the circle)
ax.plot([siswa_x_end, 4.5], [siswa_y, 3.2], color=line_color, linewidth=1)
ax.annotate('', xy=(4.5, 3.2), xytext=(4.3, 3.2),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(3.6, 4.2, 'Melihat\nPencapaian Siswa', 
        fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# 5. Between processes
# Master to Transaksi
ax.plot([5.5, 5.5], [9, 8], color=line_color, linewidth=1.5)
ax.annotate('', xy=(5.5, 8), xytext=(5.5, 8.2),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(6.2, 8.5, 'Data Pengaturan Akademik', 
        fontsize=8, ha='left', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# Transaksi to Laporan
ax.plot([5.5, 5.5], [6, 5], color=line_color, linewidth=1.5)
ax.annotate('', xy=(5.5, 5), xytext=(5.5, 5.2),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(6.2, 5.5, 'Data Mata Pelajaran\nData Tahun Akademik\nData Jurusan\nData Kelas\nData Guru', 
        fontsize=8, ha='left', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# 6. From processes to data stores
# Master to multiple data stores (lines positioned below the circle)
master_targets = [
    (8.5, 'Data Materi'),
    (8, 'Data Penilaian Siswa'),
    (7.5, 'Data Tugas'),
    (7, 'Data Jawaban'),
    (6.5, 'Data Nilai')
]

for target_y, label in master_targets:
    ax.plot([6.5, 12], [9.2, target_y], color=line_color, linewidth=1)  # Start from below circle
    ax.annotate('', xy=(12, target_y), xytext=(11.8, target_y),
                arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))

# Laporan to bottom data stores (lines positioned below the circle)
ax.plot([6.5, 12], [3.2, 5.5], color=line_color, linewidth=1)  # Start from below circle
ax.annotate('', xy=(12, 5.5), xytext=(11.8, 5.5),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(9.2, 4.3, 'Data Pengumuman', fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

ax.plot([6.5, 12], [3.0, 5], color=line_color, linewidth=1)  # Start from below circle
ax.annotate('', xy=(12, 5), xytext=(11.8, 5),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(9.2, 4.0, 'Data Penjadwalan Akademik', fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

# 7. Return flow from data stores to Siswa (complex path)
# From bottom right, going around the boundary
ax.plot([17.5, 19], [3, 3], color=line_color, linewidth=1.5)  # Right edge
ax.plot([19, 19], [3, 2], color=line_color, linewidth=1.5)    # Down
ax.plot([19, 1], [2, 2], color=line_color, linewidth=1.5)     # Bottom edge
ax.plot([1, 1], [2, 5.6], color=line_color, linewidth=1.5)   # Up to siswa
ax.annotate('', xy=(1, 5.6), xytext=(1.2, 5.6),
            arrowprops=dict(arrowstyle='->', lw=1.5, color=line_color))
ax.text(10, 2.3, 'Melihat Pencapaian Siswa', fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', edgecolor='none', alpha=0.8))

plt.tight_layout()
plt.savefig('dfd_level1_perfect.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('dfd_level1_perfect.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.show()

print("DFD Level 1 PERFECT berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_perfect.png dan dfd_level1_perfect.pdf")