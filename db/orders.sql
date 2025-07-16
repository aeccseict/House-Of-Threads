CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100),
  email VARCHAR(100),
  phone VARCHAR(20),
  address TEXT,
  promo VARCHAR(50),
  total INT,
  items TEXT,
  ALTER TABLE orders ADD COLUMN status ENUM('Pending', 'Shipped', 'Cancelled') DEFAULT 'Pending';
   -- JSON stringified array of items
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
