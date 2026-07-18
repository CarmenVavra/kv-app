<#
    Startet den kompletten App-Stack fuer die lokale Entwicklung:
    - prueft, dass MySQL erreichbar ist (laeuft hier als Windows-Dienst "MySQL80",
      geteilt mit anderen lokalen Projekten - wird von diesem Skript NICHT gestartet/gestoppt)
    - php artisan serve (Webserver)
    - php artisan queue:work (Mail-Versand etc.)
    - php artisan schedule:work (Mahnwesen-/Erinnerungs-Jobs, lokales Aequivalent zum Cron auf Netcup)
#>

$ErrorActionPreference = 'Stop'
$ProjectRoot = $PSScriptRoot
$RunDir = Join-Path $ProjectRoot '.run'
New-Item -ItemType Directory -Force -Path $RunDir | Out-Null

Write-Host "Pruefe MySQL-Verbindung (127.0.0.1:3306)..."
$mysqlOk = Test-NetConnection -ComputerName '127.0.0.1' -Port 3306 -WarningAction SilentlyContinue
if (-not $mysqlOk.TcpTestSucceeded) {
    Write-Warning "MySQL ist auf 127.0.0.1:3306 nicht erreichbar. Bitte den MySQL-Dienst (z.B. 'MySQL80') pruefen."
    exit 1
}
Write-Host "MySQL ist erreichbar."

Push-Location $ProjectRoot
try {
    $serve = Start-Process -FilePath 'php' -ArgumentList 'artisan', 'serve' -PassThru -WindowStyle Minimized
    $queue = Start-Process -FilePath 'php' -ArgumentList 'artisan', 'queue:work', '--tries=1' -PassThru -WindowStyle Minimized
    $sched = Start-Process -FilePath 'php' -ArgumentList 'artisan', 'schedule:work' -PassThru -WindowStyle Minimized

    @{ serve = $serve.Id; queue = $queue.Id; schedule = $sched.Id } |
        ConvertTo-Json | Set-Content (Join-Path $RunDir 'pids.json')
}
finally {
    Pop-Location
}

Write-Host "Laravel laeuft unter http://127.0.0.1:8000"
Write-Host "Zum Beenden: .\stop.ps1"
