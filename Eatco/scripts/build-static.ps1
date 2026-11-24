$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
Push-Location $projectRoot
try {
    $null = & php (Join-Path $projectRoot 'scripts/build-pages.php')
    if ($LASTEXITCODE -ne 0) {
        throw 'PHP could not build the static pages.'
    }
} finally {
    Pop-Location
}
