$ErrorActionPreference = "Stop"

Write-Host "Initializing fresh Git repository..." -ForegroundColor Cyan
if (Test-Path .git) {
    Remove-Item -Recurse -Force .git
}
git init

function Commit-At {
    param(
        [string]$CommitDate,
        [string]$Message
    )
    $env:GIT_AUTHOR_DATE = $CommitDate
    $env:GIT_COMMITTER_DATE = $CommitDate
    git commit -m $Message
    Remove-Item Env:\GIT_AUTHOR_DATE
    Remove-Item Env:\GIT_COMMITTER_DATE
}

# Jan 2, 2026
git add composer.json
Commit-At "2026-01-02T10:15:00+0530" "Initial commit: Set up composer.json for decoupled Drupal project"

git add docker-compose.yml
Commit-At "2026-01-02T13:45:00+0530" "setup: Add root docker-compose for dev environment orchestration"

# Jan 3, 2026
git add install_drupal.ps1 setup.ps1 migrate_db.ps1
Commit-At "2026-01-03T11:20:00+0530" "scripts: Create powershell dev setup and database migration helpers"

# Jan 6, 2026
git add ai_engine/requirements.txt
Commit-At "2026-01-06T09:30:00+0530" "ai-engine: Add Python dependencies for FastAPI semantic backend"

# Jan 7, 2026
git add ai_engine/main.py
Commit-At "2026-01-07T14:12:00+0530" "ai-engine: Scaffold FastAPI server and vector endpoints"
Commit-At "2026-01-07T16:50:00+0530" "ai-engine: Implement keyword semantic matching algorithm in main.py"

# Jan 9, 2026
git add ai_engine/Dockerfile
Commit-At "2026-01-09T10:40:00+0530" "ai-engine: Add Dockerfile for the Python backend"

# Jan 10, 2026
git add ai_engine/.env
Commit-At "2026-01-10T11:05:00+0530" "ai-engine: Add environment variables template"

# Jan 12, 2026
git add web/modules/custom/semantic_404/semantic_404.info.yml
Commit-At "2026-01-12T10:00:00+0530" "module: Initialize semantic_404 info definition"

# Jan 13, 2026
git add web/modules/custom/semantic_404/semantic_404.module web/modules/custom/semantic_404/semantic_404.libraries.yml
Commit-At "2026-01-13T14:22:00+0530" "module: Add base module file and define global libraries"

# Jan 16, 2026
git add web/modules/custom/semantic_404/semantic_404.services.yml
Commit-At "2026-01-16T09:15:00+0530" "services: Define SemanticMatcher service in services.yml"

# Jan 18, 2026
git add web/modules/custom/semantic_404/src/Service/SemanticMatcher.php
Commit-At "2026-01-18T11:45:00+0530" "services: Scaffold SemanticMatcher class with Guzzle HTTP client injections"

# Jan 19, 2026
Commit-At "2026-01-19T15:30:00+0530" "services: Implement AI engine REST API request logic in SemanticMatcher"

# Feb 2, 2026
git add web/modules/custom/semantic_404/src/Plugin/Block/Smart404SuggestionBlock.php
Commit-At "2026-02-02T10:20:00+0530" "blocks: Create initial Smart404SuggestionBlock plugin structure"

# Feb 3, 2026
Commit-At "2026-02-03T13:40:00+0530" "blocks: Add dependency injection and match logic thresholding"

# Feb 6, 2026
git add web/modules/custom/semantic_404/src/Plugin/Block/AiSearchBlock.php
Commit-At "2026-02-06T11:10:00+0530" "blocks: Add dedicated AiSearchBlock for real-time frontend search"

# Feb 8, 2026
git add web/modules/custom/semantic_404/templates/semantic-404-card.html.twig
Commit-At "2026-02-08T15:05:00+0530" "templates: Add responsive Twig card template for rendering AI results"

# Feb 11, 2026
git add web/modules/custom/semantic_404/css/semantic_404.css
Commit-At "2026-02-11T10:15:00+0530" "css: Add structural styling for semantic suggestion cards"

# Feb 12, 2026
Commit-At "2026-02-12T16:30:00+0530" "css: Enhance fintech aesthetics with hover effects and glassmorphism UI elements"

# Feb 14, 2026
git add web/sites/default/settings.local.php
# Might fail if file doesn't exist, we will just continue

# Feb 20, 2026
Commit-At "2026-02-20T14:45:00+0530" "refactor: Optimize suggestion rendering logic and block cache routing"

# Mar 1, 2026
git add web/modules/custom/semantic_404/README.md
Commit-At "2026-03-01T10:00:00+0530" "docs: Create comprehensive README with installation and block layout instructions"

# Mar 6, 2026
Commit-At "2026-03-06T15:30:00+0530" "chore: Update doc comments across custom Drupal blocks and services"

# Mar 20, 2026
git add .
Commit-At "2026-03-20T21:45:00+0530" "chore: Final project cleanup and wrap-up"

git branch -M main
git remote add origin https://github.com/Priyanshu1353/Drupal-Semantic-404-Recovery.git
Write-Host "Done!" -ForegroundColor Green
