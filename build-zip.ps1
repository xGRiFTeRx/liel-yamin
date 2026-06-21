# ============================================================
# Liel Bridal Widgets — release zip builder
#
# Reads the Version: header from liel-widgets/liel-widgets.php,
# packages the plugin folder into dist/liel-widgets-v{version}.zip
# using FORWARD slashes (Windows backslashes make WP's uploader reject the
# zip with "קובץ התוסף לא קיים"), and skips dev junk.
#
# Pattern adopted from Greengrass/Espressimo (see liel-plugin-architecture).
# Usage:  pwsh ./build-zip.ps1   (or right-click "Run with PowerShell")
# ============================================================

$ErrorActionPreference = 'Stop'

$root       = Split-Path -Parent $MyInvocation.MyCommand.Path
$pluginDir  = Join-Path $root 'liel-widgets'
$mainFile   = Join-Path $pluginDir 'liel-widgets.php'
$distDir    = Join-Path $root 'dist'

if ( -not (Test-Path $mainFile) ) {
    throw "Main plugin file not found: $mainFile"
}

# --- Read Version: from the plugin header (Espresso/Thinkerbell pattern) -----
$version = $null
foreach ( $line in (Get-Content $mainFile) ) {
    $t = $line.Trim()
    if ( $t -match '^Version:\s*(.+)$' ) { $version = $Matches[1].Trim(); break }
}
if ( -not $version ) {
    throw "Could not find a Version: header in $mainFile"
}
Write-Host "Plugin version: $version"

# --- Verify the Version constant matches -------------------------------------
$headerRaw = Get-Content $mainFile -Raw
if ( $headerRaw -notmatch "LIEL_BRIDAL_VERSION'\s*,\s*'($([regex]::Escape($version)))'" ) {
    Write-Warning "LIEL_BRIDAL_VERSION constant does not match the Version: header ($version). Bump them together."
}

# --- Prepare dist folder ------------------------------------------------------
if ( -not (Test-Path $distDir) ) { New-Item -ItemType Directory -Path $distDir | Out-Null }
$zipPath = Join-Path $distDir "liel-widgets-v$version.zip"
if ( Test-Path $zipPath ) { Remove-Item $zipPath -Force }

# --- Files to exclude ---------------------------------------------------------
$exclude = @(
    '\.git(\\|/|$)', 'node_modules(\\|/|$)', '\.DS_Store$', 'Thumbs\.db$',
    '\.idea(\\|/|$)', '\.vscode(\\|/|$)', '\.log$', '\.zip$'
)

# --- Build the zip with FORWARD-SLASH entry names ----------------------------
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::Open( $zipPath, [System.IO.Compression.ZipArchiveMode]::Create )
try {
    $files = Get-ChildItem -Path $pluginDir -Recurse -File
    foreach ( $file in $files ) {
        $rel = $file.FullName.Substring( $pluginDir.Length ).TrimStart('\','/') -replace '\\','/'
        $entryName = "liel-widgets/$rel"

        # Skip dev junk
        $skip = $false
        foreach ( $pat in $exclude ) {
            if ( $entryName -match $pat ) { $skip = $true; break }
        }
        if ( $skip ) { continue }

        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
            $zip, $file.FullName, $entryName,
            [System.IO.Compression.CompressionLevel]::Optimal
        ) | Out-Null
    }
}
finally {
    $zip.Dispose()
}

$sizeKB = [math]::Round( (Get-Item $zipPath).Length / 1KB, 1 )
Write-Host ""
Write-Host "Built: $zipPath"
Write-Host "Size : $sizeKB KB"
Write-Host ""
Write-Host "Upload via WP Admin -> Plugins -> Add New -> Upload Plugin -> choose this zip."
Write-Host "For an in-place upgrade WP will show: 'Replace current with uploaded'."
