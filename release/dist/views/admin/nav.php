<?php
// views/admin/nav.php - Persistent Admin Navigation Bar across all Admin views
use App\Models\User;
$currentAdmin = User::current();
?>
<div class="py-2 border-bottom border-dark border-opacity-25 mb-4 shadow-sm" style="background: linear-gradient(180deg, #182234 0%, #0f172a 100%);">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger text-uppercase px-2 py-1 shadow-sm"><i class="bi bi-shield-lock-fill me-1"></i> Runit Admin</span>
            <span class="text-white small fw-semibold d-none d-md-inline opacity-90">Management Console</span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-1">
            <a href="<?= app_url('admin') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'dashboard' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="<?= app_url('admin/events') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'events' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-calendar-event me-1"></i> Events
            </a>
            <a href="<?= app_url('admin/events/create') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'event-form' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-plus-circle me-1"></i> + Create Event
            </a>
            <a href="<?= app_url('admin/banners') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'banners' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-images me-1"></i> Hero Banners
            </a>
            <a href="<?= app_url('admin/speakers') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'speakers' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-person-video3 me-1"></i> Speakers
            </a>
            <a href="<?= app_url('admin/users') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'users' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-people-fill me-1"></i> Users
            </a>
            <a href="<?= app_url('admin/attendance') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'attendance' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-qr-code-scan me-1"></i> Attendance
            </a>
            <a href="<?= app_url('admin/reports') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'reports' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-graph-up me-1"></i> Reports
            </a>
            <a href="<?= app_url('admin/settings') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'settings' ? 'btn-danger shadow-sm' : 'btn-outline-light border-opacity-25' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-gear-fill me-1"></i> Footer &amp; Settings
            </a>
            <a href="<?= app_url('admin/reset') ?>" class="btn btn-sm <?= ($activeAdminTab ?? '') === 'reset' ? 'btn-danger shadow-sm' : 'btn-outline-danger text-light border-opacity-50' ?> px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Data
            </a>
            <a href="<?= app_url('') ?>" target="_blank" class="btn btn-sm btn-outline-info text-info ms-lg-2 px-2 py-1" style="font-size: 0.8rem;">
                <i class="bi bi-box-arrow-up-right me-1"></i> Live Site
            </a>
        </div>
    </div>
</div>
