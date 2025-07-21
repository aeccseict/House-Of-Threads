-- DROP existing admin table if needed and recreate
DROP TABLE IF EXISTS admin;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert test admin user
INSERT INTO admin (email, password) VALUES ('admin@gmail.com', MD5('admin123'));
