-- Settings Migration SQL
-- Run this SQL on your live database

-- Create settings table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
('header_logo', NULL, 'image', 'Logo displayed in header', NOW(), NOW()),
('footer_logo', NULL, 'image', 'Logo displayed in footer', NOW(), NOW()),
('copyright_year', '2025', 'text', 'Year displayed in copyright footer', NOW(), NOW()),
('navbar_categories', '[]', 'json', 'Category IDs to display in navbar', NOW(), NOW()),
('app_store_url', NULL, 'url', 'Apple App Store URL', NOW(), NOW()),
('play_store_url', NULL, 'url', 'Google Play Store URL', NOW(), NOW());

