import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Circle, Rectangle
import numpy as np

# Create figure and axis with proper DFD layout
fig, ax = plt.subplots(1, 1, figsize=(22, 18))
ax.set_xlim(0, 220)
ax.set_ylim(-40, 180)
ax.set_aspect('equal')
ax.axis('off')

# Colors
process_color = '#E8F4FD'
process_border = '#1976D2'
datastore_color = '#F3E5F5'
datastore_border = '#7B1FA2'
entity_color = '#E8F5E8'
entity_border = '#388E3C'
arrow_color = '#424242'

# Helper function to draw process (circle)
def draw_process(x, y, radius, number, name, ax):
    circle = Circle((x, y), radius, facecolor=process_color, 
                   edgecolor=process_border, linewidth=2)
    ax.add_patch(circle)
    
    # Add process number
    ax.text(x, y + 8, str(number), ha='center', va='center', 
            fontsize=14, fontweight='bold', color=process_border)
    
    # Add process name (split into lines if needed)
    lines = name.split('\n')
    for i, line in enumerate(lines):
        ax.text(x, y - 5 - (i * 8), line, ha='center', va='center', 
                fontsize=10, fontweight='bold', color='black')

# Helper function to draw data store
def draw_datastore(x, y, width, height, label, name, ax):
    # Main rectangle
    rect = Rectangle((x, y), width, height, facecolor=datastore_color,
                    edgecolor=datastore_border, linewidth=2)
    ax.add_patch(rect)
    
    # Left side line (open rectangle style)
    ax.plot([x, x], [y, y + height], color=datastore_border, linewidth=3)
    
    # Add label and name
    ax.text(x + 5, y + height/2 + 3, label, ha='left', va='center',
            fontsize=10, fontweight='bold', color=datastore_border)
    ax.text(x + 5, y + height/2 - 3, name, ha='left', va='center',
            fontsize=9, color='black')

# Helper function to draw external entity
def draw_entity(x, y, width, height, name, ax):
    rect = FancyBboxPatch((x, y), width, height, boxstyle="round,pad=2",
                         facecolor=entity_color, edgecolor=entity_border, 
                         linewidth=2)
    ax.add_patch(rect)
    
    ax.text(x + width/2, y + height/2, name, ha='center', va='center',
            fontsize=11, fontweight='bold', color='black')

# Helper function to draw arrow with label
def draw_arrow(start_x, start_y, end_x, end_y, label, ax, offset=0):
    # Calculate arrow direction
    dx = end_x - start_x
    dy = end_y - start_y
    length = np.sqrt(dx**2 + dy**2)
    
    if length > 0:
        # Normalize direction
        dx_norm = dx / length
        dy_norm = dy / length
        
        # Adjust start and end points to avoid overlapping with shapes
        start_x += dx_norm * 15
        start_y += dy_norm * 15
        end_x -= dx_norm * 15
        end_y -= dy_norm * 15
        
        # Draw arrow
        ax.annotate('', xy=(end_x, end_y), xytext=(start_x, start_y),
                   arrowprops=dict(arrowstyle='->', color=arrow_color, lw=1.5))
        
        # Add label
        mid_x = (start_x + end_x) / 2 + offset
        mid_y = (start_y + end_y) / 2 + offset
        ax.text(mid_x, mid_y, label, ha='center', va='center',
                fontsize=8, bbox=dict(boxstyle="round,pad=2", 
                facecolor='white', edgecolor='none', alpha=0.8))

# Draw External Entities
draw_entity(15, 130, 35, 18, 'Admin', ax)
draw_entity(15, 80, 35, 18, 'Guru', ax)
draw_entity(15, 30, 35, 18, 'Siswa', ax)

# Draw Processes (circular) - Main processes matching the image style
processes = [
    (100, 140, 18, '1', 'Master\nData'),
    (100, 90, 18, '2', 'Transaksi\nPembelajaran'),
    (100, 40, 18, '3', 'Laporan\ndan Analisis')
]

for x, y, radius, number, name in processes:
    draw_process(x, y, radius, number, name, ax)

# Draw Data Stores - Left side (Master Data)
left_datastores = [
    (140, 150, 55, 12, 'D1', 'Data Guru'),
    (140, 135, 55, 12, 'D2', 'Data Siswa'),
    (140, 120, 55, 12, 'D3', 'Data Jurusan'),
    (140, 105, 55, 12, 'D4', 'Data Mata Pelajaran'),
    (140, 90, 55, 12, 'D5', 'Data Tahun Akademik'),
    (140, 75, 55, 12, 'D6', 'Data Kelas'),
    (140, 60, 55, 12, 'D7', 'Data Jadwal'),
    (140, 45, 55, 12, 'D8', 'Data Settings')
]

for x, y, width, height, label, name in left_datastores:
    draw_datastore(x, y, width, height, label, name, ax)

# Draw Data Stores - Right side (Transaction Data)
right_datastores = [
    (140, 30, 55, 12, 'D9', 'Data Absensi'),
    (140, 15, 55, 12, 'D10', 'Data Nilai'),
    (140, 0, 55, 12, 'D11', 'Data Materi'),
    (140, -15, 55, 12, 'D12', 'Data Tugas'),
    (140, -30, 55, 12, 'D13', 'Data Pengumpulan')
]

for x, y, width, height, label, name in right_datastores:
    draw_datastore(x, y, width, height, label, name, ax)

# Draw comprehensive data flows matching e-learning system features

# Admin flows to Master Data Process (1)
draw_arrow(50, 140, 82, 140, 'Data Guru', ax, -8)
draw_arrow(82, 140, 50, 140, 'Info Data Guru', ax, 8)

draw_arrow(50, 138, 82, 138, 'Data Siswa', ax, -5)
draw_arrow(82, 138, 50, 138, 'Info Data Siswa', ax, 5)

draw_arrow(50, 136, 82, 136, 'Data Jurusan', ax, -3)
draw_arrow(82, 136, 50, 136, 'Info Data Jurusan', ax, 3)

draw_arrow(50, 134, 82, 134, 'Data Mata Pelajaran', ax, -1)
draw_arrow(82, 134, 50, 134, 'Info Mata Pelajaran', ax, 1)

# Guru flows to Transaction Process (2)
draw_arrow(50, 90, 82, 90, 'Data Materi', ax, -8)
draw_arrow(82, 90, 50, 90, 'Info Materi', ax, 8)

draw_arrow(50, 88, 82, 88, 'Data Tugas', ax, -5)
draw_arrow(82, 88, 50, 88, 'Info Tugas', ax, 5)

draw_arrow(50, 86, 82, 86, 'Data Jadwal', ax, -3)
draw_arrow(82, 86, 50, 86, 'Info Jadwal', ax, 3)

draw_arrow(50, 84, 82, 84, 'Data Absensi', ax, -1)
draw_arrow(82, 84, 50, 84, 'Info Absensi', ax, 1)

# Siswa flows to Report Process (3)
draw_arrow(50, 40, 82, 40, 'Request Nilai', ax, -8)
draw_arrow(82, 40, 50, 40, 'Laporan Nilai', ax, 8)

draw_arrow(50, 38, 82, 38, 'Request Materi', ax, -5)
draw_arrow(82, 38, 50, 38, 'Info Materi', ax, 5)

draw_arrow(50, 36, 82, 36, 'Pengumpulan Tugas', ax, -3)
draw_arrow(82, 36, 50, 36, 'Status Tugas', ax, 3)

# Process 1 (Master) to Data Stores
draw_arrow(118, 140, 140, 156, 'Data Guru', ax)
draw_arrow(118, 138, 140, 141, 'Data Siswa', ax)
draw_arrow(118, 136, 140, 126, 'Data Jurusan', ax)
draw_arrow(118, 134, 140, 111, 'Data Mata Pelajaran', ax)
draw_arrow(118, 132, 140, 96, 'Data Tahun Akademik', ax)
draw_arrow(118, 130, 140, 81, 'Data Kelas', ax)
draw_arrow(118, 128, 140, 66, 'Data Jadwal', ax)
draw_arrow(118, 126, 140, 51, 'Data Settings', ax)

# Process 2 (Transaction) to Data Stores
draw_arrow(118, 90, 140, 36, 'Data Absensi', ax)
draw_arrow(118, 88, 140, 21, 'Data Nilai', ax)
draw_arrow(118, 86, 140, 6, 'Data Materi', ax)
draw_arrow(118, 84, 140, -9, 'Data Tugas', ax)
draw_arrow(118, 82, 140, -24, 'Data Pengumpulan', ax)

# Process 3 (Report) accessing data stores for reports
draw_arrow(118, 40, 140, 36, 'Akses Absensi', ax)
draw_arrow(118, 38, 140, 21, 'Akses Nilai', ax)
draw_arrow(118, 36, 140, 6, 'Akses Materi', ax)
draw_arrow(118, 34, 140, -9, 'Akses Tugas', ax)

# Inter-process flows
draw_arrow(100, 122, 100, 108, 'Data Pengguna\nAkademik', ax, 15)
draw_arrow(100, 72, 100, 58, 'Data Pembelajaran\ndan Penilaian', ax, 15)

# Add title and subtitle with proper positioning
ax.text(110, 170, 'DFD Level 1 - Sistem E-Learning SMK', 
        ha='center', va='center', fontsize=20, fontweight='bold')

ax.text(110, 160, 'Data Flow Diagram Level 1', 
        ha='center', va='center', fontsize=16, style='italic')

# Add legend for DFD components
legend_x = 15
legend_y = -10

# Legend title
ax.text(legend_x, legend_y, 'Keterangan:', fontsize=12, fontweight='bold')

# External Entity legend
draw_entity(legend_x, legend_y - 15, 25, 12, 'External Entity', ax)
ax.text(legend_x + 30, legend_y - 9, ': Entitas Eksternal', fontsize=10)

# Process legend
draw_process(legend_x + 12, legend_y - 35, 12, '1', 'Process', ax)
ax.text(legend_x + 30, legend_y - 35, ': Proses', fontsize=10)

# Data Store legend
draw_datastore(legend_x, legend_y - 55, 25, 8, 'D1', 'Data Store', ax)
ax.text(legend_x + 30, legend_y - 51, ': Penyimpanan Data', fontsize=10)

plt.tight_layout()
plt.savefig('dfd_level1_traditional.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('dfd_level1_traditional.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')

print("DFD Level 1 berhasil dibuat!")
print("File disimpan sebagai: dfd_level1_traditional.png dan dfd_level1_traditional.pdf")