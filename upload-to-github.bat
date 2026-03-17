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
echo Repository: https://github.com/%GITHUB_USERNAME%/Nikita-local-SchoolErp
echo Branch: Teacher_M
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
git commit -m "Student Dashboard System - Teacher_M branch"
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Commit might have failed (possibly nothing to commit)
)

echo.
echo ========================================
echo   Step 4: Renaming Branch to Teacher_M
echo ========================================
git branch -M Teacher_M

echo.
echo ========================================
echo   Step 5: Adding GitHub Remote
echo ========================================
git remote remove origin 2>nul
git remote add origin https://github.com/%GITHUB_USERNAME%/Nikita-local-SchoolErp.git
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to add remote!
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Step 6: Pushing to GitHub (Teacher_M branch)
echo ========================================
echo.
echo IMPORTANT: You will be asked for your GitHub credentials.
echo - For password, use your GitHub Personal Access Token
echo - Create token at: https://github.com/settings/tokens
echo.
git push -u origin Teacher_M

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ========================================
    echo   PUSH FAILED!
    echo ========================================
    echo.
    echo Possible solutions:
    echo 1. Make sure the repository exists on GitHub
    echo 2. Make sure branch 'Teacher_M' exists or create it
    echo 3. Use a Personal Access Token instead of password
    echo 4. Check your internet connection
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
echo Repository: https://github.com/%GITHUB_USERNAME%/Nikita-local-SchoolErp
echo Branch: Teacher_M
echo.
echo Next steps after cloning on another machine:
echo 1. Run: composer install
echo 2. Run: cp .env.example .env
echo 3. Run: php artisan key:generate
echo 4. Run: php artisan migrate:fresh --seed
echo 5. Run: php artisan serve
echo.
pause
