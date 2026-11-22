@echo off
title AYURCHAIN - 1-Click Setup (Student / User Mode)
color 0A

echo ====================================================================
echo             AYURCHAIN - AUTOMATED SYSTEM LAUNCHER                   
echo   Blockchain-Based Ayurvedic Medicine Storage & Verification System 
echo ====================================================================
echo.

powershell -ExecutionPolicy Bypass -NoProfile -File "%~dp0setup\setup.ps1"

echo.
echo Press any key to exit installer...
pause >nul
