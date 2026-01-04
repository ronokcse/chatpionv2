# PowerShell Script to Setup .env file for ChatPion2
# Based on ChatPion (CI3) project configuration

$envContent = @"
#--------------------------------------------------------------------
# Environment Configuration for ChatPion2 (CI4)
# Based on ChatPion (CI3) project configuration
#--------------------------------------------------------------------

#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

# Base URL - Auto detect or set manually
# For Laragon, use: http://chatpion2.test/
app.baseURL = 'http://chatpion2.test/'

# Force HTTPS (set to true if using SSL)
app.forceGlobalSecureRequests = false

# Content Security Policy
app.CSPEnabled = false

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
# Database configuration from ChatPion project

database.default.hostname = 192.168.10.13
database.default.database = xeroneit_chatpion
database.default.username = xerochat
database.default.password = O95eKQEXNPVlHK7rhKJNRa3n2z4l5p2
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306
database.default.charset = utf8
database.default.DBCollat = utf8_general_ci

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------
# Generate a new encryption key for CI4
# Run: php spark key:generate
# encryption.key = 

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------

session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.savePath = null
session.cookieName = ci_session
session.expiration = 7200
session.matchIP = false

#--------------------------------------------------------------------
# LOGGER
#--------------------------------------------------------------------

logger.threshold = 4
"@

# Write to .env file
$envContent | Out-File -FilePath ".env" -Encoding utf8 -Force

Write-Host "✅ .env file created successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Run: php spark key:generate" -ForegroundColor Cyan
Write-Host "2. Update app.baseURL if needed" -ForegroundColor Cyan
Write-Host "3. Setup Laragon virtual host" -ForegroundColor Cyan

