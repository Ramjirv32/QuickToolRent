<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

verify_csrf();

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$product_id = (int)($_POST['product_id'] ?? 0);
$borrower_id = (int)($_POST['borrower_id'] ?? 0);
$mode = $_POST['mode'] ?? 'hour';
$quantity = max(1, (int)($_POST['quantity'] ?? 1));
$payment_method = $_POST['payment_method'] ?? 'card';
$address = trim($_POST['address'] ?? '');

try {
  if ($product_id <= 0 || $borrower_id <= 0 || $address === '') {
    throw new RuntimeException('Missing required fields.');
  }

  $pdo = db();
  $pdo->beginTransaction();

  // Fetch product and borrower
  $pstmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND status = 'available'");
  $pstmt->execute([':id' => $product_id]);
  $product = $pstmt->fetch();
  if (!$product) {
    throw new RuntimeException('Product not available.');
  }

  $bstmt = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'borrower'");
  $bstmt->execute([':id' => $borrower_id]);
  if (!$bstmt->fetch()) {
    throw new RuntimeException('Borrower not found.');
  }

  // Compute total
  $price_unit = ($mode === 'day') ? (float)$product['price_per_day'] : (float)$product['price_per_hour'];
  $total = $price_unit * $quantity;

  // Create rental
  $rstmt = $pdo->prepare("INSERT INTO rentals
    (product_id, borrower_id, mode, quantity, total_amount, status, address, payment_method, payment_status, created_at)
    VALUES (:pid, :bid, :mode, :qty, :total, 'pending', :addr, :pm, 'unpaid', NOW())");
  $rstmt->execute([
    ':pid' => $product_id,
    ':bid' => $borrower_id,
    ':mode' => $mode,
    ':qty' => $quantity,
    ':total' => $total,
    ':addr' => $address,
    ':pm' => $payment_method,
  ]);
  $rental_id = (int)$pdo->lastInsertId();

  // Payment stub: mark immediate success except COD
  $payment_status = ($payment_method === 'cod') ? 'pending' : 'paid';
  $pystmt = $pdo->prepare("INSERT INTO payments (rental_id, amount, method, status, created_at)
                           VALUES (:rid, :amt, :m, :s, NOW())");
  $pystmt->execute([
    ':rid' => $rental_id,
    ':amt' => $total,
    ':m' => $payment_method,
    ':s' => $payment_status,
  ]);

  // Update rental status based on payment
  $ustmt = $pdo->prepare("UPDATE rentals SET payment_status = :ps, status = :st WHERE id = :rid");
  $ustmt->execute([
    ':ps' => $payment_status,
    ':st' => ($payment_status === 'paid' ? 'confirmed' : 'pending'),
    ':rid' => $rental_id,
  ]);

  $pdo->commit();

  $_SESSION['flash_success'] = 'Rental confirmed. ' .
    ($payment_status === 'paid' ? 'Delivery in 30–60 minutes!' : 'Pay on delivery (30–60 minutes).');
  header('Location: /rental-success.php?id=' . $rental_id);
  exit;
} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  $_SESSION['flash_error'] = $e->getMessage();
  header('Location: /rent-product.php?id=' . $product_id);
  exit;
}
