@echo off
cls
echo ========================================
echo   School ERP - GitHub Upload Script
echo   Repository: Lemmecode-com/School-Erp
echo ========================================
echo.

echo STEP 1: Install Git
echo ====================
echo.
echo Git is not installed or not in PATH.
echo.
echo Please install Git by following these steps:
echo.
echo 1. Download Git from: https://git-scm.com/download/win
echo 2. Run the installer
echo 3. Use default settings
echo 4. Complete the installation
echo.
echo After installation, CLOSE this window and double-click this script again.
echo.
echo OR manually run these commands in Command Prompt:
echo.
echo cd c:\xampp\htdocs\School\School
echo git init
echo git add .
echo git commit -m "Initial commit: School ERP System"
echo git branch -M develop
echo git remote add origin https://github.com/Lemmecode-com/School-Erp.git
echo git push -u origin develop
echo.
echo ========================================
pause
