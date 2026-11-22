-- ==============================================================
-- MAMBA VILLAGE NAIROBI - DATABASE SCHEMA DEFINITION
-- Fully normalized relational architecture for MySQL / MariaDB
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `mamba_village_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mamba_village_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `feedback`, `supplier_orders`, `tour_guides`, `group_inquiries`, `bookings`, `events`, `ticket_types`, `attractions`, `administrators`, `visitors`, `activity_logs`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. USERS TABLE
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('visitor','admin','finance','event_manager','guide','supplier','service_manager') NOT NULL DEFAULT 'visitor',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_login` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. VISITORS / CUSTOMERS PROFILE TABLE
CREATE TABLE IF NOT EXISTS `visitors` (
  `visitor_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `fname` VARCHAR(100) NOT NULL,
  `lname` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `id_number` VARCHAR(50) NULL,
  `country` VARCHAR(100) DEFAULT 'Kenya',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. ADMINISTRATORS PROFILE TABLE
CREATE TABLE IF NOT EXISTS `administrators` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `department` VARCHAR(100) DEFAULT 'Operations Management',
  `phone` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. ATTRACTIONS & EXHIBITIONS
CREATE TABLE IF NOT EXISTS `attractions` (
  `attraction_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `hours` VARCHAR(100) DEFAULT 'Daily: 9:00 AM – 6:00 PM',
  `badge` VARCHAR(50) DEFAULT 'Popular',
  `icon_class` VARCHAR(50) DEFAULT 'fa-solid fa-water',
  `is_featured` TINYINT(1) DEFAULT 1,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TICKET PRICING TIERS & ADD-ONS
CREATE TABLE IF NOT EXISTS `ticket_types` (
  `ticket_type_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `category` ENUM('citizen_adult','citizen_child','non_resident','student','addon') NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. EVENTS & SHOWS SCHEDULE
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_time` VARCHAR(20) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `keeper` VARCHAR(100) NOT NULL,
  `capacity` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `is_highlight` TINYINT(1) DEFAULT 0,
  `status` ENUM('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. BOOKINGS & E-TICKETS
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `booking_reference` VARCHAR(50) NOT NULL UNIQUE,
  `visit_date` DATE NOT NULL,
  `session_slot` VARCHAR(50) NOT NULL,
  `adults_qty` INT DEFAULT 1,
  `children_qty` INT DEFAULT 0,
  `non_resident_qty` INT DEFAULT 0,
  `student_qty` INT DEFAULT 0,
  `has_boat_ride` TINYINT(1) DEFAULT 0,
  `has_vip_platform` TINYINT(1) DEFAULT 0,
  `has_guide` TINYINT(1) DEFAULT 0,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'M-PESA STK Push',
  `payment_reference` VARCHAR(50) NOT NULL,
  `payment_status` ENUM('paid','pending','failed') DEFAULT 'paid',
  `gate_status` ENUM('confirmed','admitted','cancelled') DEFAULT 'confirmed',
  `admitted_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. INSTITUTIONAL & GROUP INQUIRIES
CREATE TABLE IF NOT EXISTS `group_inquiries` (
  `inquiry_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `organization_name` VARCHAR(200) NOT NULL,
  `headcount` INT NOT NULL,
  `tentative_date` DATE NOT NULL,
  `package_type` VARCHAR(100) DEFAULT 'Educational Biology Excursion',
  `notes` TEXT NULL,
  `status` ENUM('pending','approved','contacted','rejected') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. TOUR GUIDES ROSTER
CREATE TABLE IF NOT EXISTS `tour_guides` (
  `guide_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `specialty` VARCHAR(150) NOT NULL,
  `assigned_tour` VARCHAR(255) NULL,
  `status` ENUM('available','on_tour','off_duty') DEFAULT 'available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. SUPPLIERS & ORDERS
CREATE TABLE IF NOT EXISTS `supplier_orders` (
  `order_id` INT AUTO_INCREMENT PRIMARY KEY,
  `po_number` VARCHAR(50) NOT NULL UNIQUE,
  `supplier_name` VARCHAR(150) NOT NULL,
  `item_description` VARCHAR(255) NOT NULL,
  `quantity` VARCHAR(50) NOT NULL,
  `expected_date` DATE NOT NULL,
  `status` ENUM('pending','in_transit','delivered','cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. VISITOR FEEDBACK & REVIEWS
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `visitor_name` VARCHAR(100) NOT NULL,
  `rating` INT NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `comment` TEXT NOT NULL,
  `status` ENUM('approved','pending','hidden') DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. AUDIT LOGS
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
