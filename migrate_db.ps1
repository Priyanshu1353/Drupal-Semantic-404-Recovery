# migrate_db.ps1 – wipes the existing SQLite DB and reruns Drush site:install
# Use this to reset your local Drupal environment to a clean state.

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $MyInvocation.MyCommand.Path

$phpExe  = "C:\php\php.exe"
$drush   = Join-Path $Root "vendor\bin\drush"
$webRoot = Join-Path $Root "web"
$dbFile  = Join-Path $webRoot "sites\default\files\.drupal.sqlite"

Write-Host "==================================================" -ForegroundColor Magenta
Write-Host "  Drupal DB Migration (Reset + Reinstall)" -ForegroundColor Magenta
Write-Host "==================================================" -ForegroundColor Magenta

# Remove old DB
if (Test-Path $dbFile) {
    Write-Host "Removing old SQLite database..." -ForegroundColor Yellow
    Remove-Item $dbFile -Force
    Write-Host "  Removed: $dbFile" -ForegroundColor Green
} else {
    Write-Host "No existing database found, skipping removal." -ForegroundColor Gray
}

# Ensure files directory exists and is writable
$filesDir = Join-Path $webRoot "sites\default\files"
if (-not (Test-Path $filesDir)) {
    New-Item -ItemType Directory -Path $filesDir | Out-Null
    Write-Host "Created files directory: $filesDir" -ForegroundColor Green
}

# Re-install Drupal with SQLite
Write-Host "Installing fresh Drupal site (SQLite)..." -ForegroundColor Yellow
& $phpExe $drush site:install standard `
    --db-url="sqlite://sites/default/files/.drupal.sqlite" `
    --site-name="Semantic 404 Recovery" `
    --account-name=admin `
    --account-pass=admin `
    --yes `
    --root=$webRoot

# Enable the custom module
Write-Host "Enabling semantic_404 module..." -ForegroundColor Yellow
& $phpExe $drush pm:enable semantic_404 --yes --root=$webRoot

# Rebuild caches
Write-Host "Rebuilding caches..." -ForegroundColor Yellow
& $phpExe $drush cache:rebuild --root=$webRoot

Write-Host ""
Write-Host "==================================================" -ForegroundColor Green
Write-Host "  Migration complete! Site is fresh." -ForegroundColor Green
Write-Host "  Login: http://localhost:8080 (admin / admin)" -ForegroundColor White
Write-Host "==================================================" -ForegroundColor Green
