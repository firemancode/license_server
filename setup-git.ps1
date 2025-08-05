# 🚀 License Server Git Setup Script (PowerShell)
# This script sets up the Git repository with proper branches and initial structure

Write-Host "🚀 Setting up Git repository for License Server..." -ForegroundColor Green

# Check if git is installed
try {
    $gitVersion = git --version
    Write-Host "✅ Git found: $gitVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Git is not installed. Please install Git first." -ForegroundColor Red
    exit 1
}

# Initialize Git repository
Write-Host "📁 Initializing Git repository..." -ForegroundColor Yellow
git init

# Add all files
Write-Host "📦 Adding files to Git..." -ForegroundColor Yellow
git add .

# Create initial commit
Write-Host "💾 Creating initial commit..." -ForegroundColor Yellow
git commit -m "feat: initial commit - license server system

- Add Laravel 11 license management system
- Implement AdminLTE 3 admin dashboard
- Add license verification API endpoints
- Include comprehensive dummy data
- Setup proper project structure
- Add documentation and commit conventions"

# Create main branch (if not already on main)
Write-Host "🌿 Setting up main branch..." -ForegroundColor Yellow
git branch -M main

# Create develop branch
Write-Host "🔧 Creating develop branch..." -ForegroundColor Yellow
git checkout -b develop

# Create staging branch
Write-Host "🧪 Creating staging branch..." -ForegroundColor Yellow
git checkout -b staging

# Create production branch
Write-Host "🚀 Creating production branch..." -ForegroundColor Yellow
git checkout -b production

# Go back to main branch
git checkout main

Write-Host "✅ Git repository setup completed!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Branch Structure:" -ForegroundColor Cyan
Write-Host "  🌿 main       - Production ready code" -ForegroundColor White
Write-Host "  🔧 develop    - Development branch" -ForegroundColor White
Write-Host "  🧪 staging    - Pre-production testing" -ForegroundColor White
Write-Host "  🚀 production - Production deployment" -ForegroundColor White
Write-Host ""
Write-Host "📝 Next Steps:" -ForegroundColor Cyan
Write-Host "  1. Add remote repository: git remote add origin <your-repo-url>" -ForegroundColor White
Write-Host "  2. Push all branches: git push -u origin main" -ForegroundColor White
Write-Host "  3. Push other branches: git push -u origin develop staging production" -ForegroundColor White
Write-Host ""
Write-Host "📖 Commit Conventions:" -ForegroundColor Cyan
Write-Host "  - feat:     New features" -ForegroundColor White
Write-Host "  - fix:      Bug fixes" -ForegroundColor White
Write-Host "  - docs:     Documentation changes" -ForegroundColor White
Write-Host "  - style:    Code formatting" -ForegroundColor White
Write-Host "  - refactor: Code refactoring" -ForegroundColor White
Write-Host "  - perf:     Performance improvements" -ForegroundColor White
Write-Host "  - chore:    Maintenance tasks" -ForegroundColor White
Write-Host "  - build:    Build system changes" -ForegroundColor White
Write-Host "  - ci:       CI/CD changes" -ForegroundColor White
Write-Host "  - revert:   Revert changes" -ForegroundColor White
Write-Host "  - merge:    Merge commits" -ForegroundColor White
Write-Host "  - security: Security fixes" -ForegroundColor White
Write-Host "  - WIP:      Work in progress" -ForegroundColor White
Write-Host ""
Write-Host "🎯 Example commits:" -ForegroundColor Cyan
Write-Host "  git commit -m 'feat: add license activation tracking'" -ForegroundColor White
Write-Host "  git commit -m 'fix: resolve validation error'" -ForegroundColor White
Write-Host "  git commit -m 'docs: update API documentation'" -ForegroundColor White
Write-Host ""
Write-Host "📚 See COMMIT_CONVENTIONS.md for detailed guidelines" -ForegroundColor Cyan

# Show current branch status
Write-Host ""
Write-Host "Current Git Status:" -ForegroundColor Cyan
git status
Write-Host ""
git branch -a 