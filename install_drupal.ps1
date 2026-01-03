# Drupal 10 Install Helper – uses Drush via Composer
# Run AFTER setup.ps1 has started the PHP server.
# Requires: Composer in PATH, C:\php\php.exe, Drupal downloaded via Composer.

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $MyInvocation.MyCommand.Path

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  Drupal Install & Module Activation" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$phpExe  = "C:\php\php.exe"
$drush   = Join-Path $Root "vendor\bin\drush"
$webRoot = Join-Path $Root "web"

if (-not (Test-Path "$drush.bat") -and -not (Test-Path $drush)) {
    Write-Host "Drush not found. Running composer install first..." -ForegroundColor Yellow
    Set-Location $Root
    composer install --no-interaction
}

# Install Drupal site with SQLite (no MySQL needed for dev)
Write-Host "Installing Drupal site (SQLite)..." -ForegroundColor Yellow
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

# Clear caches
Write-Host "Clearing caches..." -ForegroundColor Yellow
& $phpExe $drush cache:rebuild --root=$webRoot

Write-Host ""
Write-Host "==================================================" -ForegroundColor Green
Write-Host "  Drupal installed and module enabled!" -ForegroundColor Green
Write-Host "  Visit http://localhost:8080 to configure the block." -ForegroundColor White
Write-Host "==================================================" -ForegroundColor Green
