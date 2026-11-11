<?php
use App\Models\User;
$currentUser = User::current();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Runit Events Hub | Webinar & Event Platform') ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Base URL for SEO-friendly path resolution -->
    <base href="<?= app_base_url() ?>">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Runit Events Hub Header -->
<header>
    <!-- 1. Top Navbar -->
    <div class="bms-navbar-top">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <!-- Runit Brand Logo -->
            <a href="<?= app_url('') ?>" class="d-flex align-items-center text-decoration-none">
                <img src="assets/logo/horizontal-lockup-light.png" alt="Runit Events Hub" style="max-height: 42px;" onerror="this.onerror=null; this.src='assets/logo/primary-logo-light.png';">
            </a>

            <!-- Search Bar -->
            <div class="bms-search-box d-none d-md-block flex-grow-1 mx-3">
                <form action="<?= app_url('explore') ?>" method="GET" class="m-0">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="q" placeholder="Search for Webinars, Masterclasses, Workshops, Certifications..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </form>
            </div>

            <!-- Right Controls -->
            <div class="d-flex align-items-center gap-3">
                <!-- Location / Mode Indicator -->
                <div class="d-none d-lg-flex align-items-center text-white small px-2 py-1 gap-1" style="cursor: pointer;">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span class="fw-semibold">All India (Online)</span>
                </div>

                <?php if ($currentUser): ?>
                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-bms-red dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span><?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($currentUser['name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($currentUser['email']) ?></div>
                                <span class="badge bg-secondary mt-1"><?= htmlspecialchars($currentUser['role_name']) ?></span>
                            </li>
                            <li><a class="dropdown-item py-2" href="<?= app_url('dashboard') ?>"><i class="bi bi-ticket-perforated me-2 text-danger"></i> My Attendee Passes</a></li>
                            <?php if (has_certificate_programs()): ?>
                                <li><a class="dropdown-item py-2" href="<?= app_url('dashboard') ?>#certs"><i class="bi bi-award me-2 text-warning"></i> My Certificates</a></li>
                            <?php endif; ?>
                            <?php if (User::isAdmin()): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-primary" href="<?= app_url('admin') ?>"><i class="bi bi-gear-fill me-2"></i> Admin Management</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= app_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= app_url('login') ?>" class="btn btn-sm btn-outline-light px-3">Sign In</a>
                    <a href="<?= app_url('register') ?>" class="btn btn-sm btn-bms-red px-3">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. Secondary Subnav Bar -->
    <div class="bms-subnav d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-1">
                <a href="<?= app_url('') ?>" class="nav-link <?= ($page ?? '') === 'home' ? 'active' : '' ?>">Home</a>
                <a href="<?= app_url('explore') ?>" class="nav-link <?= ($page ?? '') === 'events' && empty($_GET['status']) && empty($_GET['type']) ? 'active' : '' ?>">All Webinars</a>
                <a href="<?= app_url('explore', ['type' => 'Workshop']) ?>" class="nav-link <?= ($_GET['type'] ?? '') === 'Workshop' ? 'active' : '' ?>">Hands-On Workshops</a>
                <a href="<?= app_url('explore', ['type' => 'Corporate Training']) ?>" class="nav-link <?= ($_GET['type'] ?? '') === 'Corporate Training' ? 'active' : '' ?>">Corporate Training</a>
                <?php if (has_certificate_programs()): ?>
                    <a href="<?= app_url('explore', ['type' => 'Certification Program']) ?>" class="nav-link <?= ($_GET['type'] ?? '') === 'Certification Program' ? 'active' : '' ?>">Certification Tracks</a>
                <?php endif; ?>
                <a href="<?= app_url('explore', ['status' => 'completed']) ?>" class="nav-link <?= ($_GET['status'] ?? '') === 'completed' ? 'active' : '' ?>">Past Highlights & Replays</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <?php if (has_certificate_programs()): ?>
                    <a href="<?= app_url('verify-cert') ?>" class="text-warning small text-decoration-none fw-semibold">
                        <i class="bi bi-patch-check-fill me-1"></i> Verify Certificate
                    </a>
                <?php endif; ?>
                <?php if (User::isAdmin()): ?>
                    <a href="<?= app_url('admin') ?>" class="text-white small text-decoration-none badge bg-danger px-2 py-1">
                        <i class="bi bi-shield-fill"></i> Admin Console
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main class="flex-grow-1">
