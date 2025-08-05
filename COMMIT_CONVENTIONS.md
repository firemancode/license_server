# 📝 Commit Message Conventions

This document outlines the commit message conventions used in this project.

## 🏷️ Commit Types

### `feat`
Digunakan untuk menambahkan fitur baru ke aplikasi.
```bash
git commit -m "feat: add license activation tracking"
git commit -m "feat: implement bulk license operations"
```

### `fix`
Digunakan untuk commit yang memperbaiki bug atau masalah dalam aplikasi.
```bash
git commit -m "fix: resolve license validation error"
git commit -m "fix: correct database migration issue"
```

### `docs`
Digunakan untuk perubahan yang hanya berkaitan dengan dokumentasi.
```bash
git commit -m "docs: update API documentation"
git commit -m "docs: add installation guide"
```

### `style`
Digunakan untuk perubahan yang hanya berkaitan dengan format atau gaya kode, seperti indentasi, pemformatan ulang, atau spasi. Tidak mengubah fungsionalitas kode.
```bash
git commit -m "style: format code according to PSR-12"
git commit -m "style: fix indentation in blade templates"
```

### `refactor`
Digunakan untuk perubahan kode yang tidak memperbaiki bug atau menambah fitur, tetapi hanya untuk perbaikan internal kode seperti refactor untuk meningkatkan performa atau maintainability.
```bash
git commit -m "refactor: optimize license validation logic"
git commit -m "refactor: improve database query performance"
```

### `perf`
Digunakan untuk perubahan yang meningkatkan performa aplikasi.
```bash
git commit -m "perf: optimize database queries"
git commit -m "perf: improve API response time"
```

### `chore`
Digunakan untuk commit yang berkaitan dengan tugas rutin yang tidak mengubah fungsionalitas aplikasi, seperti pembaruan dependensi, build script, atau pengaturan konfigurasi proyek.
```bash
git commit -m "chore: update composer dependencies"
git commit -m "chore: update npm packages"
```

### `build`
Digunakan untuk perubahan yang berkaitan dengan sistem build, seperti pengaturan webpack, script build, atau CI/CD pipeline.
```bash
git commit -m "build: update webpack configuration"
git commit -m "build: add production build script"
```

### `ci`
Digunakan untuk perubahan yang berkaitan dengan konfigurasi CI/CD, misalnya perubahan pada .gitlab-ci.yml, .github/workflows, atau file lainnya terkait build dan deploy.
```bash
git commit -m "ci: add GitHub Actions workflow"
git commit -m "ci: configure automated testing"
```

### `revert`
Digunakan untuk mengembalikan commit sebelumnya (rollback). Ini sering digunakan untuk membatalkan perubahan yang tidak diinginkan.
```bash
git commit -m "revert: remove experimental feature"
git commit -m "revert: rollback to previous stable version"
```

### `merge`
Digunakan untuk commit yang dilakukan setelah penggabungan (merge) cabang (branch).
```bash
git commit -m "merge: integrate feature branch into main"
git commit -m "merge: resolve conflicts in license controller"
```

### `security`
Digunakan untuk commit yang berhubungan dengan perbaikan atau peningkatan keamanan.
```bash
git commit -m "security: fix SQL injection vulnerability"
git commit -m "security: implement rate limiting"
```

### `WIP`
Digunakan untuk commit yang menandakan bahwa pekerjaan masih dalam proses dan belum selesai. Biasanya digunakan untuk berbagi progres dengan tim atau menyimpan pekerjaan sementara.
```bash
git commit -m "WIP: implementing license validation"
git commit -m "WIP: working on admin dashboard"
```

## 📋 Format Commit Message

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Contoh Lengkap:
```bash
feat(license): add bulk activation feature

- Implement bulk license activation
- Add validation for multiple licenses
- Update admin interface for bulk operations

Closes #123
```

## 🌿 Branch Strategy

### Main Branches
- `main` - Production ready code
- `develop` - Development branch
- `staging` - Pre-production testing

### Feature Branches
- `feature/` - New features
- `bugfix/` - Bug fixes
- `hotfix/` - Critical fixes for production

### Contoh Branch Naming:
```bash
feature/license-activation
bugfix/validation-error
hotfix/security-vulnerability
```

## 🔄 Workflow

1. **Create Feature Branch**
   ```bash
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

## 📊 Branch Protection Rules

### Main Branch
- Require pull request reviews
- Require status checks to pass
- Restrict pushes to main branch

### Develop Branch
- Require pull request reviews
- Allow auto-merge for minor changes

### Staging Branch
- Require pull request reviews
- Require approval from senior developers

## 🚀 Release Process

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

## 📝 Contoh Commit Messages

### Feature Development
```bash
feat(license): implement license key generation
feat(admin): add bulk license operations
feat(api): add license verification endpoint
```

### Bug Fixes
```bash
fix(validation): resolve license key format issue
fix(database): correct migration timestamp
fix(ui): fix responsive layout on mobile
```

### Documentation
```bash
docs(readme): update installation instructions
docs(api): add endpoint documentation
docs(deployment): add production setup guide
```

### Refactoring
```bash
refactor(controller): simplify license validation logic
refactor(model): improve database relationships
refactor(view): extract reusable components
```

### Performance
```bash
perf(database): optimize license queries
perf(api): improve response time
perf(cache): implement Redis caching
```

### Security
```bash
security(auth): implement rate limiting
security(validation): fix SQL injection vulnerability
security(api): add request signature verification
```

---

**Note**: Always use present tense in commit messages and be descriptive about what the commit does. 