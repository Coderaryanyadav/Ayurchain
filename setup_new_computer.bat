@echo off
setlocal EnableDelayedExpansion
title AYURCHAIN - 1-Click Complete System Setup
color 0A

echo ====================================================================
echo             AYURCHAIN - AUTOMATED SYSTEM SETUP INSTALLER            
echo   Blockchain-Based Ayurvedic Medicine Storage & Verification System 
echo ====================================================================
echo.

:: Check for Administrator Privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Elevating permissions to Administrator...
    powershell -Command "Start-Process cmd -ArgumentList '/c \"\"%~dp0setup_new_computer.bat\"\"' -Verb RunAs"
    exit /b
)

echo [OK] Running with Administrator Privileges.
echo.

:: Execute the Master PowerShell Setup Script
powershell -ExecutionPolicy Bypass -NoProfile -File "%~dp0setup\setup.ps1"

echo.
echo Press any key to exit installer...
pause >nul
