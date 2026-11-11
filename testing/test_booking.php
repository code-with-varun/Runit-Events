<?php
/**
 * Automated Test: Seat Capacity Calculation & Urgency Formatter
 */
require_once __DIR__ . '/../development/vendor/autoload.php';
require_once __DIR__ . '/../development/src/helpers.php';

echo "=== RUNNING SEAT CAPACITY & URGENCY TESTS ===\n";

try {
    // 1. Test Past Event Formatting
    $pastEvt = ['status' => 'completed', 'event_date' => '2026-10-10', 'capacity' => 100, 'booked_count' => 50];
    $pastStatus = format_seat_status($pastEvt);
    if (!$pastStatus['is_past'] || $pastStatus['can_book']) {
        throw new Exception("Past event was incorrectly marked as bookable.");
    }
    echo "✓ [PASS] Past event correctly formatted: '{$pastStatus['text']}'\n";

    // 2. Test Full Event (0 seats left)
    $fullEvt = ['status' => 'published', 'event_date' => date('Y-m-d', strtotime('+7 days')), 'capacity' => 20, 'booked_count' => 20];
    $fullStatus = format_seat_status($fullEvt);
    if (!$fullStatus['is_full'] || $fullStatus['can_book']) {
        throw new Exception("Full event was incorrectly marked as bookable.");
    }
    echo "✓ [PASS] Sold out event correctly formatted: '{$fullStatus['text']}'\n";

    // 3. Test Few Seats Left (< 5 or > 85%)
    $fewEvt = ['status' => 'published', 'event_date' => date('Y-m-d', strtotime('+7 days')), 'capacity' => 20, 'booked_count' => 18];
    $fewStatus = format_seat_status($fewEvt);
    if ($fewStatus['chip_class'] !== 'few-left') {
        throw new Exception("Event with 2 seats left should have 'few-left' urgency.");
    }
    echo "✓ [PASS] Few seats urgency correctly triggered: '{$fewStatus['text']}'\n";

    echo "\nALL BOOKING LOGIC TESTS PASSED SUCCESSFULLY.\n";
} catch (\Throwable $t) {
    echo "✗ [FAIL] " . $t->getMessage() . "\n";
    exit(1);
}
