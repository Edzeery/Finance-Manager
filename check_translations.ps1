$baseDir = "C:\laragon\www\Finance-Manager\resources\lang"
$languages = @("ar", "en", "fr")

function Get-TopLevelKeys {
    param([string]$FilePath)
    $content = Get-Content -Path $FilePath -Raw -Encoding UTF8
    $keys = @()

    # Match patterns like: 'key' => or "key" => or key => (without quotes)
    $matches = [regex]::Matches($content, "(?m)^\s*['""]?([a-zA-Z0-9_\-\.]+)['""]?\s*=>")
    foreach ($m in $matches) {
        $keys += $m.Groups[1].Value
    }
    return $keys
}

# Collect all PHP files from the 3 language dirs
$arFiles = Get-ChildItem -Path "$baseDir\ar" -Filter "*.php" -Name
$enFiles = Get-ChildItem -Path "$baseDir\en" -Filter "*.php" -Name
$frFiles = Get-ChildItem -Path "$baseDir\fr" -Filter "*.php" -Name

$allFiles = ($arFiles + $enFiles + $frFiles) | Sort-Object -Unique

Write-Host "=" * 80
Write-Host "  TRANSLATION KEY COMPARISON: ar / en / fr"
Write-Host "=" * 80
Write-Host ""

$totalMissing = 0

foreach ($file in $allFiles) {
    $exists = @{
        ar = Test-Path "$baseDir\ar\$file"
        en = Test-Path "$baseDir\en\$file"
        fr = Test-Path "$baseDir\fr\$file"
    }

    $presentIn = $languages | Where-Object { $exists[$_] }

    if ($presentIn.Count -lt 2) {
        $missingIn = $languages | Where-Object { -not $exists[$_] }
        Write-Host "[FILE MISSING]" -ForegroundColor Yellow
        Write-Host "  $file exists in: $($presentIn -join ', ')"
        Write-Host "  Missing from:   $($missingIn -join ', ')"
        Write-Host ""
        $totalMissing++
        continue
    }

    $keys = @{}
    foreach ($lang in $presentIn) {
        $keys[$lang] = Get-TopLevelKeys -FilePath "$baseDir\$lang\$file"
    }

    # Build a union of all keys
    $allKeys = @()
    foreach ($lang in $presentIn) {
        $allKeys += $keys[$lang]
    }
    $allKeys = $allKeys | Sort-Object -Unique

    $fileMissing = $false
    $diffOutput = @()

    foreach ($key in $allKeys) {
        $missingLangs = @()
        foreach ($lang in $presentIn) {
            if ($key -notin $keys[$lang]) {
                $missingLangs += $lang
            }
        }
        if ($missingLangs.Count -gt 0) {
            $fileMissing = $true
            $diffOutput += "    Key '$key' missing in: $($missingLangs -join ', ')"
            $totalMissing += $missingLangs.Count
        }
    }

    if ($fileMissing) {
        Write-Host "[MISMATCH] $file" -ForegroundColor Red
        foreach ($line in $diffOutput) {
            Write-Host $line
        }
        Write-Host ""
    }
}

Write-Host "=" * 80
Write-Host "  TOTAL MISSING KEY ENTRIES: $totalMissing"
Write-Host "=" * 80

# Also check vendor/status-kit translations
Write-Host ""
Write-Host "=" * 80
Write-Host "  VENDOR STATUS-KIT TRANSLATION COMPARISON"
Write-Host "=" * 80
Write-Host ""

$statusKitBase = "$baseDir\vendor\status-kit"
foreach ($lang in $languages) {
    $path = "$statusKitBase\$lang\statuses.php"
    if (Test-Path $path) {
        $k = Get-TopLevelKeys -FilePath $path
        Write-Host "  [$lang] statuses.php: $($k.Count) keys" -ForegroundColor Cyan
    } else {
        Write-Host "  [$lang] statuses.php: FILE MISSING" -ForegroundColor Yellow
    }
}

Write-Host ""
$statusKitFiles = @{}
foreach ($lang in $languages) {
    $p = "$statusKitBase\$lang\statuses.php"
    if (Test-Path $p) {
        $statusKitFiles[$lang] = Get-TopLevelKeys -FilePath $p
    } else {
        $statusKitFiles[$lang] = @()
    }
}

$allStatusKeys = @()
foreach ($lang in $languages) { $allStatusKeys += $statusKitFiles[$lang] }
$allStatusKeys = $allStatusKeys | Sort-Object -Unique

foreach ($key in $allStatusKeys) {
    $missingLangs = @()
    foreach ($lang in $languages) {
        if ($key -notin $statusKitFiles[$lang]) {
            $missingLangs += $lang
        }
    }
    if ($missingLangs.Count -gt 0) {
        Write-Host "    Key '$key' missing in: $($missingLangs -join ', ')" -ForegroundColor Red
    }
}

$hasDiffs = $false
foreach ($key in $allStatusKeys) {
    foreach ($lang in $languages) {
        if ($key -notin $statusKitFiles[$lang]) {
            $hasDiffs = $true
        }
    }
}
if (-not $hasDiffs) {
    Write-Host "  All status-kit translation keys are consistent across all languages." -ForegroundColor Green
}

Write-Host ""
Write-Host "DONE." -ForegroundColor Green
