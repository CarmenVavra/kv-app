@echo off
setlocal
rem Vite-Dev-Server (falls "npm run dev" laeuft): fester Port, siehe vite.config.js
set "VITE_PORT=6450"
for /f "tokens=5" %%v in ('netstat -ano ^| findstr /R /C:":%VITE_PORT%  *0\.0\.0\.0:0 " /C:":%VITE_PORT%  *\[::\]:0 "') do taskkill /PID %%v /T /F >nul 2>&1
if exist "%~dp0public\hot" del /q "%~dp0public\hot" >nul 2>&1
powershell -ExecutionPolicy Bypass -File "%~dp0stop.ps1"
pause
endlocal
