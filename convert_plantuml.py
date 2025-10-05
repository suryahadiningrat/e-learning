#!/usr/bin/env python3
"""
Script untuk mengkonversi PlantUML ke PNG dan PDF
"""

import os
import subprocess
import sys
from pathlib import Path

def install_plantuml():
    """Install PlantUML menggunakan brew jika belum ada"""
    try:
        # Check if plantuml is already installed
        result = subprocess.run(['plantuml', '-version'], 
                              capture_output=True, text=True)
        if result.returncode == 0:
            print("✅ PlantUML sudah terinstall")
            return True
    except FileNotFoundError:
        pass
    
    print("🔄 Installing PlantUML...")
    try:
        # Install using brew
        subprocess.run(['brew', 'install', 'plantuml'], check=True)
        print("✅ PlantUML berhasil diinstall")
        return True
    except subprocess.CalledProcessError:
        print("❌ Gagal install PlantUML dengan brew")
        return False
    except FileNotFoundError:
        print("❌ Homebrew tidak ditemukan. Silakan install Homebrew terlebih dahulu")
        return False

def convert_plantuml_to_images(puml_file, output_dir):
    """Convert PlantUML file to PNG and PDF"""
    
    if not install_plantuml():
        return False
    
    # Create output directory if not exists
    Path(output_dir).mkdir(parents=True, exist_ok=True)
    
    # Convert to PNG
    print(f"🔄 Converting {puml_file} to PNG...")
    try:
        subprocess.run([
            'plantuml', 
            '-tpng', 
            '-o', output_dir,
            puml_file
        ], check=True)
        print(f"✅ PNG berhasil dibuat")
    except subprocess.CalledProcessError as e:
        print(f"❌ Gagal membuat PNG: {e}")
        return False
    
    # Convert to PDF (via SVG first, then PDF)
    print(f"🔄 Converting {puml_file} to PDF...")
    try:
        # First convert to SVG
        subprocess.run([
            'plantuml', 
            '-tsvg', 
            '-o', output_dir,
            puml_file
        ], check=True)
        
        # Find the SVG file
        puml_name = Path(puml_file).stem
        svg_file = os.path.join(output_dir, f"{puml_name}.svg")
        pdf_file = os.path.join(output_dir, f"{puml_name}.pdf")
        
        # Convert SVG to PDF using rsvg-convert (if available) or cairosvg
        try:
            subprocess.run(['rsvg-convert', '-f', 'pdf', '-o', pdf_file, svg_file], check=True)
            print(f"✅ PDF berhasil dibuat menggunakan rsvg-convert")
        except FileNotFoundError:
            try:
                # Try with cairosvg
                import cairosvg
                cairosvg.svg2pdf(url=svg_file, write_to=pdf_file)
                print(f"✅ PDF berhasil dibuat menggunakan cairosvg")
            except ImportError:
                print("⚠️  Untuk membuat PDF, install rsvg-convert atau cairosvg:")
                print("   brew install librsvg")
                print("   atau: pip install cairosvg")
                return False
        
        # Clean up SVG file
        os.remove(svg_file)
        
    except subprocess.CalledProcessError as e:
        print(f"❌ Gagal membuat PDF: {e}")
        return False
    
    return True

def main():
    """Main function"""
    current_dir = os.getcwd()
    puml_file = os.path.join(current_dir, "use_case_diagram_plantuml.puml")
    output_dir = os.path.join(current_dir, "diagrams_output")
    
    if not os.path.exists(puml_file):
        print(f"❌ File {puml_file} tidak ditemukan")
        return
    
    print("🚀 Memulai konversi PlantUML...")
    
    if convert_plantuml_to_images(puml_file, output_dir):
        print(f"🎉 Konversi selesai!")
        print(f"📁 Output tersimpan di: {output_dir}")
    else:
        print("❌ Konversi gagal")

if __name__ == "__main__":
    main()