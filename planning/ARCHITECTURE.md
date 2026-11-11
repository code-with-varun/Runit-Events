# System Architecture & Technical Design

## 1. Architectural Overview
The platform follows a clean, decoupled **Model-View-Controller (MVC)** design pattern built on PHP 8.x with a MySQL relational database.

```
                    +---------------------------+
                    |        HTTP Client        |
                    +-------------+-------------+
                                  |
                                  v
                    +---------------------------+
                    |    Front Controller       |
                    |    (public/index.php)     |
                    +-------------+-------------+
                                  |
            +---------------------+---------------------+
            |                     |                     |
            v                     v                     v
    +---------------+     +---------------+     +---------------+
    | AuthController|     |EventController|     |AdminController|
    +-------+-------+     +-------+-------+     +-------+-------+
            |                     |                     |
            +---------------------+---------------------+
                                  |
                                  v
                    +---------------------------+
                    |       Models & Data       |
                    |  (Event, User, CMSBanner) |
                    +-------------+-------------+
                                  |
                                  v
                    +---------------------------+
                    |         Database          |
                    |      (MySQL / PDO)        |
                    +---------------------------+
```

## 2. Directory Layout
- **`planning/`**: Business requirement document (BRD), project specs, architecture docs, and database schemas.
- **`development/`**: Active PHP MVC source code, public assets, templates, and libraries.
- **`testing/`**: Automated test scripts, test database seeders, and QA checklists.
- **`release/`**: Production-ready deployment package, FTP scripts, and release documentation.

## 3. Security & Validation
- **Authentication**: Native PHP sessions backed by secure password hashing (`PASSWORD_BCRYPT`).
- **Role-Based Access Control (RBAC)**: Distinct permissions for `admin` (Role 1), `speaker` (Role 2), and `learner` (Role 3).
- **SQL Injection Prevention**: 100% prepared PDO statements with parameterized binding.
- **XSS Sanitization**: Strict HTML entity encoding on all user-supplied inputs and video embed URL extractors.

