#!/usr/bin/env python3
"""
Script untuk mengkonversi diagram Mermaid ke PNG dan PDF
Menggunakan Mermaid CLI atau alternatif browser-based
"""

import os
import subprocess
import sys
from pathlib import Path

def check_node_npm():
    """Cek apakah Node.js dan npm tersedia"""
    try:
        # Cek Node.js
        result = subprocess.run(['node', '--version'], capture_output=True, text=True)
        if result.returncode != 0:
            return False, "Node.js tidak ditemukan"
        
        # Cek npm
        result = subprocess.run(['npm', '--version'], capture_output=True, text=True)
        if result.returncode != 0:
            return False, "npm tidak ditemukan"
        
        return True, "Node.js dan npm tersedia"
    except FileNotFoundError:
        return False, "Node.js atau npm tidak terinstall"

def install_mermaid_cli():
    """Install Mermaid CLI"""
    try:
        print("📦 Menginstall Mermaid CLI...")
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

def convert_with_mermaid_cli(mmd_file, output_dir, name):
    """Konversi menggunakan Mermaid CLI"""
    png_file = output_dir / f"{name}.png"
    pdf_file = output_dir / f"{name}.pdf"
    
    success_count = 0
    
    # Konversi ke PNG
    try:
        cmd = ['npx', '@mermaid-js/mermaid-cli', '-i', str(mmd_file), '-o', str(png_file)]
        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ PNG berhasil dibuat: {png_file}")
            success_count += 1
        else:
            print(f"❌ Gagal membuat PNG: {result.stderr}")
    except Exception as e:
        print(f"❌ Error membuat PNG: {e}")
    
    # Konversi ke PDF
    try:
        cmd = ['npx', '@mermaid-js/mermaid-cli', '-i', str(mmd_file), '-o', str(pdf_file)]
        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ PDF berhasil dibuat: {pdf_file}")
            success_count += 1
        else:
            print(f"❌ Gagal membuat PDF: {result.stderr}")
    except Exception as e:
        print(f"❌ Error membuat PDF: {e}")
    
    return success_count

def create_browser_instructions(output_dir):
    """Buat instruksi untuk konversi manual menggunakan browser"""
    instructions = """# Instruksi Konversi Manual ke PNG dan PDF

## Metode 1: Menggunakan Browser (Recommended)

### Untuk PNG:
1. Buka file HTML yang sesuai di browser
2. Klik kanan pada diagram
3. Pilih "Save image as..." atau "Simpan gambar sebagai..."
4. Simpan dengan nama yang diinginkan

### Untuk PDF:
1. Buka file HTML di browser
2. Tekan Ctrl+P (Windows/Linux) atau Cmd+P (Mac)
3. Pilih "Save as PDF" atau "Simpan sebagai PDF"
4. Atur pengaturan sesuai kebutuhan
5. Klik "Save" atau "Simpan"

## Metode 2: Menggunakan Mermaid Live Editor

1. Buka https://mermaid.live/
2. Copy kode dari file .mmd
3. Paste ke editor
4. Klik "Actions" → "Download PNG" atau "Download SVG"
5. Untuk PDF: Download SVG → Convert ke PDF menggunakan online converter

## Metode 3: Menggunakan Mermaid CLI

Jika Node.js sudah terinstall:

```bash
# Install Mermaid CLI
npm install -g @mermaid-js/mermaid-cli

# Konversi ke PNG
mmdc -i diagram.mmd -o diagram.png

# Konversi ke PDF
mmdc -i diagram.mmd -o diagram.pdf
```

## File yang Tersedia

- `all_diagrams.html` - Semua diagram dalam satu file
- `diagram_*.html` - File HTML individual untuk setiap diagram
- `diagram_*.mmd` - Source code Mermaid untuk setiap diagram

## Tips

- Gunakan browser Chrome/Edge untuk hasil terbaik
- Pastikan diagram ter-render dengan baik sebelum save
- Untuk diagram besar, gunakan zoom out browser
- PNG cocok untuk presentasi dan web
- PDF cocok untuk dokumentasi dan printing
"""
    
    with open(output_dir / "KONVERSI_MANUAL.txt", 'w', encoding='utf-8') as f:
        f.write(instructions)

def create_batch_script(output_dir):
    """Buat script batch untuk konversi otomatis"""
    batch_script = """@echo off
echo E-Learning SMK - Diagram Converter
echo ====================================

REM Cek apakah Node.js tersedia
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Node.js tidak ditemukan. Silakan install Node.js terlebih dahulu.
    pause
    exit /b 1
)

REM Install Mermaid CLI jika belum ada
echo Installing Mermaid CLI...
npm install -g @mermaid-js/mermaid-cli

REM Konversi semua file .mmd
for %%f in (*.mmd) do (
    echo Converting %%f...
    npx @mermaid-js/mermaid-cli -i "%%f" -o "%%~nf.png"
    npx @mermaid-js/mermaid-cli -i "%%f" -o "%%~nf.pdf"
)

echo.
echo Konversi selesai!
echo File PNG dan PDF telah dibuat.
pause
"""
    
    with open(output_dir / "convert.bat", 'w', encoding='utf-8') as f:
        f.write(batch_script)

def create_shell_script(output_dir):
    """Buat script shell untuk konversi otomatis"""
    shell_script = """#!/bin/bash
echo "E-Learning SMK - Diagram Converter"
echo "===================================="

# Cek apakah Node.js tersedia
if ! command -v node &> /dev/null; then
    echo "Node.js tidak ditemukan. Silakan install Node.js terlebih dahulu."
    exit 1
fi

# Install Mermaid CLI jika belum ada
echo "Installing Mermaid CLI..."
npm install -g @mermaid-js/mermaid-cli

# Konversi semua file .mmd
for file in *.mmd; do
    if [ -f "$file" ]; then
        echo "Converting $file..."
        npx @mermaid-js/mermaid-cli -i "$file" -o "${file%.mmd}.png"
        npx @mermaid-js/mermaid-cli -i "$file" -o "${file%.mmd}.pdf"
    fi
done

echo ""
echo "Konversi selesai!"
echo "File PNG dan PDF telah dibuat."
"""
    
    with open(output_dir / "convert.sh", 'w', encoding='utf-8') as f:
        f.write(shell_script)
    
    # Buat file executable
    os.chmod(output_dir / "convert.sh", 0o755)

def main():
    """Fungsi utama"""
    print("🎓 E-Learning SMK - Image Converter")
    print("=" * 50)
    
    # Setup paths
    script_dir = Path(__file__).parent
    output_dir = script_dir / "diagrams_output"
    
    if not output_dir.exists():
        print(f"❌ Direktori {output_dir} tidak ditemukan!")
        print("Jalankan generate_diagrams.py terlebih dahulu.")
        return
    
    # Cek file .mmd
    mmd_files = list(output_dir.glob("*.mmd"))
    if not mmd_files:
        print("❌ Tidak ada file .mmd yang ditemukan!")
        return
    
    print(f"📊 Ditemukan {len(mmd_files)} file .mmd")
    
    # Cek Node.js dan npm
    node_available, node_message = check_node_npm()
    print(f"🔍 {node_message}")
    
    if node_available:
        # Install Mermaid CLI
        if install_mermaid_cli():
            # Konversi semua file
            success_count = 0
            total_files = len(mmd_files)
            
            for mmd_file in mmd_files:
                name = mmd_file.stem
                print(f"\n🔄 Mengkonversi: {name}")
                success_count += convert_with_mermaid_cli(mmd_file, output_dir, name)
            
            print(f"\n🎉 Konversi selesai!")
            print(f"✅ {success_count}/{total_files * 2} file berhasil dikonversi")
        else:
            print("❌ Gagal menginstall Mermaid CLI")
            node_available = False
    
    if not node_available:
        print("\n📝 Node.js tidak tersedia. Membuat instruksi manual...")
    
    # Buat instruksi manual
    create_browser_instructions(output_dir)
    
    # Buat script batch/shell
    create_batch_script(output_dir)
    create_shell_script(output_dir)
    
    print(f"\n📁 Output tersimpan di: {output_dir}")
    print(f"📖 Baca instruksi: {output_dir / 'KONVERSI_MANUAL.txt'}")
    print(f"🖥️  Script Windows: {output_dir / 'convert.bat'}")
    print(f"🐧 Script Linux/Mac: {output_dir / 'convert.sh'}")
    
    if not node_available:
        print(f"\n💡 Untuk konversi otomatis:")
        print(f"   1. Install Node.js dari https://nodejs.org/")
        print(f"   2. Jalankan script convert.bat (Windows) atau convert.sh (Linux/Mac)")
        print(f"   3. Atau gunakan instruksi manual di KONVERSI_MANUAL.txt")

if __name__ == "__main__":
    main()
