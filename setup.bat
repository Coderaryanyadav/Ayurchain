@echo off
title AYURCHAIN - 1-Click Setup (No Admin Required)
color 0A

echo ====================================================================
echo          AYURCHAIN - SETUP (NO ADMIN PRIVILEGES NEEDED)             
echo ====================================================================
echo.

powershell -ExecutionPolicy Bypass -NoProfile -File "%~dp0setup\setup.ps1"

echo.
echo Press any key to continue...
pause >nul
