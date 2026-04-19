$ErrorActionPreference = 'Stop'

$appPath = Join-Path $PSScriptRoot 'intake-register-app'
$dbPath = Join-Path $PSScriptRoot 'data\intake_register.sqlite'
$dbDir = Split-Path -Parent $dbPath
$repoRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$portablePhp = Join-Path $repoRoot 'tools\php\runtime\php.exe'

if (Test-Path $portablePhp) {
    $phpCmd = $portablePhp
}
else {
    $phpCmd = (Get-Command php -ErrorAction SilentlyContinue)?.Source
}

if (-not $phpCmd) {
    throw 'PHP was not found. Install PHP 8.2+ and ensure `php` is on PATH, or place portable PHP at tools/php/runtime/php.exe.'
}

if (-not (Test-Path $dbDir)) {
    New-Item -ItemType Directory -Path $dbDir | Out-Null
}

if (-not (Test-Path $dbPath)) {
    New-Item -ItemType File -Path $dbPath | Out-Null
}

Push-Location $appPath
try {
    & $phpCmd artisan config:clear | Out-Host
    & $phpCmd artisan migrate --force | Out-Host
    & $phpCmd artisan serve --host=127.0.0.1 --port=8080
}
finally {
    Pop-Location
}
