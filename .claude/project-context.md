# Project Context for Claude Code

## Project Identity
- **Name:** MA.GIA DONNA
- **Type:** Laravel 10.x Web Application
- **Purpose:** Wellness Center Management System
- **Client:** AGstudio Digital

## Critical Information

### Repository
- **GitHub:** https://github.com/alexgentilitn/magia
- **Current Branch:** `claude/confirm-status-01NaRPJZBUHxak94aM2zKA1u`
- **Branch Pattern:** `claude/[description]-[session-id]`

### Production
- **URL:** https://www.agstudio.digital/magia/public/
- **Server:** Aruba Shared Hosting (NO SSH)
- **Deploy:** Automatic via GitHub Actions FTP

### Database
```
Host: localhost
Database: agstudiodiital_magia
Username: agstudiodiital_agstudiomagia
Password: $Magia2015!
```

## Important Notes

⚠️ **vendor/** is versioned (unusual but necessary for shared hosting)
⚠️ **.env** is in repo but EXCLUDED from FTP deploy
⚠️ Modify .env ONLY via FTP directly on server
⚠️ Deploy is automatic on push to claude/** branches

## Essential Reading

📖 **Before coding, read:**
1. `/QUICK_START.md` - 2 minute overview
2. `/CLAUDE_MEMORY.md` - Complete project memory (MANDATORY)
3. `/GUIDA PROGETTO/` - Technical documentation

## Troubleshooting Tools

🔧 **Diagnostics:** https://www.agstudio.digital/magia/public/diagnose.php
🧹 **Clear Cache:** https://www.agstudio.digital/magia/public/clear-cache.php
📊 **Actions:** https://github.com/alexgentilitn/magia/actions

---

**Last Updated:** 2025-11-14
**Session:** confirm-status-01NaRPJZBUHxak94aM2zKA1u
