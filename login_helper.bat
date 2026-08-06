@echo off
title Login Helper - Social Media Mining
echo ====================================================
echo   MEMBUKA CHROME PROFILE UNTUK LOGIN MEDIA SOSIAL
echo ====================================================
echo.
echo Silakan login ke akun Instagram, TikTok, dan Facebook
echo Anda pada tab-tab Chrome yang akan terbuka.
echo.
echo Setelah Anda berhasil login, silakan TUTUP jendela
echo Chrome tersebut. Sesi login akan tersimpan otomatis.
echo.
echo ====================================================
echo Membuka Chrome...

:: Temukan lokasi instalasi Chrome standar di Windows
set "CHROME_PATH=C:\Program Files\Google\Chrome\Application\chrome.exe"
if not exist "%CHROME_PATH%" set "CHROME_PATH=C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"
if not exist "%CHROME_PATH%" (
    :: Fallback jika di luar path standar, gunakan perintah start standar
    start chrome --user-data-dir="%~dp0chrome-session" "https://www.tiktok.com" "https://www.instagram.com" "https://www.facebook.com"
) else (
    "%CHROME_PATH%" --user-data-dir="%~dp0chrome-session" "https://www.tiktok.com" "https://www.instagram.com" "https://www.facebook.com"
)

echo.
echo Chrome telah ditutup. Sesi login Anda berhasil disimpan!
pause
