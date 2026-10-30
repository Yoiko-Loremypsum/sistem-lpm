@echo off
title Python Face Service - Sistem LPM UMI
echo ========================================================
echo   Layanan Verifikasi Wajah (Python Face Service)
echo   Universitas Methodist Indonesia - Sistem LPM
echo ========================================================
echo.

cd /d "%~dp0"

if not exist ".venv\Scripts\python.exe" (
    echo [ERROR] Virtual environment belum dibuat.
    echo Menyiapkan virtual environment...
    python -m venv .venv
    call .venv\Scripts\activate.bat
    pip install -r requirements.txt
) else (
    call .venv\Scripts\activate.bat
)

echo Menjalankan layanan di http://127.0.0.1:5001 ...
python app.py
pause
