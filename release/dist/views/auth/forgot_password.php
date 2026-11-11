<?php
$pageTitle = 'Reset Password | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="light-card shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="assets/logo/primary-logo.png" alt="Runit Events Hub Logo" class="img-fluid mb-3" style="max-height: 52px;" onerror="this.onerror=null; this.src='assets/logo/app-icon-red.png';">
                    <h3 class="fw-bold text-dark mb-1">Reset Password</h3>
                    <p class="text-secondary small">Enter your registered email and choose a new password</p>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success py-3 small mb-4">
                        <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> <?= htmlspecialchars($success) ?>
                        <div class="mt-3">
                            <a href="<?= app_url('login') ?>" class="btn btn-bms-red btn-sm px-3">Proceed to Sign In &rarr;</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($success)): ?>
                <!-- Form -->
                <form action="<?= app_url('forgot-password') ?>" method="POST" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Registered Email or Username *</label>
                        <input type="text" name="email" class="form-control" placeholder="e.g. you@company.com or admin" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <span class="text-muted" style="font-size: 0.72rem;">Enter the email you registered with.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">New Password *</label>
                        <input type="password" name="new_password" class="form-control" placeholder="At least 6 characters" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-bold">Confirm New Password *</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required>
                    </div>

                    <button type="submit" class="btn btn-bms-red w-100 py-2 fs-6 mb-3">
                        <i class="bi bi-key-fill me-1"></i> Reset Password Now
                    </button>
                </form>
                <?php endif; ?>

                <div class="mt-4 text-center text-secondary small pt-3 border-top">
                    Remember your password? <a href="<?= app_url('login') ?>" class="text-danger text-decoration-none fw-bold">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

