#!/bin/bash
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
