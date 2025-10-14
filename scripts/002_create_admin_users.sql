/*
  Create a dedicated admin_users table for the admin panel.
  - Does not modify existing users table.
  - Password strategy: prefer bcrypt in password_hash; use password_sha256 as a fallback.
  - Idempotent: uses IF NOT EXISTS where possible.
*/
CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL DEFAULT 'Administrator',
  role ENUM('admin','manager') NOT NULL DEFAULT 'admin',
  password_hash VARCHAR(255) NULL,
  password_sha256 CHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
