# PowerShell Script to Setup Laragon Virtual Host for ChatPion2

$vhostContent = @"
<VirtualHost *:80>
    DocumentRoot "C:/laragon/www/chatpion2/public"
    ServerName chatpion2.test
    <Directory "C:/laragon/www/chatpion2/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
"@

$vhostPath = "C:\laragon\etc\apache2\sites-enabled\chatpion2.test.conf"
$hostsPath = "C:\Windows\System32\drivers\etc\hosts"

Write-Host "Setting up Laragon Virtual Host..." -ForegroundColor Yellow

# Create virtual host file
try {
    $vhostContent | Out-File -FilePath $vhostPath -Encoding utf8 -Force
    Write-Host "✅ Virtual host file created: $vhostPath" -ForegroundColor Green
} catch {
    Write-Host "❌ Error creating virtual host file: $_" -ForegroundColor Red
    Write-Host "Please create it manually:" -ForegroundColor Yellow
    Write-Host "File: $vhostPath" -ForegroundColor Cyan
    Write-Host "Content:" -ForegroundColor Cyan
    Write-Host $vhostContent
}

# Check hosts file
Write-Host "`nChecking hosts file..." -ForegroundColor Yellow
$hostsContent = Get-Content $hostsPath -ErrorAction SilentlyContinue
if ($hostsContent -match "chatpion2\.test") {
    Write-Host "✅ Hosts file already contains chatpion2.test" -ForegroundColor Green
} else {
    Write-Host "⚠️  Hosts file needs to be updated manually:" -ForegroundColor Yellow
    Write-Host "File: $hostsPath" -ForegroundColor Cyan
    Write-Host "Add this line:" -ForegroundColor Cyan
    Write-Host "127.0.0.1    chatpion2.test" -ForegroundColor White
    Write-Host "`nNote: You may need to run PowerShell as Administrator to edit hosts file." -ForegroundColor Yellow
}

Write-Host "`n✅ Setup complete!" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Restart Laragon (Stop All → Start All)" -ForegroundColor Cyan
Write-Host "2. Open browser: http://chatpion2.test" -ForegroundColor Cyan
Write-Host "3. If hosts file wasn't updated, add the entry manually" -ForegroundColor Cyan

