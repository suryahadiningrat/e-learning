import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Rectangle
import numpy as np

# Create figure and axis
fig, ax = plt.subplots(1, 1, figsize=(24, 18))
ax.set_xlim(0, 240)
ax.set_ylim(0, 180)
ax.set_aspect('equal')
ax.axis('off')

# Colors
process_color = '#FFFFFF'
process_border = '#000000'
boundary_color = '#F0F0F0'
boundary_border = '#000000'
datastore_color = '#FFFFFF'
datastore_border = '#000000'
entity_color = '#FFFFFF'
entity_border = '#000000'
arrow_color = '#000000'

# Helper function to draw process (circle)
def draw_process(x, y, radius, number, name, ax):
    circle = Circle((x, y), radius, facecolor=process_color, 
                   edgecolor=process_border, linewidth=2)
    ax.add_patch(circle)
    
    # Add process number
    ax.text(x, y + 5, str(number), ha='center', va='center', 
            fontsize=16, fontweight='bold', color='black')
    
    # Add process name
    ax.text(x, y - 8, name, ha='center', va='center', 
            fontsize=12, fontweight='bold', color='black')

# Helper function to draw data store (simple rectangle)
def draw_datastore(x, y, width, height, label, name, ax):
    # Main rectangle
    rect = Rectangle((x, y), width, height, facecolor=datastore_color,
                    edgecolor=datastore_border, linewidth=1.5)
    ax.add_patch(rect)
    
    # Left side line (open rectangle style)
    ax.plot([x, x], [y, y + height], color=datastore_border, linewidth=2)
    
    # Add label and name
    ax.text(x + width/2, y + height/2, f"{label} {name}", ha='center', va='center',
            fontsize=9, color='black')

# Helper function to draw external entity
def draw_entity(x, y, width, height, name, ax):
    rect = Rectangle((x, y), width, height, facecolor=entity_color, 
                    edgecolor=entity_border, linewidth=2)
    ax.add_patch(rect)
    
    ax.text(x + width/2, y + height/2, name, ha='center', va='center',
            fontsize=11, fontweight='bold', color='black')

# Helper function to draw boundary rectangle
def draw_boundary(x, y, width, height, ax):
    rect = Rectangle((x, y), width, height, facecolor='none',
                    edgecolor=boundary_border, linewidth=2)
    ax.add_patch(rect)

# Helper function to draw arrow with label
def draw_arrow(start_x, start_y, end_x, end_y, label, ax, offset_x=0, offset_y=0):
    # Draw arrow
    ax.annotate('', xy=(end_x, end_y), xytext=(start_x, start_y),
               arrowprops=dict(arrowstyle='->', color=arrow_color, lw=1.5))
    
    # Add label
    mid_x = (start_x + end_x) / 2 + offset_x
    mid_y = (start_y + end_y) / 2 + offset_y
    ax.text(mid_x, mid_y, label, ha='center', va='center',
            fontsize=8, color='black')

# Draw External Entities (left side)
draw_entity(20, 140, 40, 20, 'Admin', ax)
draw_entity(20, 100, 40, 20, 'Guru', ax)
draw_entity(20, 60, 40, 20, 'Siswa', ax)

# Draw main system boundary (large outer rectangle)
main_boundary_x, main_boundary_y = 75, -5
main_boundary_w, main_boundary_h = 155, 180
draw_boundary(main_boundary_x, main_boundary_y, main_boundary_w, main_boundary_h, ax)

# Draw Process 1 Boundary (Master Data) - nested inside main boundary
boundary1_x, boundary1_y = 80, 120
boundary1_w, boundary1_h = 145, 50
draw_boundary(boundary1_x, boundary1_y, boundary1_w, boundary1_h, ax)

# Draw Process 1 (Master)
draw_process(110, 145, 15, '1', 'Master', ax)

# Data stores for Process 1 (right side of boundary)
datastores_p1_right = [
    (170, 160, 50, 8, 'D1', 'Data Guru'),
    (170, 150, 50, 8, 'D2', 'Data Siswa'),
    (170, 140, 50, 8, 'D3', 'Data Jurusan'),
    (170, 130, 50, 8, 'D4', 'Data Kelas'),
    (170, 120, 50, 8, 'D5', 'Data Tahun Akademik'),
    (170, 110, 50, 8, 'D6', 'Data Mata Pelajaran')
]

for x, y, w, h, label, name in datastores_p1_right:
    draw_datastore(x, y, w, h, label, name, ax)

# Draw Process 2 Boundary (Transaksi) - nested inside main boundary
boundary2_x, boundary2_y = 80, 60
boundary2_w, boundary2_h = 145, 50
draw_boundary(boundary2_x, boundary2_y, boundary2_w, boundary2_h, ax)

# Draw Process 2 (Transaksi)
draw_process(110, 85, 15, '2', 'Transaksi', ax)

# Data stores for Process 2 (right side of boundary)
datastores_p2_right = [
    (170, 100, 50, 8, 'D7', 'Data Mata Pelajaran'),
    (170, 90, 50, 8, 'D8', 'Data Tahun Akademik'),
    (170, 80, 50, 8, 'D9', 'Data Jurusan'),
    (170, 70, 50, 8, 'D10', 'Data Siswa'),
    (170, 60, 50, 8, 'D11', 'Data Guru')
]

for x, y, w, h, label, name in datastores_p2_right:
    draw_datastore(x, y, w, h, label, name, ax)

# Draw Process 3 Boundary (Laporan) - nested inside main boundary
boundary3_x, boundary3_y = 80, 5
boundary3_w, boundary3_h = 145, 50
draw_boundary(boundary3_x, boundary3_y, boundary3_w, boundary3_h, ax)

# Draw Process 3 (Laporan)
draw_process(110, 30, 15, '3', 'Laporan', ax)

# Additional data stores on the far right (outside process boundaries but inside main)
right_datastores = [
    (180, 45, 40, 8, 'D12', 'Data Pengumpulan Tugas'),
    (180, 35, 40, 8, 'D13', 'Data Penilaian Siswa'),
    (180, 25, 40, 8, 'D14', 'Data Pengumpulan Akademik')
]

for x, y, w, h, label, name in right_datastores:
    draw_datastore(x, y, w, h, label, name, ax)

# Data flows from external entities to processes
# Admin to Master Process
ax.annotate('', xy=(95, 145), xytext=(60, 120), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.text(75, 135, 'Data Guru\nData Siswa\nData Jurusan\nData Kelas', 
        fontsize=8, ha='center', va='center', bbox=dict(boxstyle="round,pad=0.3", facecolor='white', alpha=0.8))

# Guru to Transaksi Process
ax.annotate('', xy=(95, 85), xytext=(60, 80), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.text(75, 82, 'Data Materi\nData Tugas\nData Nilai', 
        fontsize=8, ha='center', va='center', bbox=dict(boxstyle="round,pad=0.3", facecolor='white', alpha=0.8))

# Siswa to Transaksi Process
ax.annotate('', xy=(95, 85), xytext=(60, 60), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.text(75, 70, 'Data Pengumpulan\nData Absensi', 
        fontsize=8, ha='center', va='center', bbox=dict(boxstyle="round,pad=0.3", facecolor='white', alpha=0.8))

# Siswa to Laporan Process
ax.annotate('', xy=(95, 30), xytext=(60, 60), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='black'))
ax.text(75, 45, 'Request Laporan', 
        fontsize=8, ha='center', va='center', bbox=dict(boxstyle="round,pad=0.3", facecolor='white', alpha=0.8))

# Inter-process flows
# Master to Transaksi
ax.annotate('', xy=(110, 110), xytext=(110, 130), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='blue'))
ax.text(120, 120, 'Data Pengumpulan Akademik', 
        fontsize=8, ha='left', va='center', bbox=dict(boxstyle="round,pad=0.3", facecolor='lightblue', alpha=0.8))

# Transaksi to Laporan
ax.annotate('', xy=(110, 55), xytext=(110, 70), 
            arrowprops=dict(arrowstyle='->', lw=1.5, color='blue'))
ax.text(120, 62, 'Data Penilaian Siswa', 
        fontsize=8, ha='left', va='center', bbox=dict(boxstyle="round,pad=0.3", facecolor='lightblue', alpha=0.8))

# Process to data store flows (simplified)
# Master process to its data stores
ax.annotate('', xy=(170, 145), xytext=(125, 145), 
            arrowprops=dict(arrowstyle='->', lw=1, color='green'))

# Transaksi process to its data stores
ax.annotate('', xy=(170, 85), xytext=(125, 85), 
            arrowprops=dict(arrowstyle='->', lw=1, color='green'))

# Laporan process to right data stores
ax.annotate('', xy=(180, 35), xytext=(125, 30), 
            arrowprops=dict(arrowstyle='->', lw=1, color='green'))

# Add title
ax.text(120, 175, 'DFD Level 1 - Sistem E-Learning SMK', 
        ha='center', va='center', fontsize=18, fontweight='bold')

plt.tight_layout()
plt.savefig('dfd_level1_accurate.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('dfd_level1_accurate.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')

print("DFD Level 1 yang akurat berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_accurate.png dan dfd_level1_accurate.pdf")