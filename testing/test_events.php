<?php
/**
 * Automated Test: Event Search & Status Filtering
 * Tests that upcoming live events and completed past replays are correctly categorized.
 */
require_once __DIR__ . '/../development/vendor/autoload.php';
require_once __DIR__ . '/../development/src/helpers.php';

use App\Database;
use App\Models\Event;

echo "=== RUNNING EVENT SEARCH & STATUS TESTS ===\n";

try {
    // 1. Test Upcoming Events
    $upcoming = Event::search(['status' => 'published']);
    echo "✓ [PASS] Found " . count($upcoming) . " upcoming published events.\n";
    foreach ($upcoming as $e) {
        if ($e['status'] !== 'published' || $e['event_date'] < date('Y-m-d')) {
            throw new Exception("Event ID {$e['id']} should not be in upcoming list (Date: {$e['event_date']})");
        }
    }
    echo "  -> Verified all events are published and have event_date >= CURDATE()\n";

    // 2. Test Completed Events
    $completed = Event::search(['status' => 'completed']);
    echo "✓ [PASS] Found " . count($completed) . " completed/concluded events.\n";
    foreach ($completed as $c) {
        $isConcluded = ($c['status'] === 'completed') || ($c['event_date'] < date('Y-m-d'));
        if (!$isConcluded) {
            throw new Exception("Event ID {$c['id']} should not be in completed list");
        }
    }
    echo "  -> Verified all completed events have status='completed' or event_date < CURDATE()\n";

    echo "\nALL EVENT TESTS PASSED SUCCESSFULLY.\n";
} catch (\Throwable $t) {
    echo "✗ [FAIL] " . $t->getMessage() . "\n";
    exit(1);
}

