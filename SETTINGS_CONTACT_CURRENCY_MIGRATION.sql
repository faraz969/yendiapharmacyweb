-- Settings Contact and Currency Migration SQL
-- Run this SQL on your live database to add the new settings fields

INSERT INTO `settings` (`key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
('contact_phone', '+1 800 900', 'text', 'Contact phone number displayed in topbar', NOW(), NOW()),
('contact_email', 'info@pharmacystore.com', 'email', 'Contact email address', NOW(), NOW()),
('topbar_tagline', 'Super Value Deals - Save more with coupons', 'text', 'Tagline displayed in top utility bar', NOW(), NOW()),
('currency', 'USD', 'text', 'Currency code (USD, NGN, EUR, etc.)', NOW(), NOW()),
('currency_symbol', '$', 'text', 'Currency symbol ($, ₦, €, etc.)', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

