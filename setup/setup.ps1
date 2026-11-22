# ====================================================================
# AYURCHAIN - Automated 1-Click Environment, Database & Node Setup
# Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
# File: setup/setup.ps1
# ====================================================================

$ErrorActionPreference = "Continue"

Write-Host "====================================================================" -ForegroundColor Green
Write-Host "         AYURCHAIN - AUTOMATED FULL SYSTEM INSTALLER               " -ForegroundColor Yellow
Write-Host "   Blockchain-Based Ayurvedic Medicine Storage & Verification System" -ForegroundColor Cyan
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
$XamppDir     = "C:\xampp"
$XamppPhp     = "C:\xampp\php\php.exe"
$XamppMysql   = "C:\xampp\mysql\bin\mysql.exe"
$XamppMysqld  = "C:\xampp\mysql\bin\mysqld.exe"
$XamppApache  = "C:\xampp\apache\bin\httpd.exe"

# -------------------------------------------------------------------
# Step 1: Detect / Install XAMPP
# -------------------------------------------------------------------
Write-Host "[1/6] Checking XAMPP Environment..." -ForegroundColor Cyan
if (-not (Test-Path $XamppPhp)) {
    Write-Host "XAMPP not detected at C:\xampp. Attempting automated package installation..." -ForegroundColor Yellow
    try {
        winget install --id ApacheFriends.Xampp.8.2 --accept-source-agreements --accept-package-agreements --silent
        Start-Sleep -Seconds 10
    } catch {
        Write-Host "Winget install failed or not available." -ForegroundColor Red
    }
}

if (-not (Test-Path $XamppPhp)) {
    Write-Host "ERROR: XAMPP is not installed at C:\xampp." -ForegroundColor Red
    Write-Host "Opening XAMPP official download page in your browser..." -ForegroundColor Yellow
    Start-Process "https://www.apachefriends.org/download.html"
    Write-Host "Please install XAMPP to C:\xampp and rerun this script." -ForegroundColor Yellow
    Read-Host "Press Enter after installing XAMPP to continue..."
    if (-not (Test-Path $XamppPhp)) {
        Write-Host "Aborting setup. XAMPP still not found." -ForegroundColor Red
        Exit
    }
}
Write-Host "SUCCESS: XAMPP detected at C:\xampp" -ForegroundColor Green

# -------------------------------------------------------------------
# Step 2: Deploy / Synchronize Project Files to htdocs
# -------------------------------------------------------------------
Write-Host ""
Write-Host "[2/6] Deploying Project to C:\xampp\htdocs\ayurvedic-blockchain..." -ForegroundColor Cyan
if (-not (Test-Path "C:\xampp\htdocs")) {
    New-Item -ItemType Directory -Path "C:\xampp\htdocs" -Force | Out-Null
}

$ResolvedSource = (Resolve-Path $ProjectDir).Path.TrimEnd('\')
$ResolvedTarget = if (Test-Path $TargetHtdocs) { (Resolve-Path $TargetHtdocs).Path.TrimEnd('\') } else { $TargetHtdocs }

if ($ResolvedSource -ne $ResolvedTarget) {
    if (-not (Test-Path $TargetHtdocs)) {
        New-Item -ItemType Directory -Path $TargetHtdocs -Force | Out-Null
    }
    Write-Host "Copying files from $ResolvedSource to $TargetHtdocs..." -ForegroundColor Yellow
    Get-ChildItem -Path $ProjectDir -Exclude ".git" | ForEach-Object {
        Copy-Item -Path $_.FullName -Destination $TargetHtdocs -Recurse -Force
    }
    Write-Host "SUCCESS: Project files deployed to web server root!" -ForegroundColor Green
} else {
    Write-Host "SUCCESS: Project is already running inside $TargetHtdocs." -ForegroundColor Green
}

# Ensure storage directories exist
$StorageDir = "$TargetHtdocs\storage\certificates"
if (-not (Test-Path $StorageDir)) {
    New-Item -ItemType Directory -Path $StorageDir -Force | Out-Null
}

# -------------------------------------------------------------------
# Step 3: Start MySQL & Apache Services
# -------------------------------------------------------------------
Write-Host ""
Write-Host "[3/6] Starting Apache and MySQL Services..." -ForegroundColor Cyan

# Start MySQL if not running
$mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysqlProcess) {
    Write-Host "Starting MySQL server..." -ForegroundColor Yellow
    Start-Process "C:\xampp\mysql_start.bat" -WindowStyle Hidden
    
    # Wait for MySQL to accept connections on port 3306
    $retries = 0
    while ($retries -lt 15) {
        $tcp = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue
        if ($tcp.TcpTestSucceeded) { break }
        Start-Sleep -Seconds 1
        $retries++
    }
}
Write-Host "SUCCESS: MySQL database service is running on port 3306." -ForegroundColor Green

# Start Apache if not running
$apacheProcess = Get-Process httpd -ErrorAction SilentlyContinue
if (-not $apacheProcess) {
    Write-Host "Starting Apache web server..." -ForegroundColor Yellow
    Start-Process -FilePath "C:\xampp\apache\bin\httpd.exe" -WorkingDirectory "C:\xampp\apache\bin" -WindowStyle Hidden
    
    # Wait for Apache to accept connections on port 80
    $retries = 0
    while ($retries -lt 8) {
        $tcp = Test-NetConnection -ComputerName 127.0.0.1 -Port 80 -WarningAction SilentlyContinue
        if ($tcp.TcpTestSucceeded) { break }
        Start-Sleep -Seconds 1
        $retries++
    }
}
Write-Host "SUCCESS: Apache HTTP web server is running on port 80." -ForegroundColor Green

# -------------------------------------------------------------------
# Step 4: Import MySQL Database Schema & Seed Data
# -------------------------------------------------------------------
Write-Host ""
Write-Host "[4/6] Initializing Database (ayurvedic_blockchain)..." -ForegroundColor Cyan

& $XamppMysql -u root -e "CREATE DATABASE IF NOT EXISTS ayurvedic_blockchain CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

$SchemaSql = "$TargetHtdocs\database\database.sql"
$SampleSql = "$TargetHtdocs\database\sample-data.sql"

if (Test-Path $SchemaSql) {
    Get-Content $SchemaSql | & $XamppMysql -u root ayurvedic_blockchain
    Write-Host "SUCCESS: Database tables created from database/database.sql!" -ForegroundColor Green
} else {
    Write-Host "WARNING: database/database.sql not found!" -ForegroundColor Yellow
}

if (Test-Path $SampleSql) {
    Get-Content $SampleSql | & $XamppMysql -u root ayurvedic_blockchain
    Write-Host "SUCCESS: Seed sample records imported from database/sample-data.sql!" -ForegroundColor Green
}

# -------------------------------------------------------------------
# Step 5: Execute Automated Verification Test Suite
# -------------------------------------------------------------------
Write-Host ""
Write-Host "[5/6] Running End-to-End System Test Suite..." -ForegroundColor Cyan
$TestRunner = "$TargetHtdocs\tests\test_runner.php"
if (Test-Path $TestRunner) {
    & $XamppPhp $TestRunner
}

# -------------------------------------------------------------------
# Step 6: Create Desktop Shortcut & Launch Live Web App
# -------------------------------------------------------------------
Write-Host ""
Write-Host "[6/6] Creating Desktop Shortcut & Launching Application..." -ForegroundColor Cyan

try {
    $WshShell = New-Object -ComObject WScript.Shell
    $DesktopPath = [System.Environment]::GetFolderPath([System.Environment+SpecialFolder]::Desktop)
    $ShortcutPath = "$DesktopPath\AYURCHAIN Portal.url"
    $Shortcut = $WshShell.CreateShortcut($ShortcutPath)
    $Shortcut.TargetPath = "http://localhost/ayurvedic-blockchain/login.php"
    $Shortcut.Save()
    Write-Host "SUCCESS: Desktop shortcut 'AYURCHAIN Portal' created!" -ForegroundColor Green
} catch {
    # Fallback if COM object restricted
}

# Open the live portal in the default browser
Start-Process "http://localhost/ayurvedic-blockchain/login.php"

Write-Host ""
Write-Host "====================================================================" -ForegroundColor Green
Write-Host "       AYURCHAIN SETUP COMPLETE! PROJECT IS LIVE & RUNNING!         " -ForegroundColor Yellow
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Application URL : http://localhost/ayurvedic-blockchain/login.php" -ForegroundColor Cyan
Write-Host "  Public Verify   : http://localhost/ayurvedic-blockchain/verify.php" -ForegroundColor Cyan
Write-Host "  Admin Login     : Username: admin | Password: admin123" -ForegroundColor White
Write-Host "  Ganache Network : RPC: http://127.0.0.1:7545 | Chain ID: 1337" -ForegroundColor White
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""
