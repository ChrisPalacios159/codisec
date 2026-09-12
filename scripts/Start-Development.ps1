param(
    [string]$PhpPath = 'C:\Users\Usuario\Tools\php-8.3.33\php.exe',
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'
$ProjectRoot = Split-Path -Parent $PSScriptRoot

if (-not (Test-Path -LiteralPath $PhpPath)) {
    throw "No se encontró PHP 8.3 en $PhpPath"
}

if (-not (Get-NetTCPConnection -LocalPort 3306 -State Listen -ErrorAction SilentlyContinue)) {
    $MySql = 'C:\xampp\mysql\bin\mysqld.exe'
    if (-not (Test-Path -LiteralPath $MySql)) {
        throw 'No se encontró MariaDB de XAMPP.'
    }
    Start-Process -FilePath $MySql -ArgumentList '--defaults-file=C:\xampp\mysql\bin\my.ini' -WindowStyle Hidden
    Start-Sleep -Seconds 2
}

if (-not (Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue)) {
    Start-Process -FilePath $PhpPath -ArgumentList @('artisan', 'serve', '--host=127.0.0.1', "--port=$Port") -WorkingDirectory $ProjectRoot -WindowStyle Hidden
}

Write-Output "Sitio: http://127.0.0.1:$Port"
Write-Output "Mantenimiento: http://127.0.0.1:$Port/admin"
