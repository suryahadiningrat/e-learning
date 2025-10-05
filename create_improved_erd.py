import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch
import numpy as np

# Set up the figure with larger size for better readability
fig, ax = plt.subplots(1, 1, figsize=(24, 18))
ax.set_xlim(0, 100)
ax.set_ylim(0, 100)
ax.set_aspect('equal')
ax.axis('off')

# Define colors
ENTITY_COLOR = '#E8F4FD'  # Light blue
ENTITY_BORDER = '#2E86AB'  # Dark blue
ATTRIBUTE_COLOR = '#F8F9FA'  # Light gray
ATTRIBUTE_BORDER = '#6C757D'  # Gray
PRIMARY_KEY_COLOR = '#FFE066'  # Gold
PRIMARY_KEY_BORDER = '#F39C12'  # Orange
RELATIONSHIP_COLOR = '#FFE5E5'  # Light pink
RELATIONSHIP_BORDER = '#E74C3C'  # Red
TEXT_COLOR = '#2C3E50'  # Dark gray

def draw_entity(x, y, name, width=12, height=6):
    """Draw an entity (oval)"""
    entity = patches.Ellipse((x, y), width, height, 
                           facecolor=ENTITY_COLOR, 
                           edgecolor=ENTITY_BORDER, 
                           linewidth=2)
    ax.add_patch(entity)
    ax.text(x, y, name, ha='center', va='center', 
            fontsize=11, fontweight='bold', color=TEXT_COLOR)
    return entity

def draw_attribute(x, y, name, is_primary=False, width=8, height=3):
    """Draw an attribute (rounded rectangle)"""
    if is_primary:
        color = PRIMARY_KEY_COLOR
        border_color = PRIMARY_KEY_BORDER
        fontweight = 'bold'
    else:
        color = ATTRIBUTE_COLOR
        border_color = ATTRIBUTE_BORDER
        fontweight = 'normal'
    
    attr = FancyBboxPatch((x-width/2, y-height/2), width, height,
                         boxstyle="round,pad=0.1",
                         facecolor=color,
                         edgecolor=border_color,
                         linewidth=1.5)
    ax.add_patch(attr)
    
    # Underline primary keys
    if is_primary:
        ax.text(x, y, name, ha='center', va='center', 
                fontsize=9, fontweight=fontweight, color=TEXT_COLOR)
        ax.plot([x-len(name)*0.25, x+len(name)*0.25], [y-0.5, y-0.5], 
                color=PRIMARY_KEY_BORDER, linewidth=1.5)
    else:
        ax.text(x, y, name, ha='center', va='center', 
                fontsize=9, fontweight=fontweight, color=TEXT_COLOR)
    
    return attr

def draw_relationship(x, y, name, width=10, height=5):
    """Draw a relationship (diamond)"""
    diamond_x = [x, x+width/2, x, x-width/2, x]
    diamond_y = [y+height/2, y, y-height/2, y, y+height/2]
    
    relationship = patches.Polygon(list(zip(diamond_x, diamond_y)),
                                 facecolor=RELATIONSHIP_COLOR,
                                 edgecolor=RELATIONSHIP_BORDER,
                                 linewidth=2)
    ax.add_patch(relationship)
    ax.text(x, y, name, ha='center', va='center', 
            fontsize=9, fontweight='bold', color=TEXT_COLOR)
    return relationship

def draw_line(start_pos, end_pos, style='-', color='#34495E', linewidth=1.5):
    """Draw a connection line"""
    ax.plot([start_pos[0], end_pos[0]], [start_pos[1], end_pos[1]], 
            style, color=color, linewidth=linewidth)

# Title
ax.text(50, 95, 'Entity Relationship Diagram - E-Learning SMK System', 
        ha='center', va='center', fontsize=18, fontweight='bold', color=TEXT_COLOR)

# ============= MAIN ENTITIES LAYOUT =============
# Top row - Core User Management
users_pos = (15, 85)
jurusan_pos = (50, 85)
mata_pelajaran_pos = (85, 85)

# Second row - User Types
guru_pos = (15, 70)
siswa_pos = (35, 70)
kelas_pos = (65, 70)

# Third row - Academic Management
jadwal_pos = (50, 55)
tahun_akademik_pos = (15, 40)
settings_pos = (85, 40)

# Fourth row - Learning Activities
absensi_pos = (25, 25)
nilai_pos = (50, 25)
materi_pos = (75, 25)

# Fifth row - Assignment System
tugas_pos = (35, 10)
pengumpulan_tugas_pos = (65, 10)

# ============= DRAW ENTITIES =============
draw_entity(*users_pos, 'USERS')
draw_entity(*jurusan_pos, 'JURUSAN')
draw_entity(*mata_pelajaran_pos, 'MATA_PELAJARAN')
draw_entity(*guru_pos, 'GURU')
draw_entity(*siswa_pos, 'SISWA')
draw_entity(*kelas_pos, 'KELAS')
draw_entity(*jadwal_pos, 'JADWAL')
draw_entity(*tahun_akademik_pos, 'TAHUN_AKADEMIK')
draw_entity(*settings_pos, 'SETTINGS')
draw_entity(*absensi_pos, 'ABSENSI')
draw_entity(*nilai_pos, 'NILAI')
draw_entity(*materi_pos, 'MATERI')
draw_entity(*tugas_pos, 'TUGAS')
draw_entity(*pengumpulan_tugas_pos, 'PENGUMPULAN_TUGAS')

# ============= DRAW ATTRIBUTES =============
# USERS attributes
draw_attribute(15, 90, 'id', True)
draw_attribute(8, 88, 'username')
draw_attribute(22, 88, 'email')
draw_attribute(8, 82, 'password')
draw_attribute(22, 82, 'full_name')
draw_attribute(15, 80, 'role')

# JURUSAN attributes
draw_attribute(50, 90, 'id', True)
draw_attribute(43, 88, 'nama_jurusan')
draw_attribute(57, 88, 'kode_jurusan')

# MATA_PELAJARAN attributes
draw_attribute(85, 90, 'id', True)
draw_attribute(78, 88, 'kode')
draw_attribute(92, 88, 'nama')
draw_attribute(78, 82, 'deskripsi')
draw_attribute(92, 82, 'status')

# GURU attributes
draw_attribute(15, 75, 'id', True)
draw_attribute(8, 73, 'user_id')
draw_attribute(22, 73, 'nip')
draw_attribute(8, 67, 'bidang_studi')
draw_attribute(22, 67, 'jenis_kelamin')
draw_attribute(8, 65, 'tempat_lahir')
draw_attribute(22, 65, 'tanggal_lahir')

# SISWA attributes
draw_attribute(35, 75, 'id', True)
draw_attribute(28, 73, 'user_id')
draw_attribute(42, 73, 'nis')
draw_attribute(28, 67, 'nisn')
draw_attribute(42, 67, 'kelas_id')
draw_attribute(28, 65, 'jenis_kelamin')
draw_attribute(42, 65, 'tempat_lahir')

# KELAS attributes
draw_attribute(65, 75, 'id', True)
draw_attribute(58, 73, 'nama_kelas')
draw_attribute(72, 73, 'jurusan_id')
draw_attribute(58, 67, 'tingkat')
draw_attribute(72, 67, 'kapasitas')

# JADWAL attributes
draw_attribute(50, 60, 'id', True)
draw_attribute(43, 58, 'kelas_id')
draw_attribute(57, 58, 'guru_id')
draw_attribute(43, 52, 'mata_pelajaran')
draw_attribute(57, 52, 'hari')
draw_attribute(43, 50, 'jam_mulai')
draw_attribute(57, 50, 'jam_selesai')

# TAHUN_AKADEMIK attributes
draw_attribute(15, 45, 'id', True)
draw_attribute(8, 43, 'tahun_akademik')
draw_attribute(22, 43, 'semester')
draw_attribute(8, 37, 'tanggal_mulai')
draw_attribute(22, 37, 'tanggal_selesai')
draw_attribute(15, 35, 'is_active')

# SETTINGS attributes
draw_attribute(85, 45, 'id', True)
draw_attribute(78, 43, 'key')
draw_attribute(92, 43, 'value')

# ABSENSI attributes
draw_attribute(25, 30, 'id', True)
draw_attribute(18, 28, 'siswa_id')
draw_attribute(32, 28, 'jadwal_id')
draw_attribute(18, 22, 'tanggal')
draw_attribute(32, 22, 'status')
draw_attribute(25, 20, 'keterangan')

# NILAI attributes
draw_attribute(50, 30, 'id', True)
draw_attribute(43, 28, 'siswa_id')
draw_attribute(57, 28, 'jadwal_id')
draw_attribute(43, 22, 'nilai_tugas')
draw_attribute(57, 22, 'nilai_ulangan')
draw_attribute(43, 20, 'nilai_uts')
draw_attribute(57, 20, 'nilai_uas')

# MATERI attributes
draw_attribute(75, 30, 'id', True)
draw_attribute(68, 28, 'jadwal_id')
draw_attribute(82, 28, 'judul')
draw_attribute(68, 22, 'konten')
draw_attribute(82, 22, 'file_path')

# TUGAS attributes
draw_attribute(35, 15, 'id', True)
draw_attribute(28, 13, 'nama_tugas')
draw_attribute(42, 13, 'jadwal_id')
draw_attribute(28, 7, 'deskripsi')
draw_attribute(42, 7, 'deadline')
draw_attribute(35, 5, 'created_by')

# PENGUMPULAN_TUGAS attributes
draw_attribute(65, 15, 'id', True)
draw_attribute(58, 13, 'tugas_id')
draw_attribute(72, 13, 'siswa_id')
draw_attribute(58, 7, 'link_tugas')
draw_attribute(72, 7, 'catatan')
draw_attribute(58, 5, 'status')
draw_attribute(72, 5, 'submitted_at')

# ============= DRAW RELATIONSHIPS =============
# User relationships
draw_relationship(15, 77.5, 'MEMILIKI\nPROFIL\nGURU', 8, 4)
draw_relationship(25, 77.5, 'MEMILIKI\nPROFIL\nSISWA', 8, 4)

# Class and department relationships
draw_relationship(57.5, 77.5, 'BELAJAR\nDI', 6, 3)
draw_relationship(57.5, 85, 'MEMILIKI\nJURUSAN', 6, 3)

# Schedule relationships
draw_relationship(32.5, 62.5, 'MENGAJAR\nDI', 6, 3)
draw_relationship(57.5, 62.5, 'DIJADWALKAN\nDI', 6, 3)
draw_relationship(67.5, 62.5, 'MATA_PELAJARAN\nJADWAL', 8, 3)

# Learning activity relationships
draw_relationship(37.5, 37.5, 'ABSEN\nSISWA', 6, 3)
draw_relationship(50, 37.5, 'NILAI\nJADWAL', 6, 3)
draw_relationship(62.5, 37.5, 'MATERI\nJADWAL', 6, 3)

# Assignment relationships
draw_relationship(42.5, 17.5, 'TUGAS\nJADWAL', 6, 3)
draw_relationship(50, 17.5, 'PENGUMPULAN', 6, 3)

# ============= DRAW CONNECTION LINES =============
# User to profile connections
draw_line(users_pos, (15, 77.5))
draw_line((15, 77.5), guru_pos)
draw_line(users_pos, (25, 77.5))
draw_line((25, 77.5), siswa_pos)

# Class relationships
draw_line(siswa_pos, (57.5, 77.5))
draw_line((57.5, 77.5), kelas_pos)
draw_line(kelas_pos, (57.5, 85))
draw_line((57.5, 85), jurusan_pos)

# Schedule connections
draw_line(guru_pos, (32.5, 62.5))
draw_line((32.5, 62.5), jadwal_pos)
draw_line(kelas_pos, (57.5, 62.5))
draw_line((57.5, 62.5), jadwal_pos)
draw_line(jadwal_pos, (67.5, 62.5))
draw_line((67.5, 62.5), mata_pelajaran_pos)

# Learning activities
draw_line(siswa_pos, (37.5, 37.5))
draw_line((37.5, 37.5), absensi_pos)
draw_line(jadwal_pos, (50, 37.5))
draw_line((50, 37.5), nilai_pos)
draw_line(jadwal_pos, (62.5, 37.5))
draw_line((62.5, 37.5), materi_pos)

# Assignment system
draw_line(jadwal_pos, (42.5, 17.5))
draw_line((42.5, 17.5), tugas_pos)
draw_line(tugas_pos, (50, 17.5))
draw_line((50, 17.5), pengumpulan_tugas_pos)
draw_line(siswa_pos, pengumpulan_tugas_pos)

# ============= LEGEND =============
legend_x = 5
legend_y = 15

ax.text(legend_x, legend_y, 'Legend:', fontsize=12, fontweight='bold', color=TEXT_COLOR)

# Entity legend
entity_legend = patches.Ellipse((legend_x + 2, legend_y - 3), 3, 1.5, 
                               facecolor=ENTITY_COLOR, edgecolor=ENTITY_BORDER, linewidth=2)
ax.add_patch(entity_legend)
ax.text(legend_x + 5, legend_y - 3, 'Entity', fontsize=10, color=TEXT_COLOR)

# Attribute legend
attr_legend = FancyBboxPatch((legend_x + 1, legend_y - 5.5), 2, 1,
                           boxstyle="round,pad=0.1",
                           facecolor=ATTRIBUTE_COLOR, edgecolor=ATTRIBUTE_BORDER, linewidth=1.5)
ax.add_patch(attr_legend)
ax.text(legend_x + 5, legend_y - 5, 'Attribute', fontsize=10, color=TEXT_COLOR)

# Primary key legend
pk_legend = FancyBboxPatch((legend_x + 1, legend_y - 7.5), 2, 1,
                         boxstyle="round,pad=0.1",
                         facecolor=PRIMARY_KEY_COLOR, edgecolor=PRIMARY_KEY_BORDER, linewidth=1.5)
ax.add_patch(pk_legend)
ax.text(legend_x + 5, legend_y - 7, 'Primary Key', fontsize=10, color=TEXT_COLOR)

# Relationship legend
rel_x = [legend_x + 2, legend_x + 3, legend_x + 2, legend_x + 1, legend_x + 2]
rel_y = [legend_y - 8.5, legend_y - 9, legend_y - 9.5, legend_y - 9, legend_y - 8.5]
rel_legend = patches.Polygon(list(zip(rel_x, rel_y)),
                           facecolor=RELATIONSHIP_COLOR, edgecolor=RELATIONSHIP_BORDER, linewidth=2)
ax.add_patch(rel_legend)
ax.text(legend_x + 5, legend_y - 9, 'Relationship', fontsize=10, color=TEXT_COLOR)

plt.tight_layout()
plt.savefig('erd_professional_clean.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('erd_professional_clean.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')

print("ERD profesional telah dibuat:")
print("- erd_professional_clean.png")
print("- erd_professional_clean.pdf")