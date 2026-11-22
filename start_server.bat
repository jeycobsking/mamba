@echo off
echo ==============================================================
echo MAMBA VILLAGE NAIROBI - WEB APPLICATION LAUNCHER
echo Starting PHP Built-in Server on http://localhost:8080...
echo ==============================================================
start "" "http://localhost:8080/index.php"
"C:\xampp\php\php.exe" -S localhost:8080 -t "%~dp0"
pause
