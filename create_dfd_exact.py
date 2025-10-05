import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch
import numpy as np

def draw_external_entity(x, y, width, height, text, ax):
    """Draw external entity as rectangle"""
    rect = patches.Rectangle((x, y), width, height, linewidth=2, 
                           edgecolor='black', facecolor='lightgray', alpha=0.8)
    ax.add_patch(rect)
    ax.text(x + width/2, y + height/2, text, fontsize=12, fontweight='bold',
            ha='center', va='center')

def draw_process(x, y, radius, number, text, ax):
    """Draw process as circle"""
    circle = patches.Circle((x, y), radius, linewidth=2, 
                          edgecolor='black', facecolor='white')
    ax.add_patch(circle)
    ax.text(x, y + 5, number, fontsize=14, fontweight='bold',
            ha='center', va='center')
    ax.text(x, y - 5, text, fontsize=10, fontweight='bold',
            ha='center', va='center')

def draw_datastore(x, y, width, height, label, name, ax):
    """Draw data store as open rectangle"""
    # Left line
    ax.plot([x, x], [y, y + height], 'k-', linewidth=2)
    # Top line
    ax.plot([x, x + width], [y + height, y + height], 'k-', linewidth=2)
    # Bottom line
    ax.plot([x, x + width], [y, y], 'k-', linewidth=2)
    
    # Label and name
    ax.text(x + 5, y + height/2, f"{label} {name}", fontsize=9,
            ha='left', va='center')

def draw_boundary(x, y, width, height, ax):
    """Draw boundary rectangle"""
    rect = patches.Rectangle((x, y), width, height, linewidth=2, 
                           edgecolor='black', facecolor='none', linestyle='-')
    ax.add_patch(rect)

def draw_data_flow_line(x1, y1, x2, y2, ax, color='black', linewidth=1):
    """Draw data flow line"""
    ax.plot([x1, x2], [y1, y2], color=color, linewidth=linewidth)

# Create figure
fig, ax = plt.subplots(1, 1, figsize=(24, 18))
ax.set_xlim(-10, 250)
ax.set_ylim(-10, 200)
ax.set_aspect('equal')
ax.axis('off')

# Title
ax.text(120, 190, 'DFD Level 1 - Sistem E-Learning SMK', fontsize=16, fontweight='bold', ha='center')

# External Entities (left side)
draw_external_entity(10, 150, 50, 25, 'Admin', ax)
draw_external_entity(10, 110, 50, 25, 'Guru', ax)
draw_external_entity(10, 70, 50, 25, 'Siswa', ax)

# Main system boundary
draw_boundary(75, 20, 165, 160, ax)

# Process 1 Boundary (Master Data)
draw_boundary(80, 130, 155, 45, ax)
draw_process(110, 152, 12, '1', 'Master', ax)

# Process 2 Boundary (Transaksi)
draw_boundary(80, 75, 155, 45, ax)
draw_process(110, 97, 12, '2', 'Transaksi', ax)

# Process 3 Boundary (Laporan)
draw_boundary(80, 25, 155, 45, ax)
draw_process(110, 47, 12, '3', 'Laporan', ax)

# Data Stores for Process 1 (Master Data) - right side
datastores_master = [
    (140, 165, 90, 8, 'D1', 'Data Guru'),
    (140, 155, 90, 8, 'D2', 'Data Siswa'),
    (140, 145, 90, 8, 'D3', 'Data Jurusan'),
    (140, 135, 90, 8, 'D4', 'Data Kelas'),
    (140, 125, 90, 8, 'D5', 'Data Tahun Akademik'),
    (140, 115, 90, 8, 'D6', 'Data Mata Pelajaran')
]

for x, y, w, h, label, name in datastores_master:
    draw_datastore(x, y, w, h, label, name, ax)

# Data Stores for Process 2 (Transaksi) - right side
datastores_transaksi = [
    (140, 110, 90, 8, 'D7', 'Data Mata Pelajaran'),
    (140, 100, 90, 8, 'D8', 'Data Tahun Akademik'),
    (140, 90, 90, 8, 'D9', 'Data Jurusan'),
    (140, 80, 90, 8, 'D10', 'Data Siswa'),
    (140, 70, 90, 8, 'D11', 'Data Guru')
]

for x, y, w, h, label, name in datastores_transaksi:
    draw_datastore(x, y, w, h, label, name, ax)

# Additional Data Stores (far right)
additional_datastores = [
    (185, 55, 50, 8, 'D12', 'Data Pengumpulan Tugas'),
    (185, 45, 50, 8, 'D13', 'Data Penilaian Siswa'),
    (185, 35, 50, 8, 'D14', 'Data Pengumpulan Akademik')
]

for x, y, w, h, label, name in additional_datastores:
    draw_datastore(x, y, w, h, label, name, ax)

# Data Flow Lines - exactly matching reference image
# Admin flows - multiple horizontal lines to data stores
admin_center_y = 162
data_flow_labels = ['Data Guru', 'Data Siswa', 'Data Jurusan', 'Data Kelas', 'Data Tahun Akademik', 'Data Mata Pelajaran']

# Draw multiple horizontal lines from Admin
for i, (_, ds_y, _, _, _, _) in enumerate(datastores_master):
    # Horizontal line from Admin to data store
    draw_data_flow_line(60, admin_center_y, 140, ds_y + 4, ax)
    # Add label above the line
    ax.text(100, ds_y + 8, data_flow_labels[i], fontsize=7, ha='center', va='bottom')

# Guru flows - to Transaksi process and some data stores
guru_center_y = 122
# Line to Process 2
draw_data_flow_line(60, guru_center_y, 98, 97, ax)
ax.text(79, 109, 'Data Materi\nData Penilaian Siswa', fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', alpha=0.8))

# Additional lines from Guru to some data stores
draw_data_flow_line(60, guru_center_y, 140, 105, ax)  # To D7
draw_data_flow_line(60, guru_center_y, 140, 95, ax)   # To D8

# Siswa flows - exactly like reference
siswa_center_y = 82
# To Process 2 (Transaksi)
draw_data_flow_line(60, siswa_center_y, 98, 97, ax)
ax.text(79, 89, 'Materi\nPengumpulan', fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', alpha=0.8))

# To Process 3 (Laporan)
draw_data_flow_line(60, siswa_center_y, 98, 47, ax)
ax.text(79, 64, 'Request Laporan', fontsize=8, ha='center', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='white', alpha=0.8))

# Inter-process flows
# Master to Transaksi
draw_data_flow_line(110, 140, 110, 109, ax, color='blue', linewidth=2)
ax.text(125, 124, 'Data Pengumpulan Akademik', fontsize=8, ha='left', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='lightblue', alpha=0.8))

# Transaksi to Laporan
draw_data_flow_line(110, 85, 110, 59, ax, color='blue', linewidth=2)
ax.text(125, 72, 'Data Penilaian Siswa', fontsize=8, ha='left', va='center',
        bbox=dict(boxstyle="round,pad=0.2", facecolor='lightblue', alpha=0.8))

# Process to Data Store connections (green arrows)
# Master process to its data stores
draw_data_flow_line(122, 152, 140, 152, ax, color='green', linewidth=1.5)

# Transaksi process to its data stores
draw_data_flow_line(122, 97, 140, 97, ax, color='green', linewidth=1.5)

# Laporan process to additional data stores
draw_data_flow_line(122, 47, 185, 47, ax, color='green', linewidth=1.5)

# Add more detailed data flow lines like in reference
# Additional flows from external entities to boundaries (like in reference)

# Admin to Master boundary - multiple entry points
draw_data_flow_line(60, 162, 80, 162, ax)  # Main entry to boundary
draw_data_flow_line(60, 160, 80, 160, ax)  # Secondary entry
draw_data_flow_line(60, 158, 80, 158, ax)  # Third entry

# Guru to Transaksi boundary
draw_data_flow_line(60, 122, 80, 122, ax)  # Main entry
draw_data_flow_line(60, 120, 80, 120, ax)  # Secondary entry

# Siswa to multiple boundaries
draw_data_flow_line(60, 82, 80, 82, ax)   # To Transaksi boundary
draw_data_flow_line(60, 80, 80, 80, ax)   # Secondary to Transaksi
draw_data_flow_line(60, 78, 80, 47, ax)   # To Laporan boundary

# Add boundary exit flows (like in reference image)
# From Process 1 boundary to Process 2 boundary
draw_data_flow_line(110, 130, 110, 120, ax, color='blue', linewidth=2)

# From Process 2 boundary to Process 3 boundary  
draw_data_flow_line(110, 75, 110, 70, ax, color='blue', linewidth=2)

# From Process 3 boundary to external (like in reference)
draw_data_flow_line(80, 35, 60, 82, ax, color='red', linewidth=1.5)  # Back to Siswa

# Add more arrows for better flow indication
# Multiple arrows from Admin
ax.annotate('', xy=(80, 162), xytext=(60, 162), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.annotate('', xy=(80, 160), xytext=(60, 160), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))

# Multiple arrows from Guru
ax.annotate('', xy=(80, 122), xytext=(60, 122), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.annotate('', xy=(80, 120), xytext=(60, 120), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))

# Multiple arrows from Siswa
ax.annotate('', xy=(80, 82), xytext=(60, 82), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.annotate('', xy=(80, 47), xytext=(60, 78), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))

plt.tight_layout()
plt.savefig('dfd_level1_exact.png', dpi=300, bbox_inches='tight')
plt.savefig('dfd_level1_exact.pdf', bbox_inches='tight')
plt.show()

print("DFD Level 1 yang persis seperti referensi berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_exact.png dan dfd_level1_exact.pdf")