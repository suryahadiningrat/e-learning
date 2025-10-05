import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Ellipse
import numpy as np

# Set up the figure
fig, ax = plt.subplots(1, 1, figsize=(24, 16))
ax.set_xlim(0, 120)
ax.set_ylim(0, 90)
ax.axis('off')

# Define colors (simple black and white as requested)
entity_color = '#FFFFFF'  # White for entities
attribute_color = '#FFFFFF'  # White for attributes
relation_color = '#FFFFFF'  # White for relationships
text_color = '#000000'  # Black for text

# Helper functions
def draw_entity(ax, x, y, width, height, name):
    """Draw an oval entity"""
    ellipse = Ellipse((x, y), width, height, 
                     facecolor=entity_color, 
                     edgecolor='black', 
                     linewidth=2)
    ax.add_patch(ellipse)
    ax.text(x, y, name, ha='center', va='center', fontsize=11, fontweight='bold')

def draw_attribute(ax, x, y, width, height, name, is_key=False):
    """Draw a rectangular attribute"""
    rect = FancyBboxPatch((x-width/2, y-height/2), width, height,
                         boxstyle="round,pad=0.02",
                         facecolor=attribute_color,
                         edgecolor='black',
                         linewidth=1)
    ax.add_patch(rect)
    if is_key:
        ax.text(x, y, name, ha='center', va='center', fontsize=9, 
                fontweight='bold')
        # Add underline for primary key
        ax.plot([x-len(name)*0.4, x+len(name)*0.4], [y-1.5, y-1.5], 'k-', linewidth=1.5)
    else:
        ax.text(x, y, name, ha='center', va='center', fontsize=9)

def draw_relationship(ax, x, y, width, height, name):
    """Draw a diamond relationship"""
    diamond = patches.RegularPolygon((x, y), 4, radius=width/2,
                                   orientation=np.pi/4,
                                   facecolor=relation_color,
                                   edgecolor='black',
                                   linewidth=2)
    ax.add_patch(diamond)
    ax.text(x, y, name, ha='center', va='center', fontsize=10, fontweight='bold')

def draw_line(ax, x1, y1, x2, y2):
    """Draw a connecting line"""
    ax.plot([x1, x2], [y1, y2], 'k-', linewidth=1.5)

# Top row entities - GURU and SISWA
guru_x, guru_y = 25, 75
siswa_x, siswa_y = 95, 75

draw_entity(ax, guru_x, guru_y, 16, 8, 'GURU')
draw_entity(ax, siswa_x, siswa_y, 16, 8, 'SISWA')

# GURU attributes positioned around the entity
guru_attrs = [
    ('id_guru', guru_x-12, guru_y+12, True),
    ('nama_lengkap', guru_x+12, guru_y+12, False),
    ('email', guru_x-12, guru_y-12, False),
    ('no_hp', guru_x+12, guru_y-12, False),
    ('nip', guru_x, guru_y+15, False),
    ('alamat', guru_x, guru_y-15, False)
]

for attr, x, y, is_key in guru_attrs:
    draw_attribute(ax, x, y, 10, 4, attr, is_key)
    draw_line(ax, guru_x, guru_y, x, y)

# SISWA attributes positioned around the entity
siswa_attrs = [
    ('id_siswa', siswa_x-12, siswa_y+12, True),
    ('nama_lengkap', siswa_x+12, siswa_y+12, False),
    ('nisn', siswa_x-12, siswa_y-12, False),
    ('email', siswa_x+12, siswa_y-12, False),
    ('alamat', siswa_x, siswa_y+15, False),
    ('tgl_lahir', siswa_x, siswa_y-15, False)
]

for attr, x, y, is_key in siswa_attrs:
    draw_attribute(ax, x, y, 10, 4, attr, is_key)
    draw_line(ax, siswa_x, siswa_y, x, y)

# Center entity - KELAS
kelas_x, kelas_y = 60, 50
draw_entity(ax, kelas_x, kelas_y, 16, 8, 'KELAS')

# KELAS attributes
kelas_attrs = [
    ('id_kelas', kelas_x, kelas_y+15, True),
    ('nama_kelas', kelas_x-15, kelas_y+8, False),
    ('tingkat', kelas_x+15, kelas_y+8, False),
    ('kapasitas', kelas_x-15, kelas_y-8, False),
    ('tahun_ajaran', kelas_x+15, kelas_y-8, False),
    ('wali_kelas', kelas_x, kelas_y-15, False)
]

for attr, x, y, is_key in kelas_attrs:
    draw_attribute(ax, x, y, 10, 4, attr, is_key)
    draw_line(ax, kelas_x, kelas_y, x, y)

# Relationships between GURU-KELAS and SISWA-KELAS
mengajar_x, mengajar_y = 42.5, 62.5
belajar_x, belajar_y = 77.5, 62.5

draw_relationship(ax, mengajar_x, mengajar_y, 10, 6, 'mengajar')
draw_relationship(ax, belajar_x, belajar_y, 10, 6, 'belajar')

# Connect relationships
draw_line(ax, guru_x, guru_y, mengajar_x, mengajar_y)
draw_line(ax, mengajar_x, mengajar_y, kelas_x, kelas_y)
draw_line(ax, siswa_x, siswa_y, belajar_x, belajar_y)
draw_line(ax, belajar_x, belajar_y, kelas_x, kelas_y)

# Bottom row entities
jurusan_x, jurusan_y = 25, 25
mapel_x, mapel_y = 95, 25
absensi_x, absensi_y = 40, 35

draw_entity(ax, jurusan_x, jurusan_y, 16, 8, 'JURUSAN')
draw_entity(ax, mapel_x, mapel_y, 20, 8, 'MATA_PELAJARAN')
draw_entity(ax, absensi_x, absensi_y, 16, 8, 'ABSENSI')

# JURUSAN attributes
jurusan_attrs = [
    ('id_jurusan', jurusan_x, jurusan_y+12, True),
    ('nama_jurusan', jurusan_x-12, jurusan_y+6, False),
    ('kode_jurusan', jurusan_x+12, jurusan_y+6, False),
    ('kepala_jurusan', jurusan_x-12, jurusan_y-6, False),
    ('deskripsi', jurusan_x+12, jurusan_y-6, False)
]

for attr, x, y, is_key in jurusan_attrs:
    draw_attribute(ax, x, y, 10, 4, attr, is_key)
    draw_line(ax, jurusan_x, jurusan_y, x, y)

# MATA_PELAJARAN attributes
mapel_attrs = [
    ('id_mata_pelajaran', mapel_x, mapel_y+12, True),
    ('nama_mata_pelajaran', mapel_x-15, mapel_y+6, False),
    ('kode_mata_pelajaran', mapel_x+15, mapel_y+6, False),
    ('sks', mapel_x-15, mapel_y-6, False),
    ('semester', mapel_x+15, mapel_y-6, False),
    ('deskripsi', mapel_x, mapel_y-12, False)
]

for attr, x, y, is_key in mapel_attrs:
    draw_attribute(ax, x, y, 12, 4, attr, is_key)
    draw_line(ax, mapel_x, mapel_y, x, y)

# ABSENSI attributes
absensi_attrs = [
    ('id_absensi', absensi_x, absensi_y+12, True),
    ('tanggal', absensi_x-12, absensi_y+6, False),
    ('status', absensi_x+12, absensi_y+6, False),
    ('keterangan', absensi_x-12, absensi_y-6, False),
    ('jam_masuk', absensi_x+12, absensi_y-6, False)
]

for attr, x, y, is_key in absensi_attrs:
    draw_attribute(ax, x, y, 10, 4, attr, is_key)
    draw_line(ax, absensi_x, absensi_y, x, y)

# Additional relationships
memiliki_x, memiliki_y = 42.5, 37.5
terdiri_dari_x, terdiri_dari_y = 77.5, 37.5
berlangsung_x, berlangsung_y = 50, 42.5

draw_relationship(ax, memiliki_x, memiliki_y, 10, 6, 'memiliki')
draw_relationship(ax, terdiri_dari_x, terdiri_dari_y, 10, 6, 'terdiri_dari')
draw_relationship(ax, berlangsung_x, berlangsung_y, 10, 6, 'berlangsung')

# Connect additional relationships
draw_line(ax, jurusan_x, jurusan_y, memiliki_x, memiliki_y)
draw_line(ax, memiliki_x, memiliki_y, kelas_x, kelas_y)
draw_line(ax, mapel_x, mapel_y, terdiri_dari_x, terdiri_dari_y)
draw_line(ax, terdiri_dari_x, terdiri_dari_y, kelas_x, kelas_y)
draw_line(ax, absensi_x, absensi_y, berlangsung_x, berlangsung_y)
draw_line(ax, berlangsung_x, berlangsung_y, kelas_x, kelas_y)

# Bottom entities - Additional entities
tahun_akademik_x, tahun_akademik_y = 60, 10
jadwal_x, jadwal_y = 20, 5
materi_x, materi_y = 60, 5
nilai_x, nilai_y = 100, 5

draw_entity(ax, tahun_akademik_x, tahun_akademik_y, 18, 6, 'TAHUN_AKADEMIK')
draw_entity(ax, jadwal_x, jadwal_y, 14, 6, 'JADWAL')
draw_entity(ax, materi_x, materi_y, 14, 6, 'MATERI')
draw_entity(ax, nilai_x, nilai_y, 14, 6, 'NILAI')

# TAHUN_AKADEMIK attributes
tahun_attrs = [
    ('id_tahun', tahun_akademik_x-12, tahun_akademik_y+8, True),
    ('tahun_mulai', tahun_akademik_x+12, tahun_akademik_y+8, False),
    ('tahun_selesai', tahun_akademik_x-12, tahun_akademik_y-8, False),
    ('semester', tahun_akademik_x+12, tahun_akademik_y-8, False)
]

for attr, x, y, is_key in tahun_attrs:
    draw_attribute(ax, x, y, 10, 3, attr, is_key)
    draw_line(ax, tahun_akademik_x, tahun_akademik_y, x, y)

# Add title
ax.text(60, 85, 'Entity Relationship Diagram - E-Learning SMK System', 
        ha='center', va='center', fontsize=18, fontweight='bold')

# Add legend in bottom left
legend_x = 8
legend_y = 15
ax.text(legend_x, legend_y, 'Legend:', fontsize=12, fontweight='bold')

# Entity legend
draw_entity(ax, legend_x + 6, legend_y - 4, 8, 4, 'Entity')
ax.text(legend_x + 15, legend_y - 4, 'Entity', fontsize=10, va='center')

# Attribute legend
draw_attribute(ax, legend_x + 6, legend_y - 8, 8, 3, 'Attribute', False)
ax.text(legend_x + 15, legend_y - 8, 'Attribute', fontsize=10, va='center')

# Primary key legend
draw_attribute(ax, legend_x + 6, legend_y - 12, 8, 3, 'Primary Key', True)
ax.text(legend_x + 15, legend_y - 12, 'Primary Key', fontsize=10, va='center')

# Relationship legend
draw_relationship(ax, legend_x + 6, legend_y - 16, 8, 4, 'Relationship')
ax.text(legend_x + 15, legend_y - 16, 'Relationship', fontsize=10, va='center')

plt.tight_layout()
plt.savefig('/Users/suryahadiningrat/Documents/projects/e-learning/diagrams_output/erd_clean_layout.png', 
            dpi=300, bbox_inches='tight', facecolor='white')
plt.savefig('/Users/suryahadiningrat/Documents/projects/e-learning/diagrams_output/erd_clean_layout.pdf', 
            bbox_inches='tight', facecolor='white')
plt.show()

print("ERD with clean layout has been generated!")
print("Files saved:")
print("- erd_clean_layout.png")
print("- erd_clean_layout.pdf")