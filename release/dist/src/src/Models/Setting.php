<?php
namespace App\Models;

use App\Database;

class Setting {
    private static ?array $cache = null;

    public static function all(): array {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            // Ensure settings table exists
            Database::execute("
                CREATE TABLE IF NOT EXISTS `settings` (
                    `key` VARCHAR(100) PRIMARY KEY,
                    `value` LONGTEXT NULL
                ) ENGINE=InnoDB;
            ");

            $rows = Database::fetchAll("SELECT `key`, `value` FROM `settings`");
            $settings = [];
            foreach ($rows as $r) {
                $settings[$r['key']] = $r['value'];
            }

            // Defaults
            $defaults = [
                'org_name' => 'Runit Events Hub',
                'tagline' => 'Enterprise Webinars, Masterclasses & Executive Programs',
                'support_email' => 'support@runito.in',
                'support_phone' => '+91 (80) 4567-8900',
                'office_address' => 'Runit Innovation Hub, Bengaluru, India',
                'footer_about' => 'Runit Events Hub is the official corporate event and technical webinar platform of Runit. We host enterprise-level masterclasses, workshops, and certification summits with verified credentials.',
                'linkedin_url' => 'https://linkedin.com',
                'twitter_url' => 'https://x.com',
                'youtube_url' => 'https://youtube.com',
                'github_url' => 'https://github.com',
                'copyright_text' => '© ' . date('Y') . ' Runit. All rights reserved.'
            ];

            self::$cache = array_merge($defaults, $settings);
            return self::$cache;
        } catch (\Throwable $e) {
            return [
                'org_name' => 'Runit Events Hub',
                'tagline' => 'Enterprise Webinars, Masterclasses & Executive Programs',
                'support_email' => 'support@runito.in',
                'support_phone' => '+91 (80) 4567-8900',
                'office_address' => 'Runit Innovation Hub, Bengaluru, India',
                'footer_about' => 'Runit Events Hub is the official corporate event and technical webinar platform of Runit.',
                'linkedin_url' => 'https://linkedin.com',
                'twitter_url' => 'https://x.com',
                'youtube_url' => 'https://youtube.com',
                'github_url' => 'https://github.com',
                'copyright_text' => '© ' . date('Y') . ' Runit. All rights reserved.'
            ];
        }
    }

    public static function get(string $key, string $default = ''): string {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): bool {
        self::$cache = null;
        return Database::execute("
            INSERT INTO `settings` (`key`, `value`) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
        ", [$key, $value]);
    }

    public static function updateAll(array $data): void {
        foreach ($data as $k => $v) {
            self::set($k, (string)$v);
        }
        self::$cache = null;
    }
}

