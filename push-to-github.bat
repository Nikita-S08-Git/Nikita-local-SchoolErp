@echo off
echo ========================================
echo Pushing to GitHub - Branch: Teacher-M/P1-06-dynamic-dashboard-data
echo ========================================
echo.

cd /d "%~dp0"

echo Adding all files...
git add -A

echo.
echo Enter commit message:
set /p COMMIT_MSG="> "

echo.
echo Committing changes...
git commit -m "%COMMIT_MSG%"

echo.
echo Pushing to remote...
git push origin Teacher-M/P1-06-dynamic-dashboard-data

echo.
echo ========================================
echo Done! Code pushed successfully.
echo ========================================
pause
