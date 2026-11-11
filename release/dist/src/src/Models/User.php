<?php
namespace App\Models;

use App\Database;

class User {
    public static function findById(int $id): ?array {
        return Database::fetchOne("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ?
        ", [$id]);
    }

    public static function findByEmailOrUsername(string $identifier): ?array {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        if (strcasecmp($identifier, 'admin') === 0) {
            $admin = Database::fetchOne("
                SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = 1
                LIMIT 1
            ");
            if ($admin) {
                return $admin;
            }
        }

        return Database::fetchOne("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? OR u.name = ?
            LIMIT 1
        ", [$identifier, $identifier]);
    }

    public static function current(): ?array {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return self::findById((int)$_SESSION['user_id']);
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isAdmin(): bool {
        $user = self::current();
        return $user && ($user['role_name'] === 'Admin' || (int)$user['role_id'] === 1);
    }

    public static function isSpeaker(): bool {
        $user = self::current();
        return $user && ($user['role_name'] === 'Speaker' || (int)$user['role_id'] === 2);
    }

    public static function authenticate(string $identifier, string $password): ?array {
        $user = self::findByEmailOrUsername($identifier);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public static function register(array $data): array {
        $existing = self::findByEmailOrUsername($data['email']);
        if ($existing) {
            return ['success' => false, 'message' => 'An account with this email already exists.'];
        }

        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $roleId = 3; // Default 'User'

        $ok = Database::execute("
            INSERT INTO users (role_id, name, email, mobile, company, designation, password_hash)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [
            $roleId,
            $data['name'],
            $data['email'],
            $data['mobile'] ?? null,
            $data['company'] ?? null,
            $data['designation'] ?? null,
            $hash
        ]);

        if ($ok) {
            $newId = (int)Database::lastInsertId();
            return ['success' => true, 'user_id' => $newId, 'message' => 'Registration successful!'];
        }

        return ['success' => false, 'message' => 'Failed to create user account.'];
    }

    /**
     * Admin creates speaker account
     */
    public static function createSpeaker(array $data): array {
        $existing = self::findByEmailOrUsername($data['email']);
        if ($existing) {
            return ['success' => false, 'message' => 'A user with this email already exists.'];
        }

        $hash = password_hash($data['password'] ?: 'speaker123', PASSWORD_BCRYPT);
        $roleId = 2; // Speaker role

        $ok = Database::execute("
            INSERT INTO users (role_id, name, email, mobile, company, designation, password_hash, avatar)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $roleId,
            $data['name'],
            $data['email'],
            $data['mobile'] ?? null,
            $data['company'] ?? null,
            $data['designation'] ?? null,
            $hash,
            $data['photo'] ?? 'assets/speakers/sarah.jpg'
        ]);

        if ($ok) {
            $newId = (int)Database::lastInsertId();
            return ['success' => true, 'user_id' => $newId, 'message' => 'Speaker account created successfully!'];
        }

        return ['success' => false, 'message' => 'Failed to create speaker account.'];
    }

    public static function getAllSpeakers(): array {
        return Database::fetchAll("
            SELECT u.* 
            FROM users u 
            WHERE u.role_id = 2 
            ORDER BY u.name ASC
        ");
    }

    public static function getAllUsers(): array {
        return Database::fetchAll("
            SELECT u.*, r.name as role_name,
                   (SELECT COUNT(*) FROM bookings WHERE user_id = u.id AND status != 'cancelled') as total_bookings,
                   (SELECT COUNT(*) FROM certificates WHERE user_id = u.id) as total_certs
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.role_id ASC, u.id ASC
        ");
    }

    public static function resetPassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        return Database::execute("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $userId]);
    }

    public static function resetPasswordByEmail(string $email, string $newPassword): bool {
        $user = self::findByEmailOrUsername($email);
        if (!$user) {
            return false;
        }
        return self::resetPassword((int)$user['id'], $newPassword);
    }
}
