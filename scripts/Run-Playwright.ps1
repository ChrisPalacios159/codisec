param(
    [string]$BaseUrl = 'http://127.0.0.1:8000',
    [string]$PhpPath = 'C:\Users\Usuario\Tools\php-8.3.33\php.exe'
)

$ErrorActionPreference = 'Stop'
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$RunId = [Guid]::NewGuid().ToString('N')
$Email = "e2e-playwright-$RunId@codisec.local"
$Password = "E2e!$RunId"
$EmailBase64 = [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($Email))
$PasswordBase64 = [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($Password))

$env:E2E_BASE_URL = $BaseUrl
$env:E2E_EMAIL = $Email
$env:E2E_PASSWORD = $Password
$env:E2E_PHP_BINARY = $PhpPath

$ExitCode = 1
try {
    Push-Location $ProjectRoot
    & $PhpPath artisan tinker "--execute=App\Models\User::create(['name' => 'Playwright E2E', 'email' => base64_decode('$EmailBase64'), 'password' => Illuminate\Support\Facades\Hash::make(base64_decode('$PasswordBase64')), 'role' => 'administrator', 'is_active' => true]);"
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo crear el usuario temporal de Playwright.' }
    & npm.cmd run test:e2e
    $ExitCode = $LASTEXITCODE
} finally {
    & $PhpPath artisan tinker "--execute=App\Models\User::where('email', base64_decode('$EmailBase64'))->delete();"
    Pop-Location
    Remove-Item Env:E2E_BASE_URL, Env:E2E_EMAIL, Env:E2E_PASSWORD, Env:E2E_PHP_BINARY -ErrorAction SilentlyContinue
}

exit $ExitCode
