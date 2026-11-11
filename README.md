# 🎟️ Runit Events Hub

> **Enterprise Corporate Event, Masterclass, and Certification Platform**  
> Developed for **Runit Infotech** — Live at [https://runito.in/events/](https://runito.in/events/)

---

## 📁 Repository Structure

This repository is organized into distinct stages of the software engineering lifecycle:

```
runit-events/
├── 📋 planning/
│   ├── runit events brd.xlsx       # Business Requirement Document (BRD)
│   ├── database_schema.sql         # Canonical MySQL schema and seed data
│   ├── PROJECT_SPEC.md             # Complete functional and UI specifications
│   └── ARCHITECTURE.md             # MVC system design and security model
│
├── 💻 development/
│   ├── config/                     # Application and database environment configs
│   ├── database/                   # Schema scripts
│   ├── public/                     # Web entry root (CSS, JS, assets, logo pack)
│   ├── src/                        # Controllers, Models, and Services (MVC)
│   ├── views/                      # Modern BookMyShow-styled PHP blade/views
│   ├── composer.json               # PHP Composer dependencies
│   ├── index.php                   # Public routing entry point
│   └── run_server.bat              # Local development launcher
│
├── 🧪 testing/
│   ├── test_events.php             # Automated event status and search tests
│   ├── test_auth.php               # Automated authentication & RBAC verification
│   ├── test_booking.php            # Automated capacity and seat urgency tests
│   └── TEST_CHECKLIST.md           # End-to-end QA validation checklist
│
├── 🚀 release/
│   ├── dist/                       # Clean production distribution (final output)
│   ├── build_release.ps1           # Automated release packaging script
│   ├── deploy_live.ps1             # Direct FTP deployment to Hostinger server
│   ├── DEPLOYMENT_GUIDE.md         # Production server setup and configuration
│   └── CHANGELOG.md                # Release version history and patch notes
│
├── .gitignore                      # Git ignore rules
├── README.md                       # Repository guide (this file)
└── run_server.bat                  # Root convenience server launcher
```

---

## ⚡ Quick Start (Local Development)

### Prerequisites
- PHP 8.1+ with `pdo_mysql`, `openssl`, `gd`, `mbstring` extensions enabled.
- MySQL 8.0+ (via XAMPP or native service).

### Running Locally
1. Start MySQL and import `planning/database_schema.sql` into a database named `runit_events_db`.
2. Double-click `run_server.bat` or run:
   ```cmd
   "C:\xampp\php\php.exe" -S localhost:8000 -t development/public
   ```
3. Open `http://localhost:8000` in your web browser.

---

## 🧪 Running Automated Tests
```cmd
"C:\xampp\php\php.exe" -f testing/test_events.php
"C:\xampp\php\php.exe" -f testing/test_auth.php
"C:\xampp\php\php.exe" -f testing/test_booking.php
```

---

## 🚀 Deploying to Production
To build and deploy the final live output directly to `https://runito.in/events/`:
```powershell
powershell -ExecutionPolicy Bypass -File release/deploy_live.ps1
```

---

## 🔐 Credentials & Roles
- **Administrator**: `admin@runitevents.com`
- **Speaker / Instructor**: Role ID 2
- **Learner / Attendee**: Role ID 3

---
© 2026 Runit Infotech. All rights reserved.
