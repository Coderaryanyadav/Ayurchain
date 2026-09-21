# ====================================================================
# AYURCHAIN - Automated 1-Click Environment & Database Installer
# Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
# File: setup.ps1
# ====================================================================

$ErrorActionPreference = "Continue"

Write-Host "====================================================================" -ForegroundColor Green
Write-Host "             AYURCHAIN - AUTOMATED SETUP INSTALLER                  " -ForegroundColor Yellow
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""

$ProjectDir = $PSScriptRoot
$TargetHtdocs = "C:\xampp\htdocs\ayurvedic-blockchain"
$XamppPhp = "C:\xampp\php\php.exe"
$XamppMysql = "C:\xampp\mysql\bin\mysql.exe"
$XamppMysqld = "C:\xampp\mysql\bin\mysqld.exe"
$XamppApache = "C:\xampp\apache\bin\httpd.exe"

# Step 1: Check XAMPP Installation
Write-Host "[1/5] Checking XAMPP Installation..." -ForegroundColor Cyan
if (-not (Test-Path $XamppPhp)) {
    Write-Host "XAMPP not found at C:\xampp. Attempting automated installation via Winget..." -ForegroundColor Yellow
    winget install --id ApacheFriends.Xampp.8.2 --accept-source-agreements --accept-package-agreements --silent
    Start-Sleep -Seconds 10
}

if (-not (Test-Path $XamppPhp)) {
    Write-Host "ERROR: XAMPP is not installed at C:\xampp. Please install XAMPP manually from https://www.apachefriends.org/" -ForegroundColor Red
    Pause
    Exit
}
Write-Host "SUCCESS: XAMPP detected at C:\xampp" -ForegroundColor Green

# Step 2: Deploy Project Files to htdocs
Write-Host ""
Write-Host "[2/5] Deploying AYURCHAIN files to C:\xampp\htdocs\ayurvedic-blockchain..." -ForegroundColor Cyan
if (-not (Test-Path "C:\xampp\htdocs")) {
    New-Item -ItemType Directory -Path "C:\xampp\htdocs" -Force | Out-Null
}

if (-not (Test-Path $TargetHtdocs)) {
    New-Item -ItemType Directory -Path $TargetHtdocs -Force | Out-Null
}

Copy-Item -Path "$ProjectDir\*" -Destination $TargetHtdocs -Recurse -Force
Write-Host "SUCCESS: Project files deployed to htdocs!" -ForegroundColor Green

# Step 3: Start MySQL & Apache Services
Write-Host ""
Write-Host "[3/5] Starting MySQL and Apache Servers..." -ForegroundColor Cyan

# Start MySQL if not running
$mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysqlProcess) {
    Write-Host "Starting MySQL server..." -ForegroundColor Yellow
    Start-Process -FilePath $XamppMysqld --defaults-file="C:\xampp\mysql\bin\my.ini" -WindowStyle Hidden
    Start-Sleep -Seconds 4
}
Write-Host "SUCCESS: MySQL is running." -ForegroundColor Green

# Start Apache if not running
$apacheProcess = Get-Process httpd -ErrorAction SilentlyContinue
if (-not $apacheProcess) {
    Write-Host "Starting Apache server..." -ForegroundColor Yellow
    Start-Process -FilePath $XamppApache -WindowStyle Hidden
    Start-Sleep -Seconds 3
}
Write-Host "SUCCESS: Apache is running." -ForegroundColor Green

# Step 4: Import Database Schema & Seed Data
Write-Host ""
Write-Host "[4/5] Setting up MySQL Database (ayurvedic_blockchain)..." -ForegroundColor Cyan

$SqlFile = "$TargetHtdocs\database.sql"
if (Test-Path $SqlFile) {
    # Execute database.sql using MySQL CLI
    & $XamppMysql -u root -e "CREATE DATABASE IF NOT EXISTS ayurvedic_blockchain;"
    Get-Content $SqlFile | & $XamppMysql -u root ayurvedic_blockchain
    Write-Host "SUCCESS: Database ayurvedic_blockchain created and populated with seed data!" -ForegroundColor Green
}
else {
    Write-Host "WARNING: database.sql not found in project root." -ForegroundColor Yellow
}

# Step 5: Launch Project in Web Browser
Write-Host ""
Write-Host "[5/5] Launching AYURCHAIN Application in Web Browser..." -ForegroundColor Cyan
Start-Process "http://localhost/ayurvedic-blockchain/"

Write-Host ""
Write-Host "====================================================================" -ForegroundColor Green
Write-Host "        AYURCHAIN SETUP COMPLETE! APPLICATION IS NOW LIVE!          " -ForegroundColor Yellow
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Application URL : http://localhost/ayurvedic-blockchain/" -ForegroundColor White
Write-Host "Admin Login     : http://localhost/ayurvedic-blockchain/login.php" -ForegroundColor White
Write-Host "Credentials     : Username: admin | Password: admin123" -ForegroundColor White
Write-Host ""
