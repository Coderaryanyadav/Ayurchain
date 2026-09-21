@echo off
TITLE AYURCHAIN - Automated 1-Click Setup Script
COLOR 0A
echo ====================================================================
echo             AYURCHAIN - AUTOMATED SETUP LAUNCHER
echo ====================================================================
echo.
echo Launching PowerShell automated environment and database installer...
echo.

PowerShell -ExecutionPolicy Bypass -File "%~dp0setup.ps1"

pause
