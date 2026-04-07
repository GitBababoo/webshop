@echo off
echo ========================================
echo 🛡️ Shopee TH - GitHub ULTIMATE Fast Upload (V2)
echo ========================================
echo.

:: Set predefined URL
set repo_url=https://github.com/GitBababoo/webshop.git

:: 1. Initialize Git
if not exist .git (
    echo [1/5] Initializing Git...
    git init
) else (
    echo [1/5] Git already initialized.
)

:: 2. Set Identity (Fixed the "Author identity unknown" error)
echo [2/5] Setting Git Identity for this repo...
git config user.email "gitbababoo@users.noreply.github.com"
git config user.name "GitBababoo"

:: 3. Add and Commit
echo [3/5] Staging and Committing files...
git add .
git commit -m "🚀 Complete Project Release: Shopee TH Advanced Ecosystem with Self-Healing Guards & E2E Crawler"

:: 4. Remote Setup
echo [4/5] Connecting to GitHub: %repo_url%
git remote remove origin >nul 2>&1
git remote add origin %repo_url%
git branch -M main

:: 5. Push
echo.
echo [5/5] Pushing to GitHub...
echo (หากมีหน้าต่างถาม Username/Password ของ GitHub ให้กรอกข้อมูลของคุณเพื่อยืนยันสิทธิ์นะครับ)
echo.
git push -u origin main

echo.
echo ========================================
echo ✅ เสร็จสิ้น! ลองตรวจสอบหน้า GitHub ของคุณที่:
echo %repo_url%
echo ========================================
pause
