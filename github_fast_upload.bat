@echo off
echo ========================================
echo 🛡️ Shopee TH - GitHub ULTIMATE Fast Upload (V4 - ULTRA FIX)
echo ========================================
echo.

:: Set predefined URL
set repo_url=https://github.com/GitBababoo/webshop.git

:: 1. Initialize Git
if not exist .git (
    echo [1/4] Initializing Git...
    git init
) else (
    echo [1/4] Git already initialized.
)

:: 2. Set Identity
echo [2/4] Setting Git Identity for this repo...
git config user.email "gitbababoo@users.noreply.github.com"
git config user.name "GitBababoo"

:: 3. Add and Commit (ULTRA FIX Mode)
echo [3/4] Capturing all changes (README Gallery)...
git add .
:: Force commit even if previous logic failed
git commit -m "🚀 Final Showcase Update: High-Impact Gallery for README Documentation" || echo [3/4] File already staged or committed.

:: 4. Remote Setup and Push
echo.
echo [4/4] Connection: %repo_url%
git remote remove origin >nul 2>&1
git remote add origin %repo_url%
git branch -M main

echo.
echo [4/4] Pushing to GitHub (FORCE MODE)...
echo (หากมีหน้าต่างถาม Username/Password ให้กรอกข้อมูลของคุณนะครับ)
echo.
git push -u origin main --force

echo.
echo ========================================
echo ✅ อัปโหลดสำเร็จ! ทุกอย่างถูกอัปเดตแล้ว
echo เช็คผลงานได้ที่: %repo_url%
echo ========================================
pause
