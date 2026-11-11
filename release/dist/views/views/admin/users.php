<?php
$pageTitle = 'User Management | Runit Events Hub Admin';
$activeAdminTab = 'users';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger text-uppercase px-2 py-1"><i class="bi bi-people-fill me-1"></i> User Directory</span>
                <span class="text-secondary small">Comprehensive registry of all registered attendees, speakers, and admins</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Platform Users &amp; Credentials</h3>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <a href="<?= app_url('admin/speakers') ?>" class="btn btn-outline-danger btn-sm px-3">
                <i class="bi bi-person-video3 me-1"></i> Manage Speakers
            </a>
            <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Dashboard</a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show py-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show py-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- User Role Metrics Summary -->
    <?php
    $totalUsers = count($users);
    $adminCount = 0;
    $speakerCount = 0;
    $attendeeCount = 0;
    foreach ($users as $u) {
        if ((int)$u['role_id'] === 1) $adminCount++;
        elseif ((int)$u['role_id'] === 2) $speakerCount++;
        else $attendeeCount++;
    }
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">Total Accounts</span>
                <div class="fs-2 fw-bold text-dark mb-1"><?= $totalUsers ?></div>
                <span class="small text-muted">Across all roles</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">Attendees / Learners</span>
                <div class="fs-2 fw-bold text-primary mb-1"><?= $attendeeCount ?></div>
                <span class="small text-success"><i class="bi bi-person-check me-1"></i> Direct Public Accounts</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">Speakers &amp; Instructors</span>
                <div class="fs-2 fw-bold text-danger mb-1"><?= $speakerCount ?></div>
                <span class="small text-danger"><i class="bi bi-mic me-1"></i> Presenters</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">Administrators</span>
                <div class="fs-2 fw-bold text-dark mb-1"><?= $adminCount ?></div>
                <span class="small text-secondary"><i class="bi bi-shield-lock me-1"></i> Full Access</span>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 1px solid #eef2f6 !important;">
        <div class="p-3 bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold text-dark mb-0">Entire Registered Users (<?= count($users) ?>)</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="userSearchInput" class="form-control form-control-sm" placeholder="Filter by name, email, or company..." style="width: 260px;" onkeyup="filterUserTable()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table light-table table-hover mb-0 align-middle" id="usersTable">
                <thead>
                    <tr>
                        <th class="ps-3">User &amp; Profile</th>
                        <th>Role</th>
                        <th>Contact Details</th>
                        <th>Organization &amp; Designation</th>
                        <th>Activity</th>
                        <th>Joined</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="bi bi-people fs-1 d-block mb-2 text-muted"></i>
                                No user accounts found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block user-name"><?= htmlspecialchars($u['name']) ?></strong>
                                            <span class="text-muted small user-email font-monospace" style="font-size: 0.75rem;"><?= htmlspecialchars($u['email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ((int)$u['role_id'] === 1): ?>
                                        <span class="badge bg-danger text-uppercase px-2 py-1"><i class="bi bi-shield-lock-fill me-1"></i> Admin</span>
                                    <?php elseif ((int)$u['role_id'] === 2): ?>
                                        <span class="badge bg-dark text-uppercase px-2 py-1"><i class="bi bi-person-video3 me-1"></i> Speaker</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="bi bi-person me-1"></i> Attendee</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <div class="text-dark"><i class="bi bi-envelope text-secondary me-1"></i> <?= htmlspecialchars($u['email']) ?></div>
                                    <?php if (!empty($u['mobile'])): ?>
                                        <div class="text-secondary"><i class="bi bi-telephone text-secondary me-1"></i> <?= htmlspecialchars($u['mobile']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <div class="text-dark fw-semibold user-company"><?= htmlspecialchars($u['company'] ?: 'Independent') ?></div>
                                    <div class="text-secondary"><?= htmlspecialchars($u['designation'] ?: 'Professional') ?></div>
                                </td>
                                <td class="small">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark border" title="Bookings Made">
                                            <i class="bi bi-ticket-perforated text-danger me-1"></i> <?= (int)$u['total_bookings'] ?> Passes
                                        </span>
                                        <?php if ((int)$u['total_certs'] > 0): ?>
                                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning" title="Certificates Earned">
                                                <i class="bi bi-award-fill text-warning me-1"></i> <?= (int)$u['total_certs'] ?> Certs
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="small text-secondary">
                                    <?= date('M j, Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" onclick="preparePasswordReset(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>', '<?= htmlspecialchars(addslashes($u['email'])) ?>')">
                                        <i class="bi bi-key-fill text-warning me-1"></i> Reset Password
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Admin Password Reset Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                    <i class="bi bi-key-fill text-warning me-2"></i> Reset User Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= app_url('admin/users') ?>" method="POST">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="resetUserId" value="">

                <div class="modal-body bg-light">
                    <div class="p-3 bg-white rounded border mb-3">
                        <span class="text-secondary small d-block">Target Account:</span>
                        <strong class="text-dark fs-6 d-block" id="resetUserName">-</strong>
                        <span class="text-muted font-monospace small" id="resetUserEmail">-</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Enter New Password *</label>
                        <div class="input-group">
                            <input type="text" name="new_password" id="newPasswordInput" class="form-control" placeholder="Enter new password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('newPasswordInput').value='Runit@' + Math.floor(1000 + Math.random() * 9000);">
                                <i class="bi bi-shuffle me-1"></i> Generate
                            </button>
                        </div>
                        <span class="text-muted" style="font-size: 0.75rem;">You can enter a custom password or click Generate to create a temporary PIN.</span>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-bms-red px-3">
                        <i class="bi bi-check2-circle me-1"></i> Update Password Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function preparePasswordReset(userId, name, email) {
    document.getElementById('resetUserId').value = userId;
    document.getElementById('resetUserName').innerText = name;
    document.getElementById('resetUserEmail').innerText = email;
    document.getElementById('newPasswordInput').value = 'Runit@2026';
}

function filterUserTable() {
    var filter = document.getElementById('userSearchInput').value.toLowerCase();
    var rows = document.querySelectorAll('#usersTable tbody tr');
    rows.forEach(function(row) {
        var text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

