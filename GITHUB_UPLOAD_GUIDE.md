# 🚀 Quick GitHub Upload Guide

## Option 1: Using the Batch Script (Easiest - Windows)

1. **Double-click** `upload-to-github.bat` in your project folder
2. **Enter** your GitHub username when prompted
3. **Follow** the on-screen instructions
4. **Enter** your GitHub credentials when asked
   - Use Personal Access Token for password (see below)

## Option 2: Manual Commands

### Prerequisites
- Install Git: https://git-scm.com/download/win
- Create GitHub account: https://github.com/signup

### Steps

1. **Create Repository on GitHub**
   - Go to https://github.com/new
   - Repository name: `School-Erp`
   - Choose Public or Private
   - **DO NOT** initialize with README/.gitignore
   - Click "Create repository"

2. **Open Command Prompt in project folder**
   ```bash
   cd c:\xampp\htdocs\School\School
   ```

3. **Run these commands one by one**
   ```bash
   git init
   git add .
   git commit -m "Initial commit: School ERP System"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/School-Erp.git
   git push -u origin main
   ```

4. **Enter credentials when prompted**
   - Username: Your GitHub username
   - Password: Your GitHub Personal Access Token (NOT your GitHub password!)

## Creating Personal Access Token

1. Go to https://github.com/settings/tokens
2. Click "Generate new token (classic)"
3. Give it a name (e.g., "School ERP Upload")
4. Select these scopes:
   - ✅ repo (full control of private repositories)
5. Click "Generate token"
6. **COPY THE TOKEN IMMEDIATELY** - you won't see it again!
7. Use this token as your password when pushing

## Troubleshooting

### "Git is not recognized"
- Git is not installed or not in PATH
- Solution: Install Git from https://git-scm.com/download/win
- Or add Git to PATH: `C:\Program Files\Git\cmd`

### "Authentication failed"
- You're using your GitHub password instead of Personal Access Token
- Solution: Create token at https://github.com/settings/tokens

### "Remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/School-Erp.git
```

### "Nothing to commit"
- Files might be ignored by .gitignore
- Or already committed
- Check with: `git status`

### "Permission denied"
- Make sure repository is created on GitHub
- Check you're using correct username in URL
- Verify token has repo scope

## After Upload

1. **Verify upload**
   - Visit: https://github.com/YOUR_USERNAME/School-Erp

2. **Update README.md**
   - Replace `YOUR_USERNAME` with your actual username
   - Add your email in Author section
   - Add screenshots if available

3. **Add topics to repository**
   - Go to repository Settings
   - Add topics: `laravel`, `school-management`, `erp`, `php`, `bootstrap`

4. **Share your project!**
   - Add to your resume
   - Share on LinkedIn
   - Show to friends and colleagues

## Useful Git Commands

```bash
# Check status
git status

# View commit history
git log --oneline

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Discard local changes
git checkout -- filename

# Pull latest changes
git pull origin main

# Add new files after initial commit
git add .
git commit -m "Add new files"
git push
```

## Need Help?

- Git Documentation: https://git-scm.com/doc
- GitHub Guides: https://guides.github.com/
- Laravel Documentation: https://laravel.com/docs

---

Good luck with your upload! 🎉
