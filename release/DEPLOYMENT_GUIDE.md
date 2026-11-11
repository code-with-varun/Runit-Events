# Production Deployment Guide

## Live Environments
- **Primary Live URL**: `https://runito.in/events/`
- **Hosting Provider**: Hostinger (cPanel / Apache / PHP 8.x)
- **Database**: MySQL 8.x (`u935057905_runit_aios`)

## 1. Fast Automated Deployment via PowerShell
To deploy the final release bundle to the live server automatically:
```powershell
powershell -ExecutionPolicy Bypass -File release/deploy_live.ps1
```

## 2. Manual FTP Deployment
If uploading manually via FileZilla / WinSCP:
- **Host**: `ftp.runito.in`
- **Username**: `u935057905.runit`
- **Target Folder**: `/events/`
- Upload the contents of `release/dist/` into `/events/`.

## 3. Database Updates
Run any schema updates from `planning/database_schema.sql` via phpMyAdmin on Hostinger.

## 4. Environment Auto-Detection
The application dynamically detects production vs. localhost:
- In production (`HTTP_HOST` contains `runito.in`), it automatically switches to production database credentials and sets `base_url` to `https://runito.in/events`.
- In local development (`localhost`), it uses `127.0.0.1` and `http://localhost:8000`.
