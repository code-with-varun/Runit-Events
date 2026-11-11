<?php
/**
 * Automated Test: Authentication & User Models
 */
require_once __DIR__ . '/../development/vendor/autoload.php';
require_once __DIR__ . '/../development/src/helpers.php';

use App\Database;
use App\Models\User;

echo "=== RUNNING AUTH & USER MODEL TESTS ===\n";

try {
    $admin = Database::fetchOne("SELECT * FROM users WHERE email = 'admin@runitevents.com'");
    if (!$admin) {
        throw new Exception("Default administrator account not found in database.");
    }
    echo "✓ [PASS] Admin user found: {$admin['email']} (Role ID: {$admin['role_id']})\n";

    if (!password_verify('Admin@123', $admin['password_hash'])) {
        echo "  -> Note: Default password was customized.\n";
    } else {
        echo "✓ [PASS] Default password verification succeeded.\n";
    }

    $roles = Database::fetchAll("SELECT * FROM roles");
    echo "✓ [PASS] Verified " . count($roles) . " roles exist in system.\n";

    echo "\nALL AUTH TESTS PASSED SUCCESSFULLY.\n";
} catch (\Throwable $t) {
    echo "✗ [FAIL] " . $t->getMessage() . "\n";
    exit(1);
}
