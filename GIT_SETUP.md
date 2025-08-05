# 🚀 Manual Git Setup Guide

Karena Git tidak terdeteksi di sistem Anda, berikut adalah panduan manual untuk setup repository Git.

## 📋 Prerequisites

### 1. Install Git
Download dan install Git dari: https://git-scm.com/downloads

### 2. Configure Git
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

## 🛠️ Manual Setup Steps

### Step 1: Initialize Repository
```bash
cd license-server
git init
```

### Step 2: Add Files
```bash
git add .
```

### Step 3: Create Initial Commit
```bash
git commit -m "feat: initial commit - license server system

- Add Laravel 11 license management system
- Implement AdminLTE 3 admin dashboard
- Add license verification API endpoints
- Include comprehensive dummy data
- Setup proper project structure
- Add documentation and commit conventions"
```

### Step 4: Create Branches
```bash
# Set main as default branch
git branch -M main

# Create develop branch
git checkout -b develop

# Create staging branch
git checkout -b staging

# Create production branch
git checkout -b production

# Return to main branch
git checkout main
```

### Step 5: Add Remote Repository
```bash
# Replace with your actual repository URL
git remote add origin https://github.com/yourusername/license-server.git
```

### Step 6: Push to Remote
```bash
# Push main branch
git push -u origin main

# Push other branches
git push -u origin develop
git push -u origin staging
git push -u origin production
```

## 📊 Branch Structure

```
🌿 main       - Production ready code
🔧 develop    - Development branch
🧪 staging    - Pre-production testing
🚀 production - Production deployment
```

## 📝 Commit Conventions

### Commit Types
- `feat` - New features
- `fix` - Bug fixes
- `docs` - Documentation changes
- `style` - Code formatting
- `refactor` - Code refactoring
- `perf` - Performance improvements
- `chore` - Maintenance tasks
- `build` - Build system changes
- `ci` - CI/CD changes
- `revert` - Revert changes
- `merge` - Merge commits
- `security` - Security fixes
- `WIP` - Work in progress

### Examples
```bash
git commit -m "feat: add license activation tracking"
git commit -m "fix: resolve validation error"
git commit -m "docs: update API documentation"
git commit -m "style: format code according to PSR-12"
git commit -m "refactor: optimize license validation logic"
git commit -m "perf: improve database query performance"
git commit -m "chore: update composer dependencies"
git commit -m "build: update webpack configuration"
git commit -m "ci: add GitHub Actions workflow"
git commit -m "security: fix SQL injection vulnerability"
```

## 🔄 Workflow

### Development Workflow
1. **Create Feature Branch**
   ```bash
   git checkout develop
   git checkout -b feature/new-feature
   ```

2. **Make Changes & Commit**
   ```bash
   git add .
   git commit -m "feat: add new license validation"
   ```

3. **Push to Remote**
   ```bash
   git push origin feature/new-feature
   ```

4. **Create Pull Request**
   - Merge to `develop` for testing
   - Merge to `staging` for pre-production
   - Merge to `main` for production

### Release Process
1. **Create Release Branch**
   ```bash
   git checkout -b release/v1.2.0
   ```

2. **Update Version**
   ```bash
   # Update version in composer.json
   # Update CHANGELOG.md
   git commit -m "chore: bump version to 1.2.0"
   ```

3. **Merge to Main**
   ```bash
   git checkout main
   git merge release/v1.2.0
   git tag v1.2.0
   ```

4. **Cleanup**
   ```bash
   git branch -d release/v1.2.0
   ```

## 📚 Additional Resources

- **Git Documentation**: https://git-scm.com/doc
- **GitHub Guides**: https://guides.github.com/
- **Conventional Commits**: https://www.conventionalcommits.org/

## 🎯 Quick Commands

### Check Status
```bash
git status
git branch -a
git log --oneline
```

### Switch Branches
```bash
git checkout main
git checkout develop
git checkout staging
git checkout production
```

### Update Branches
```bash
git pull origin main
git pull origin develop
```

### Create New Feature
```bash
git checkout develop
git checkout -b feature/your-feature-name
# Make changes
git add .
git commit -m "feat: your feature description"
git push origin feature/your-feature-name
```

---

**Note**: Setelah Git terinstall, Anda bisa menjalankan script `setup-git.ps1` untuk setup otomatis. 