@echo off
echo ==============================================================
echo MAMBA VILLAGE NAIROBI - WEB PLATFORM PROTOTYPE PREVIEW
echo CISY 401 Final Year Project (Kenya Methodist University)
echo ==============================================================
echo Opening prototype in your default web browser...
start "" "%~dp0index.html"
echo.
echo If you prefer a local HTTP server, starting Python web server on port 8080...
python -m http.server 8080 --directory "%~dp0"
pause

