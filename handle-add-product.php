<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

verify_csrf();

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$owner_id = (int)($_POST['owner_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price_per_hour = (float)($_POST['price_per_hour'] ?? 0);
$price_per_day = (float)($_POST['price_per_day'] ?? 0);
$image_url = trim($_POST['image_url'] ?? '');

try {
  if ($owner_id <= 0 || $name === '' || $price_per_hour < 0 || $price_per_day < 0) {
    throw new RuntimeException('Please fill all required fields correctly.');
  }
  if ($price_per_hour > MAX_PRICE_PER_HOUR) {
    throw new RuntimeException('Price per hour exceeds the fair limit of $' . number_format(MAX_PRICE_PER_HOUR, 2));
  }
  if ($price_per_day > MAX_PRICE_PER_DAY) {
    throw new RuntimeException('Price per day exceeds the fair limit of $' . number_format(MAX_PRICE_PER_DAY, 2));
  }

  $pdo = db();

  // Ensure owner exists and is role=owner
  $st = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'owner'");
  $st->execute([':id' => $owner_id]);
  if (!$st->fetch()) {
    throw new RuntimeException('Selected owner not found.');
  }

  $sql = "INSERT INTO products (owner_id, name, description, price_per_hour, price_per_day, image_url, status, created_at)
          VALUES (:owner_id, :name, :description, :pph, :ppd, :img, 'available', NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':owner_id' => $owner_id,
    ':name' => $name,
    ':description' => $description,
    ':pph' => $price_per_hour,
    ':ppd' => $price_per_day,
    ':img' => ($image_url !== '' ? $image_url : null),
  ]);

  $_SESSION['flash_success'] = 'Product added successfully.';
  header('Location: /add-product.php');
  exit;
} catch (Throwable $e) {
  $_SESSION['flash_error'] = $e->getMessage();
  header('Location: /add-product.php');
  exit;
}
