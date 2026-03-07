@echo off
cls
echo ========================================
echo   Create New GitHub Repository
echo ========================================
echo.
echo This script will help you push to YOUR OWN GitHub account.
echo.
echo Steps:
echo 1. Go to https://github.com/new
echo 2. Create a new repository
echo 3. Copy the repository URL
echo 4. Run this script
echo.
echo ========================================
echo.

set /p REPO_URL="Enter your new repository URL: "

if "%REPO_URL%"=="" (
    echo ERROR: Repository URL cannot be empty!
    pause
    exit /b 1
)

cd c:\xampp\htdocs\School\School

echo.
echo Configuring remote to: %REPO_URL%
echo.

"C:\Program Files\Git\cmd\git.exe" remote remove origin 2>nul
"C:\Program Files\Git\cmd\git.exe" remote add origin %REPO_URL%

echo.
echo ========================================
echo   Pushing to GitHub
echo ========================================
echo.
echo You will be prompted for GitHub credentials.
echo Use Personal Access Token as password.
echo Get token: https://github.com/settings/tokens
echo.

pause

"C:\Program Files\Git\cmd\git.exe" push -u origin main --force

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   SUCCESS!
    echo ========================================
    echo.
    echo Project uploaded!
    echo Repository: %REPO_URL%
    echo.
) else (
    echo.
    echo ========================================
    echo   PUSH FAILED!
    echo ========================================
    echo.
    echo Make sure:
    echo 1. Repository exists on GitHub
    echo 2. You're logged into correct account
    echo 3. Using Personal Access Token as password
    echo.
)

pause
