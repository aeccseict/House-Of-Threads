CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (username, password)
VALUES ('admin', MD5('admin123'));


-- Add a sample admin:
INSERT INTO admins (name, email, password) VALUES (
  'Admin', 'admin@sareestyle.com',
  '<?php echo password_hash("admin123", PASSWORD_DEFAULT); ?>'
);
