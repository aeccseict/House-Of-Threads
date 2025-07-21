CREATE TABLE promo_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  discount_percentage INT NOT NULL,
  expires_at DATETIME,
  usage_limit INT DEFAULT 1,
  used_count INT DEFAULT 0
);
