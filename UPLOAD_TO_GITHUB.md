# 📤 Upload Project to GitHub - Step by Step

## Repository URL
```
https://github.com/Lemmecode-com/School-Erp
```

## ⚡ Quick Upload (After Installing Git)

### Step 1: Install Git
1. Download: https://git-scm.com/download/win
2. Install with default settings
3. Restart Command Prompt

### Step 2: Upload Commands
Open Command Prompt and run:

```bash
cd c:\xampp\htdocs\School\School

git init
git add .
git commit -m "Initial commit: School ERP System"
git branch -M develop
git remote add origin https://github.com/Lemmecode-com/School-Erp.git
git push -u origin develop
```

### Step 3: Enter Credentials
When prompted:
- **Username**: Your GitHub username
- **Password**: GitHub Personal Access Token (NOT regular password)

### Get Personal Access Token:
1. Go to: https://github.com/settings/tokens
2. Click "Generate new token (classic)"
3. Name: "School ERP Upload"
4. Select scope: ✅ `repo`
5. Click "Generate token"
6. **COPY THE TOKEN** (won't see it again!)
7. Use as password when pushing

---

## 🖱️ Alternative: GitHub Desktop (No Commands)

1. **Download GitHub Desktop**
   - https://desktop.github.com/
   - Install and sign in

2. **Add Your Project**
   - File → Add Local Repository
   - Choose: `c:\xampp\htdocs\School\School`
   - Click "Add repository"

3. **Publish to GitHub**
   - Click "Publish repository"
   - Name: `School-Erp`
   - Organization: `Lemmecode-com` (if you have access)
   - Or publish to your account and transfer later

---

## 📋 Manual Upload (No Git Installation)

1. **Zip Your Project**
   - Right-click `School` folder
   - Send to → Compressed (zipped) folder

2. **Upload to GitHub**
   - Go to: https://github.com/Lemmecode-com/School-Erp
   - Create new branch: `develop`
   - Click "Upload files"
   - Drag your zip file
   - Commit changes

---

## ✅ Verify Upload

After pushing, check:
```
https://github.com/Lemmecode-com/School-Erp/tree/develop
```

You should see all your project files!

---

## 🔧 Troubleshooting

### "Git not recognized"
- Git not installed
- Solution: Install from https://git-scm.com/download/win

### "Authentication failed"
- Using wrong password
- Solution: Use Personal Access Token, not GitHub password

### "Repository not found"
- Make sure you have access to Lemmecode-com organization
- Or create repo at your account first

### "Permission denied"
- You need write access to the repository
- Contact repository owner for access

---

## 📞 Need Help?

Contact: Check repository for contact information

Good luck! 🚀
