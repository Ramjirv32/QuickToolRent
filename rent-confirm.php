<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate()) {
  header('Location: ' . (BASE_URL ?: '/') . 'index.php'); 
  exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$start = $_POST['start_time'] ?? '';
$end = $_POST['end_time'] ?? '';
$method = $_POST['payment_method'] ?? 'card';

// Get product
$stmt = db()->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) { 
  $_SESSION['flash_error'] = 'Product not found.';
  header('Location: ' . (BASE_URL ?: '/') . 'index.php'); 
  exit; 
}

// Validate dates
$start_dt = strtotime($start);
$end_dt = strtotime($end);

if (!$start_dt || !$end_dt || $end_dt <= $start_dt) {
  $_SESSION['flash_error'] = 'Invalid rental dates. End time must be after start time.';
  header('Location: ' . (BASE_URL ?: '/') . 'rent-product.php?id=' . $product_id); 
  exit;
}

// Calculate rental duration and cost
$hours = max(1, ceil(($end_dt - $start_dt) / 3600));
$total = $hours * (float)$product['price_per_hour'];

// Random ETA 30–60 minutes
$eta = rand(30, 60);
$borrower_id = current_user()['id'];

// Insert rental record
try {
  $insert = db()->prepare("INSERT INTO rentals (product_id, borrower_id, start_time, end_time, total_amount, payment_method, status, delivery_eta_minutes) VALUES (?,?,?,?,?,?, 'paid', ?)");
  $insert->execute([
    $product_id, 
    $borrower_id, 
    date('Y-m-d H:i:s', $start_dt), 
    date('Y-m-d H:i:s', $end_dt), 
    $total, 
    $method, 
    $eta
  ]);
  
  $rental_id = db()->lastInsertId();
  
  // Mark product unavailable (simple demo)
  db()->prepare("UPDATE products SET status = 'unavailable' WHERE id = ?")->execute([$product_id]);
  
  $_SESSION['flash_success'] = 'Rental confirmed successfully!';
  header('Location: ' . (BASE_URL ?: '/') . 'thankyou.php?rid=' . $rental_id);
  exit;
  
} catch (Exception $e) {
  $_SESSION['flash_error'] = 'Failed to complete rental. Please try again.';
  header('Location: ' . (BASE_URL ?: '/') . 'rent-product.php?id=' . $product_id); 
  exit;
}
