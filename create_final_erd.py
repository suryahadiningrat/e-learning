import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch
import numpy as np

# Set up the figure with optimal size
fig, ax = plt.subplots(1, 1, figsize=(28, 20))
ax.set_xlim(0, 140)
ax.set_ylim(0, 100)
ax.set_aspect('equal')
ax.axis('off')

# Professional color scheme
COLORS = {
    'entity': '#E3F2FD',      # Light blue
    'entity_border': '#1976D2', # Blue
    'attribute': '#FAFAFA',    # Very light gray
    'attribute_border': '#757575', # Gray
    'primary_key': '#FFF3E0',  # Light orange
    'primary_key_border': '#FF9800', # Orange
    'relationship': '#FCE4EC', # Light pink
    'relationship_border': '#E91E63', # Pink
    'text': '#263238',         # Dark blue-gray
    'line': '#546E7A'          # Blue-gray
}

def draw_entity(x, y, name, width=16, height=8):
    """Draw an entity (oval)"""
    entity = patches.Ellipse((x, y), width, height, 
                           facecolor=COLORS['entity'], 
                           edgecolor=COLORS['entity_border'], 
                           linewidth=2.5)
    ax.add_patch(entity)
    ax.text(x, y, name, ha='center', va='center', 
            fontsize=12, fontweight='bold', color=COLORS['text'])
    return entity

def draw_attribute(x, y, name, is_primary=False, width=10, height=4):
    """Draw an attribute (rounded rectangle)"""
    if is_primary:
        color = COLORS['primary_key']
        border_color = COLORS['primary_key_border']
        fontweight = 'bold'
    else:
        color = COLORS['attribute']
        border_color = COLORS['attribute_border']
        fontweight = 'normal'
    
    attr = FancyBboxPatch((x-width/2, y-height/2), width, height,
                         boxstyle="round,pad=0.2",
                         facecolor=color,
                         edgecolor=border_color,
                         linewidth=1.8)
    ax.add_patch(attr)
    
    # Underline primary keys
    if is_primary:
        ax.text(x, y, name, ha='center', va='center', 
                fontsize=10, fontweight=fontweight, color=COLORS['text'])
        ax.plot([x-len(name)*0.3, x+len(name)*0.3], [y-0.8, y-0.8], 
                color=COLORS['primary_key_border'], linewidth=2)
    else:
        ax.text(x, y, name, ha='center', va='center', 
                fontsize=10, fontweight=fontweight, color=COLORS['text'])
    
    return attr

def draw_relationship(x, y, name, width=12, height=6):
    """Draw a relationship (diamond)"""
    diamond_x = [x, x+width/2, x, x-width/2, x]
    diamond_y = [y+height/2, y, y-height/2, y, y+height/2]
    
    relationship = patches.Polygon(list(zip(diamond_x, diamond_y)),
                                 facecolor=COLORS['relationship'],
                                 edgecolor=COLORS['relationship_border'],
                                 linewidth=2.5)
    ax.add_patch(relationship)
    ax.text(x, y, name, ha='center', va='center', 
            fontsize=9, fontweight='bold', color=COLORS['text'])
    return relationship

def draw_line(start_pos, end_pos, style='-', color=None, linewidth=2):
    """Draw a connection line"""
    if color is None:
        color = COLORS['line']
    ax.plot([start_pos[0], end_pos[0]], [start_pos[1], end_pos[1]], 
            style, color=color, linewidth=linewidth)

# Title
ax.text(70, 95, 'Entity Relationship Diagram - E-Learning SMK System', 
        ha='center', va='center', fontsize=20, fontweight='bold', color=COLORS['text'])

# ============= ORGANIZED LAYOUT =============
# Row 1: Core System Entities (Top)
users_pos = (20, 85)
jurusan_pos = (70, 85)
mata_pelajaran_pos = (120, 85)

# Row 2: User Profile Entities
guru_pos = (20, 70)
siswa_pos = (45, 70)
kelas_pos = (95, 70)

# Row 3: Academic Core
jadwal_pos = (70, 55)
tahun_akademik_pos = (20, 40)
settings_pos = (120, 40)

# Row 4: Learning Activities
absensi_pos = (35, 25)
nilai_pos = (70, 25)
materi_pos = (105, 25)

# Row 5: Assignment System
tugas_pos = (50, 10)
pengumpulan_tugas_pos = (90, 10)

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

# ============= DRAW ATTRIBUTES WITH BETTER SPACING =============
# USERS attributes (left side)
draw_attribute(20, 92, 'id', True)
draw_attribute(10, 89, 'username')
draw_attribute(30, 89, 'email')
draw_attribute(10, 81, 'password')
draw_attribute(30, 81, 'full_name')
draw_attribute(20, 78, 'role')

# JURUSAN attributes (center top)
draw_attribute(70, 92, 'id', True)
draw_attribute(60, 89, 'nama_jurusan')
draw_attribute(80, 89, 'kode_jurusan')

# MATA_PELAJARAN attributes (right side)
draw_attribute(120, 92, 'id', True)
draw_attribute(110, 89, 'kode')
draw_attribute(130, 89, 'nama')
draw_attribute(110, 81, 'deskripsi')
draw_attribute(130, 81, 'status')

# GURU attributes
draw_attribute(20, 77, 'id', True)
draw_attribute(10, 74, 'user_id')
draw_attribute(30, 74, 'nip')
draw_attribute(5, 67, 'bidang_studi')
draw_attribute(25, 67, 'jenis_kelamin')
draw_attribute(10, 63, 'tempat_lahir')
draw_attribute(30, 63, 'tanggal_lahir')

# SISWA attributes
draw_attribute(45, 77, 'id', True)
draw_attribute(35, 74, 'user_id')
draw_attribute(55, 74, 'nis')
draw_attribute(35, 67, 'nisn')
draw_attribute(55, 67, 'kelas_id')
draw_attribute(35, 63, 'jenis_kelamin')
draw_attribute(55, 63, 'tempat_lahir')

# KELAS attributes
draw_attribute(95, 77, 'id', True)
draw_attribute(85, 74, 'nama_kelas')
draw_attribute(105, 74, 'jurusan_id')
draw_attribute(85, 67, 'tingkat')
draw_attribute(105, 67, 'kapasitas')

# JADWAL attributes (center)
draw_attribute(70, 62, 'id', True)
draw_attribute(60, 59, 'kelas_id')
draw_attribute(80, 59, 'guru_id')
draw_attribute(55, 52, 'mata_pelajaran')
draw_attribute(85, 52, 'hari')
draw_attribute(60, 48, 'jam_mulai')
draw_attribute(80, 48, 'jam_selesai')

# TAHUN_AKADEMIK attributes
draw_attribute(20, 47, 'id', True)
draw_attribute(10, 44, 'tahun_akademik')
draw_attribute(30, 44, 'semester')
draw_attribute(5, 36, 'tanggal_mulai')
draw_attribute(25, 36, 'tanggal_selesai')
draw_attribute(20, 33, 'is_active')

# SETTINGS attributes
draw_attribute(120, 47, 'id', True)
draw_attribute(110, 44, 'key')
draw_attribute(130, 44, 'value')

# ABSENSI attributes
draw_attribute(35, 32, 'id', True)
draw_attribute(25, 29, 'siswa_id')
draw_attribute(45, 29, 'jadwal_id')
draw_attribute(25, 21, 'tanggal')
draw_attribute(45, 21, 'status')
draw_attribute(35, 18, 'keterangan')

# NILAI attributes
draw_attribute(70, 32, 'id', True)
draw_attribute(60, 29, 'siswa_id')
draw_attribute(80, 29, 'jadwal_id')
draw_attribute(55, 21, 'nilai_tugas')
draw_attribute(85, 21, 'nilai_ulangan')
draw_attribute(60, 18, 'nilai_uts')
draw_attribute(80, 18, 'nilai_uas')

# MATERI attributes
draw_attribute(105, 32, 'id', True)
draw_attribute(95, 29, 'jadwal_id')
draw_attribute(115, 29, 'judul')
draw_attribute(95, 21, 'konten')
draw_attribute(115, 21, 'file_path')

# TUGAS attributes
draw_attribute(50, 17, 'id', True)
draw_attribute(40, 14, 'nama_tugas')
draw_attribute(60, 14, 'jadwal_id')
draw_attribute(35, 6, 'deskripsi')
draw_attribute(55, 6, 'deadline')
draw_attribute(50, 3, 'created_by')

# PENGUMPULAN_TUGAS attributes
draw_attribute(90, 17, 'id', True)
draw_attribute(80, 14, 'tugas_id')
draw_attribute(100, 14, 'siswa_id')
draw_attribute(75, 6, 'link_tugas')
draw_attribute(95, 6, 'catatan')
draw_attribute(80, 3, 'status')
draw_attribute(100, 3, 'submitted_at')

# ============= DRAW RELATIONSHIPS =============
# User profile relationships
draw_relationship(20, 77.5, 'MEMILIKI\nPROFIL', 10, 5)
draw_relationship(32.5, 77.5, 'PROFIL\nSISWA', 8, 4)

# Academic relationships
draw_relationship(82.5, 77.5, 'BELAJAR\nDI', 8, 4)
draw_relationship(82.5, 85, 'MEMILIKI\nJURUSAN', 8, 4)

# Schedule relationships
draw_relationship(45, 62.5, 'MENGAJAR', 8, 4)
draw_relationship(82.5, 62.5, 'DIJADWALKAN', 8, 4)
draw_relationship(95, 70, 'MATA_PELAJARAN\nJADWAL', 10, 5)

# Learning activity relationships
draw_relationship(52.5, 37.5, 'ABSEN', 6, 3)
draw_relationship(70, 37.5, 'NILAI', 6, 3)
draw_relationship(87.5, 37.5, 'MATERI', 6, 3)

# Assignment relationships
draw_relationship(60, 17.5, 'TUGAS', 6, 3)
draw_relationship(70, 17.5, 'PENGUMPULAN', 8, 4)

# ============= DRAW CONNECTION LINES =============
# User to profiles
draw_line(users_pos, (20, 77.5))
draw_line((20, 77.5), guru_pos)
draw_line(users_pos, (32.5, 77.5))
draw_line((32.5, 77.5), siswa_pos)

# Class and department
draw_line(siswa_pos, (82.5, 77.5))
draw_line((82.5, 77.5), kelas_pos)
draw_line(kelas_pos, (82.5, 85))
draw_line((82.5, 85), jurusan_pos)

# Schedule connections
draw_line(guru_pos, (45, 62.5))
draw_line((45, 62.5), jadwal_pos)
draw_line(kelas_pos, (82.5, 62.5))
draw_line((82.5, 62.5), jadwal_pos)
draw_line(mata_pelajaran_pos, (95, 70))
draw_line((95, 70), jadwal_pos)

# Learning activities
draw_line(siswa_pos, (52.5, 37.5))
draw_line((52.5, 37.5), absensi_pos)
draw_line(jadwal_pos, (70, 37.5))
draw_line((70, 37.5), nilai_pos)
draw_line(jadwal_pos, (87.5, 37.5))
draw_line((87.5, 37.5), materi_pos)

# Assignment system
draw_line(jadwal_pos, (60, 17.5))
draw_line((60, 17.5), tugas_pos)
draw_line(tugas_pos, (70, 17.5))
draw_line((70, 17.5), pengumpulan_tugas_pos)
draw_line(siswa_pos, pengumpulan_tugas_pos)

# ============= LEGEND =============
legend_x = 5
legend_y = 15

ax.text(legend_x, legend_y, 'Legend:', fontsize=14, fontweight='bold', color=COLORS['text'])

# Entity legend
entity_legend = patches.Ellipse((legend_x + 4, legend_y - 4), 6, 3, 
                               facecolor=COLORS['entity'], 
                               edgecolor=COLORS['entity_border'], linewidth=2.5)
ax.add_patch(entity_legend)
ax.text(legend_x + 12, legend_y - 4, 'Entity', fontsize=12, color=COLORS['text'])

# Attribute legend
attr_legend = FancyBboxPatch((legend_x + 1, legend_y - 8.5), 6, 2,
                           boxstyle="round,pad=0.2",
                           facecolor=COLORS['attribute'], 
                           edgecolor=COLORS['attribute_border'], linewidth=1.8)
ax.add_patch(attr_legend)
ax.text(legend_x + 12, legend_y - 7.5, 'Attribute', fontsize=12, color=COLORS['text'])

# Primary key legend
pk_legend = FancyBboxPatch((legend_x + 1, legend_y - 12.5), 6, 2,
                         boxstyle="round,pad=0.2",
                         facecolor=COLORS['primary_key'], 
                         edgecolor=COLORS['primary_key_border'], linewidth=1.8)
ax.add_patch(pk_legend)
ax.text(legend_x + 12, legend_y - 11.5, 'Primary Key', fontsize=12, color=COLORS['text'])

# Relationship legend
rel_x = [legend_x + 4, legend_x + 7, legend_x + 4, legend_x + 1, legend_x + 4]
rel_y = [legend_y - 14.5, legend_y - 16.5, legend_y - 18.5, legend_y - 16.5, legend_y - 14.5]
rel_legend = patches.Polygon(list(zip(rel_x, rel_y)),
                           facecolor=COLORS['relationship'], 
                           edgecolor=COLORS['relationship_border'], linewidth=2.5)
ax.add_patch(rel_legend)
ax.text(legend_x + 12, legend_y - 16.5, 'Relationship', fontsize=12, color=COLORS['text'])

plt.tight_layout()
plt.savefig('erd_final_clean.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('erd_final_clean.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')

print("ERD final yang rapi telah dibuat:")
print("- erd_final_clean.png")
print("- erd_final_clean.pdf")