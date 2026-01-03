# Semantic 404 Recovery – Native Windows Setup Script
# Run this once to create the Python venv, install deps, and start the AI engine.
# Prerequisite: Python 3.11+ in PATH and C:\php\php.exe available.

$ErrorActionPreference = "Stop"
$PSScriptRoot_local = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $PSScriptRoot_local

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  Semantic 404 Recovery – Setup" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# ── 1. Python virtual environment ──────────────────────────────────────────
$venvPath = Join-Path $PSScriptRoot_local "ai_engine\.venv"

if (-not (Test-Path $venvPath)) {
    Write-Host "[1/4] Creating Python virtual environment..." -ForegroundColor Yellow
    python -m venv $venvPath
} else {
    Write-Host "[1/4] Virtual environment already exists, skipping." -ForegroundColor Green
}

# ── 2. Install Python dependencies ─────────────────────────────────────────
Write-Host "[2/4] Installing Python dependencies..." -ForegroundColor Yellow
& "$venvPath\Scripts\pip.exe" install -r (Join-Path $PSScriptRoot_local "ai_engine\requirements.txt") --quiet

Write-Host "      Dependencies installed." -ForegroundColor Green

# ── 3. Start AI Engine in background ───────────────────────────────────────
Write-Host "[3/4] Starting FastAPI AI Engine on http://localhost:8000 ..." -ForegroundColor Yellow

$uvicorn = "$venvPath\Scripts\uvicorn.exe"
$mainPy  = Join-Path $PSScriptRoot_local "ai_engine\main.py"
$aiJob   = Start-Job -ScriptBlock {
    param($uv, $dir)
    Set-Location $dir
    & $uv main:app --host 127.0.0.1 --port 8000
} -ArgumentList $uvicorn, (Join-Path $PSScriptRoot_local "ai_engine")

Start-Sleep -Seconds 3

# Quick health check
try {
    $resp = Invoke-RestMethod -Uri "http://localhost:8000/health" -ErrorAction Stop
    Write-Host "      AI Engine is healthy: $($resp.status)" -ForegroundColor Green
} catch {
    Write-Host "      WARNING: AI Engine health check failed. Check the job log." -ForegroundColor Red
}

# ── 4. Start Drupal (PHP built-in server) ─────────────────────────────────
Write-Host "[4/4] Starting Drupal on http://localhost:8080 ..." -ForegroundColor Yellow

$phpExe  = "C:\php\php.exe"
$webRoot = Join-Path $PSScriptRoot_local "web"

if (-not (Test-Path $phpExe)) {
    Write-Host "      WARNING: PHP not found at $phpExe. Skipping Drupal server." -ForegroundColor Red
} else {
    Start-Job -ScriptBlock {
        param($php, $root)
        & $php -S 127.0.0.1:8080 -t $root
    } -ArgumentList $phpExe, $webRoot | Out-Null

    Start-Sleep -Seconds 2
    Write-Host "      Drupal server started." -ForegroundColor Green
}

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  All services running!" -ForegroundColor Green
Write-Host "  AI Engine : http://localhost:8000/docs" -ForegroundColor White
Write-Host "  Drupal    : http://localhost:8080" -ForegroundColor White
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Test AI Engine:" -ForegroundColor Yellow
Write-Host '  Invoke-RestMethod "http://localhost:8000/match?path=investment"' -ForegroundColor Gray
Write-Host ""
Write-Host "Press CTRL+C to stop watching job output (servers keep running in background)." -ForegroundColor DarkGray
Receive-Job -Job $aiJob -Wait
