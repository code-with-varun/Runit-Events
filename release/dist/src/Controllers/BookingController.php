<?php
namespace App\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Services\BookingService;
use App\Services\QRService;
use App\Services\PDFService;
use App\Database;

class BookingController {
    public function checkout() {
        if (!User::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header("Location: " . app_url('login', ['notice' => 'Please log in or register to reserve your seat.']));
            exit;
        }

        $eventId = (int)($_GET['event_id'] ?? 0);
        $event = Event::getById($eventId);

        if (!$event) {
            header("Location: " . app_url('explore'));
            exit;
        }

        $user = User::current();
        $availableSeats = max(0, $event['capacity'] - $event['booked_count']);

        require __DIR__ . '/../../views/booking/checkout.php';
    }

    public function process() {
        if (!User::isLoggedIn()) {
            header("Location: " . app_url('login'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . app_url('explore'));
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $seatCount = 1;
        $user = User::current();

        $result = BookingService::bookSeat($eventId, $user['id'], $seatCount);

        if ($result['success']) {
            header("Location: " . app_url('booking-success/' . $result['booking_id']));
            exit;
        } elseif (!empty($result['is_waitlisted'])) {
            header("Location: " . app_url('event/' . $eventId, ['waitlisted' => 1, 'msg' => $result['message']]));
            exit;
        } else {
            header("Location: " . app_url('event/' . $eventId, ['error' => $result['message']]));
            exit;
        }
    }

    public function success() {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        $booking = Database::fetchOne("
            SELECT b.*, e.title as event_title, e.event_date, e.start_time, e.duration_minutes, e.meeting_platform, e.meeting_link,
                   u.name as user_name, u.email as user_email
            FROM bookings b
            JOIN events e ON b.event_id = e.id
            JOIN users u ON b.user_id = u.id
            WHERE b.id = ?
        ", [$bookingId]);

        if (!$booking) {
            header("Location: index.php?page=events");
            exit;
        }

        $qrDataUri = QRService::generateDataUri($booking['booking_code']);

        require __DIR__ . '/../../views/booking/success.php';
    }

    public function downloadPass() {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        $booking = Database::fetchOne("
            SELECT b.*, e.title as event_title, e.event_date, e.start_time, e.duration_minutes, e.meeting_platform, e.meeting_link,
                   u.name as user_name, u.email as user_email
            FROM bookings b
            JOIN events e ON b.event_id = e.id
            JOIN users u ON b.user_id = u.id
            WHERE b.id = ?
        ", [$bookingId]);

        if (!$booking) {
            die("Booking pass not found.");
        }

        $qrDataUri = QRService::generateDataUri($booking['booking_code']);

        $pdfContent = PDFService::generatePass([
            'booking_code' => $booking['booking_code'],
            'event_title' => $booking['event_title'],
            'event_date' => $booking['event_date'],
            'start_time' => date('h:i A', strtotime($booking['start_time'])),
            'duration' => $booking['duration_minutes'] . ' mins',
            'attendee_name' => $booking['user_name'],
            'attendee_email' => $booking['user_email'],
            'meeting_platform' => $booking['meeting_platform'],
            'meeting_link' => $booking['meeting_link'],
            'qr_data_uri' => $qrDataUri
        ]);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Pass_' . $booking['booking_code'] . '.pdf"');
        echo $pdfContent;
        exit;
    }

    public function downloadIcal() {
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        $booking = Database::fetchOne("
            SELECT b.*, e.title as event_title, e.short_summary, e.event_date, e.start_time, e.duration_minutes, e.meeting_link
            FROM bookings b
            JOIN events e ON b.event_id = e.id
            WHERE b.id = ?
        ", [$bookingId]);

        if (!$booking) {
            die("Event not found.");
        }

        $startTs = strtotime($booking['event_date'] . ' ' . $booking['start_time']);
        $endTs = $startTs + ($booking['duration_minutes'] * 60);

        $dtStart = gmdate('Ymd\THis\Z', $startTs);
        $dtEnd = gmdate('Ymd\THis\Z', $endTs);
        $summary = $booking['event_title'];
        $description = $booking['short_summary'] . " \\nJoin URL: " . $booking['meeting_link'];

        $ics = "BEGIN:VCALENDAR\r\n"
             . "VERSION:2.0\r\n"
             . "PRODID:-//EventHub//EN\r\n"
             . "CALSCALE:GREGORIAN\r\n"
             . "METHOD:PUBLISH\r\n"
             . "BEGIN:VEVENT\r\n"
             . "UID:event-" . $booking['id'] . "@eventhub.com\r\n"
             . "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"
             . "DTSTART:" . $dtStart . "\r\n"
             . "DTEND:" . $dtEnd . "\r\n"
             . "SUMMARY:" . $summary . "\r\n"
             . "DESCRIPTION:" . $description . "\r\n"
             . "LOCATION:" . $booking['meeting_link'] . "\r\n"
             . "STATUS:CONFIRMED\r\n"
             . "END:VEVENT\r\n"
             . "END:VCALENDAR\r\n";

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="invite_' . $booking['booking_code'] . '.ics"');
        echo $ics;
        exit;
    }
}
