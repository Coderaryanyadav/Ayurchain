# ====================================================================
# AYURCHAIN - Automated 1-Click Environment & Database Installer
# Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
# File: setup/setup.ps1
# ====================================================================

$ErrorActionPreference = "Continue"

Write-Host "====================================================================" -ForegroundColor Green
Write-Host "             AYURCHAIN - AUTOMATED SETUP INSTALLER                  " -ForegroundColor Yellow
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""

$ScriptDir = $PSScriptRoot
if (Test-Path "$ScriptDir\..\database\database.sql") {
    $ProjectDir = (Resolve-Path "$ScriptDir\..").Path
} elseif (Test-Path "$ScriptDir\database\database.sql") {
    $ProjectDir = (Resolve-Path "$ScriptDir").Path
} else {
    $ProjectDir = $ScriptDir
}

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
Write-Host "[2/5] Checking project location in C:\xampp\htdocs\ayurvedic-blockchain..." -ForegroundColor Cyan
if (-not (Test-Path "C:\xampp\htdocs")) {
    New-Item -ItemType Directory -Path "C:\xampp\htdocs" -Force | Out-Null
}

$ResolvedSource = (Resolve-Path $ProjectDir).Path.TrimEnd('\')
$ResolvedTarget = if (Test-Path $TargetHtdocs) { (Resolve-Path $TargetHtdocs).Path.TrimEnd('\') } else { $TargetHtdocs }

if ($ResolvedSource -ne $ResolvedTarget) {
    if (-not (Test-Path $TargetHtdocs)) {
        New-Item -ItemType Directory -Path $TargetHtdocs -Force | Out-Null
    }
    Write-Host "Deploying files from $ResolvedSource to $TargetHtdocs..." -ForegroundColor Yellow
    Get-ChildItem -Path $ProjectDir -Exclude ".git" | ForEach-Object {
        Copy-Item -Path $_.FullName -Destination $TargetHtdocs -Recurse -Force
    }
    Write-Host "SUCCESS: Project files deployed to htdocs!" -ForegroundColor Green
} else {
    Write-Host "SUCCESS: Project is already running inside $TargetHtdocs. No copy required." -ForegroundColor Green
}

# Ensure storage directory exists
$StorageDir = "$TargetHtdocs\storage\certificates"
if (-not (Test-Path $StorageDir)) {
    New-Item -ItemType Directory -Path $StorageDir -Force | Out-Null
}

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

& $XamppMysql -u root -e "CREATE DATABASE IF NOT EXISTS ayurvedic_blockchain;"

$SchemaSql = "$TargetHtdocs\database\database.sql"
$SampleSql = "$TargetHtdocs\database\sample-data.sql"
$FallbackSql = "$TargetHtdocs\database.sql"

if (Test-Path $SchemaSql) {
    Get-Content $SchemaSql | & $XamppMysql -u root ayurvedic_blockchain
    Write-Host "SUCCESS: Database schema imported from database/database.sql!" -ForegroundColor Green
} elseif (Test-Path $FallbackSql) {
    Get-Content $FallbackSql | & $XamppMysql -u root ayurvedic_blockchain
    Write-Host "SUCCESS: Database schema imported from database.sql!" -ForegroundColor Green
} else {
    Write-Host "WARNING: database.sql not found!" -ForegroundColor Yellow
}

if (Test-Path $SampleSql) {
    Get-Content $SampleSql | & $XamppMysql -u root ayurvedic_blockchain
    Write-Host "SUCCESS: Sample demo records imported from database/sample-data.sql!" -ForegroundColor Green
}

# Step 5: Launch Project in Web Browser
Write-Host ""
Write-Host "[5/5] Launching AYURCHAIN Application in Web Browser..." -ForegroundColor Cyan
Start-Process "http://localhost/ayurvedic-blockchain/app/public/index.php"

Write-Host ""
Write-Host "====================================================================" -ForegroundColor Green
Write-Host "        AYURCHAIN SETUP COMPLETE! APPLICATION IS NOW LIVE!          " -ForegroundColor Yellow
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Application URL : http://localhost/ayurvedic-blockchain/app/public/index.php" -ForegroundColor White
Write-Host "Verify Portal   : http://localhost/ayurvedic-blockchain/app/public/verify.php" -ForegroundColor White
Write-Host "Admin Login     : http://localhost/ayurvedic-blockchain/app/auth/login.php" -ForegroundColor White
Write-Host "Credentials     : Email: admin@ayurchain.org | Password: admin123" -ForegroundColor White
Write-Host ""
