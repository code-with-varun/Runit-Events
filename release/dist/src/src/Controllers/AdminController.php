<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\CMSBanner;
use App\Models\Certificate;
use App\Models\Setting;
use App\Database;

class AdminController {
    private function checkAuth() {
        if (!User::isAdmin()) {
            header("Location: " . app_url('login', ['notice' => 'Admin access required.']));
            exit;
        }
    }

    private function handleUpload(string $key, string $subfolder): ?string {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$key];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed, true)) {
                $destDir = __DIR__ . '/../../public/uploads/' . trim($subfolder, '/') . '/';
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0755, true);
                }
                $filename = trim($subfolder, '/') . '_' . date('Ymd_His') . '_' . mt_rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $destDir . $filename)) {
                    return 'uploads/' . trim($subfolder, '/') . '/' . $filename;
                }
            }
        }
        return null;
    }

    private function ensureSchema(): void {
        try {
            $col = Database::fetchOne("SHOW COLUMNS FROM events LIKE 'recording_url'");
            if (!$col) {
                Database::execute("ALTER TABLE events ADD COLUMN recording_url TEXT NULL AFTER meeting_link");
            } else {
                Database::execute("ALTER TABLE events MODIFY COLUMN recording_url TEXT NULL");
            }

            $colCard = Database::fetchOne("SHOW COLUMNS FROM events LIKE 'card_image'");
            if (!$colCard) {
                Database::execute("ALTER TABLE events ADD COLUMN card_image VARCHAR(255) NULL AFTER banner_image");
            }

            $colPoster = Database::fetchOne("SHOW COLUMNS FROM events LIKE 'poster_image'");
            if (!$colPoster) {
                Database::execute("ALTER TABLE events ADD COLUMN poster_image VARCHAR(255) NULL AFTER card_image");
            }
        } catch (\Throwable $e) {
            // Non-critical if column already exists or table alteration has permissions
        }
    }

    public function dashboard() {
        $this->checkAuth();

        $stats = [
            'total_users' => Database::fetchOne("SELECT COUNT(*) as c FROM users WHERE role_id != 1")['c'] ?? 0,
            'total_speakers' => Database::fetchOne("SELECT COUNT(*) as c FROM users WHERE role_id = 2")['c'] ?? 0,
            'total_events' => Database::fetchOne("SELECT COUNT(*) as c FROM events")['c'] ?? 0,
            'total_bookings' => Database::fetchOne("SELECT COUNT(*) as c FROM bookings WHERE status != 'cancelled'")['c'] ?? 0,
            'total_certs' => Database::fetchOne("SELECT COUNT(*) as c FROM certificates")['c'] ?? 0,
        ];

        $recentBookings = Database::fetchAll("
            SELECT b.*, e.title as event_title, u.name as user_name, u.email as user_email
            FROM bookings b
            JOIN events e ON b.event_id = e.id
            JOIN users u ON b.user_id = u.id
            ORDER BY b.created_at DESC
            LIMIT 6
        ");

        $eventsOverview = Database::fetchAll("
            SELECT e.*, 
                   (SELECT COUNT(*) FROM bookings WHERE event_id = e.id AND status != 'cancelled') as total_registered,
                   (SELECT COUNT(*) FROM attendance WHERE event_id = e.id AND is_eligible_for_cert = 1) as total_certified
            FROM events e
            ORDER BY e.event_date DESC
            LIMIT 5
        ");

        require __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function events() {
        $this->checkAuth();

        $events = Database::fetchAll("
            SELECT e.*, c.name as category_name,
                   (SELECT COUNT(*) FROM bookings WHERE event_id = e.id AND status != 'cancelled') as current_bookings
            FROM events e
            JOIN event_categories c ON e.category_id = c.id
            ORDER BY e.event_date DESC
        ");

        require __DIR__ . '/../../views/admin/events.php';
    }

    public function eventForm() {
        $this->checkAuth();
        $this->ensureSchema();

        $id = (int)($_GET['id'] ?? 0);
        $event = $id > 0 ? Event::getById($id) : null;
        $linkedBanner = $id > 0 ? Database::fetchOne("SELECT * FROM cms_banners WHERE event_id = ?", [$id]) : null;
        $categories = Database::fetchAll("SELECT * FROM event_categories ORDER BY name ASC");
        $allSpeakers = User::getAllSpeakers();
        $mappedSpeaker = $id > 0 ? Database::fetchOne("SELECT * FROM event_speakers WHERE event_id = ? ORDER BY is_keynote DESC, id ASC LIMIT 1", [$id]) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $code = trim($_POST['event_code'] ?? ('EVT-' . date('Y') . '-' . mt_rand(100, 999)));
            $catId = (int)($_POST['category_id'] ?? 1);
            $type = $_POST['event_type'] ?? 'Webinar';
            $summary = trim($_POST['short_summary'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $date = $_POST['event_date'] ?? date('Y-m-d');
            $time = $_POST['start_time'] ?? '18:00';
            $duration = (int)($_POST['duration_minutes'] ?? 60);
            $capacity = (int)($_POST['capacity'] ?? 100);
            $meetingLink = trim($_POST['meeting_link'] ?? '');
            $recordingUrl = trim($_POST['recording_url'] ?? '');
            // Automatically extract clean URL if full iframe HTML was pasted
            if (preg_match('/src=["\']([^"\']+)["\']/i', $recordingUrl, $matches)) {
                $recordingUrl = $matches[1];
            }
            if (!empty($recordingUrl) && strpos($recordingUrl, '/showcase/watch/') !== false) {
                $recordingUrl = str_replace('/showcase/watch/', '/showcase/embed/', $recordingUrl);
            }
            $status = $_POST['status'] ?? 'published';
            $certEligible = isset($_POST['is_certificate_eligible']) ? 1 : 0;
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isTrending = isset($_POST['is_trending']) ? 1 : 0;

            // Direct upload or picker/preset for 3 image formats (Landscape, Square, Portrait)
            $uploadedBanner = $this->handleUpload('banner_file', 'events');
            $bannerImg = $uploadedBanner ?: trim($_POST['banner_image'] ?? 'assets/banners/hero_ai.jpg');

            $uploadedCard = $this->handleUpload('card_file', 'events');
            $cardImg = $uploadedCard ?: trim($_POST['card_image'] ?? ($bannerImg ?: 'assets/banners/hero_ai.jpg'));

            $uploadedPoster = $this->handleUpload('poster_file', 'events');
            $posterImg = $uploadedPoster ?: trim($_POST['poster_image'] ?? '');

            if ($id > 0) {
                Database::execute("
                    UPDATE events SET 
                        title = ?, category_id = ?, event_type = ?, short_summary = ?, description = ?,
                        event_date = ?, start_time = ?, duration_minutes = ?, capacity = ?, meeting_link = ?,
                        recording_url = ?, status = ?, is_certificate_eligible = ?, is_featured = ?, is_trending = ?, 
                        banner_image = ?, card_image = ?, poster_image = ?
                    WHERE id = ?
                ", [
                    $title, $catId, $type, $summary, $desc,
                    $date, $time, $duration, $capacity, $meetingLink,
                    $recordingUrl, $status, $certEligible, $isFeatured, $isTrending, 
                    $bannerImg, $cardImg, $posterImg,
                    $id
                ]);
            } else {
                Database::execute("
                    INSERT INTO events (
                        title, event_code, category_id, event_type, short_summary, description,
                        event_date, start_time, duration_minutes, capacity, meeting_link,
                        recording_url, status, is_certificate_eligible, is_featured, is_trending, 
                        banner_image, card_image, poster_image, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ", [
                    $title, $code, $catId, $type, $summary, $desc,
                    $date, $time, $duration, $capacity, $meetingLink,
                    $recordingUrl, $status, $certEligible, $isFeatured, $isTrending, 
                    $bannerImg, $cardImg, $posterImg, User::current()['id']
                ]);
                $id = Database::lastInsertId();
            }

            // Sync with event_videos for recordings if URL provided
            if (!empty($recordingUrl) && $id > 0) {
                $existingVid = Database::fetchOne("SELECT id FROM event_videos WHERE event_id = ?", [$id]);
                $source = 'other';
                if (strpos($recordingUrl, 'youtube') !== false || strpos($recordingUrl, 'youtu.be') !== false) {
                    $source = 'youtube';
                } elseif (strpos($recordingUrl, 'vimeo') !== false) {
                    $source = 'vimeo';
                } elseif (strpos($recordingUrl, 'showcase') !== false) {
                    $source = 'showcase';
                }
                if ($existingVid) {
                    Database::execute("UPDATE event_videos SET title = ?, video_url = ?, video_source = ? WHERE id = ?", [
                        $title . ' - Recording', $recordingUrl, $source, $existingVid['id']
                    ]);
                } else {
                    Database::execute("INSERT INTO event_videos (event_id, title, video_url, video_source, duration) VALUES (?, ?, ?, ?, ?)", [
                        $id, $title . ' - Recording', $recordingUrl, $source, $duration . ' mins'
                    ]);
                }
            }

            // Sync with cms_banners if Hero Slider promo requested
            $syncHero = isset($_POST['sync_hero_banner']) ? 1 : 0;
            if ($syncHero && $id > 0) {
                $heroCta = trim($_POST['hero_cta_text'] ?? 'Reserve Seat');
                $heroBadge = trim($_POST['hero_badge_text'] ?? 'FEATURED');
                $heroOrder = (int)($_POST['hero_display_order'] ?? 1);
                $heroSource = $_POST['hero_image_source'] ?? 'same';

                $heroImgPath = $bannerImg;
                if ($heroSource === 'custom') {
                    $uploadedHero = $this->handleUpload('hero_banner_file', 'banners');
                    $heroImgPath = $uploadedHero ?: trim($_POST['hero_image_path'] ?? $bannerImg);
                }

                $targetUrl = 'event/' . $id;

                $existingHero = Database::fetchOne("SELECT id FROM cms_banners WHERE event_id = ?", [$id]);
                if ($existingHero) {
                    Database::execute("
                        UPDATE cms_banners 
                        SET title = ?, subtitle = ?, image_path = ?, cta_text = ?, target_url = ?, badge_text = ?, display_order = ?, is_active = 1
                        WHERE id = ?
                    ", [$title, $summary, $heroImgPath, $heroCta, $targetUrl, $heroBadge, $heroOrder, $existingHero['id']]);
                } else {
                    Database::execute("
                        INSERT INTO cms_banners (title, subtitle, image_path, cta_text, event_id, target_url, badge_text, display_order, is_active)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                    ", [$title, $summary, $heroImgPath, $heroCta, $id, $targetUrl, $heroBadge, $heroOrder]);
                }
            } else {
                // If hero switch was turned off, set existing hero banner inactive
                if ($id > 0) {
                    Database::execute("UPDATE cms_banners SET is_active = 0 WHERE event_id = ?", [$id]);
                }
            }

            // Sync mapped speaker
            $speakerUserId = (int)($_POST['speaker_user_id'] ?? 0);
            if ($id > 0) {
                if ($speakerUserId > 0) {
                    $spkUser = User::findById($speakerUserId);
                    if ($spkUser) {
                        $spkName = $spkUser['name'];
                        $spkTitle = trim($_POST['speaker_title'] ?? '') ?: ($spkUser['designation'] ?: 'Keynote Speaker');
                        $spkCompany = trim($_POST['speaker_company'] ?? '') ?: ($spkUser['company'] ?: 'Runit');
                        $spkBio = trim($_POST['speaker_bio'] ?? '') ?: ($spkUser['name'] . ' is leading enterprise technical sessions at Runit.');
                        $spkPhoto = $spkUser['avatar'] ?: 'assets/speakers/sarah.jpg';

                        $existingSpeaker = Database::fetchOne("SELECT id FROM event_speakers WHERE event_id = ?", [$id]);
                        if ($existingSpeaker) {
                            Database::execute("
                                UPDATE event_speakers 
                                SET user_id = ?, name = ?, title = ?, company = ?, bio = ?, photo = ?, is_keynote = 1
                                WHERE id = ?
                            ", [$speakerUserId, $spkName, $spkTitle, $spkCompany, $spkBio, $spkPhoto, $existingSpeaker['id']]);
                        } else {
                            Database::execute("
                                INSERT INTO event_speakers (event_id, user_id, name, title, company, bio, photo, is_keynote)
                                VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                            ", [$id, $speakerUserId, $spkName, $spkTitle, $spkCompany, $spkBio, $spkPhoto]);
                        }
                    }
                }
            }

            header("Location: " . app_url('admin/events', ['saved' => 1]));
            exit;
        }

        require __DIR__ . '/../../views/admin/event_form.php';
    }

    public function banners() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'update';
            if ($action === 'update_animation') {
                $anim = $_POST['slider_animation'] ?? 'fade';
                $interval = (int)($_POST['slider_interval'] ?? 5000);
                Setting::set('slider_animation', $anim);
                Setting::set('slider_interval', (string)$interval);
                header("Location: " . app_url('admin/banners', ['anim_saved' => 1]));
                exit;
            }
            if ($action === 'create') {
                $uploaded = $this->handleUpload('banner_file', 'banners');
                $imgPath = $uploaded ?: trim($_POST['image_path'] ?? 'assets/banners/hero_ai.jpg');

                Database::execute("
                    INSERT INTO cms_banners (title, subtitle, image_path, cta_text, target_url, badge_text, display_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ", [
                    trim($_POST['title'] ?? 'New Banner'),
                    trim($_POST['subtitle'] ?? ''),
                    $imgPath,
                    trim($_POST['cta_text'] ?? 'Register Now'),
                    trim($_POST['target_url'] ?? 'explore'),
                    trim($_POST['badge_text'] ?? 'FEATURED'),
                    (int)($_POST['display_order'] ?? 1),
                    isset($_POST['is_active']) ? 1 : 0
                ]);
            } else {
                $id = (int)$_POST['id'];
                $uploaded = $this->handleUpload('banner_file_' . $id, 'banners');
                $imgPath = $uploaded ?: trim($_POST['image_path'] ?? '');

                CMSBanner::update($id, [
                    'title' => trim($_POST['title']),
                    'subtitle' => trim($_POST['subtitle'] ?? ''),
                    'image_path' => $imgPath,
                    'cta_text' => trim($_POST['cta_text'] ?? 'Register Now'),
                    'target_url' => trim($_POST['target_url'] ?? 'explore'),
                    'badge_text' => trim($_POST['badge_text'] ?? 'FEATURED'),
                    'display_order' => (int)$_POST['display_order'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ]);
            }
            header("Location: " . app_url('admin/banners', ['saved' => 1]));
            exit;
        }

        $banners = CMSBanner::getAll();
        require __DIR__ . '/../../views/admin/banners.php';
    }

    public function speakers() {
        $this->checkAuth();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uploadedPhoto = $this->handleUpload('photo_file', 'speakers');
            $photo = $uploadedPhoto ?: trim($_POST['photo'] ?? 'assets/speakers/sarah.jpg');

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? 'speaker123'),
                'mobile' => trim($_POST['mobile'] ?? ''),
                'company' => trim($_POST['company'] ?? ''),
                'designation' => trim($_POST['designation'] ?? ''),
                'photo' => $photo
            ];

            if (empty($data['name']) || empty($data['email'])) {
                $error = 'Speaker Name and Email are required.';
            } else {
                $res = User::createSpeaker($data);
                if ($res['success']) {
                    $success = 'Speaker account created successfully! The speaker can now log in using these credentials.';
                } else {
                    $error = $res['message'];
                }
            }
        }

        $speakers = User::getAllSpeakers();
        require __DIR__ . '/../../views/admin/speakers.php';
    }

    public function attendance() {
        $this->checkAuth();

        $eventId = (int)($_GET['event_id'] ?? 1);
        $events = Database::fetchAll("SELECT id, title, event_date FROM events ORDER BY event_date DESC");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $userId = (int)$_POST['user_id'];
            $pct = (float)$_POST['percentage_attended'];
            $isEligible = ($pct >= 75.0) ? 1 : 0;

            $existing = Database::fetchOne("SELECT id FROM attendance WHERE event_id = ? AND user_id = ?", [$eventId, $userId]);
            if ($existing) {
                Database::execute("
                    UPDATE attendance 
                    SET percentage_attended = ?, is_eligible_for_cert = ?, marked_by = ? 
                    WHERE id = ?
                ", [$pct, $isEligible, User::current()['id'], $existing['id']]);
            } else {
                Database::execute("
                    INSERT INTO attendance (event_id, user_id, source, percentage_attended, is_eligible_for_cert, marked_by)
                    VALUES (?, ?, 'manual', ?, ?, ?)
                ", [$eventId, $userId, $pct, $isEligible, User::current()['id']]);
            }

            if ($isEligible) {
                Certificate::issue($eventId, $userId);
            }

            header("Location: " . app_url('admin/attendance', ['event_id' => $eventId, 'updated' => 1]));
            exit;
        }

        $attendees = Database::fetchAll("
            SELECT b.id as booking_id, b.booking_code, b.created_at as booked_at,
                   u.id as user_id, u.name as user_name, u.email as user_email, u.company as user_company,
                   a.percentage_attended, a.is_eligible_for_cert, a.source as attendance_source,
                   c.cert_number
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            LEFT JOIN attendance a ON a.event_id = b.event_id AND a.user_id = u.id
            LEFT JOIN certificates c ON c.event_id = b.event_id AND c.user_id = u.id
            WHERE b.event_id = ? AND b.status != 'cancelled'
            ORDER BY u.name ASC
        ", [$eventId]);

        $selectedEvent = Event::getById($eventId);

        require __DIR__ . '/../../views/admin/attendance.php';
    }

    public function users() {
        $this->checkAuth();

        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'reset_password') {
                $userId = (int)($_POST['user_id'] ?? 0);
                $newPassword = trim($_POST['new_password'] ?? '');

                if ($userId > 0 && !empty($newPassword)) {
                    if (User::resetPassword($userId, $newPassword)) {
                        $success = 'Password successfully updated for User ID #' . $userId;
                    } else {
                        $error = 'Failed to reset password. Please try again.';
                    }
                } else {
                    $error = 'Invalid user ID or password cannot be empty.';
                }
            }
        }

        $users = User::getAllUsers();
        require __DIR__ . '/../../views/admin/users.php';
    }

    public function reports() {
        $this->checkAuth();

        $eventMetrics = Database::fetchAll("
            SELECT e.id, e.title, e.event_date, e.capacity, e.booked_count,
                   (e.capacity - e.booked_count) as seats_left,
                   (SELECT COUNT(*) FROM attendance WHERE event_id = e.id) as actual_attended,
                   (SELECT COUNT(*) FROM certificates WHERE event_id = e.id) as certs_issued,
                   (SELECT ROUND(AVG(rating), 1) FROM feedback WHERE event_id = e.id) as avg_rating
            FROM events e
            ORDER BY e.event_date DESC
        ");

        require __DIR__ . '/../../views/admin/reports.php';
    }

    public function settings() {
        $this->checkAuth();

        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                Setting::updateAll($_POST);
                $success = 'Platform settings and footer details updated successfully!';
            } catch (\Throwable $e) {
                $error = 'Failed to save settings: ' . $e->getMessage();
            }
        }

        $settings = Setting::all();
        require __DIR__ . '/../../views/admin/settings.php';
    }

    public function reset() {
        $this->checkAuth();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $confirmation = trim($_POST['confirm_reset'] ?? '');
            if ($confirmation === 'RESET_CONFIRMED') {
                try {
                    Database::execute("SET FOREIGN_KEY_CHECKS = 0");
                    Database::execute("TRUNCATE TABLE bookings");
                    Database::execute("TRUNCATE TABLE booking_attendees");
                    Database::execute("TRUNCATE TABLE attendance");
                    Database::execute("TRUNCATE TABLE certificates");
                    Database::execute("TRUNCATE TABLE waitlists");
                    Database::execute("TRUNCATE TABLE feedback");
                    Database::execute("TRUNCATE TABLE event_speakers");
                    Database::execute("TRUNCATE TABLE event_agendas");
                    Database::execute("TRUNCATE TABLE event_materials");
                    Database::execute("TRUNCATE TABLE event_videos");
                    Database::execute("TRUNCATE TABLE event_gallery");
                    Database::execute("TRUNCATE TABLE event_links");
                    Database::execute("TRUNCATE TABLE events");
                    Database::execute("TRUNCATE TABLE cms_banners");

                    // Delete test users except Admin (id = 1)
                    Database::execute("DELETE FROM users WHERE id != 1");
                    Database::execute("ALTER TABLE users AUTO_INCREMENT = 2");

                    // Add 1 default clean placeholder banner
                    Database::execute("
                        INSERT INTO cms_banners (id, title, subtitle, image_path, cta_text, target_url, badge_text, display_order, is_active)
                        VALUES (1, 'Welcome to Runit Events Hub', 'Masterclasses, Technical Webinars & Professional Certifications', 'assets/banners/hero_ai.jpg', 'Browse All Webinars', 'explore', 'FEATURED', 1, 1)
                    ");

                    Database::execute("SET FOREIGN_KEY_CHECKS = 1");

                    $_SESSION['flash_success'] = 'Application data has been successfully reset! All dummy bookings, events, and records have been cleared. You can now start entering details manually.';
                    header("Location: " . app_url('admin/events'));
                    exit;
                } catch (\Throwable $e) {
                    $error = 'Reset failed: ' . $e->getMessage();
                }
            } else {
                $error = 'Confirmation code did not match. Reset cancelled.';
            }
        }

        require __DIR__ . '/../../views/admin/reset.php';
    }
}
