# E-Learning SMK - System Diagrams

## 📋 Daftar Diagram

1. **Use Case Diagram** - Menunjukkan interaksi antara aktor dengan sistem
2. **Activity Diagram** - Menggambarkan alur proses login
3. **Context Diagram** - Sistem dalam konteks lingkungan eksternal
4. **Hierarchical Diagram** - Struktur modul sistem secara berjenjang
5. **DAD Level 1** - Data Flow Diagram tingkat pertama
6. **ERD** - Entity Relationship Diagram struktur database

## 📥 Cara Download PNG/PDF

### Metode 1: Menggunakan Browser (Recommended)
1. Buka file `all_diagrams.html` di browser
2. Klik kanan pada diagram → "Save image as..." → Simpan sebagai PNG
3. Untuk PDF: Ctrl+P (Print) → Save as PDF

### Metode 2: File Individual
- Buka file HTML individual untuk setiap diagram
- Gunakan cara yang sama untuk save sebagai PNG/PDF

### Metode 3: Menggunakan Mermaid CLI
```bash
# Install Mermaid CLI
npm install -g @mermaid-js/mermaid-cli

# Konversi ke PNG
mmdc -i diagram.mmd -o diagram.png

# Konversi ke PDF
mmdc -i diagram.mmd -o diagram.pdf
```

## 📁 File yang Tersedia

- `all_diagrams.html` - Semua diagram dalam satu file
- `*.html` - File HTML individual untuk setiap diagram
- `*.mmd` - Source code Mermaid untuk setiap diagram
- `README.md` - File ini

## 🛠️ Edit Diagram

Untuk mengedit diagram:
1. Edit file `.mmd` yang sesuai
2. Regenerate file HTML menggunakan script Python
3. Atau gunakan Mermaid Live Editor: https://mermaid.live/

## 📝 Catatan

- Semua diagram dibuat menggunakan Mermaid
- Diagram dapat di-render di browser modern
- Source code tersedia dalam format `.mmd`
- File HTML dapat dibuka offline setelah pertama kali load
