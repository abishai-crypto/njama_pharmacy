@echo off
title NJAMA PHARMACY - Local Counter Server
color 0A

echo ========================================================
echo        🏥 NJAMA PHARMACY MANAGEMENT SYSTEM
echo       Inawasha Mfumo wa Dukani (Local Counter)...
echo ========================================================
echo.

cd /d "%~dp0"

:: Angalia kama PHP inapatikana
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP haijapatikana kwenye mfumo huu!
    echo Tafadhali hakikisha Laragon au XAMPP imewashwa.
    pause
    exit /b
)

:: Anzisha server kwa nyuma na fungua kivinjari
echo Inafungua kivinjari cha duka (Google Chrome)...
start http://127.0.0.1:8000

echo Mfumo unawaka kwenye http://127.0.0.1:8000 ...
echo [MUHIMU] Usifunge dirisha hili wakati duka linaendelea kuuza!
echo Unaweza kulishusha chini (Minimize).
echo.

php artisan serve --host=127.0.0.1 --port=8000
pause
