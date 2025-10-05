import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyBboxPatch, Rectangle
import numpy as np

# Set up the figure
# Create figure with much larger canvas for better spacing
fig, ax = plt.subplots(figsize=(40, 32))
ax.set_xlim(0, 200)
ax.set_ylim(0, 160)
ax.set_aspect('equal')
ax.axis('off')

# Professional color scheme matching traditional ERD
COLORS = {
    'entity': '#FFFFFF',           # White for entities
    'entity_border': '#000000',    # Black border
    'attribute': '#FFFFFF',        # White for attributes  
    'attribute_border': '#000000', # Black border
    'primary_key': '#FFFFFF',      # White for primary keys
    'primary_key_border': '#000000', # Black border
    'relationship': '#FFFFFF',     # White for relationships
    'relationship_border': '#000000', # Black border
    'text': '#000000',             # Black text
    'line': '#000000'              # Black lines
}

def draw_entity(x, y, name, width=12, height=6):
    """Draw an entity as rectangle"""
    entity = Rectangle((x-width/2, y-height/2), width, height,
                      facecolor=COLORS['entity'], 
                      edgecolor=COLORS['entity_border'], 
                      linewidth=2)
    ax.add_patch(entity)
    ax.text(x, y, name, ha='center', va='center', 
            fontsize=10, fontweight='bold', color=COLORS['text'])
    return entity

def draw_attribute(x, y, name, is_primary=False, width=8, height=4):
    """Draw an attribute as oval"""
    if is_primary:
        # Primary key with underline
        attr = patches.Ellipse((x, y), width, height,
                             facecolor=COLORS['primary_key'], 
                             edgecolor=COLORS['primary_key_border'], 
                             linewidth=2)
        ax.add_patch(attr)
        ax.text(x, y, name, ha='center', va='center', 
                fontsize=9, fontweight='bold', color=COLORS['text'])
        # Underline for primary key
        ax.plot([x-len(name)*0.25, x+len(name)*0.25], [y-0.6, y-0.6], 
                color=COLORS['primary_key_border'], linewidth=1.5)
    else:
        attr = patches.Ellipse((x, y), width, height,
                             facecolor=COLORS['attribute'], 
                             edgecolor=COLORS['attribute_border'], 
                             linewidth=2)
        ax.add_patch(attr)
        ax.text(x, y, name, ha='center', va='center', 
                fontsize=9, color=COLORS['text'])
    return attr

def draw_relationship(x, y, name, width=10, height=6):
    """Draw a relationship as diamond"""
    diamond_x = [x, x+width/2, x, x-width/2, x]
    diamond_y = [y+height/2, y, y-height/2, y, y+height/2]
    
    relationship = patches.Polygon(list(zip(diamond_x, diamond_y)),
                                 facecolor=COLORS['relationship'],
                                 edgecolor=COLORS['relationship_border'],
                                 linewidth=2)
    ax.add_patch(relationship)
    ax.text(x, y, name, ha='center', va='center', 
            fontsize=8, fontweight='bold', color=COLORS['text'])
    return relationship

def draw_line(start_pos, end_pos, style='-', color=None, linewidth=1.5):
    """Draw a connection line"""
    if color is None:
        color = COLORS['line']
    ax.plot([start_pos[0], end_pos[0]], [start_pos[1], end_pos[1]], 
            style, color=color, linewidth=linewidth, zorder=0)

# Title with updated position for larger canvas
ax.text(100, 150, 'Entity Relationship Diagram - E-Learning SMK System', 
        ha='center', va='center', fontsize=24, fontweight='bold', color=COLORS['text'])

# ============= MAIN ENTITIES POSITIONING WITH MUCH WIDER SPACING =============
# Row 1: Core entities (much wider horizontal spacing)
users_pos = (30, 130)
jurusan_pos = (100, 130) 
mata_pelajaran_pos = (170, 130)

# Row 2: User profiles (much wider spacing)
guru_pos = (30, 105)
siswa_pos = (100, 105)
kelas_pos = (170, 105)

# Row 3: Academic activities (much wider spacing)
jadwal_pos = (65, 80)
tahun_akademik_pos = (30, 55)
settings_pos = (170, 55)

# Row 4: Learning activities (much wider spacing)
absensi_pos = (30, 30)
nilai_pos = (100, 30)
materi_pos = (170, 30)

# Row 5: Assignment system (much wider spacing)
tugas_pos = (65, 5)
pengumpulan_tugas_pos = (135, 5)

# ============= DRAW CONNECTION LINES FIRST (BEHIND) =============
# User profile connections - updated for new layout
draw_line(users_pos, (30, 117.5))  # USERS to relationship
draw_line((30, 117.5), guru_pos)   # relationship to GURU
draw_line(users_pos, (65, 117.5))  # USERS to relationship
draw_line((65, 117.5), siswa_pos)  # relationship to SISWA

# Class and department connections - updated for new layout
draw_line(siswa_pos, (135, 105))  # SISWA to relationship
draw_line((135, 105), kelas_pos)  # relationship to KELAS
draw_line(kelas_pos, (135, 117.5))  # KELAS to relationship
draw_line((135, 117.5), jurusan_pos) # relationship to JURUSAN

# Schedule connections - updated for new layout
draw_line(guru_pos, (47.5, 92.5))   # GURU to relationship
draw_line((47.5, 92.5), jadwal_pos) # relationship to JADWAL
draw_line(kelas_pos, (117.5, 92.5))  # KELAS to relationship
draw_line((117.5, 92.5), jadwal_pos) # relationship to JADWAL
draw_line(mata_pelajaran_pos, (117.5, 105)) # MATA_PELAJARAN to relationship
draw_line((117.5, 105), jadwal_pos)    # relationship to JADWAL

# Learning activity connections - updated for new layout
draw_line(siswa_pos, (65, 67.5))  # SISWA to relationship
draw_line((65, 67.5), absensi_pos) # relationship to ABSENSI
draw_line(jadwal_pos, (82.5, 55))    # JADWAL to relationship
draw_line((82.5, 55), nilai_pos)     # relationship to NILAI
draw_line(jadwal_pos, (117.5, 55))    # JADWAL to relationship
draw_line((117.5, 55), materi_pos)    # relationship to MATERI

# Assignment connections - updated for new layout
draw_line(jadwal_pos, (65, 42.5)) # JADWAL to relationship
draw_line((65, 42.5), tugas_pos)  # relationship to TUGAS
draw_line(tugas_pos, (100, 5))   # TUGAS to relationship
draw_line((100, 5), pengumpulan_tugas_pos) # relationship to PENGUMPULAN_TUGAS
draw_line(siswa_pos, pengumpulan_tugas_pos) # SISWA to PENGUMPULAN_TUGAS

# ============= DRAW ENTITIES =============
draw_entity(*users_pos, 'USERS')
draw_entity(*jurusan_pos, 'JURUSAN')
draw_entity(*mata_pelajaran_pos, 'MATA_PELAJARAN', 14, 6)
draw_entity(*guru_pos, 'GURU')
draw_entity(*siswa_pos, 'SISWA')
draw_entity(*kelas_pos, 'KELAS')
draw_entity(*jadwal_pos, 'JADWAL')
draw_entity(*tahun_akademik_pos, 'TAHUN_AKADEMIK', 14, 6)
draw_entity(*settings_pos, 'SETTINGS')
draw_entity(*absensi_pos, 'ABSENSI')
draw_entity(*nilai_pos, 'NILAI')
draw_entity(*materi_pos, 'MATERI')
draw_entity(*tugas_pos, 'TUGAS')
draw_entity(*pengumpulan_tugas_pos, 'PENGUMPULAN_TUGAS', 16, 6)

# ============= DRAW RELATIONSHIPS =============
# Relationship positions (updated for new wider spacing)
# Between entities relationships
memiliki_login_pos = (30, 117.5)
memiliki_profil_pos = (65, 117.5)
mengajar_pos = (47.5, 92.5)
belajar_pos = (135, 105)
mengatur_jadwal_pos = (117.5, 92.5)
mencatat_absensi_pos = (65, 67.5)
memberikan_nilai_pos = (82.5, 55)
menyediakan_materi_pos = (117.5, 55)
memberikan_tugas_pos = (65, 42.5)
mengumpulkan_tugas_pos = (100, 5)

draw_relationship(*memiliki_login_pos, 'MEMILIKI')
draw_relationship(*memiliki_profil_pos, 'PROFIL')
draw_relationship(*mengajar_pos, 'MENGAJAR')
draw_relationship(*belajar_pos, 'BELAJAR_DI')
draw_relationship(*mengatur_jadwal_pos, 'DIJADWALKAN')
draw_relationship(*mencatat_absensi_pos, 'ABSEN')
draw_relationship(*memberikan_nilai_pos, 'DINILAI')
draw_relationship(*menyediakan_materi_pos, 'MATERI')
draw_relationship(*memberikan_tugas_pos, 'TUGAS')
draw_relationship(*mengumpulkan_tugas_pos, 'KUMPUL')

# ============= DRAW ATTRIBUTES =============
# USERS attributes (updated positions for new layout)
draw_attribute(15, 140, 'user_id', True)  # Primary key
draw_attribute(10, 135, 'username')
draw_attribute(10, 125, 'password')
draw_attribute(45, 140, 'email')
draw_attribute(50, 135, 'role')
draw_attribute(50, 125, 'created_at')

# JURUSAN attributes (updated positions for new layout)
draw_attribute(85, 140, 'jurusan_id', True)  # Primary key
draw_attribute(80, 135, 'nama_jurusan')
draw_attribute(115, 140, 'kode_jurusan')

# MATA_PELAJARAN attributes (updated positions for new layout)
draw_attribute(155, 140, 'mapel_id', True)  # Primary key
draw_attribute(150, 135, 'nama_mapel')
draw_attribute(185, 140, 'kode_mapel')
draw_attribute(185, 135, 'sks')
draw_attribute(185, 125, 'deskripsi')

# GURU attributes (updated positions for new layout)
draw_attribute(15, 115, 'guru_id', True)  # Primary key
draw_attribute(10, 110, 'nip')
draw_attribute(10, 100, 'nama_guru')
draw_attribute(45, 115, 'alamat')
draw_attribute(50, 110, 'telepon')

# SISWA attributes (updated positions for new layout)
draw_attribute(85, 115, 'siswa_id', True)  # Primary key
draw_attribute(80, 110, 'nis')
draw_attribute(80, 100, 'nama_siswa')
draw_attribute(115, 115, 'alamat')
draw_attribute(120, 110, 'telepon')
draw_attribute(120, 100, 'tanggal_lahir')

# KELAS attributes (updated positions for new layout)
draw_attribute(155, 115, 'kelas_id', True)  # Primary key
draw_attribute(150, 110, 'nama_kelas')
draw_attribute(150, 100, 'tingkat')
draw_attribute(185, 115, 'kapasitas')
draw_attribute(190, 110, 'wali_kelas')

# JADWAL attributes (updated positions for new layout)
draw_attribute(50, 90, 'jadwal_id', True)  # Primary key
draw_attribute(45, 85, 'hari')
draw_attribute(45, 75, 'jam_mulai')
draw_attribute(80, 90, 'jam_selesai')
draw_attribute(85, 85, 'ruangan')
draw_attribute(85, 75, 'semester')
draw_attribute(95, 85, 'tahun_ajaran')

# TAHUN_AKADEMIK attributes (updated positions for new layout)
draw_attribute(15, 65, 'tahun_id', True)  # Primary key
draw_attribute(10, 60, 'tahun_mulai')
draw_attribute(10, 50, 'tahun_selesai')
draw_attribute(45, 65, 'semester')
draw_attribute(50, 60, 'status')
draw_attribute(50, 50, 'created_at')

# SETTINGS attributes (updated positions for new layout)
draw_attribute(155, 65, 'setting_id', True)  # Primary key
draw_attribute(150, 60, 'nama_setting')
draw_attribute(150, 50, 'nilai_setting')
draw_attribute(185, 65, 'deskripsi')
draw_attribute(190, 60, 'tipe_data')
draw_attribute(190, 50, 'kategori')

# ABSENSI attributes (updated positions for new layout)
draw_attribute(15, 40, 'absensi_id', True)  # Primary key
draw_attribute(10, 35, 'tanggal')
draw_attribute(10, 25, 'status')
draw_attribute(45, 40, 'keterangan')
draw_attribute(50, 35, 'jam_masuk')
draw_attribute(50, 25, 'jam_keluar')

# NILAI attributes (updated positions for new layout)
draw_attribute(85, 40, 'nilai_id', True)  # Primary key
draw_attribute(80, 35, 'nilai_tugas')
draw_attribute(80, 25, 'nilai_uts')
draw_attribute(115, 40, 'nilai_uas')
draw_attribute(120, 35, 'nilai_akhir')
draw_attribute(120, 25, 'grade')
draw_attribute(135, 35, 'tanggal_input')

# MATERI attributes (updated positions for new layout)
draw_attribute(155, 40, 'materi_id', True)  # Primary key
draw_attribute(150, 35, 'judul_materi')
draw_attribute(150, 25, 'deskripsi')
draw_attribute(185, 40, 'file_path')
draw_attribute(190, 35, 'tanggal_upload')
draw_attribute(190, 25, 'ukuran_file')
draw_attribute(205, 35, 'tipe_file')

# TUGAS attributes (updated positions for new layout)
draw_attribute(50, 15, 'tugas_id', True)  # Primary key
draw_attribute(45, 10, 'judul_tugas')
draw_attribute(45, 0, 'deskripsi')
draw_attribute(80, 15, 'tanggal_buat')
draw_attribute(85, 10, 'deadline')
draw_attribute(85, 0, 'bobot_nilai')
draw_attribute(100, 10, 'status')

# PENGUMPULAN_TUGAS attributes (updated positions for new layout)
draw_attribute(120, 15, 'pengumpulan_id', True)  # Primary key
draw_attribute(115, 10, 'tanggal_kumpul')
draw_attribute(115, 0, 'file_jawaban')
draw_attribute(150, 15, 'status_pengumpulan')
draw_attribute(155, 10, 'nilai_tugas')
draw_attribute(155, 0, 'komentar')
draw_attribute(170, 10, 'tanggal_koreksi')

# Connect attributes to entities with lines (updated for new positions)
# USERS attribute connections
draw_line(users_pos, (15, 140))
draw_line(users_pos, (10, 135))
draw_line(users_pos, (10, 125))
draw_line(users_pos, (45, 140))
draw_line(users_pos, (50, 135))
draw_line(users_pos, (50, 125))

# JURUSAN attribute connections
draw_line(jurusan_pos, (85, 140))
draw_line(jurusan_pos, (80, 135))
draw_line(jurusan_pos, (115, 140))

# MATA_PELAJARAN attribute connections
draw_line(mata_pelajaran_pos, (155, 140))
draw_line(mata_pelajaran_pos, (150, 135))
draw_line(mata_pelajaran_pos, (185, 140))
draw_line(mata_pelajaran_pos, (185, 135))
draw_line(mata_pelajaran_pos, (185, 125))

# GURU attribute connections
draw_line(guru_pos, (15, 115))
draw_line(guru_pos, (10, 110))
draw_line(guru_pos, (10, 100))
draw_line(guru_pos, (45, 115))
draw_line(guru_pos, (50, 110))

# SISWA attribute connections
draw_line(siswa_pos, (85, 115))
draw_line(siswa_pos, (80, 110))
draw_line(siswa_pos, (80, 100))
draw_line(siswa_pos, (115, 115))
draw_line(siswa_pos, (120, 110))
draw_line(siswa_pos, (120, 100))

# KELAS attribute connections
draw_line(kelas_pos, (155, 115))
draw_line(kelas_pos, (150, 110))
draw_line(kelas_pos, (150, 100))
draw_line(kelas_pos, (185, 115))
draw_line(kelas_pos, (190, 110))

# JADWAL attribute connections
draw_line(jadwal_pos, (50, 90))
draw_line(jadwal_pos, (45, 85))
draw_line(jadwal_pos, (45, 75))
draw_line(jadwal_pos, (80, 90))
draw_line(jadwal_pos, (85, 85))
draw_line(jadwal_pos, (85, 75))
draw_line(jadwal_pos, (95, 85))

# TAHUN_AKADEMIK attribute connections
draw_line(tahun_akademik_pos, (15, 65))
draw_line(tahun_akademik_pos, (10, 60))
draw_line(tahun_akademik_pos, (10, 50))
draw_line(tahun_akademik_pos, (45, 65))
draw_line(tahun_akademik_pos, (50, 60))
draw_line(tahun_akademik_pos, (50, 50))

# SETTINGS attribute connections
draw_line(settings_pos, (155, 65))
draw_line(settings_pos, (150, 60))
draw_line(settings_pos, (150, 50))
draw_line(settings_pos, (185, 65))
draw_line(settings_pos, (190, 60))
draw_line(settings_pos, (190, 50))

# ABSENSI attribute connections
draw_line(absensi_pos, (15, 40))
draw_line(absensi_pos, (10, 35))
draw_line(absensi_pos, (10, 25))
draw_line(absensi_pos, (45, 40))
draw_line(absensi_pos, (50, 35))
draw_line(absensi_pos, (50, 25))

# NILAI attribute connections
draw_line(nilai_pos, (85, 40))
draw_line(nilai_pos, (80, 35))
draw_line(nilai_pos, (80, 25))
draw_line(nilai_pos, (115, 40))
draw_line(nilai_pos, (120, 35))
draw_line(nilai_pos, (120, 25))
draw_line(nilai_pos, (135, 35))

# MATERI attribute connections
draw_line(materi_pos, (155, 40))
draw_line(materi_pos, (150, 35))
draw_line(materi_pos, (150, 25))
draw_line(materi_pos, (185, 40))
draw_line(materi_pos, (190, 35))
draw_line(materi_pos, (190, 25))
draw_line(materi_pos, (205, 35))

# TUGAS attribute connections
draw_line(tugas_pos, (50, 15))
draw_line(tugas_pos, (45, 10))
draw_line(tugas_pos, (45, 0))
draw_line(tugas_pos, (80, 15))
draw_line(tugas_pos, (85, 10))
draw_line(tugas_pos, (85, 0))
draw_line(tugas_pos, (100, 10))

# PENGUMPULAN_TUGAS attribute connections
draw_line(pengumpulan_tugas_pos, (120, 15))
draw_line(pengumpulan_tugas_pos, (115, 10))
draw_line(pengumpulan_tugas_pos, (115, 0))
draw_line(pengumpulan_tugas_pos, (150, 15))
draw_line(pengumpulan_tugas_pos, (155, 10))
draw_line(pengumpulan_tugas_pos, (155, 0))
draw_line(pengumpulan_tugas_pos, (170, 10))

plt.tight_layout()
plt.savefig('erd_traditional_style.png', dpi=300, bbox_inches='tight', 
            facecolor='white', edgecolor='none')
plt.savefig('erd_traditional_style.pdf', bbox_inches='tight', 
            facecolor='white', edgecolor='none')

print("ERD dengan gaya tradisional telah dibuat:")
print("- erd_traditional_style.png")
print("- erd_traditional_style.pdf")