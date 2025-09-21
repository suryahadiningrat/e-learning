#!/usr/bin/env python3
"""
Script untuk mengkonversi diagram Mermaid ke PNG dan PDF
Menggunakan Mermaid CLI dan Puppeteer
"""

import os
import subprocess
import sys
import json
from pathlib import Path

def check_dependencies():
    """Cek apakah dependencies yang diperlukan sudah terinstall"""
    try:
        # Cek Node.js
        result = subprocess.run(['node', '--version'], capture_output=True, text=True)
        if result.returncode != 0:
            print("❌ Node.js tidak ditemukan. Silakan install Node.js terlebih dahulu.")
            return False
        print(f"✅ Node.js ditemukan: {result.stdout.strip()}")
        
        # Cek npm
        result = subprocess.run(['npm', '--version'], capture_output=True, text=True)
        if result.returncode != 0:
            print("❌ npm tidak ditemukan. Silakan install npm terlebih dahulu.")
            return False
        print(f"✅ npm ditemukan: {result.returncode}")
        
        return True
    except FileNotFoundError:
        print("❌ Node.js atau npm tidak ditemukan. Silakan install Node.js terlebih dahulu.")
        return False

def install_mermaid_cli():
    """Install Mermaid CLI jika belum terinstall"""
    try:
        # Cek apakah @mermaid-js/mermaid-cli sudah terinstall
        result = subprocess.run(['npx', '@mermaid-js/mermaid-cli', '--version'], 
                              capture_output=True, text=True)
        if result.returncode == 0:
            print("✅ Mermaid CLI sudah terinstall")
            return True
    except:
        pass
    
    print("📦 Menginstall Mermaid CLI...")
    try:
        result = subprocess.run(['npm', 'install', '-g', '@mermaid-js/mermaid-cli'], 
                              capture_output=True, text=True)
        if result.returncode == 0:
            print("✅ Mermaid CLI berhasil diinstall")
            return True
        else:
            print(f"❌ Gagal menginstall Mermaid CLI: {result.stderr}")
            return False
    except Exception as e:
        print(f"❌ Error menginstall Mermaid CLI: {e}")
        return False

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

def create_mermaid_file(diagram, output_dir):
    """Buat file .mmd untuk diagram"""
    safe_name = "".join(c for c in diagram['name'] if c.isalnum() or c in (' ', '-', '_')).rstrip()
    safe_name = safe_name.replace(' ', '_').lower()
    
    mmd_file = output_dir / f"{safe_name}.mmd"
    
    with open(mmd_file, 'w', encoding='utf-8') as f:
        f.write(diagram['content'])
    
    return mmd_file, safe_name

def convert_to_png(mmd_file, output_dir, name):
    """Konversi file .mmd ke PNG"""
    png_file = output_dir / f"{name}.png"
    
    try:
        cmd = [
            'npx', '@mermaid-js/mermaid-cli',
            '-i', str(mmd_file),
            '-o', str(png_file),
            '-t', 'default',
            '-b', 'white'
        ]
        
        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ PNG berhasil dibuat: {png_file}")
            return True
        else:
            print(f"❌ Gagal membuat PNG: {result.stderr}")
            return False
    except Exception as e:
        print(f"❌ Error membuat PNG: {e}")
        return False

def convert_to_pdf(mmd_file, output_dir, name):
    """Konversi file .mmd ke PDF"""
    pdf_file = output_dir / f"{name}.pdf"
    
    try:
        cmd = [
            'npx', '@mermaid-js/mermaid-cli',
            '-i', str(mmd_file),
            '-o', str(pdf_file),
            '-t', 'default',
            '-b', 'white'
        ]
        
        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ PDF berhasil dibuat: {pdf_file}")
            return True
        else:
            print(f"❌ Gagal membuat PDF: {result.stderr}")
            return False
    except Exception as e:
        print(f"❌ Error membuat PDF: {e}")
        return False

def create_html_report(diagrams, output_dir):
    """Buat laporan HTML dengan semua diagram"""
    html_file = output_dir / "diagrams_report.html"
    
    html_content = """
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-Learning SMK - System Diagrams</title>
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
            .diagram-image {
                text-align: center;
                margin: 15px 0;
            }
            .diagram-image img {
                max-width: 100%;
                height: auto;
                border: 1px solid #ccc;
                border-radius: 5px;
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
            .info {
                background-color: #e8f4f8;
                padding: 10px;
                border-radius: 4px;
                margin: 10px 0;
                border-left: 4px solid #3498db;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎓 E-Learning SMK - System Diagrams</h1>
            
            <div class="info">
                <strong>📋 Informasi:</strong> Dokumen ini berisi semua diagram sistem E-Learning SMK yang telah dikonversi ke format PNG dan PDF.
            </div>
    """
    
    for i, diagram in enumerate(diagrams, 1):
        safe_name = "".join(c for c in diagram['name'] if c.isalnum() or c in (' ', '-', '_')).rstrip()
        safe_name = safe_name.replace(' ', '_').lower()
        
        html_content += f"""
            <div class="diagram-section">
                <h2>{i}. {diagram['name']}</h2>
                <div class="diagram-image">
                    <img src="{safe_name}.png" alt="{diagram['name']}">
                </div>
                <div class="download-links">
                    <a href="{safe_name}.png" download>📥 Download PNG</a>
                    <a href="{safe_name}.pdf" download>📄 Download PDF</a>
                </div>
            </div>
        """
    
    html_content += """
            <div class="info">
                <strong>📝 Catatan:</strong> 
                <ul>
                    <li>Semua diagram dibuat menggunakan Mermaid</li>
                    <li>PNG format untuk preview dan presentasi</li>
                    <li>PDF format untuk dokumentasi dan printing</li>
                    <li>Diagram dapat di-edit dengan mengubah file .mmd yang sesuai</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    """
    
    with open(html_file, 'w', encoding='utf-8') as f:
        f.write(html_content)
    
    print(f"✅ Laporan HTML berhasil dibuat: {html_file}")

def main():
    """Fungsi utama"""
    print("🎓 E-Learning SMK - Diagram Converter")
    print("=" * 50)
    
    # Cek dependencies
    if not check_dependencies():
        sys.exit(1)
    
    # Install Mermaid CLI
    if not install_mermaid_cli():
        sys.exit(1)
    
    # Setup paths
    script_dir = Path(__file__).parent
    md_file = script_dir / "all_diagrams.md"
    output_dir = script_dir / "diagrams_output"
    
    # Buat direktori output
    output_dir.mkdir(exist_ok=True)
    
    if not md_file.exists():
        print(f"❌ File {md_file} tidak ditemukan!")
        sys.exit(1)
    
    print(f"📖 Membaca file: {md_file}")
    
    # Extract diagrams
    diagrams = extract_mermaid_diagrams(md_file)
    print(f"📊 Ditemukan {len(diagrams)} diagram")
    
    if not diagrams:
        print("❌ Tidak ada diagram Mermaid yang ditemukan!")
        sys.exit(1)
    
    # Konversi setiap diagram
    success_count = 0
    for i, diagram in enumerate(diagrams, 1):
        print(f"\n🔄 Memproses diagram {i}/{len(diagrams)}: {diagram['name']}")
        
        # Buat file .mmd
        mmd_file, safe_name = create_mermaid_file(diagram, output_dir)
        
        # Konversi ke PNG
        png_success = convert_to_png(mmd_file, output_dir, safe_name)
        
        # Konversi ke PDF
        pdf_success = convert_to_pdf(mmd_file, output_dir, safe_name)
        
        if png_success and pdf_success:
            success_count += 1
    
    # Buat laporan HTML
    create_html_report(diagrams, output_dir)
    
    print(f"\n🎉 Konversi selesai!")
    print(f"✅ {success_count}/{len(diagrams)} diagram berhasil dikonversi")
    print(f"📁 Output tersimpan di: {output_dir}")
    print(f"🌐 Buka laporan HTML: {output_dir / 'diagrams_report.html'}")

if __name__ == "__main__":
    main()
