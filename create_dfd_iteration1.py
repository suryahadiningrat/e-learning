import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch
import numpy as np

# Create figure and axis
fig, ax = plt.subplots(1, 1, figsize=(16, 12))
ax.set_xlim(0, 16)
ax.set_ylim(0, 12)
ax.set_aspect('equal')
ax.axis('off')

# Colors
line_color = 'black'
text_color = 'black'
bg_color = 'white'

# Main boundary rectangle
boundary = patches.Rectangle((1, 1), 14, 10, linewidth=2, edgecolor=line_color, facecolor='none')
ax.add_patch(boundary)

# External Entities (outside boundary)
# Admin - top left
admin_rect = patches.Rectangle((0.2, 9.5), 1.6, 1, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(admin_rect)
ax.text(1, 10, 'Admin', fontsize=10, ha='center', va='center', weight='bold')

# Guru - middle left
guru_rect = patches.Rectangle((0.2, 7), 1.6, 1, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(guru_rect)
ax.text(1, 7.5, 'Guru', fontsize=10, ha='center', va='center', weight='bold')

# Siswa - bottom left
siswa_rect = patches.Rectangle((0.2, 4.5), 1.6, 1, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(siswa_rect)
ax.text(1, 5, 'Siswa', fontsize=10, ha='center', va='center', weight='bold')

# Processes (circles inside boundary)
# Master - top center
master_circle = patches.Circle((5, 9), 0.8, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(master_circle)
ax.text(5, 9.2, '1', fontsize=10, ha='center', va='center', weight='bold')
ax.text(5, 8.8, 'Master', fontsize=10, ha='center', va='center', weight='bold')

# Transaksi - middle center
transaksi_circle = patches.Circle((5, 6), 0.8, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(transaksi_circle)
ax.text(5, 6.2, '2', fontsize=10, ha='center', va='center', weight='bold')
ax.text(5, 5.8, 'Transaksi', fontsize=10, ha='center', va='center', weight='bold')

# Laporan - bottom center
laporan_circle = patches.Circle((5, 3), 0.8, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(laporan_circle)
ax.text(5, 3.2, '3', fontsize=10, ha='center', va='center', weight='bold')
ax.text(5, 2.8, 'Laporan', fontsize=10, ha='center', va='center', weight='bold')

# Data Stores (right side, open rectangles)
data_stores = [
    ('Data Guru', 9.5),
    ('Data Siswa', 8.8),
    ('Data Jawaban', 8.1),
    ('Data Kelas', 7.4),
    ('Data Tahun Akademik', 6.7),
    ('Data Mata Pelajaran', 6.0)
]

for i, (name, y_pos) in enumerate(data_stores):
    # Open rectangle (no left line)
    ax.plot([12, 15], [y_pos, y_pos], color=line_color, linewidth=1.5)  # top
    ax.plot([12, 15], [y_pos-0.5, y_pos-0.5], color=line_color, linewidth=1.5)  # bottom
    ax.plot([15, 15], [y_pos, y_pos-0.5], color=line_color, linewidth=1.5)  # right
    
    ax.text(13.5, y_pos-0.25, name, fontsize=9, ha='center', va='center')

# Additional data stores at bottom right
bottom_stores = [
    ('Data Pengumuman', 4.5),
    ('Data Penjadwalan Siswa', 3.8),
    ('Data Penjadwalan Akademik', 3.1)
]

for name, y_pos in bottom_stores:
    ax.plot([12, 15], [y_pos, y_pos], color=line_color, linewidth=1.5)  # top
    ax.plot([12, 15], [y_pos-0.5, y_pos-0.5], color=line_color, linewidth=1.5)  # bottom
    ax.plot([15, 15], [y_pos, y_pos-0.5], color=line_color, linewidth=1.5)  # right
    
    ax.text(13.5, y_pos-0.25, name, fontsize=9, ha='center', va='center')

# Data flows - Admin to Master
ax.annotate('', xy=(4.2, 9), xytext=(1.8, 10),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.8, 9.7, 'Data Guru\nData Siswa\nData Kelas\nData Tahun Akademik\nData Mata Pelajaran', 
        fontsize=8, ha='left', va='center')

# Data flows - Guru to Transaksi
ax.annotate('', xy=(4.2, 6), xytext=(1.8, 7.5),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.5, 6.8, 'Data Materi\nData Penilaian Siswa', 
        fontsize=8, ha='center', va='center')

# Data flows - Siswa to Transaksi
ax.annotate('', xy=(4.2, 6), xytext=(1.8, 5),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.5, 5.5, 'Data Diri', fontsize=8, ha='center', va='center')

# Data flows - Siswa to Laporan
ax.annotate('', xy=(4.2, 3), xytext=(1.8, 5),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.5, 4, 'Melihat\nPencapaian Siswa', fontsize=8, ha='center', va='center')

# Data flows from processes to data stores
# Master to data stores
for i, (name, y_pos) in enumerate(data_stores):
    ax.annotate('', xy=(12, y_pos-0.25), xytext=(5.8, 9),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Transaksi to data stores (middle ones)
middle_stores_y = [8.1, 7.4, 6.7, 6.0]  # Data Jawaban, Kelas, Tahun Akademik, Mata Pelajaran
for y_pos in middle_stores_y:
    ax.annotate('', xy=(12, y_pos-0.25), xytext=(5.8, 6),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Laporan to bottom data stores
for name, y_pos in bottom_stores:
    ax.annotate('', xy=(12, y_pos-0.25), xytext=(5.8, 3),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Return flows from data stores to processes
# Some return flows
ax.annotate('', xy=(4.2, 9), xytext=(12, 9.25),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(8, 9.5, 'Data Pengumuman Akademik', fontsize=8, ha='center', va='center')

ax.annotate('', xy=(4.2, 6), xytext=(12, 6.25),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

ax.annotate('', xy=(4.2, 3), xytext=(12, 4.25),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(8, 3.8, 'Melihat\nPencapaian Siswa', fontsize=8, ha='center', va='center')

# Additional boundary lines inside
inner_boundary1 = patches.Rectangle((1.5, 7.5), 11, 3, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(inner_boundary1)

inner_boundary2 = patches.Rectangle((1.5, 4.5), 11, 2.5, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(inner_boundary2)

inner_boundary3 = patches.Rectangle((1.5, 1.5), 11, 2.5, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(inner_boundary3)

plt.title('DFD Level 1 - Sistem E-Learning', fontsize=14, weight='bold', pad=20)
plt.tight_layout()

# Save the figure
plt.savefig('dfd_level1_iteration1.png', dpi=300, bbox_inches='tight', facecolor='white')
plt.savefig('dfd_level1_iteration1.pdf', bbox_inches='tight', facecolor='white')

print("DFD Level 1 Iteration 1 berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_iteration1.png dan dfd_level1_iteration1.pdf")

plt.show()