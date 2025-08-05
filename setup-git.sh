#!/bin/bash

# 🚀 License Server Git Setup Script
# This script sets up the Git repository with proper branches and initial structure

echo "🚀 Setting up Git repository for License Server..."

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed. Please install Git first."
    exit 1
fi

# Initialize Git repository
echo "📁 Initializing Git repository..."
git init

# Add all files
echo "📦 Adding files to Git..."
git add .

# Create initial commit
echo "💾 Creating initial commit..."
git commit -m "feat: initial commit - license server system

- Add Laravel 11 license management system
- Implement AdminLTE 3 admin dashboard
- Add license verification API endpoints
- Include comprehensive dummy data
- Setup proper project structure
- Add documentation and commit conventions"

# Create main branch (if not already on main)
echo "🌿 Setting up main branch..."
git branch -M main

# Create develop branch
echo "🔧 Creating develop branch..."
git checkout -b develop

# Create staging branch
echo "🧪 Creating staging branch..."
git checkout -b staging

# Create production branch
echo "🚀 Creating production branch..."
git checkout -b production

# Go back to main branch
git checkout main

echo "✅ Git repository setup completed!"
echo ""
echo "📋 Branch Structure:"
echo "  🌿 main       - Production ready code"
echo "  🔧 develop    - Development branch"
echo "  🧪 staging    - Pre-production testing"
echo "  🚀 production - Production deployment"
echo ""
echo "📝 Next Steps:"
echo "  1. Add remote repository: git remote add origin <your-repo-url>"
echo "  2. Push all branches: git push -u origin main"
echo "  3. Push other branches: git push -u origin develop staging production"
echo ""
echo "📖 Commit Conventions:"
echo "  - feat:     New features"
echo "  - fix:      Bug fixes"
echo "  - docs:     Documentation changes"
echo "  - style:    Code formatting"
echo "  - refactor: Code refactoring"
echo "  - perf:     Performance improvements"
echo "  - chore:    Maintenance tasks"
echo "  - build:    Build system changes"
echo "  - ci:       CI/CD changes"
echo "  - revert:   Revert changes"
echo "  - merge:    Merge commits"
echo "  - security: Security fixes"
echo "  - WIP:      Work in progress"
echo ""
echo "🎯 Example commits:"
echo "  git commit -m 'feat: add license activation tracking'"
echo "  git commit -m 'fix: resolve validation error'"
echo "  git commit -m 'docs: update API documentation'"
echo ""
echo "📚 See COMMIT_CONVENTIONS.md for detailed guidelines" 