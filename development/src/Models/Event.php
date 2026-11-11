<?php
namespace App\Models;

use App\Database;

class Event {
    public static function getById(int $id): ?array {
        return Database::fetchOne("
            SELECT e.*, c.name as category_name, c.slug as category_slug
            FROM events e
            JOIN event_categories c ON e.category_id = c.id
            WHERE e.id = ?
        ", [$id]);
    }

    public static function getUpcoming(int $limit = 6): array {
        return Database::fetchAll("
            SELECT e.*, c.name as category_name, c.slug as category_slug,
                   (e.capacity - e.booked_count) as seats_left,
                   ROUND((e.booked_count / e.capacity) * 100) as fill_percentage
            FROM events e
            JOIN event_categories c ON e.category_id = c.id
            WHERE e.status = 'published' AND e.event_date >= CURDATE()
            ORDER BY e.event_date ASC, e.start_time ASC
            LIMIT {$limit}
        ");
    }

    public static function getTrending(int $limit = 4): array {
        return Database::fetchAll("
            SELECT e.*, c.name as category_name,
                   (e.capacity - e.booked_count) as seats_left,
                   ROUND((e.booked_count / e.capacity) * 100) as fill_percentage
            FROM events e
            JOIN event_categories c ON e.category_id = c.id
            WHERE e.status = 'published' AND (e.is_trending = 1 OR e.booked_count > 100)
            ORDER BY e.booked_count DESC
            LIMIT {$limit}
        ");
    }

    public static function getPast(int $limit = 6): array {
        return Database::fetchAll("
            SELECT e.*, c.name as category_name,
                   (SELECT COUNT(*) FROM event_materials WHERE event_id = e.id) as materials_count,
                   (SELECT COUNT(*) FROM event_videos WHERE event_id = e.id) as videos_count,
                   (SELECT ROUND(AVG(rating), 1) FROM feedback WHERE event_id = e.id) as avg_rating
            FROM events e
            JOIN event_categories c ON e.category_id = c.id
            WHERE (e.status = 'completed' OR (e.status != 'draft' AND e.event_date < CURDATE()))
            ORDER BY e.event_date DESC
            LIMIT {$limit}
        ");
    }

    public static function getSpeakers(int $eventId): array {
        return Database::fetchAll("SELECT * FROM event_speakers WHERE event_id = ? ORDER BY is_keynote DESC, id ASC", [$eventId]);
    }

    public static function getAgendas(int $eventId): array {
        return Database::fetchAll("SELECT * FROM event_agendas WHERE event_id = ? ORDER BY display_order ASC, time_start ASC", [$eventId]);
    }

    public static function getMaterials(int $eventId): array {
        return Database::fetchAll("SELECT * FROM event_materials WHERE event_id = ? ORDER BY is_pre_read DESC, id ASC", [$eventId]);
    }

    public static function getGallery(int $eventId): array {
        return Database::fetchAll("SELECT * FROM event_gallery WHERE event_id = ? ORDER BY display_order ASC", [$eventId]);
    }

    public static function getVideos(int $eventId): array {
        return Database::fetchAll("SELECT * FROM event_videos WHERE event_id = ?", [$eventId]);
    }

    public static function getLinks(int $eventId): array {
        return Database::fetchAll("SELECT * FROM event_links WHERE event_id = ?", [$eventId]);
    }

    public static function getReviews(int $eventId): array {
        return Database::fetchAll("
            SELECT f.*, u.name as user_name, u.company as user_company, u.designation as user_designation
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            WHERE f.event_id = ? AND f.is_approved = 1
            ORDER BY f.created_at DESC
        ", [$eventId]);
    }

    public static function search(array $filters = []): array {
        $sql = "
            SELECT e.*, c.name as category_name,
                   (e.capacity - e.booked_count) as seats_left,
                   ROUND((e.booked_count / e.capacity) * 100) as fill_percentage,
                   (SELECT COUNT(*) FROM event_materials WHERE event_id = e.id) as materials_count,
                   (SELECT COUNT(*) FROM event_videos WHERE event_id = e.id) as videos_count,
                   (SELECT ROUND(AVG(rating), 1) FROM feedback WHERE event_id = e.id) as avg_rating
            FROM events e
            JOIN event_categories c ON e.category_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['query'])) {
            $sql .= " AND (e.title LIKE ? OR e.description LIKE ? OR e.short_summary LIKE ?)";
            $q = '%' . $filters['query'] . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['event_type'])) {
            $sql .= " AND e.event_type = ?";
            $params[] = $filters['event_type'];
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'completed') {
                $sql .= " AND (e.status = 'completed' OR (e.status != 'draft' AND e.event_date < CURDATE()))";
            } elseif ($filters['status'] === 'published') {
                $sql .= " AND e.status = 'published' AND e.event_date >= CURDATE()";
            } else {
                $sql .= " AND e.status = ?";
                $params[] = $filters['status'];
            }
        } else {
            $sql .= " AND e.status != 'draft'";
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.event_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['status']) && $filters['status'] === 'completed') {
            $sql .= " ORDER BY e.event_date DESC, e.start_time DESC";
        } else {
            $sql .= " ORDER BY CASE WHEN e.event_date >= CURDATE() THEN 0 ELSE 1 END ASC, e.event_date ASC, e.start_time ASC";
        }

        return Database::fetchAll($sql, $params);
    }
}
