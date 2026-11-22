@echo off
title AYURCHAIN - Portable Local Server (Port 8000)
color 0A

echo ====================================================================
echo        AYURCHAIN - PORTABLE SERVER LAUNCHER (NO ADMIN REQUIRED)      
echo ====================================================================
echo.

set PHP_BIN=C:\xampp\php\php.exe

if not exist "%PHP_BIN%" (
    where php >nul 2>&1
    if %errorlevel% equ 0 (
        set PHP_BIN=php
    ) else (
        echo [ERROR] PHP executable not found at C:\xampp\php\php.exe or in PATH.
        echo Please ensure XAMPP is installed or PHP is in PATH.
        pause
        exit /b
    )
)

echo Starting MySQL if not running...
start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" >nul 2>&1

echo.
echo ====================================================================
echo [OK] AyurChain is running at: http://localhost:8000/
echo [OK] Admin Login: http://localhost:8000/login.php
echo [OK] Public Verify: http://localhost:8000/verify.php
echo ====================================================================
echo.
echo (Keep this window open while using the application)
echo.

start http://localhost:8000/login.php

"%PHP_BIN%" -S localhost:8000 -t "%~dp0"
pause
