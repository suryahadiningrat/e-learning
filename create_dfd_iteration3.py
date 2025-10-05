import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np

# Create figure and axis with exact proportions
fig, ax = plt.subplots(1, 1, figsize=(12, 9))
ax.set_xlim(0, 12)
ax.set_ylim(0, 9)
ax.set_aspect('equal')
ax.axis('off')

# Colors
line_color = 'black'
text_color = 'black'
bg_color = 'white'

# Main system boundary - large rectangle
main_boundary = patches.Rectangle((1.5, 1), 9.5, 7.5, linewidth=2, edgecolor=line_color, facecolor='none')
ax.add_patch(main_boundary)

# External Entities (outside boundary, left side)
# Admin - top left
admin_rect = patches.Rectangle((0.2, 7.5), 1.2, 0.7, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(admin_rect)
ax.text(0.8, 7.85, 'Admin', fontsize=9, ha='center', va='center', weight='bold')

# Guru - middle left
guru_rect = patches.Rectangle((0.2, 5.8), 1.2, 0.7, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(guru_rect)
ax.text(0.8, 6.15, 'Guru', fontsize=9, ha='center', va='center', weight='bold')

# Siswa - bottom left
siswa_rect = patches.Rectangle((0.2, 4.1), 1.2, 0.7, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(siswa_rect)
ax.text(0.8, 4.45, 'Siswa', fontsize=9, ha='center', va='center', weight='bold')

# Processes (circles inside boundary)
# Master - top center
master_circle = patches.Circle((4, 7), 0.5, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(master_circle)
ax.text(4, 7.15, '1', fontsize=8, ha='center', va='center', weight='bold')
ax.text(4, 6.85, 'Master', fontsize=8, ha='center', va='center', weight='bold')

# Transaksi - middle center
transaksi_circle = patches.Circle((4, 5), 0.5, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(transaksi_circle)
ax.text(4, 5.15, '2', fontsize=8, ha='center', va='center', weight='bold')
ax.text(4, 4.85, 'Transaksi', fontsize=8, ha='center', va='center', weight='bold')

# Laporan - bottom center
laporan_circle = patches.Circle((4, 3), 0.5, linewidth=1.5, edgecolor=line_color, facecolor=bg_color)
ax.add_patch(laporan_circle)
ax.text(4, 3.15, '3', fontsize=8, ha='center', va='center', weight='bold')
ax.text(4, 2.85, 'Laporan', fontsize=8, ha='center', va='center', weight='bold')

# Data Stores (right side, open rectangles)
data_stores_main = [
    ('Data Guru', 7.8),
    ('Data Siswa', 7.4),
    ('Data Jawaban', 7.0),
    ('Data Kelas', 6.6),
    ('Data Tahun Akademik', 6.2),
    ('Data Mata Pelajaran', 5.8)
]

for name, y_pos in data_stores_main:
    # Open rectangle (no left line) - exactly like reference
    ax.plot([7.5, 10.5], [y_pos+0.15, y_pos+0.15], color=line_color, linewidth=1.2)  # top
    ax.plot([7.5, 10.5], [y_pos-0.15, y_pos-0.15], color=line_color, linewidth=1.2)  # bottom
    ax.plot([10.5, 10.5], [y_pos+0.15, y_pos-0.15], color=line_color, linewidth=1.2)  # right
    
    ax.text(9, y_pos, name, fontsize=7, ha='center', va='center')

# Bottom data stores
bottom_stores = [
    ('Data Pengumuman', 4.2),
    ('Data Penjadwalan Siswa', 3.8),
    ('Data Penjadwalan Akademik', 3.4)
]

for name, y_pos in bottom_stores:
    ax.plot([7.5, 10.5], [y_pos+0.15, y_pos+0.15], color=line_color, linewidth=1.2)  # top
    ax.plot([7.5, 10.5], [y_pos-0.15, y_pos-0.15], color=line_color, linewidth=1.2)  # bottom
    ax.plot([10.5, 10.5], [y_pos+0.15, y_pos-0.15], color=line_color, linewidth=1.2)  # right
    
    ax.text(9, y_pos, name, fontsize=7, ha='center', va='center')

# Data flows - exactly positioned like reference
# Admin to Master with multiple labels
ax.plot([1.4, 3.5], [7.85, 7], color=line_color, linewidth=1)
ax.annotate('', xy=(3.5, 7), xytext=(3.3, 7.1),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))

# Admin flow labels - positioned above the line
admin_labels = ['Data Guru', 'Data Siswa', 'Data Kelas', 'Data Tahun Akademik', 'Data Mata Pelajaran']
for i, label in enumerate(admin_labels):
    ax.text(2.2, 8.2 - i*0.1, label, fontsize=6, ha='left', va='center')

# Guru to Transaksi
ax.plot([1.4, 3.5], [6.15, 5], color=line_color, linewidth=1)
ax.annotate('', xy=(3.5, 5), xytext=(3.3, 5.1),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(2.2, 5.6, 'Data Materi\nData Penilaian Siswa', fontsize=7, ha='center', va='center')

# Siswa to Transaksi
ax.plot([1.4, 3.5], [4.45, 5], color=line_color, linewidth=1)
ax.annotate('', xy=(3.5, 5), xytext=(3.3, 4.9),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(2.2, 4.7, 'Data Diri', fontsize=7, ha='center', va='center')

# Siswa to Laporan
ax.plot([1.4, 3.5], [4.45, 3], color=line_color, linewidth=1)
ax.annotate('', xy=(3.5, 3), xytext=(3.3, 3.1),
            arrowprops=dict(arrowstyle='->', lw=1, color=line_color))
ax.text(2.2, 3.7, 'Melihat\nPencapaian Siswa', fontsize=7, ha='center', va='center')

# Flows from processes to data stores
# Master to all main data stores
for name, y_pos in data_stores_main:
    ax.plot([4.5, 7.5], [7, y_pos], color=line_color, linewidth=0.8)
    ax.annotate('', xy=(7.5, y_pos), xytext=(7.3, y_pos),
                arrowprops=dict(arrowstyle='->', lw=0.8, color=line_color))

# Transaksi to middle data stores (Data Jawaban, Kelas, Tahun, Mata Pelajaran)
transaksi_targets = [7.0, 6.6, 6.2, 5.8]
for y_pos in transaksi_targets:
    ax.plot([4.5, 7.5], [5, y_pos], color=line_color, linewidth=0.8)
    ax.annotate('', xy=(7.5, y_pos), xytext=(7.3, y_pos),
                arrowprops=dict(arrowstyle='->', lw=0.8, color=line_color))

# Laporan to bottom data stores
for name, y_pos in bottom_stores:
    ax.plot([4.5, 7.5], [3, y_pos], color=line_color, linewidth=0.8)
    ax.annotate('', xy=(7.5, y_pos), xytext=(7.3, y_pos),
                arrowprops=dict(arrowstyle='->', lw=0.8, color=line_color))

# Return flows from data stores to processes
# Data Pengumuman Akademik back to Master
ax.plot([7.5, 4.5], [7.8, 7], color=line_color, linewidth=0.8)
ax.annotate('', xy=(4.5, 7), xytext=(4.7, 6.9),
            arrowprops=dict(arrowstyle='->', lw=0.8, color=line_color))
ax.text(6, 7.5, 'Data Pengumuman Akademik', fontsize=7, ha='center', va='center')

# Return flows to Transaksi
ax.plot([7.5, 4.5], [6.2, 5], color=line_color, linewidth=0.8)
ax.annotate('', xy=(4.5, 5), xytext=(4.7, 4.9),
            arrowprops=dict(arrowstyle='->', lw=0.8, color=line_color))

# Return flow to Laporan
ax.plot([7.5, 4.5], [4.2, 3], color=line_color, linewidth=0.8)
ax.annotate('', xy=(4.5, 3), xytext=(4.7, 2.9),
            arrowprops=dict(arrowstyle='->', lw=0.8, color=line_color))
ax.text(6, 3.6, 'Melihat\nPencapaian Siswa', fontsize=7, ha='center', va='center')

# Internal section boundaries - exactly like reference
# Top section (Master area)
top_section = patches.Rectangle((2, 6.2), 8.5, 2, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(top_section)

# Middle section (Transaksi area)
mid_section = patches.Rectangle((2, 4.2), 8.5, 1.8, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(mid_section)

# Bottom section (Laporan area)
bot_section = patches.Rectangle((2, 2.2), 8.5, 1.8, linewidth=1, edgecolor=line_color, facecolor='none')
ax.add_patch(bot_section)

plt.tight_layout()

# Save the figure
plt.savefig('dfd_level1_iteration3.png', dpi=300, bbox_inches='tight', facecolor='white')
plt.savefig('dfd_level1_iteration3.pdf', bbox_inches='tight', facecolor='white')

print("DFD Level 1 Iteration 3 berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_iteration3.png dan dfd_level1_iteration3.pdf")

plt.show()