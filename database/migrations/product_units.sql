-- Product Units table (purchase / selling units)
-- Run this on your MySQL/MariaDB database if you prefer raw SQL over `php artisan migrate`

CREATE TABLE IF NOT EXISTS `product_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `type` enum('purchase','selling','both') NOT NULL DEFAULT 'both',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_units_value_unique` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default units (same options as before)
INSERT INTO `product_units` (`name`, `value`, `type`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
  ('Box', 'box', 'purchase', 1, 1, NOW(), NOW()),
  ('Pack', 'pack', 'purchase', 1, 2, NOW(), NOW()),
  ('Bottle', 'bottle', 'purchase', 1, 3, NOW(), NOW()),
  ('Piece', 'piece', 'both', 1, 4, NOW(), NOW()),
  ('Tablet', 'tablet', 'selling', 1, 5, NOW(), NOW()),
  ('Capsule', 'capsule', 'selling', 1, 6, NOW(), NOW()),
  ('ML', 'ml', 'selling', 1, 7, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `type` = VALUES(`type`),
  `is_active` = VALUES(`is_active`),
  `sort_order` = VALUES(`sort_order`),
  `updated_at` = NOW();
