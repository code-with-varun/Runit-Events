-- Runit Events Hub Database Schema (MySQL 8.0 / MariaDB)
-- CREATE DATABASE IF NOT EXISTS `runit_events_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `runit_events_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `waitlists`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `cms_pages`;
DROP TABLE IF EXISTS `cms_banners`;
DROP TABLE IF EXISTS `feedback`;
DROP TABLE IF EXISTS `event_links`;
DROP TABLE IF EXISTS `event_videos`;
DROP TABLE IF EXISTS `event_gallery`;
DROP TABLE IF EXISTS `event_materials`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `booking_attendees`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `event_agendas`;
DROP TABLE IF EXISTS `event_speakers`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `event_categories`;
DROP TABLE IF EXISTS `user_profiles`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Roles
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL
) ENGINE=InnoDB;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Admin', 'Full CMS & platform management permissions'),
(2, 'Speaker', 'Speaker / Presenter permissions to manage assigned webinars and materials'),
(3, 'User', 'Registered participant who can book seats, attend, and download certificates'),
(4, 'Guest', 'Unauthenticated visitor');

-- 2. Users
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL DEFAULT 3,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `mobile` VARCHAR(30) NULL,
    `company` VARCHAR(150) NULL,
    `designation` VARCHAR(150) NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(255) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `email_verified_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 3. Categories
CREATE TABLE `event_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `icon` VARCHAR(60) DEFAULT 'bi-bookmark',
    `description` TEXT NULL
) ENGINE=InnoDB;

INSERT INTO `event_categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Webinar', 'webinar', 'bi-camera-video', 'Live interactive technical webinars and masterclasses'),
(2, 'Corporate Training', 'corporate-training', 'bi-briefcase', 'Enterprise learning & development programs'),
(3, 'Workshop', 'workshop', 'bi-tools', 'Hands-on guided technical workshops with lab exercises'),
(4, 'Conference', 'conference', 'bi-people', 'Industry summits and keynotes with multiple tracks'),
(5, 'Certification Program', 'certification-program', 'bi-award', 'Comprehensive skill training with certified credentials'),
(6, 'Meetup', 'meetup', 'bi-cup-hot', 'Community gatherings, lightning talks, and networking');

-- 4. Events
CREATE TABLE `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `event_code` VARCHAR(50) NOT NULL UNIQUE,
    `category_id` INT NOT NULL,
    `event_type` VARCHAR(50) NOT NULL DEFAULT 'Webinar',
    `banner_image` VARCHAR(255) NULL,
    `short_summary` VARCHAR(350) NULL,
    `description` LONGTEXT NULL,
    `event_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `duration_minutes` INT NOT NULL DEFAULT 60,
    `capacity` INT NOT NULL DEFAULT 100,
    `booked_count` INT NOT NULL DEFAULT 0,
    `venue_type` ENUM('online', 'in-person', 'hybrid') DEFAULT 'online',
    `venue_address` VARCHAR(255) NULL,
    `meeting_platform` VARCHAR(60) DEFAULT 'Zoom',
    `meeting_link` VARCHAR(500) NULL,
    `reg_start_date` DATETIME NULL,
    `reg_end_date` DATETIME NULL,
    `is_certificate_eligible` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_trending` TINYINT(1) DEFAULT 0,
    `price` DECIMAL(10,2) DEFAULT 0.00,
    `status` ENUM('draft', 'published', 'ongoing', 'completed', 'cancelled') DEFAULT 'published',
    `created_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_events_cat` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_events_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Event Speakers
CREATE TABLE `event_speakers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `user_id` INT NULL,
    `name` VARCHAR(150) NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `company` VARCHAR(150) NULL,
    `bio` TEXT NULL,
    `photo` VARCHAR(255) NULL,
    `linkedin_url` VARCHAR(255) NULL,
    `twitter_url` VARCHAR(255) NULL,
    `is_keynote` TINYINT(1) DEFAULT 0,
    CONSTRAINT `fk_speakers_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Event Agendas
CREATE TABLE `event_agendas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `time_start` TIME NOT NULL,
    `time_end` TIME NOT NULL,
    `topic` VARCHAR(200) NOT NULL,
    `speaker_name` VARCHAR(150) NULL,
    `description` TEXT NULL,
    `display_order` INT DEFAULT 1,
    CONSTRAINT `fk_agenda_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Bookings
CREATE TABLE `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_code` VARCHAR(50) NOT NULL UNIQUE,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `seat_count` INT NOT NULL DEFAULT 1,
    `status` ENUM('reserved', 'confirmed', 'attended', 'cancelled') DEFAULT 'confirmed',
    `reserved_until` DATETIME NULL,
    `qr_token` VARCHAR(100) NOT NULL UNIQUE,
    `pdf_pass_url` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bookings_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Booking Attendees
CREATE TABLE `booking_attendees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `attendee_name` VARCHAR(150) NOT NULL,
    `attendee_email` VARCHAR(191) NOT NULL,
    `attendee_mobile` VARCHAR(30) NULL,
    `designation` VARCHAR(100) NULL,
    `company` VARCHAR(100) NULL,
    CONSTRAINT `fk_attendees_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Attendance
CREATE TABLE `attendance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `booking_id` INT NULL,
    `source` ENUM('join_link', 'qr_scan', 'manual') DEFAULT 'join_link',
    `join_time` DATETIME NULL,
    `leave_time` DATETIME NULL,
    `duration_minutes` INT DEFAULT 0,
    `percentage_attended` DECIMAL(5,2) DEFAULT 0.00,
    `is_eligible_for_cert` TINYINT(1) DEFAULT 0,
    `marked_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_att_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_att_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Certificates
CREATE TABLE `certificates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cert_number` VARCHAR(60) NOT NULL UNIQUE,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `booking_id` INT NULL,
    `issue_date` DATE NOT NULL,
    `file_path` VARCHAR(255) NULL,
    `qr_verification_url` VARCHAR(255) NOT NULL,
    `download_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_cert_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cert_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Event Materials
CREATE TABLE `event_materials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL DEFAULT 'PDF',
    `file_size_kb` INT DEFAULT 0,
    `is_pre_read` TINYINT(1) DEFAULT 0,
    `download_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mat_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Event Gallery
CREATE TABLE `event_gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(255) NULL,
    `display_order` INT DEFAULT 1,
    CONSTRAINT `fk_gallery_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 13. Event Videos
CREATE TABLE `event_videos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `video_url` VARCHAR(500) NOT NULL,
    `video_source` ENUM('youtube', 'vimeo', 'internal') DEFAULT 'youtube',
    `duration` VARCHAR(30) NULL,
    CONSTRAINT `fk_video_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 14. Event Links
CREATE TABLE `event_links` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `link_type` VARCHAR(50) DEFAULT 'github',
    CONSTRAINT `fk_link_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 15. Reviews & Ratings
CREATE TABLE `feedback` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `feedback_text` TEXT NULL,
    `suggestions` TEXT NULL,
    `is_approved` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_fb_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 16. CMS Banners (1920x500 Clean Graphics)
CREATE TABLE `cms_banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `subtitle` VARCHAR(255) NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `cta_text` VARCHAR(60) DEFAULT 'Book Your Seat',
    `event_id` INT NULL,
    `target_url` VARCHAR(255) NULL,
    `badge_text` VARCHAR(50) DEFAULT 'FEATURED WEBINAR',
    `display_order` INT DEFAULT 1,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 17. CMS Pages
CREATE TABLE `cms_pages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `title` VARCHAR(200) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 18. Waitlists
CREATE TABLE `waitlists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `position` INT NOT NULL DEFAULT 1,
    `status` ENUM('waiting', 'promoted', 'expired') DEFAULT 'waiting',
    `notified_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_wl_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 19. Favorites
CREATE TABLE `favorites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_fav` (`event_id`, `user_id`),
    CONSTRAINT `fk_fav_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SEED DATA
-- Admin credentials: User 'admin', Password 'admin'
-- Hash: $2y$10$mnKW1KgMLZLo.6EBaYQDb.Ha2iuAR.MUT9Pd0aBz.OcGR1FAexNu6
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `mobile`, `company`, `designation`, `password_hash`) VALUES
(1, 1, 'Admin', 'admin@runitevents.com', '+91 98765 43210', 'Runit Events Hub', 'Administrator', '$2y$10$lhx2IGcHXxw2aW.bOixGJ.Vqsu8bRjfk.1ECD6qlwAlowi5yrrX0e'),
(2, 2, 'Dr. Sarah Mitchell', 'sarah.speaker@eventhub.com', '+91 98765 11111', 'Anthropic Labs', 'Principal AI Researcher', '$2y$10$lhx2IGcHXxw2aW.bOixGJ.Vqsu8bRjfk.1ECD6qlwAlowi5yrrX0e'),
(3, 2, 'Vikram Malhotra', 'vikram.speaker@eventhub.com', '+91 98765 22222', 'CloudScale Architectures', 'Chief Cloud Evangelist', '$2y$10$lhx2IGcHXxw2aW.bOixGJ.Vqsu8bRjfk.1ECD6qlwAlowi5yrrX0e');

-- Events Seed
INSERT INTO `events` (`id`, `title`, `event_code`, `category_id`, `event_type`, `banner_image`, `short_summary`, `description`, `event_date`, `start_time`, `duration_minutes`, `capacity`, `booked_count`, `venue_type`, `venue_address`, `meeting_platform`, `meeting_link`, `is_certificate_eligible`, `is_featured`, `is_trending`, `price`, `status`, `created_by`) VALUES
(1, 'Generative AI & LLM Systems: From Zero to Production Masterclass', 'EVT-2026-AI01', 1, 'Webinar', 'assets/banners/banner_ai.jpg', 'Master the architecture of building enterprise-scale agentic AI workflows, vector search embeddings, and fine-tuned LLM microservices.', 'Join Dr. Sarah Mitchell for an immersive masterclass dissecting how real-world engineering teams scale GenAI architectures. We will cover Retrieval-Augmented Generation (RAG) at scale, evaluating hallucinations, quantization for edge inference, and secure guardrail design with interactive live demos.', '2026-11-15', '18:30:00', 90, 500, 342, 'online', 'Zoom Video Webinar', 'Zoom', 'https://zoom.us/j/98765432100', 1, 1, 1, 0.00, 'published', 1),
(2, 'Cloud-Native Kubernetes & Distributed Systems Security', 'EVT-2026-K8S', 3, 'Workshop', 'assets/banners/banner_cloud.jpg', 'A hands-on deep dive into zero-trust cluster configurations, eBPF observability, and automated GitOps pipelines.', 'This 2-hour technical hands-on workshop walks through hardening production Kubernetes clusters. Learn how to configure Cilium eBPF for lightning-fast container networking, implement SPIFFE/SPIRE for mutual mTLS authentication, and maintain zero-trust security postures.', '2026-11-20', '19:00:00', 120, 250, 180, 'online', 'Google Meet Live Stream', 'Google Meet', 'https://meet.google.com/abc-defg-hij', 1, 1, 0, 0.00, 'published', 1),
(3, 'FinTech Microservices Architecture & High-Frequency Transactions', 'EVT-2026-FIN03', 2, 'Corporate Training', 'assets/banners/banner_fintech.jpg', 'Architecting resilient transactional ledgers handling 50,000+ operations/sec with Kafka and distributed state machines.', 'In-depth enterprise architectural breakdown of building high-concurrency payment and trading engines. Explores two-phase commit alternatives, Saga orchestrations, event sourcing, idempotent APIs, and regulatory audit compliance.', '2026-11-28', '17:00:00', 90, 300, 215, 'online', 'Microsoft Teams Live', 'MS Teams', 'https://teams.microsoft.com/l/meetup-join/12345', 1, 0, 1, 0.00, 'published', 1),
(4, 'Complete Full-Stack Architecture Certification Summit 2026', 'EVT-2026-CERT04', 5, 'Certification Program', 'assets/banners/banner_cert.jpg', 'Official certification track covering system design, relational scaling, modern frontend caching, and distributed resilience.', 'Full-day certification boot-camp offering verified digital credentials. Complete practical real-world assessments and earn a verifiable industry credential with permanent QR verification.', '2026-12-05', '10:00:00', 240, 150, 138, 'online', 'Zoom Pro Virtual Stage', 'Zoom', 'https://zoom.us/j/99887766554', 1, 1, 1, 0.00, 'published', 1),
(5, 'Modern Web Performance: Core Web Vitals & Sub-Second Latency', 'EVT-2026-PERF05', 1, 'Webinar', 'assets/banners/banner_web.jpg', 'Proven strategies for slashing LCP, optimizing INP, and eliminating hydration bottlenecks in modern web applications.', 'Completed session recording and slide repository covering Chrome DevTools tracing, rendering pipeline optimization, and edge CDN rendering architectures.', '2026-10-10', '18:00:00', 60, 400, 400, 'online', 'Completed Webinar Recording', 'Zoom', 'https://youtube.com/watch?v=example1', 1, 0, 0, 0.00, 'completed', 1);

-- Speakers
INSERT INTO `event_speakers` (`event_id`, `user_id`, `name`, `title`, `company`, `bio`, `photo`, `linkedin_url`, `is_keynote`) VALUES
(1, 2, 'Dr. Sarah Mitchell', 'Principal AI Researcher', 'Anthropic Labs', 'Sarah has spent over a decade leading deep learning research teams focusing on generative transformer models.', 'assets/speakers/sarah.jpg', 'https://linkedin.com/in/sarahmitchell', 1),
(2, 3, 'Vikram Malhotra', 'Chief Cloud Architect', 'CloudScale Architectures', 'AWS Hero & CNCF Contributor with 15+ years experience building petabyte-scale distributed systems.', 'assets/speakers/vikram.jpg', 'https://linkedin.com/in/vikrammalhotra', 1);

-- Agendas
INSERT INTO `event_agendas` (`event_id`, `time_start`, `time_end`, `topic`, `speaker_name`, `description`, `display_order`) VALUES
(1, '18:30:00', '18:45:00', 'Introduction & State of GenAI Systems in 2026', 'Dr. Sarah Mitchell', 'Welcome address, macro trends in foundational models, and the evolution of agentic architectures.', 1),
(1, '18:45:00', '19:15:00', 'Building Production RAG Pipelines with Hybrid Search', 'Dr. Sarah Mitchell', 'Live coding session demonstrating dense vector search combined with BM25 lexical ranking and re-ranking.', 2),
(1, '19:15:00', '19:45:00', 'Quantization, Caching, and Mitigating Hallucinations', 'Dr. Sarah Mitchell', 'Operational techniques to keep latency under 200ms while retaining model accuracy.', 3),
(1, '19:45:00', '20:00:00', 'Interactive Audience Q&A and Resource Distribution', 'Dr. Sarah Mitchell', 'Live interactive question round and access to GitHub starter repositories.', 4);

-- Banners (1920x500 clean artwork)
INSERT INTO `cms_banners` (`id`, `title`, `subtitle`, `image_path`, `cta_text`, `event_id`, `target_url`, `badge_text`, `display_order`, `is_active`) VALUES
(1, 'Generative AI & LLM Systems: From Zero to Production', 'Masterclass', 'assets/banners/hero_ai.jpg', 'Book Seat', 1, 'index.php?page=event-detail&id=1', 'EXCLUSIVE', 1, 1),
(2, 'Cloud-Native Kubernetes & Distributed Security', 'Workshop', 'assets/banners/hero_k8s.jpg', 'Join Workshop', 2, 'index.php?page=event-detail&id=2', 'WORKSHOP', 2, 1),
(3, 'Full-Stack Architecture Certification Summit 2026', 'Certification', 'assets/banners/hero_cert.jpg', 'Enroll Now', 4, 'index.php?page=event-detail&id=4', 'CERTIFICATION', 3, 1);

-- Past Event Materials & Highlights
INSERT INTO `event_materials` (`event_id`, `title`, `file_path`, `file_type`, `file_size_kb`, `is_pre_read`, `download_count`) VALUES
(1, 'Pre-Event Reading: Vector Indexing & RAG Topologies.pdf', 'uploads/materials/rag_pre_read.pdf', 'PDF', 2450, 1, 128),
(5, 'Modern Web Performance 2026 - Master Presentation Slides.pdf', 'uploads/materials/web_perf_slides.pdf', 'PDF', 6800, 0, 312);

INSERT INTO `event_videos` (`event_id`, `title`, `video_url`, `video_source`, `duration`) VALUES
(5, 'Full Session Recording: Core Web Vitals Deep Dive', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', '58 mins');

INSERT INTO `event_links` (`event_id`, `title`, `url`, `link_type`) VALUES
(1, 'Official GitHub Workshop Starter Repo', 'https://github.com/runit-events/genai-masterclass-2026', 'github');
