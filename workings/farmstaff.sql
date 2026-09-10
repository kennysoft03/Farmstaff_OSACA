-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table farmstaff.admin_users
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.admin_users: ~0 rows (approximately)
REPLACE INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
	(1, 'admin', '$2y$10$0kBSt3JvwvPOHirN7G/lW./PW8mdFoDB6U2Uja65uUhh0Mq4X5iEC', 'admin@steavek.com', '2026-04-24 16:04:37');

-- Dumping structure for table farmstaff.fs_admin_users
CREATE TABLE IF NOT EXISTS `fs_admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `surname` varchar(100) NOT NULL,
  `othernames` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','moderator') NOT NULL DEFAULT 'admin',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_admin_users: ~1 rows (approximately)
REPLACE INTO `fs_admin_users` (`id`, `surname`, `othernames`, `email`, `username`, `password`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
	(1, 'System', 'Administrator', 'admin@farmstaff.ng', 'admin', '$2y$12$QKerZY5p7AfYt.XFp/JR4.KYIsfpyj5Ia2TdZ3MJP3sUY/jF7eCvi', 'superadmin', 1, '2026-09-04 08:30:15', '2026-09-03 17:06:48', '2026-09-04 09:30:15');

-- Dumping structure for table farmstaff.fs_attendance
CREATE TABLE IF NOT EXISTS `fs_attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `work_history_id` int DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','half-day','leave') NOT NULL DEFAULT 'present',
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `worker_id` (`worker_id`),
  KEY `employer_id` (`employer_id`),
  KEY `work_history_id` (`work_history_id`),
  CONSTRAINT `fk_att_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_attendance: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_audit_logs
CREATE TABLE IF NOT EXISTS `fs_audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `actor_type` enum('admin','employer','system') NOT NULL DEFAULT 'system',
  `actor_id` int NOT NULL,
  `actor_name` varchar(200) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `target_id` int DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  KEY `module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_audit_logs: ~4 rows (approximately)
REPLACE INTO `fs_audit_logs` (`id`, `actor_type`, `actor_id`, `actor_name`, `action`, `module`, `target_id`, `target_type`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
	(1, 'employer', 1, 'J-STOK Farms', 'register', 'auth', NULL, NULL, 'New employer registered', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0', '2026-09-03 19:33:01'),
	(2, 'admin', 1, 'System Administrator', 'login', 'auth', NULL, NULL, 'Admin logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0', '2026-09-03 19:41:34'),
	(3, 'admin', 1, 'System Administrator', 'login', 'auth', NULL, NULL, 'Admin logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0', '2026-09-03 19:54:53'),
	(4, 'employer', 1, 'J-STOK Farms', 'login', 'auth', NULL, NULL, 'Employer logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0', '2026-09-03 19:55:05'),
	(5, 'admin', 1, 'System Administrator', 'login', 'auth', NULL, NULL, 'Admin logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0', '2026-09-04 09:30:15');

-- Dumping structure for table farmstaff.fs_background_checks
CREATE TABLE IF NOT EXISTS `fs_background_checks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employer_id` int NOT NULL,
  `worker_id` int DEFAULT NULL,
  `search_query` varchar(100) NOT NULL COMMENT 'Phone or ID searched',
  `search_type` enum('phone','worker_id','name') NOT NULL DEFAULT 'phone',
  `result_found` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employer_id` (`employer_id`),
  KEY `worker_id` (`worker_id`),
  CONSTRAINT `fk_bc_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_background_checks: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_employers
CREATE TABLE IF NOT EXISTS `fs_employers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `farm_name` varchar(200) NOT NULL,
  `contact_person` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text,
  `lga` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT 'Ondo',
  `farm_type` varchar(100) DEFAULT NULL,
  `farm_size` varchar(100) DEFAULT NULL,
  `reg_number` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','suspended') NOT NULL DEFAULT 'pending',
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `reputation_score` decimal(3,2) DEFAULT '0.00',
  `total_ratings` int DEFAULT '0',
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_employers: ~0 rows (approximately)
REPLACE INTO `fs_employers` (`id`, `farm_name`, `contact_person`, `email`, `phone`, `password`, `address`, `lga`, `state`, `farm_type`, `farm_size`, `reg_number`, `logo`, `status`, `email_verified`, `reputation_score`, `total_ratings`, `verification_token`, `reset_token`, `reset_expires`, `created_at`, `updated_at`) VALUES
	(1, 'J-STOK Farms', 'Akinrinwa Kehinde', 'kennysoft03@yahoo.com', '8036 489 913', '$2y$10$GsgQv/5Idk1ZUzgiqTridOc3uF.474AlGoi.qPx0oFY4YyJJc.59C', 'Ibule Soro Akure', 'Ifedore', 'Ondo', 'Crop Farming', '3', '5345445', NULL, 'active', 1, 0.00, 0, 'd3afd1b03407679b4353b4e9c69dce2e7e62ce1f682fc7d9e8d7bacc83d19f4c', NULL, NULL, '2026-09-03 19:33:01', '2026-09-03 19:33:01');

-- Dumping structure for table farmstaff.fs_farm_ratings
CREATE TABLE IF NOT EXISTS `fs_farm_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employer_id` int NOT NULL,
  `worker_id` int NOT NULL,
  `work_history_id` int DEFAULT NULL,
  `overall_rating` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1-5',
  `working_conditions` tinyint(1) DEFAULT NULL,
  `safety` tinyint(1) DEFAULT NULL,
  `payment_promptness` tinyint(1) DEFAULT NULL,
  `treatment` tinyint(1) DEFAULT NULL,
  `review_text` text,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text,
  `reviewed_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employer_id` (`employer_id`),
  KEY `worker_id` (`worker_id`),
  CONSTRAINT `fk_rating_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rating_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_farm_ratings: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_incidents
CREATE TABLE IF NOT EXISTS `fs_incidents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `incident_type` enum('theft','misconduct','assault','insubordination','negligence','fraud','other') NOT NULL,
  `incident_date` date NOT NULL,
  `description` text NOT NULL,
  `evidence_file` varchar(255) DEFAULT NULL,
  `severity` enum('minor','moderate','severe') NOT NULL DEFAULT 'moderate',
  `status` enum('pending','reviewed','accepted','rejected','appealed') NOT NULL DEFAULT 'pending',
  `admin_notes` text,
  `reviewed_by` int DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `disciplinary_points` int DEFAULT '0' COMMENT 'Points deducted from trust score',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `worker_id` (`worker_id`),
  KEY `employer_id` (`employer_id`),
  CONSTRAINT `fk_inc_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inc_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_incidents: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_notifications
CREATE TABLE IF NOT EXISTS `fs_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recipient_type` enum('admin','employer') NOT NULL,
  `recipient_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `link` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_notifications: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_skills
CREATE TABLE IF NOT EXISTS `fs_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `skill_name` varchar(150) NOT NULL,
  `proficiency` enum('beginner','intermediate','skilled','expert') NOT NULL DEFAULT 'intermediate',
  `rating` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1-5 rating',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_by` int DEFAULT NULL COMMENT 'admin id',
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `worker_id` (`worker_id`),
  KEY `employer_id` (`employer_id`),
  CONSTRAINT `fk_skill_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_skill_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_skills: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_trust_score_log
CREATE TABLE IF NOT EXISTS `fs_trust_score_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` int NOT NULL,
  `old_score` decimal(4,2) NOT NULL,
  `new_score` decimal(4,2) NOT NULL,
  `change_amount` decimal(4,2) NOT NULL,
  `change_reason` varchar(255) NOT NULL,
  `reference_id` int DEFAULT NULL,
  `reference_type` enum('incident','rating','attendance','manual') NOT NULL DEFAULT 'manual',
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `worker_id` (`worker_id`),
  CONSTRAINT `fk_ts_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_trust_score_log: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_workers
CREATE TABLE IF NOT EXISTS `fs_workers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` varchar(20) NOT NULL COMMENT 'FSR-XXXXX unique registry ID',
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `othername` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `alt_phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text,
  `lga` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT 'Ondo',
  `photo` varchar(255) DEFAULT NULL,
  `id_type` enum('NIN','Voters Card','Drivers License','International Passport','Staff ID') DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `id_document` varchar(255) DEFAULT NULL,
  `skills` text COMMENT 'JSON array of skill tags',
  `trust_score` decimal(4,2) DEFAULT '50.00',
  `disciplinary_points` int DEFAULT '0',
  `status` enum('active','inactive','flagged','suspended') NOT NULL DEFAULT 'active',
  `registered_by` int DEFAULT NULL COMMENT 'employer id who registered',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `worker_id` (`worker_id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `registered_by` (`registered_by`),
  CONSTRAINT `fk_worker_employer` FOREIGN KEY (`registered_by`) REFERENCES `fs_employers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_workers: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_worker_ratings
CREATE TABLE IF NOT EXISTS `fs_worker_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `work_history_id` int DEFAULT NULL,
  `overall_rating` tinyint(1) NOT NULL DEFAULT '3',
  `work_quality` tinyint(1) DEFAULT NULL,
  `punctuality` tinyint(1) DEFAULT NULL,
  `teamwork` tinyint(1) DEFAULT NULL,
  `reliability` tinyint(1) DEFAULT NULL,
  `review_text` text,
  `would_rehire` tinyint(1) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `worker_id` (`worker_id`),
  KEY `employer_id` (`employer_id`),
  CONSTRAINT `fk_wr_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wr_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_worker_ratings: ~0 rows (approximately)

-- Dumping structure for table farmstaff.fs_work_history
CREATE TABLE IF NOT EXISTS `fs_work_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `worker_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `role` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `duration_days` int DEFAULT NULL,
  `responsibilities` text,
  `leaving_reason` varchar(255) DEFAULT NULL,
  `employer_remarks` text,
  `status` enum('active','completed','terminated') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `worker_id` (`worker_id`),
  KEY `employer_id` (`employer_id`),
  CONSTRAINT `fk_wh_employer` FOREIGN KEY (`employer_id`) REFERENCES `fs_employers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wh_worker` FOREIGN KEY (`worker_id`) REFERENCES `fs_workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table farmstaff.fs_work_history: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
