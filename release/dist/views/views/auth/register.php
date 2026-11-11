<?php
$pageTitle = 'Create Account | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-9">
            <div class="light-card shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="assets/logo/primary-logo.png" alt="Runit Events Hub Logo" class="img-fluid mb-3" style="max-height: 52px;" onerror="this.onerror=null; this.src='assets/logo/app-icon-red.png';">
                    <h3 class="fw-bold text-dark mb-1">Create Learner Account</h3>
                    <p class="text-secondary small">Join Runit Events Hub for webinars, workshops, and verified credentials.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= app_url('register') ?>" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Full Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="john@company.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Mobile Phone</label>
                            <input type="tel" name="mobile" class="form-control" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Company / Organization</label>
                            <input type="text" name="company" class="form-control" placeholder="e.g. Infosys, TCS, Startup">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Designation</label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g. Lead Architect, Engineer">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-bms-red w-100 py-2 fs-6 mb-3">
                        Complete Free Registration &rarr;
                    </button>
                </form>

                <div class="text-center text-secondary small pt-3 border-top">
                    Already registered? <a href="<?= app_url('login') ?>" class="text-danger text-decoration-none fw-bold">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
