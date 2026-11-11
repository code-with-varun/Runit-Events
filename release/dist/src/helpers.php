<?php
// src/helpers.php - URL & Routing Utilities

if (!function_exists('app_base_url')) {
    function app_base_url(): string {
        $isProduction = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'runito.in') !== false);
        if ($isProduction) {
            return 'https://runito.in/events/';
        }
        $port = $_SERVER['SERVER_PORT'] ?? 80;
        if ((string)$port === '8000') {
            return 'http://localhost:8000/';
        }
        return '/events/';
    }
}

if (!function_exists('app_url')) {
    function app_url(string $route = '', array $params = []): string {
        $base = app_base_url();
        $route = ltrim($route, '/');
        $query = !empty($params) ? ('?' . http_build_query($params)) : '';
        return $base . $route . $query;
    }
}

if (!function_exists('format_embed_url')) {
    function format_embed_url(?string $url): string {
        $url = trim((string)$url);
        if ($url === '') return '';

        // If user pasted iframe HTML tag: <iframe ... src="..." ...>
        if (preg_match('/src=["\']([^"\']+)["\']/i', $url, $matches)) {
            $url = $matches[1];
        }

        // Decode HTML entities if any
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // YouTube: https://www.youtube.com/watch?v=XYZ or https://youtu.be/XYZ or https://www.youtube.com/embed/XYZ
        if (preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            return 'https://www.youtube-nocookie.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
        }

        // Vimeo: https://vimeo.com/12345 or https://player.vimeo.com/video/12345
        if (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/i', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        // Runit Showcase or internal watch URLs: /showcase/watch/XYZ -> /showcase/embed/XYZ
        if (strpos($url, '/showcase/watch/') !== false) {
            $url = str_replace('/showcase/watch/', '/showcase/embed/', $url);
        }

        return $url;
    }
}

if (!function_exists('get_media_gallery')) {
    function get_media_gallery(): array {
        $publicDir = dirname(__DIR__) . '/public';
        $images = [];

        // Check assets/banners/
        $bannerDir = $publicDir . '/assets/banners';
        if (is_dir($bannerDir)) {
            foreach (glob($bannerDir . '/*.{jpg,jpeg,png,webp,svg}', GLOB_BRACE) as $file) {
                $images[] = [
                    'path' => 'assets/banners/' . basename($file),
                    'name' => basename($file),
                    'type' => 'Banner Preset'
                ];
            }
        }

        // Check assets/speakers/
        $speakerDir = $publicDir . '/assets/speakers';
        if (is_dir($speakerDir)) {
            foreach (glob($speakerDir . '/*.{jpg,jpeg,png,webp,svg}', GLOB_BRACE) as $file) {
                $images[] = [
                    'path' => 'assets/speakers/' . basename($file),
                    'name' => basename($file),
                    'type' => 'Speaker Photo'
                ];
            }
        }

        // Check uploads/events/
        $uploadEvents = $publicDir . '/uploads/events';
        if (is_dir($uploadEvents)) {
            foreach (glob($uploadEvents . '/*.{jpg,jpeg,png,webp,svg}', GLOB_BRACE) as $file) {
                $images[] = [
                    'path' => 'uploads/events/' . basename($file),
                    'name' => basename($file),
                    'type' => 'Uploaded Event Media'
                ];
            }
        }

        // Check uploads/banners/
        $uploadBanners = $publicDir . '/uploads/banners';
        if (is_dir($uploadBanners)) {
            foreach (glob($uploadBanners . '/*.{jpg,jpeg,png,webp,svg}', GLOB_BRACE) as $file) {
                $images[] = [
                    'path' => 'uploads/banners/' . basename($file),
                    'name' => basename($file),
                    'type' => 'Uploaded Banner'
                ];
            }
        }

        return $images;
    }
}

if (!function_exists('format_seat_status')) {
    function format_seat_status(array $event): array {
        $today = date('Y-m-d');
        $isPast = (!empty($event['status']) && $event['status'] === 'completed') || 
                  (!empty($event['event_date']) && $event['event_date'] < $today);

        if ($isPast) {
            return [
                'text' => 'Session Completed',
                'badge_class' => 'bg-secondary text-white',
                'chip_class' => 'completed',
                'icon' => 'bi-check-circle',
                'is_full' => false,
                'is_past' => true,
                'can_book' => false
            ];
        }

        $capacity = max(1, (int)($event['capacity'] ?? 100));
        $booked = (int)($event['booked_count'] ?? 0);
        $left = max(0, $capacity - $booked);
        $pct = round(($booked / $capacity) * 100);

        if ($left <= 0) {
            return [
                'text' => 'No Seats Left (Waitlist)',
                'badge_class' => 'bg-danger text-white',
                'chip_class' => 'full',
                'icon' => 'bi-lock-fill',
                'is_full' => true,
                'is_past' => false,
                'can_book' => false
            ];
        } elseif ($left <= 5 || $pct >= 85) {
            return [
                'text' => 'Few Seats Left 🔥',
                'badge_class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                'chip_class' => 'few-left',
                'icon' => 'bi-fire',
                'is_full' => false,
                'is_past' => false,
                'can_book' => true
            ];
        } elseif ($pct >= 40) {
            return [
                'text' => 'Filling Fast',
                'badge_class' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50',
                'chip_class' => 'filling-fast',
                'icon' => 'bi-lightning-charge-fill',
                'is_full' => false,
                'is_past' => false,
                'can_book' => true
            ];
        } else {
            return [
                'text' => 'Registrations Open',
                'badge_class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                'chip_class' => 'available',
                'icon' => 'bi-check2-circle',
                'is_full' => false,
                'is_past' => false,
                'can_book' => true
            ];
        }
    }
}

if (!function_exists('has_certificate_programs')) {
    function has_certificate_programs(): bool {
        static $cached = null;
        if ($cached !== null) return $cached;
        try {
            $row = \App\Database::fetchOne("SELECT COUNT(*) as c FROM events WHERE is_certificate_eligible = 1");
            $cached = ($row && (int)$row['c'] > 0);
        } catch (\Throwable $e) {
            $cached = false;
        }
        return $cached;
    }
}



