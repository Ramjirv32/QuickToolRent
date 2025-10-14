/*
  Seed a default admin user.
  - Login: admin@example.com
  - Password: admin123 (stored as SHA-256 for the initial seed)
  - You can later migrate to a bcrypt hash by updating password_hash via PHP.
*/
INSERT INTO admin_users (email, name, role, password_hash, password_sha256)
VALUES
('admin@example.com', 'Site Administrator', 'admin', NULL, SHA2('admin123', 256))
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  role = VALUES(role),
  password_hash = VALUES(password_hash),
  password_sha256 = VALUES(password_sha256);
