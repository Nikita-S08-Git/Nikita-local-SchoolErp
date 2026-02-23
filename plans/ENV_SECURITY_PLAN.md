# Environment Configuration Security Plan

## Overview
This plan outlines the steps to remove the `.env` file from Git tracking and secure environment configuration for the Laravel 12 School ERP project.

## Current Status Analysis

### Issues Identified
1. ✅ `.env` is already in `.gitignore` (line 3)
2. ⚠️ `.env` file contains sensitive production data:
   - Database credentials: `u609425940_schoolerp` / `SE$2354@s`
   - Production APP_KEY: `base64:DbEXBTry0UMmzkz8rwQ3QlR30LQ+eHRZYvJTCSPWIUk=`
   - Production URL: `https://examination.lemmecode.in`
   - Session domain: `.lemmecode.in`
3. ⚠️ `.env` may still be tracked in Git history
4. ⚠️ `.env.example` needs School ERP specific placeholders

## Git Commands to Execute

### Step 1: Remove .env from Git Tracking
```bash
# Remove .env from Git index (stops tracking) but keep the local file
git rm --cached .env
```

### Step 2: Verify .env is in .gitignore
```bash
# Check if .env is already in .gitignore (it is, but verify)
type .gitignore | findstr ".env"
```

### Step 3: Commit the Changes
```bash
# Commit the removal of .env from tracking
git add .gitignore
git commit -m "security: Remove .env from Git tracking and update .env.example"
```

### Step 4: Verify .env is No Longer Tracked
```bash
# This should show .env is untracked
git status
```

### Step 5: Push Changes to Remote
```bash
# Push to remote repository
git push origin main
```

## Important Notes

### ⚠️ Git History Cleanup (Optional but Recommended)
The `.env` file may still exist in Git history. To completely remove it from all commits:

```bash
# WARNING: This rewrites Git history - coordinate with team first!
# Use BFG Repo-Cleaner (recommended) or git filter-branch

# Option 1: Using BFG Repo-Cleaner (faster, safer)
# Download from: https://rtyley.github.io/bfg-repo-cleaner/
java -jar bfg.jar --delete-files .env
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# Option 2: Using git filter-branch (built-in but slower)
git filter-branch --force --index-filter "git rm --cached --ignore-unmatch .env" --prune-empty --tag-name-filter cat -- --all
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# After cleanup, force push (requires team coordination)
git push origin --force --all
git push origin --force --tags
```

### 🔒 Security Recommendations
1. **Rotate Secrets**: After removing from Git, consider rotating:
   - Database password: `SE$2354@s`
   - APP_KEY (regenerate with `php artisan key:generate`)
   - Any API keys or tokens

2. **Team Communication**: Ensure all team members:
   - Have their own local `.env` file
   - Never commit `.env` files
   - Use `.env.example` as a template

3. **CI/CD Configuration**: Update deployment pipelines to use environment variables or secure secret management

## Updated .env.example Template

Create or update `.env.example` with the following safe placeholders:

```env
# Application Configuration
APP_NAME="School ERP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Localization
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Maintenance Mode
APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# Security
BCRYPT_ROUNDS=12

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Broadcasting & Queue
BROADCAST_CONNECTION=log
QUEUE_CONNECTION=sync

# Cache Configuration
CACHE_STORE=file
# CACHE_PREFIX=

# Filesystem
FILESYSTEM_DISK=local

# Redis Configuration (Optional)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Memcached Configuration (Optional)
MEMCACHED_HOST=127.0.0.1

# Mail Configuration
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@schoolerp.local"
MAIL_FROM_NAME="${APP_NAME}"

# AWS Configuration (Optional - for S3 storage)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Pusher Configuration (Optional - for real-time features)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Vite Configuration
VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# School ERP Specific Configuration
# SCHOOL_NAME="Your School Name"
# SCHOOL_CODE="SCH001"
# ACADEMIC_YEAR_START_MONTH=4
# DEFAULT_TIMEZONE=Asia/Calcutta
# ENABLE_SMS_NOTIFICATIONS=false
# ENABLE_EMAIL_NOTIFICATIONS=true
# MAX_STUDENTS_PER_DIVISION=60
# ENABLE_ONLINE_ADMISSION=true
# ENABLE_FEE_PAYMENT_GATEWAY=false
# PAYMENT_GATEWAY_KEY=
# PAYMENT_GATEWAY_SECRET=
```

## Implementation Checklist

- [ ] Backup current `.env` file to a secure location
- [ ] Run `git rm --cached .env` to stop tracking
- [ ] Update `.env.example` with safe placeholders
- [ ] Verify `.env` is in `.gitignore`
- [ ] Commit changes with descriptive message
- [ ] Push to remote repository
- [ ] Verify `.env` shows as untracked in `git status`
- [ ] Consider rotating sensitive credentials
- [ ] Optional: Clean Git history using BFG or filter-branch
- [ ] Document process for team members
- [ ] Update deployment/CI-CD configurations

## Quick Start Commands (Copy-Paste Ready)

```bash
# Step 1: Remove from tracking
git rm --cached .env

# Step 2: Stage .gitignore (already has .env)
git add .gitignore

# Step 3: Update .env.example (do this manually or via Code mode)
# Copy the template above to .env.example

# Step 4: Stage .env.example
git add .env.example

# Step 5: Commit
git commit -m "security: Remove .env from Git tracking and update .env.example with safe placeholders"

# Step 6: Verify
git status

# Step 7: Push
git push origin main
```

## Post-Implementation Verification

```bash
# Verify .env is not tracked
git ls-files | findstr ".env"
# Should only show .env.example, NOT .env

# Verify .env is ignored
git status
# .env should appear under "Untracked files" or not at all

# Check .gitignore
type .gitignore | findstr ".env"
# Should show .env is listed
```

## Team Communication Template

```
Subject: Important: .env File Security Update

Team,

We have removed the .env file from Git tracking to improve security. 

Action Required:
1. Pull the latest changes: git pull origin main
2. Ensure you have a local .env file (it won't be deleted)
3. If you need to recreate .env, copy from .env.example:
   copy .env.example .env
4. Update your .env with your local configuration
5. Run: php artisan key:generate (if APP_KEY is empty)

Important:
- NEVER commit .env files
- Use .env.example as a template
- Keep sensitive credentials secure

Questions? Contact the development lead.
```

## Additional Security Measures

### 1. Add Pre-commit Hook
Create `.git/hooks/pre-commit` to prevent accidental commits:

```bash
#!/bin/sh
if git diff --cached --name-only | grep -q "^.env$"; then
    echo "Error: Attempting to commit .env file!"
    echo "Please remove .env from your commit."
    exit 1
fi
```

### 2. Use Environment-Specific Files
Consider using:
- `.env.local` - Local development
- `.env.staging` - Staging environment
- `.env.production` - Production (never commit!)

All should be in `.gitignore`.

### 3. Secret Management Tools
For production, consider:
- Laravel Forge
- AWS Secrets Manager
- HashiCorp Vault
- Azure Key Vault
- Google Cloud Secret Manager

## References
- [Laravel Environment Configuration](https://laravel.com/docs/12.x/configuration#environment-configuration)
- [Git Remove Sensitive Data](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/removing-sensitive-data-from-a-repository)
- [BFG Repo-Cleaner](https://rtyley.github.io/bfg-repo-cleaner/)
