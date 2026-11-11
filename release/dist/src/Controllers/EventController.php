<?php
namespace App\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Database;

class EventController {
    public function list() {
        $filters = [
            'query' => $_GET['q'] ?? '',
            'category_id' => $_GET['category'] ?? '',
            'event_type' => $_GET['type'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $events = Event::search($filters);
        $categories = Database::fetchAll("SELECT * FROM event_categories ORDER BY name ASC");
        
        require __DIR__ . '/../../views/events/list.php';
    }

    public function detail() {
        $id = (int)($_GET['id'] ?? 0);
        $event = Event::getById($id);

        if (!$event) {
            header("Location: index.php?page=events");
            exit;
        }

        // If completed or past event date, redirect to past event highlights library
        $isPast = ($event['status'] === 'completed') || (!empty($event['event_date']) && $event['event_date'] < date('Y-m-d'));
        if ($isPast) {
            header("Location: " . app_url('past/' . $id));
            exit;
        }

        $speakers = Event::getSpeakers($id);
        $agendas = Event::getAgendas($id);
        $materials = Event::getMaterials($id);
        $reviews = Event::getReviews($id);

        $currentUser = User::current();
        $isBooked = false;
        $booking = null;

        if ($currentUser) {
            $booking = Database::fetchOne("
                SELECT * FROM bookings 
                WHERE event_id = ? AND user_id = ? AND status != 'cancelled'
            ", [$id, $currentUser['id']]);
            $isBooked = !empty($booking);
        }

        $availableSeats = max(0, $event['capacity'] - $event['booked_count']);
        $fillPercentage = min(100, round(($event['booked_count'] / $event['capacity']) * 100));

        require __DIR__ . '/../../views/events/detail.php';
    }

    public function pastDetail() {
        $id = (int)($_GET['id'] ?? 0);
        $event = Event::getById($id);

        if (!$event) {
            header("Location: index.php?page=events");
            exit;
        }

        $speakers = Event::getSpeakers($id);
        $materials = Event::getMaterials($id);
        $gallery = Event::getGallery($id);
        $videos = Event::getVideos($id);
        $links = Event::getLinks($id);
        $reviews = Event::getReviews($id);

        $currentUser = User::current();
        $userCert = null;
        if ($currentUser) {
            $userCert = Database::fetchOne("SELECT * FROM certificates WHERE event_id = ? AND user_id = ?", [$id, $currentUser['id']]);
        }

        require __DIR__ . '/../../views/events/past_detail.php';
    }
}
