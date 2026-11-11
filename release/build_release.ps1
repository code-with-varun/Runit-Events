# build_release.ps1
# Builds the clean final output for the live server into release/dist/

$srcDir = Join-Path $PSScriptRoot "..\development"
$distDir = Join-Path $PSScriptRoot "dist"

Write-Host "Packaging release from development/ to release/dist/..." -ForegroundColor Cyan

if (Test-Path $distDir) {
    Remove-Item -Path $distDir -Recurse -Force
}
New-Item -ItemType Directory -Path $distDir -Force | Out-Null

$copyItems = @('config', 'src', 'views', 'public', '.htaccess', 'index.php', 'composer.json', 'composer.lock')

foreach ($item in $copyItems) {
    $src = Join-Path $srcDir $item
    $dst = Join-Path $distDir $item
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination $distDir -Recurse -Force
        Write-Host "✓ Copied: $item" -ForegroundColor Green
    }
}

# Clean any temporary files or cache in dist/
$cleanPaths = @(
    (Join-Path $distDir "public\uploads\passes\*.pdf"),
    (Join-Path $distDir "public\uploads\certificates\*.pdf")
)
foreach ($cp in $cleanPaths) {
    if (Test-Path $cp) {
        Remove-Item -Path $cp -Force -ErrorAction SilentlyContinue
    }
}

Write-Host "✓ Release packaging complete: release/dist ready for live deployment." -ForegroundColor Yellow
