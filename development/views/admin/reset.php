<?php
$pageTitle = 'Reset Application Data | Runit Events Hub Admin';
$activeAdminTab = 'reset';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-arrow-counterclockwise text-danger me-2"></i> Reset Application Data</h3>
                    <span class="text-secondary small">Clear sample/dummy events, bookings, and test records</span>
                </div>
                <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Admin Console</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i> Warning: Destructive Action
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-dark">
                        This action allows you to completely purge all sample and dummy records so you can configure every event, speaker, and hero banner manually.
                    </p>

                    <h6 class="fw-bold text-danger mt-3 mb-2"><i class="bi bi-trash3 me-1"></i> What will be PERMANENTLY deleted:</h6>
                    <ul class="text-secondary small mb-4">
                        <li>All sample and test bookings &amp; attendee lists (<code>bookings</code>, <code>booking_attendees</code>)</li>
                        <li>All attendance check-in records (<code>attendance</code>)</li>
                        <li>All issued certificate credentials (<code>certificates</code>)</li>
                        <li>All waitlist entries and attendee feedback reviews (<code>waitlists</code>, <code>feedback</code>)</li>
                        <li>All existing sample events, agendas, videos, and materials (<code>events</code>, <code>event_speakers</code>, etc.)</li>
                        <li>All test user accounts (learner / attendee registrations)</li>
                    </ul>

                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-shield-check me-1"></i> What will be PRESERVED:</h6>
                    <ul class="text-secondary small mb-4">
                        <li><strong>Administrator Account</strong>: <code>admin@runitevents.com</code> (password remains <code>admin</code>)</li>
                        <li><strong>User Roles</strong>: Admin, Speaker, User, Guest</li>
                        <li><strong>Event Categories</strong>: AI & Machine Learning, Cloud & DevOps, FinTech, Full-Stack, etc.</li>
                        <li><strong>1 Default Active Banner</strong>: Prepared in hero CMS so the homepage renders cleanly</li>
                    </ul>

                    <div class="alert alert-warning py-3 small">
                        <strong>Confirmation:</strong> Type <code>RESET_CONFIRMED</code> in the box below to authorize the application reset.
                    </div>

                    <form action="<?= app_url('admin/reset') ?>" method="POST" onsubmit="return confirm('Are you absolutely sure you want to reset all events, bookings, and test data?');">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold small">Type Confirmation Text:</label>
                            <input type="text" name="confirm_reset" class="form-control font-monospace" placeholder="RESET_CONFIRMED" required autocomplete="off">
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary px-3">Cancel</a>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-trash3-fill me-1"></i> I Understand, Reset Application Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

