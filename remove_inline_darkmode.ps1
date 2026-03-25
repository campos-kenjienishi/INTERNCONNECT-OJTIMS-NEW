# Script to remove inline dark mode code from blade files
$files = @(
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\accountinfo.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\audit.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\companies.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\maintenance.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\MOAview.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\professorTab.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\reportsExpired.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\reportsT.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\students.blade.php',
    'c:\Users\kianb\ojtms\resources\views\ojtCoordinator\upload.blade.php',
    'c:\Users\kianb\ojtms\resources\views\professor\profAcc.blade.php',
    'c:\Users\kianb\ojtms\resources\views\students\student_account.blade.php'
)

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "Processing: $file"
        $content = Get-Content $file -Raw
        $original = $content
        
        # Remove the entire dark mode toggle block using multiline regex
        $content = $content -replace '(?s)\s*// Dark mode toggle\s*const darkmodeToggle = document\.getElementById\(.*?\);.*?darkmodeToggle\.innerHTML = isDark \? .*?\n(?=\s*\}|\s*\$|\s*\/\/)', ""
        $content = $content -replace '(?s)\s*const darkmodeToggle = document\.getElementById\(.*?\);.*?darkmodeToggle\.innerHTML = isDark \? .*?\n(?=\s*\$|\s*\/\/|\s*\))', ""
        $content = $content -replace '(?s)\s*\/\/\s*Dark mode.*?darkmodeIcon.*?darkmodeToggle\.innerHTML = isDark.*?\);', ""
        
        if ($content -ne $original) {
            Set-Content $file $content -NoNewline -Encoding UTF8
            Write-Host "  ✓ Updated"
        } else {
            Write-Host "  - No changes needed"
        }
    } else {
        Write-Host "File not found: $file"
    }
}

Write-Host "Done!"
