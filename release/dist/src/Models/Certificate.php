<?php
namespace App\Models;

use App\Database;
use App\Services\QRService;
use App\Services\PDFService;

class Certificate {
    /**
     * Check if user is eligible for a certificate in an event
     */
    public static function checkEligibility(int $eventId, int $userId): array {
        $event = Event::getById($eventId);
        if (!$event || !$event['is_certificate_eligible']) {
            return ['eligible' => false, 'reason' => 'This event does not offer a participation certificate.'];
        }

        // Check attendance record
        $att = Database::fetchOne("
            SELECT * FROM attendance 
            WHERE event_id = ? AND user_id = ?
        ", [$eventId, $userId]);

        if (!$att) {
            return ['eligible' => false, 'reason' => 'Attendance was not recorded for this event.'];
        }

        if ((float)$att['percentage_attended'] < 75.0) {
            return [
                'eligible' => false, 
                'reason' => "Attendance criteria not fulfilled ({$att['percentage_attended']}% attended; minimum 75% required)."
            ];
        }

        return ['eligible' => true, 'attendance' => $att];
    }

    /**
     * Issue or retrieve certificate
     */
    public static function issue(int $eventId, int $userId): array {
        // Check if already exists
        $existing = Database::fetchOne("SELECT * FROM certificates WHERE event_id = ? AND user_id = ?", [$eventId, $userId]);
        if ($existing) {
            return ['success' => true, 'cert' => $existing, 'is_new' => false];
        }

        $eligibility = self::checkEligibility($eventId, $userId);
        if (!$eligibility['eligible']) {
            return ['success' => false, 'message' => $eligibility['reason']];
        }

        // Generate unique certificate ID: CERT-YYYY-0001234
        $year = date('Y');
        $randomSeq = str_pad((string)mt_rand(1000, 999999), 7, '0', STR_PAD_LEFT);
        $certNumber = "CERT-{$year}-{$randomSeq}";

        $verificationUrl = "index.php?page=verify-cert&cert={$certNumber}";

        $booking = Database::fetchOne("SELECT id FROM bookings WHERE event_id = ? AND user_id = ? ORDER BY id DESC", [$eventId, $userId]);
        $bookingId = $booking['id'] ?? null;

        Database::execute("
            INSERT INTO certificates (cert_number, event_id, user_id, booking_id, issue_date, qr_verification_url)
            VALUES (?, ?, ?, ?, CURDATE(), ?)
        ", [$certNumber, $eventId, $userId, $bookingId, $verificationUrl]);

        $newCert = Database::fetchOne("SELECT * FROM certificates WHERE cert_number = ?", [$certNumber]);

        return ['success' => true, 'cert' => $newCert, 'is_new' => true];
    }

    public static function findByNumber(string $certNumber): ?array {
        return Database::fetchOne("
            SELECT c.*, e.title as event_title, e.event_date, u.name as user_name, u.email as user_email, u.company as user_company
            FROM certificates c
            JOIN events e ON c.event_id = e.id
            JOIN users u ON c.user_id = u.id
            WHERE c.cert_number = ?
        ", [$certNumber]);
    }

    public static function getUserCertificates(int $userId): array {
        return Database::fetchAll("
            SELECT c.*, e.title as event_title, e.event_date, e.event_type, e.banner_image
            FROM certificates c
            JOIN events e ON c.event_id = e.id
            WHERE c.user_id = ?
            ORDER BY c.issue_date DESC
        ", [$userId]);
    }
}
