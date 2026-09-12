param(
    [string]$SiteName = 'CODISEC Laravel Staging',
    [int]$Port = 8086
)

$ErrorActionPreference = 'Stop'
$AppCmd = 'C:\Windows\System32\inetsrv\appcmd.exe'
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$PublicPath = Join-Path $ProjectRoot 'public'

if (-not (Test-Path -LiteralPath $AppCmd)) {
    throw 'IIS no está habilitado en este equipo. Instale IIS, CGI/FastCGI y URL Rewrite antes de ejecutar este script.'
}

& $AppCmd add apppool "/name:$SiteName" /managedRuntimeVersion:""
& $AppCmd add site "/name:$SiteName" "/bindings:http/*:${Port}:" "/physicalPath:$PublicPath"
& $AppCmd set app "$SiteName/" "/applicationPool:$SiteName"
Write-Output "Staging creado en http://localhost:$Port"
