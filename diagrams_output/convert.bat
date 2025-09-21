@echo off
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
