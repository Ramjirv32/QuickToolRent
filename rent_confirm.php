<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/helpers.php';
require_once __DIR__.'/includes/csrf.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate()) {
  header('Location: '.BASE_URL.'/browse.php');
  exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$start = $_POST['start_time'] ?? '';
$end = $_POST['end_time'] ?? '';
$method = $_POST['payment_method'] ?? 'card';

// Fetch product
$stmt = db()->prepare("SELECT * FROM products WHERE id = ? AND is_available = 1 LIMIT 1");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
  header('Location: '.BASE_URL.'/browse.php');
  exit;
}

// Validate times
$start_dt = strtotime($start);
$end_dt = strtotime($end);
if (!$start_dt || !$end_dt || $end_dt <= $start_dt) {
  header('Location: '.BASE_URL.'/rent.php?id='.$product_id);
  exit;
}
// Calculate hours (min 1 hour)
$hours = max(1, ceil(($end_dt - $start_dt) / 3600));
$total = $hours * (float)$product['price_per_hour'];

$eta = delivery_eta();
$borrower_id = current_user()['id'];

// Create rental (mark as paid immediately for demo)
$insert = db()->prepare("INSERT INTO rentals (product_id, borrower_id, start_time, end_time, total_amount, payment_method, status, delivery_eta_minutes) VALUES (?,?,?,?,?,?, 'paid', ?)");
$insert->execute([$product_id, $borrower_id, date('Y-m-d H:i:s', $start_dt), date('Y-m-d H:i:s', $end_dt), $total, $method, $eta]);

// Optionally set product unavailable during rental (simple approach)
$update = db()->prepare("UPDATE products SET is_available = 0 WHERE id = ?");
$update->execute([$product_id]);

// Redirect to thank you page
header('Location: '.BASE_URL.'/thankyou.php?rid='.db()->lastInsertId());
exit;
