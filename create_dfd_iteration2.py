import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch
import numpy as np

# Create figure and axis
fig, ax = plt.subplots(1, 1, figsize=(14, 10))
ax.set_xlim(0, 14)
ax.set_ylim(0, 10)
ax.set_aspect('equal')
ax.axis('off')

# Colors
line_color = 'black'
text_color = 'black'
bg_color = 'white'

# Main system boundary
boundary = patches.Rectangle((2, 1.5), 10, 7.5, linewidth=2, edgecolor=line_color, facecolor='none')
ax.add_patch(boundary)

# External Entities (outside boundary, left side)
# Admin
admin_rect = patches.Rectangle((0.3, 8), 1.4, 0.8, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(admin_rect)
ax.text(1, 8.4, 'Admin', fontsize=10, ha='center', va='center', weight='bold')

# Guru  
guru_rect = patches.Rectangle((0.3, 6), 1.4, 0.8, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(guru_rect)
ax.text(1, 6.4, 'Guru', fontsize=10, ha='center', va='center', weight='bold')

# Siswa
siswa_rect = patches.Rectangle((0.3, 4), 1.4, 0.8, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(siswa_rect)
ax.text(1, 4.4, 'Siswa', fontsize=10, ha='center', va='center', weight='bold')

# Processes (circles inside boundary)
# Master
master_circle = patches.Circle((4.5, 7.5), 0.6, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(master_circle)
ax.text(4.5, 7.7, '1', fontsize=9, ha='center', va='center', weight='bold')
ax.text(4.5, 7.3, 'Master', fontsize=9, ha='center', va='center', weight='bold')

# Transaksi
transaksi_circle = patches.Circle((4.5, 5.5), 0.6, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(transaksi_circle)
ax.text(4.5, 5.7, '2', fontsize=9, ha='center', va='center', weight='bold')
ax.text(4.5, 5.3, 'Transaksi', fontsize=9, ha='center', va='center', weight='bold')

# Laporan
laporan_circle = patches.Circle((4.5, 3.5), 0.6, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(laporan_circle)
ax.text(4.5, 3.7, '3', fontsize=9, ha='center', va='center', weight='bold')
ax.text(4.5, 3.3, 'Laporan', fontsize=9, ha='center', va='center', weight='bold')

# Data Stores (right side, open rectangles)
data_stores_right = [
    ('Data Guru', 8.5),
    ('Data Siswa', 8.0),
    ('Data Jawaban', 7.5),
    ('Data Kelas', 7.0),
    ('Data Tahun\nAkademik', 6.5),
    ('Data Mata\nPelajaran', 6.0)
]

for name, y_pos in data_stores_right:
    # Open rectangle (no left line)
    ax.plot([9, 11.5], [y_pos+0.2, y_pos+0.2], color=line_color, linewidth=1.5)  # top
    ax.plot([9, 11.5], [y_pos-0.2, y_pos-0.2], color=line_color, linewidth=1.5)  # bottom
    ax.plot([11.5, 11.5], [y_pos+0.2, y_pos-0.2], color=line_color, linewidth=1.5)  # right
    
    ax.text(10.25, y_pos, name, fontsize=8, ha='center', va='center')

# Bottom right data stores
bottom_stores = [
    ('Data\nPengumuman', 4.5),
    ('Data\nPenjadwalan Siswa', 3.8),
    ('Data Penjadwalan Akademik', 3.1)
]

for name, y_pos in bottom_stores:
    ax.plot([9, 11.5], [y_pos+0.2, y_pos+0.2], color=line_color, linewidth=1.5)  # top
    ax.plot([9, 11.5], [y_pos-0.2, y_pos-0.2], color=line_color, linewidth=1.5)  # bottom
    ax.plot([11.5, 11.5], [y_pos+0.2, y_pos-0.2], color=line_color, linewidth=1.5)  # right
    
    ax.text(10.25, y_pos, name, fontsize=8, ha='center', va='center')

# Data flows with labels
# Admin to Master
ax.plot([1.7, 3.9], [8.4, 7.5], color=line_color, linewidth=1.2)
ax.annotate('', xy=(3.9, 7.5), xytext=(3.7, 7.6),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))

# Multiple labels for Admin flows
admin_labels = ['Data Guru', 'Data Siswa', 'Data Kelas', 'Data Tahun Akademik', 'Data Mata Pelajaran']
for i, label in enumerate(admin_labels):
    ax.text(2.8, 8.2 - i*0.15, label, fontsize=7, ha='left', va='center')

# Guru to Transaksi  
ax.plot([1.7, 3.9], [6.4, 5.5], color=line_color, linewidth=1.2)
ax.annotate('', xy=(3.9, 5.5), xytext=(3.7, 5.6),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.5, 6.0, 'Data Materi\nData Penilaian Siswa', fontsize=8, ha='center', va='center')

# Siswa to Transaksi
ax.plot([1.7, 3.9], [4.4, 5.5], color=line_color, linewidth=1.2)
ax.annotate('', xy=(3.9, 5.5), xytext=(3.7, 5.4),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.5, 4.9, 'Data Diri', fontsize=8, ha='center', va='center')

# Siswa to Laporan
ax.plot([1.7, 3.9], [4.4, 3.5], color=line_color, linewidth=1.2)
ax.annotate('', xy=(3.9, 3.5), xytext=(3.7, 3.6),
            arrowprops=dict(arrowstyle='->', lw=1.2, color=line_color))
ax.text(2.5, 3.9, 'Melihat\nPencapaian Siswa', fontsize=8, ha='center', va='center')

# Flows from processes to data stores
# Master to all top data stores
for name, y_pos in data_stores_right:
    ax.plot([5.1, 9], [7.5, y_pos], color=line_color, linewidth=1)
    ax.annotate('', xy=(9, y_pos), xytext=(8.8, y_pos),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Transaksi to middle data stores
middle_y_positions = [7.5, 7.0, 6.5, 6.0]  # Jawaban, Kelas, Tahun, Mata Pelajaran
for y_pos in middle_y_positions:
    ax.plot([5.1, 9], [5.5, y_pos], color=line_color, linewidth=1)
    ax.annotate('', xy=(9, y_pos), xytext=(8.8, y_pos),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Laporan to bottom data stores
for name, y_pos in bottom_stores:
    ax.plot([5.1, 9], [3.5, y_pos], color=line_color, linewidth=1)
    ax.annotate('', xy=(9, y_pos), xytext=(8.8, y_pos),
                arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Return flows from data stores
# Data Pengumuman Akademik back to Master
ax.plot([9, 3.9], [8.5, 7.5], color=line_color, linewidth=1)
ax.annotate('', xy=(3.9, 7.5), xytext=(4.1, 7.4),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(6.5, 8.1, 'Data Pengumuman Akademik', fontsize=8, ha='center', va='center')

# Some return flows from data stores to Transaksi
ax.plot([9, 5.1], [6.5, 5.5], color=line_color, linewidth=1)
ax.annotate('', xy=(5.1, 5.5), xytext=(5.3, 5.4),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Return flow to Laporan
ax.plot([9, 5.1], [4.5, 3.5], color=line_color, linewidth=1)
ax.annotate('', xy=(5.1, 3.5), xytext=(5.3, 3.4),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(7, 4.0, 'Melihat\nPencapaian Siswa', fontsize=8, ha='center', va='center')

# Internal boundaries
# Top section boundary
top_boundary = patches.Rectangle((2.5, 6.8), 9, 2, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(top_boundary)

# Middle section boundary  
mid_boundary = patches.Rectangle((2.5, 4.8), 9, 1.8, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(mid_boundary)

# Bottom section boundary
bot_boundary = patches.Rectangle((2.5, 2.8), 9, 1.8, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(bot_boundary)

plt.tight_layout()

# Save the figure
plt.savefig('dfd_level1_iteration2.png', dpi=300, bbox_inches='tight', facecolor='white')
plt.savefig('dfd_level1_iteration2.pdf', bbox_inches='tight', facecolor='white')

print("DFD Level 1 Iteration 2 berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_iteration2.png dan dfd_level1_iteration2.pdf")

plt.show()