-- Create database (run this once in MySQL or phpMyAdmin)
CREATE DATABASE IF NOT EXISTS `recaptcha_login` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `recaptcha_login`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- You can insert users via seed_users.php to ensure proper password hashing.
