@echo off
echo ========================================================
echo Starting Runit Events Hub on PHP Built-in Server
echo Root Directory: development/public
echo Open: http://localhost:8000
echo ========================================================
"C:\xampp\php\php.exe" -S localhost:8000 -t development/public
pause
