<?php
namespace App\Models;

use App\Database;

class CMSBanner {
    public static function getActiveBanners(): array {
        return Database::fetchAll("
            SELECT b.*, e.title as event_title, e.event_date, e.start_time
            FROM cms_banners b
            LEFT JOIN events e ON b.event_id = e.id
            WHERE b.is_active = 1
            ORDER BY b.display_order ASC, b.id ASC
        ");
    }

    public static function getAll(): array {
        return Database::fetchAll("
            SELECT b.*, e.title as event_title
            FROM cms_banners b
            LEFT JOIN events e ON b.event_id = e.id
            ORDER BY b.display_order ASC
        ");
    }

    public static function update(int $id, array $data): bool {
        $imgPath = !empty($data['image_path']) ? $data['image_path'] : null;
        if ($imgPath !== null) {
            return Database::execute("
                UPDATE cms_banners 
                SET title = ?, subtitle = ?, image_path = ?, cta_text = ?, target_url = ?, badge_text = ?, display_order = ?, is_active = ?
                WHERE id = ?
            ", [
                $data['title'],
                $data['subtitle'] ?? '',
                $imgPath,
                $data['cta_text'] ?? 'Register Now',
                $data['target_url'] ?? 'explore',
                $data['badge_text'] ?? 'FEATURED',
                (int)$data['display_order'],
                (int)$data['is_active'],
                $id
            ]);
        } else {
            return Database::execute("
                UPDATE cms_banners 
                SET title = ?, subtitle = ?, cta_text = ?, target_url = ?, badge_text = ?, display_order = ?, is_active = ?
                WHERE id = ?
            ", [
                $data['title'],
                $data['subtitle'] ?? '',
                $data['cta_text'] ?? 'Register Now',
                $data['target_url'] ?? 'explore',
                $data['badge_text'] ?? 'FEATURED',
                (int)$data['display_order'],
                (int)$data['is_active'],
                $id
            ]);
        }
    }
}
