param([string]$OutputDirectory)

$ErrorActionPreference = 'Stop'
$ProjectRoot = Split-Path -Parent $PSScriptRoot
if (-not $OutputDirectory) { $OutputDirectory = Join-Path $ProjectRoot 'backups' }
New-Item -ItemType Directory -Path $OutputDirectory -Force | Out-Null
$EnvFile = Get-Content -LiteralPath (Join-Path $ProjectRoot '.env')
$Values = @{}
foreach ($Line in $EnvFile) {
    if ($Line -match '^([A-Z0-9_]+)=(.*)$') { $Values[$Matches[1]] = $Matches[2].Trim('"') }
}
$env:MYSQL_PWD = $Values.DB_PASSWORD
$Stamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$Output = Join-Path $OutputDirectory "codisec_laravel_$Stamp.sql"
try {
    & 'C:\xampp\mysql\bin\mysqldump.exe' --host=$($Values.DB_HOST) --port=$($Values.DB_PORT) --user=$($Values.DB_USERNAME) --single-transaction --routines --triggers --default-character-set=utf8mb4 --result-file=$Output $Values.DB_DATABASE
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
} finally {
    Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
}
Write-Output $Output
