@echo off
cls
echo ========================================
echo   Upload School ERP to GitHub
echo   Repository: Nikita-S08-Git/Nikita-local-SchoolErp
echo ========================================
echo.

cd c:\xampp\htdocs\School\School

echo Current Git Status:
"C:\Program Files\Git\cmd\git.exe" status --short
echo.

echo ========================================
echo   STEP 1: Configure Remote
echo ========================================
"C:\Program Files\Git\cmd\git.exe" remote remove origin 2>nul
"C:\Program Files\Git\cmd\git.exe" remote add origin https://github.com/Nikita-S08-Git/Nikita-local-SchoolErp.git
echo Remote configured: https://github.com/Nikita-S08-Git/Nikita-local-SchoolErp.git
echo.

echo ========================================
echo   STEP 2: Push to GitHub
echo ========================================
echo.
echo IMPORTANT - Authentication:
echo.
echo When prompted for credentials:
echo   Username: Enter your GitHub username
echo   Password: Enter Personal Access Token
echo.
echo Get Personal Access Token:
echo 1. Go to: https://github.com/settings/tokens
echo 2. Click "Generate new token (classic)"
echo 3. Select scope: repo
echo 4. Copy the token
echo.
echo ========================================
echo.

pause
echo.
echo Pushing to GitHub now...
echo.

"C:\Program Files\Git\cmd\git.exe" push -u origin main --force

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   SUCCESS!
    echo ========================================
    echo.
    echo Project uploaded to:
    echo https://github.com/Nikita-S08-Git/Nikita-local-SchoolErp
    echo.
    pause
    exit /b 0
) else (
    echo.
    echo ========================================
    echo   PUSH FAILED!
    echo ========================================
    echo.
    echo Error: Permission denied (403)
    echo.
    echo This means:
    echo 1. You don't have write access to this repository
    echo 2. OR you're using wrong credentials
    echo.
    echo Solutions:
    echo.
    echo A) Ask repository owner (Nikita-S08-Git) to:
    echo    - Go to repository Settings
    echo    - Click "Collaborators"
    echo    - Add your GitHub username as collaborator
    echo.
    echo B) OR create your own repository:
    echo    - Go to https://github.com/new
    echo    - Create: Nikita-local-SchoolErp
    echo    - Then run this script again
    echo.
    echo C) OR use token in command:
    echo    - Get token from https://github.com/settings/tokens
    echo    - Run: git remote set-url origin https://USERNAME:TOKEN@github.com/Nikita-S08-Git/Nikita-local-SchoolErp.git
    echo    - Run: git push -u origin main
    echo.
    pause
    exit /b 1
)
