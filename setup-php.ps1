# Automated PHP Installer for Windows
$ErrorActionPreference = "Stop"

$phpInstallDir = "C:\php"
$zipUrl = "https://windows.php.net/downloads/releases/php-8.2.33-Win32-vs16-x64.zip"
$tempZip = "$env:TEMP\php-8.2.zip"

Write-Host "==> Downloading PHP 8.2 from official Windows distribution..." -ForegroundColor Cyan
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
Invoke-WebRequest -Uri $zipUrl -OutFile $tempZip -UseBasicParsing

Write-Host "==> Extracting PHP to $phpInstallDir..." -ForegroundColor Cyan
if (-not (Test-Path $phpInstallDir)) {
    New-Item -ItemType Directory -Path $phpInstallDir -Force | Out-Null
}
Expand-Archive -Path $tempZip -DestinationPath $phpInstallDir -Force

Write-Host "==> Configuring php.ini with PostgreSQL & required extensions..." -ForegroundColor Cyan
$iniDev = Join-Path $phpInstallDir "php.ini-development"
$iniFile = Join-Path $phpInstallDir "php.ini"

if (Test-Path $iniDev) {
    $iniContent = Get-Content $iniDev -Raw
    # Enable extensions
    $iniContent = $iniContent -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
    $iniContent = $iniContent -replace ';extension=pdo_pgsql', 'extension=pdo_pgsql'
    $iniContent = $iniContent -replace ';extension=pgsql', 'extension=pgsql'
    $iniContent = $iniContent -replace ';extension=mbstring', 'extension=mbstring'
    $iniContent = $iniContent -replace ';extension=openssl', 'extension=openssl'
    $iniContent = $iniContent -replace ';extension=curl', 'extension=curl'
    $iniContent = $iniContent -replace ';extension=fileinfo', 'extension=fileinfo'
    Set-Content -Path $iniFile -Value $iniContent
}

Write-Host "==> Adding $phpInstallDir to User PATH..." -ForegroundColor Cyan
$currentPath = [Environment]::GetEnvironmentVariable("Path", [EnvironmentVariableTarget]::User)
if ($currentPath -notlike "*$phpInstallDir*") {
    [Environment]::SetEnvironmentVariable("Path", "$currentPath;$phpInstallDir", [EnvironmentVariableTarget]::User)
}
$env:Path += ";$phpInstallDir"

Write-Host ""
Write-Host "============================================================" -ForegroundColor Green
Write-Host "PHP has been successfully installed and configured!" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
& "$phpInstallDir\php.exe" -v
Write-Host ""
Write-Host "You can now run: php -S localhost:8000" -ForegroundColor Yellow
