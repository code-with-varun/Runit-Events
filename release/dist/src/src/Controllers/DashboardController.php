<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Certificate;
use App\Database;

class DashboardController {
    public function index() {
        if (!User::isLoggedIn()) {
            header("Location: index.php?page=login&notice=Please+log+in+to+view+your+dashboard.");
            exit;
        }

        $user = User::current();
        $userId = (int)$user['id'];

        // 1. Upcoming Bookings
        $upcomingBookings = Database::fetchAll("
            SELECT b.*, e.title as event_title, e.event_date, e.start_time, e.duration_minutes, e.meeting_platform, e.meeting_link, e.banner_image,
                   c.name as category_name
            FROM bookings b
            JOIN events e ON b.event_id = e.id
            JOIN event_categories c ON e.category_id = c.id
            WHERE b.user_id = ? AND e.event_date >= CURDATE() AND b.status != 'cancelled'
            ORDER BY e.event_date ASC
        ", [$userId]);

        // 2. Past Attended Events
        $pastEvents = Database::fetchAll("
            SELECT b.*, e.id as event_id, e.title as event_title, e.event_date, e.banner_image,
                   a.percentage_attended, a.is_eligible_for_cert,
                   (SELECT cert_number FROM certificates WHERE event_id = e.id AND user_id = b.user_id LIMIT 1) as cert_number
            FROM bookings b
            JOIN events e ON b.event_id = e.id
            LEFT JOIN attendance a ON a.event_id = e.id AND a.user_id = b.user_id
            WHERE b.user_id = ? AND (e.event_date < CURDATE() OR e.status = 'completed')
            ORDER BY e.event_date DESC
        ", [$userId]);

        // 3. User Certificates
        $certificates = Certificate::getUserCertificates($userId);

        // 4. Downloadable Materials from Registered Events
        $materials = Database::fetchAll("
            SELECT m.*, e.title as event_title 
            FROM event_materials m
            JOIN events e ON m.event_id = e.id
            JOIN bookings b ON b.event_id = e.id
            WHERE b.user_id = ? AND b.status != 'cancelled'
            ORDER BY m.id DESC
        ", [$userId]);

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
