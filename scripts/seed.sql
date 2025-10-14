-- USE tool_rental;

INSERT INTO users (name, email, phone, password_hash, role) VALUES
('Alice Tools', 'alice.owner@example.com', '+1-202-555-0111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner'),
('Bob Builder', 'bob.owner@example.com', '+1-202-555-0122', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner'),
('Charlie Borrower', 'charlie.borrower@example.com', '+1-202-555-0133', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'borrower'),
('Dana DIY', 'dana.borrower@example.com', '+1-202-555-0144', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'borrower'),
('Site Admin', 'admin@example.com', NULL, '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin');

INSERT INTO products (owner_id, name, description, price_per_hour, price_per_day, image_url, status, category) VALUES
((SELECT id FROM users WHERE email='alice.owner@example.com'),
 'Cordless Drill',
 'Versatile cordless drill with two batteries. Great for quick jobs.',
 8.00, 35.00, NULL, 'available', 'Power Tools'),
((SELECT id FROM users WHERE email='alice.owner@example.com'),
 'Circular Saw',
 'Reliable 7-1/4\" circular saw, includes extra blade.',
 10.00, 45.00, NULL, 'available', 'Power Tools'),
((SELECT id FROM users WHERE email='bob.owner@example.com'),
 'Ladder 12ft',
 'Aluminum ladder suitable for indoor and outdoor tasks.',
 6.00, 25.00, NULL, 'available', 'Ladders');

-- sample rentals for charts (last 7 days)
INSERT INTO rentals (product_id, borrower_id, start_time, end_time, total_amount, payment_method, status, created_at) VALUES
((SELECT id FROM products WHERE name='Cordless Drill' LIMIT 1),(SELECT id FROM users WHERE role='borrower' LIMIT 1), DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY)+INTERVAL 2 HOUR, 16.00,'card','paid', DATE_SUB(NOW(), INTERVAL 6 DAY)),
((SELECT id FROM products WHERE name='Circular Saw' LIMIT 1),(SELECT id FROM users WHERE role='borrower' LIMIT 1), DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)+INTERVAL 4 HOUR, 40.00,'upi','paid', DATE_SUB(NOW(), INTERVAL 5 DAY)),
((SELECT id FROM products WHERE name='Ladder 12ft' LIMIT 1),(SELECT id FROM users WHERE role='borrower' LIMIT 1), DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)+INTERVAL 1 DAY, 25.00,'wallet','paid', DATE_SUB(NOW(), INTERVAL 3 DAY)),
((SELECT id FROM products WHERE name='Cordless Drill' LIMIT 1),(SELECT id FROM users WHERE role='borrower' LIMIT 1), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)+INTERVAL 1 DAY, 35.00,'card','delivered', DATE_SUB(NOW(), INTERVAL 1 DAY));
