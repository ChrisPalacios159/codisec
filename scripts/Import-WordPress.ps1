param(
    [string]$WordPressRoot = 'Z:\inetpub\wwwroot\codisec',
    [string]$SourceHost = '10.10.20.233',
    [string]$PhpPath = 'C:\Users\Usuario\Tools\php-8.3.33\php.exe'
)

$ErrorActionPreference = 'Stop'
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$ConfigPath = Join-Path $WordPressRoot 'wp-config.php'
$UploadsPath = Join-Path $WordPressRoot 'wp-content\uploads'
$Config = Get-Content -LiteralPath $ConfigPath -Raw

function Read-WpDefine([string]$Name) {
    $Pattern = "define\s*\(\s*'$Name'\s*,\s*'([^']*)'\s*\)"
    $Match = [regex]::Match($Config, $Pattern)
    if (-not $Match.Success) { throw "No se encontró $Name en wp-config.php" }
    return $Match.Groups[1].Value
}

$env:WORDPRESS_DB_HOST = $SourceHost
$env:WORDPRESS_DB_PORT = '3306'
$env:WORDPRESS_DB_DATABASE = Read-WpDefine 'DB_NAME'
$env:WORDPRESS_DB_USERNAME = Read-WpDefine 'DB_USER'
$env:WORDPRESS_DB_PASSWORD = Read-WpDefine 'DB_PASSWORD'
$env:WORDPRESS_DB_PREFIX = 'wp_'

$ExitCode = 1
try {
    & $PhpPath (Join-Path $ProjectRoot 'artisan') config:clear
    & $PhpPath (Join-Path $ProjectRoot 'artisan') codisec:import-wordpress --fresh "--uploads-path=$UploadsPath"
    $ExitCode = $LASTEXITCODE
} finally {
    Remove-Item Env:WORDPRESS_DB_HOST, Env:WORDPRESS_DB_PORT, Env:WORDPRESS_DB_DATABASE, Env:WORDPRESS_DB_USERNAME, Env:WORDPRESS_DB_PASSWORD, Env:WORDPRESS_DB_PREFIX -ErrorAction SilentlyContinue
}

if ($ExitCode -ne 0) { exit $ExitCode }
