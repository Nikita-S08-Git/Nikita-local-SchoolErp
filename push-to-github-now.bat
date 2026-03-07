@echo off
cls
echo ========================================
echo   Push School ERP to GitHub
echo   Repository: Nikita-S08-Git/Nikita-local-SchoolErp
echo ========================================
echo.

cd c:\xampp\htdocs\School\School

echo Step 1: Removing old remote (if exists)...
"C:\Program Files\Git\cmd\git.exe" remote remove origin 2>nul

echo Step 2: Adding remote origin...
"C:\Program Files\Git\cmd\git.exe" remote add origin https://github.com/Nikita-S08-Git/Nikita-local-SchoolErp.git

echo Step 3: Checking connection...
"C:\Program Files\Git\cmd\git.exe" remote -v

echo.
echo ========================================
echo   PUSHING TO GITHUB
echo ========================================
echo.
echo You will be prompted for GitHub credentials.
echo.
echo IMPORTANT:
echo - Username: Your GitHub username
echo - Password: Use Personal Access Token (NOT regular password)
echo.
echo Get token at: https://github.com/settings/tokens
echo.
echo ========================================
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
) else (
    echo.
    echo ========================================
    echo   PUSH FAILED!
    echo ========================================
    echo.
    echo Possible reasons:
    echo 1. You don't have write access to the repository
    echo 2. Wrong credentials
    echo 3. Repository doesn't exist
    echo.
    echo Solutions:
    echo - Ask owner to add you as collaborator
    echo - Use Personal Access Token: https://github.com/settings/tokens
    echo - Or create your own repository
    echo.
)

pause
