# deploy_live.ps1
# Automates uploading the release/dist final package to Hostinger FTP (https://runito.in/events/)

$ftpServer = "ftp.runito.in"
$ftpUser   = "u935057905.runit"
$ftpPass   = "Maruthuvk@2026"
$baseDir   = Join-Path $PSScriptRoot "dist"

function Ensure-FtpDirectory($remotePath) {
    global: $ftpServer, $ftpUser, $ftpPass
    $segments = $remotePath.Trim('/').Split('/')
    $current = "ftp://$ftpServer/events"
    foreach ($seg in $segments) {
        if ([string]::IsNullOrWhiteSpace($seg)) { continue }
        $current += "/$seg"
        try {
            $req = [System.Net.FtpWebRequest]::Create($current)
            $req.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
            $req.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
            $resp = $req.GetResponse()
            $resp.Close()
        } catch {
            # Directory exists
        }
    }
}

$files = Get-ChildItem -Path $baseDir -Recurse -File

Write-Host "Total release files to deploy: $($files.Count)" -ForegroundColor Cyan

$successCount = 0
$failCount = 0

foreach ($f in $files) {
    $relPath = $f.FullName.Substring($baseDir.Length).TrimStart('\', '/')
    $normalizedRel = $relPath.Replace('\', '/')
    $parentDir = [System.IO.Path]::GetDirectoryName($normalizedRel).Replace('\', '/')
    
    if (-not [string]::IsNullOrWhiteSpace($parentDir)) {
        Ensure-FtpDirectory $parentDir
    }

    $ftpUrl = "ftp://$ftpServer/events/$normalizedRel"
    
    try {
        $req = [System.Net.FtpWebRequest]::Create($ftpUrl)
        $req.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $req.UseBinary = $true
        $req.Timeout = 20000

        $content = [System.IO.File]::ReadAllBytes($f.FullName)
        $req.ContentLength = $content.Length
        $stream = $req.GetRequestStream()
        $stream.Write($content, 0, $content.Length)
        $stream.Close()

        $resp = $req.GetResponse()
        $resp.Close()
        Write-Host "✓ Deployed: $normalizedRel" -ForegroundColor Green
        $successCount++
    } catch {
        Write-Host "✗ Failed: $normalizedRel - $($_.Exception.Message)" -ForegroundColor Red
        $failCount++
    }
}

Write-Host "`n=== Release Deployment: $successCount succeeded, $failCount failed ===" -ForegroundColor Yellow
