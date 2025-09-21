#!/usr/bin/env python3
"""
Script untuk generate file HTML dan MMD dari diagram Mermaid
"""

import os
from pathlib import Path

def extract_mermaid_diagrams(md_file):
    """Extract diagram Mermaid dari file markdown"""
    diagrams = []
    current_diagram = ""
    in_diagram = False
    diagram_name = ""
    
    with open(md_file, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    for i, line in enumerate(lines):
        line = line.strip()
        
        # Deteksi awal diagram
        if line.startswith('```mermaid'):
            in_diagram = True
            current_diagram = ""
            # Ambil nama diagram dari baris sebelumnya
            if i > 0:
                prev_line = lines[i-1].strip()
                if prev_line.startswith('##') or prev_line.startswith('###'):
                    diagram_name = prev_line.replace('#', '').strip()
            continue
        
        # Deteksi akhir diagram
        if line == '```' and in_diagram:
            in_diagram = False
            if current_diagram.strip():
                diagrams.append({
                    'name': diagram_name or f"diagram_{len(diagrams)+1}",
                    'content': current_diagram.strip()
                })
            current_diagram = ""
            diagram_name = ""
            continue
        
        # Kumpulkan konten diagram
        if in_diagram:
            current_diagram += line + '\n'
    
    return diagrams

def create_html_file(diagram, output_dir, name):
    """Buat file HTML untuk diagram"""
    html_content = f"""<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <style>
        body {{
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: white;
        }}
        .mermaid {{
            text-align: center;
        }}
        .title {{
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }}
    </style>
</head>
<body>
    <h1 class="title">{diagram['name']}</h1>
    <div class="mermaid">
{diagram['content']}
    </div>
    <script>
        mermaid.initialize({{ startOnLoad: true }});
    </script>
</body>
</html>"""
    
    html_file = output_dir / f"{name}.html"
    with open(html_file, 'w', encoding='utf-8') as f:
        f.write(html_content)
    
    return html_file

def create_master_html(diagrams, output_dir):
    """Buat file HTML master dengan semua diagram"""
    html_content = """<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning SMK - All Diagrams</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
            border-left: 4px solid #3498db;
            padding-left: 15px;
        }
        .diagram-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .mermaid {
            text-align: center;
            margin: 15px 0;
        }
        .info {
            background-color: #e8f4f8;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid #3498db;
        }
        .instructions {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .download-links {
            text-align: center;
            margin: 10px 0;
        }
        .download-links a {
            display: inline-block;
            margin: 5px 10px;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .download-links a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 E-Learning SMK - System Diagrams</h1>
        
        <div class="info">
            <strong>📋 Informasi:</strong> Dokumen ini berisi semua diagram sistem E-Learning SMK.
        </div>
        
        <div class="instructions">
            <strong>📥 Cara Download PNG/PDF:</strong>
            <ol>
                <li>Klik kanan pada diagram → "Save image as..." → Simpan sebagai PNG</li>
                <li>Untuk PDF: Ctrl+P (Print) → Save as PDF</li>
                <li>Atau gunakan file HTML individual untuk setiap diagram</li>
            </ol>
        </div>
"""
    
    for i, diagram in enumerate(diagrams, 1):
        safe_name = "".join(c for c in diagram['name'] if c.isalnum() or c in (' ', '-', '_')).rstrip()
        safe_name = safe_name.replace(' ', '_').lower()
        
        html_content += f"""
        <div class="diagram-section">
            <h2>{i}. {diagram['name']}</h2>
            <div class="mermaid">
{diagram['content']}
            </div>
            <div class="download-links">
                <a href="{safe_name}.html" target="_blank">📄 Buka HTML</a>
                <a href="{safe_name}.mmd" download>📝 Download Source</a>
            </div>
        </div>
        """
    
    html_content += """
        <div class="info">
            <strong>📝 Catatan:</strong> 
            <ul>
                <li>Semua diagram dibuat menggunakan Mermaid</li>
                <li>Klik kanan pada diagram untuk save sebagai gambar</li>
                <li>Gunakan Ctrl+P untuk print/save sebagai PDF</li>
                <li>File .mmd berisi source code yang dapat diedit</li>
            </ul>
        </div>
    </div>
    
    <script>
        mermaid.initialize({ 
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true
            }
        });
    </script>
</body>
</html>"""
    
    html_file = output_dir / "all_diagrams.html"
    with open(html_file, 'w', encoding='utf-8') as f:
        f.write(html_content)

def create_readme(output_dir):
    """Buat file README dengan instruksi"""
    readme_content = """# E-Learning SMK - System Diagrams

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
"""
    
    with open(output_dir / "README.md", 'w', encoding='utf-8') as f:
        f.write(readme_content)

def main():
    """Fungsi utama"""
    print("🎓 E-Learning SMK - Diagram Generator")
    print("=" * 50)
    
    # Setup paths
    script_dir = Path(__file__).parent
    md_file = script_dir / "all_diagrams.md"
    output_dir = script_dir / "diagrams_output"
    
    # Buat direktori output
    output_dir.mkdir(exist_ok=True)
    
    if not md_file.exists():
        print(f"❌ File {md_file} tidak ditemukan!")
        return
    
    print(f"📖 Membaca file: {md_file}")
    
    # Extract diagrams
    diagrams = extract_mermaid_diagrams(md_file)
    print(f"📊 Ditemukan {len(diagrams)} diagram")
    
    if not diagrams:
        print("❌ Tidak ada diagram Mermaid yang ditemukan!")
        return
    
    # Buat file untuk setiap diagram
    for i, diagram in enumerate(diagrams, 1):
        print(f"🔄 Memproses diagram {i}/{len(diagrams)}: {diagram['name']}")
        
        # Buat nama file yang aman
        safe_name = "".join(c for c in diagram['name'] if c.isalnum() or c in (' ', '-', '_')).rstrip()
        safe_name = safe_name.replace(' ', '_').lower()
        
        # Buat file .mmd
        mmd_file = output_dir / f"{safe_name}.mmd"
        with open(mmd_file, 'w', encoding='utf-8') as f:
            f.write(diagram['content'])
        
        # Buat file HTML
        html_file = create_html_file(diagram, output_dir, safe_name)
        
        print(f"✅ File dibuat: {safe_name}.mmd, {safe_name}.html")
    
    # Buat file master HTML
    create_master_html(diagrams, output_dir)
    
    # Buat README
    create_readme(output_dir)
    
    print(f"\n🎉 Generator selesai!")
    print(f"📁 Output tersimpan di: {output_dir}")
    print(f"🌐 Buka master HTML: {output_dir / 'all_diagrams.html'}")
    print(f"📖 Baca README: {output_dir / 'README.md'}")

if __name__ == "__main__":
    main()
