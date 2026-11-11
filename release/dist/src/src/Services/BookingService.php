<?php
namespace App\Services;

use App\Database;
use PDO;

class BookingService {
    /**
     * Reserve or confirm a seat for a user
     */
    public static function bookSeat(int $eventId, int $userId, int $seatCount = 1): array {
        $db = Database::getConnection();
        
        try {
            $db->beginTransaction();

            // Lock event row for atomic capacity update
            $stmt = $db->prepare("SELECT * FROM events WHERE id = ? FOR UPDATE");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch();

            if (!$event) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Event not found.'];
            }

            // Check if user already booked
            $existing = Database::fetchOne("SELECT id, booking_code FROM bookings WHERE event_id = ? AND user_id = ? AND status != 'cancelled'", [$eventId, $userId]);
            if ($existing) {
                $db->rollBack();
                return [
                    'success' => false, 
                    'message' => 'You are already registered for this event.',
                    'booking_id' => $existing['id'],
                    'booking_code' => $existing['booking_code']
                ];
            }

            $availableSeats = $event['capacity'] - $event['booked_count'];

            // If capacity full, offer waitlist
            if ($availableSeats < $seatCount) {
                // Check if already in waitlist
                $wlCheck = Database::fetchOne("SELECT id, position FROM waitlists WHERE event_id = ? AND user_id = ?", [$eventId, $userId]);
                if (!$wlCheck) {
                    $maxPos = Database::fetchOne("SELECT MAX(position) as max_p FROM waitlists WHERE event_id = ?", [$eventId])['max_p'] ?? 0;
                    $newPos = $maxPos + 1;
                    Database::execute("INSERT INTO waitlists (event_id, user_id, position) VALUES (?, ?, ?)", [$eventId, $userId, $newPos]);
                }
                $db->commit();
                return [
                    'success' => false,
                    'is_waitlisted' => true,
                    'message' => 'This event is currently at full capacity (100% booked). You have been added to the priority waitlist!'
                ];
            }

            // Increment booked count
            $stmt = $db->prepare("UPDATE events SET booked_count = booked_count + ? WHERE id = ?");
            $stmt->execute([$seatCount, $eventId]);

            // Generate Booking Code: EVT + Year + Month + Random 6 digits
            $bookingCode = 'EVT' . date('Ym') . str_pad((string)mt_rand(1000, 999999), 6, '0', STR_PAD_LEFT);
            $qrToken = 'QR_' . $eventId . '_' . $userId . '_' . bin2hex(random_bytes(6));

            // Insert booking
            $stmt = $db->prepare("
                INSERT INTO bookings (booking_code, event_id, user_id, seat_count, status, qr_token) 
                VALUES (?, ?, ?, ?, 'confirmed', ?)
            ");
            $stmt->execute([$bookingCode, $eventId, $userId, $seatCount, $qrToken]);
            $bookingId = $db->lastInsertId();

            // Insert primary attendee record
            $user = Database::fetchOne("SELECT name, email, mobile, company, designation FROM users WHERE id = ?", [$userId]);
            $stmt = $db->prepare("
                INSERT INTO booking_attendees (booking_id, attendee_name, attendee_email, attendee_mobile, designation, company)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $bookingId,
                $user['name'] ?? 'Attendee',
                $user['email'] ?? '',
                $user['mobile'] ?? '',
                $user['designation'] ?? '',
                $user['company'] ?? ''
            ]);

            $db->commit();

            return [
                'success' => true,
                'booking_id' => $bookingId,
                'booking_code' => $bookingCode,
                'event_title' => $event['title'],
                'message' => 'Seat successfully confirmed! Your attendee pass is ready.'
            ];
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()];
        }
    }
}
