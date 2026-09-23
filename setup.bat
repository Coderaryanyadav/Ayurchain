@echo off
setlocal EnableDelayedExpansion
title AYURCHAIN - 1-Click Complete System Setup
color 0A

:: Check for Administrator Privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    powershell -Command "Start-Process cmd -ArgumentList '/c \"\"%~dp0setup\setup.bat\"\"' -Verb RunAs"
    exit /b
)

call "%~dp0setup\setup.bat"
