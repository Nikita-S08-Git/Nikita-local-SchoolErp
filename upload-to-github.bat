@echo off
echo ========================================
echo   School ERP - GitHub Upload Script
echo ========================================
echo.

REM Check if git is installed
where git >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Git is not installed or not in PATH!
    echo Please install Git from: https://git-scm.com/download/win
    echo.
    pause
    exit /b 1
)

echo Git found!
echo.

REM Get GitHub username
set /p GITHUB_USERNAME="Enter your GitHub username: "
if "%GITHUB_USERNAME%"=="" (
    echo ERROR: GitHub username cannot be empty!
    pause
    exit /b 1
)

echo.
echo Repository will be: https://github.com/%GITHUB_USERNAME%/School-Erp
echo.
set /p CONFIRM="Continue? (Y/N): "
if /i not "%CONFIRM%"=="Y" (
    echo Upload cancelled.
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Step 1: Initializing Git Repository
echo ========================================
git init
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to initialize git repository!
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Step 2: Adding All Files
echo ========================================
git add .
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to add files!
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Step 3: Creating Initial Commit
echo ========================================
git commit -m "Initial commit: School ERP System"
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Commit might have failed (possibly nothing to commit)
)

echo.
echo ========================================
echo   Step 4: Renaming Branch to Main
echo ========================================
git branch -M main

echo.
echo ========================================
echo   Step 5: Adding GitHub Remote
echo ========================================
git remote remove origin 2>nul
git remote add origin https://github.com/%GITHUB_USERNAME%/School-Erp.git
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to add remote!
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Step 6: Pushing to GitHub
echo ========================================
echo.
echo IMPORTANT: You will be asked for your GitHub credentials.
echo - For password, use your GitHub Personal Access Token
echo - Create token at: https://github.com/settings/tokens
echo.
git push -u origin main

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ========================================
    echo   PUSH FAILED!
    echo ========================================
    echo.
    echo Possible solutions:
    echo 1. Make sure you created the repository on GitHub first
    echo 2. Use a Personal Access Token instead of password
    echo 3. Check your internet connection
    echo.
    echo To create a Personal Access Token:
    echo 1. Go to: https://github.com/settings/tokens
    echo 2. Click "Generate new token (classic)"
    echo 3. Give it a name and select "repo" scope
    echo 4. Copy the token and use it as password
    echo.
    pause
    exit /b 1
)

echo.
echo ========================================
echo   SUCCESS!
echo ========================================
echo.
echo Your project has been uploaded to GitHub!
echo URL: https://github.com/%GITHUB_USERNAME%/School-Erp
echo.
echo Next steps:
echo 1. Visit the repository URL above
echo 2. Add screenshots to the screenshots/ folder
echo 3. Update README.md with your information
echo 4. Share your project!
echo.
pause
