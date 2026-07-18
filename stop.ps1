<#
    Beendet die von start.ps1 gestarteten Prozesse (Laravel serve/queue/schedule).
    Ruehrt MySQL ("MySQL80") absichtlich NICHT an - dieser Dienst laeuft dauerhaft
    und wird von anderen lokalen Projekten mitgenutzt.
#>

$ProjectRoot = $PSScriptRoot
$PidFile = Join-Path $ProjectRoot '.run\pids.json'

if (-not (Test-Path $PidFile)) {
    Write-Host "Keine laufenden Prozesse gefunden (.run\pids.json existiert nicht)."
    exit 0
}

$pids = Get-Content $PidFile | ConvertFrom-Json
foreach ($property in $pids.PSObject.Properties) {
    $processId = $property.Value
    # taskkill /T beendet auch Kindprozesse - noetig, da z.B. "php artisan serve" intern
    # einen eigenen "php -S ..."-Kindprozess startet, der sonst als Waise weiterlaeuft.
    taskkill /F /T /PID $processId *> $null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Prozess $($property.Name) (PID $processId) gestoppt."
    }
    else {
        Write-Host "Prozess $($property.Name) (PID $processId) lief bereits nicht mehr."
    }
}

Remove-Item $PidFile -Force

# taskkill /F beendet Vite hart, ohne dass es seine eigene "public/hot"-Datei aufraeumt - ohne
# diesen Schritt wuerde @vite() weiter versuchen, Assets vom (toten) Dev-Server zu laden.
$HotFile = Join-Path $ProjectRoot 'public\hot'
if (Test-Path $HotFile) {
    Remove-Item $HotFile -Force
}

Write-Host "App gestoppt."
