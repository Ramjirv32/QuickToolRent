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
  
  $_SESSION['flash_success'] = 'Rental confirmed successfully! 🎉';
  
  // Redirect to My Rentals page with 2-second delay message
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
      @keyframes spin {
        to { transform: rotate(360deg); }
      }
      .spinner { animation: spin 1s linear infinite; }
      @keyframes fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .fade-in { animation: fade-in 0.5s ease-out; }
    </style>
  </head>
  <body class="bg-gradient-to-br from-orange-50 via-red-50 to-pink-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 text-center fade-in">
      <!-- Success Icon -->
      <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-check text-5xl text-green-600"></i>
      </div>
      
      <!-- Success Message -->
      <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Booking Successful!</h1>
      <p class="text-lg text-gray-600 mb-8">Your rental has been confirmed</p>
      
      <!-- Order Details -->
      <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 mb-8">
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-1">Order ID</p>
          <p class="text-2xl font-bold text-orange-600">#<?= str_pad($rental_id, 6, '0', STR_PAD_LEFT) ?></p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-gray-500">Amount</p>
            <p class="font-bold text-gray-900">₹<?= number_format($total, 2) ?></p>
          </div>
          <div>
            <p class="text-gray-500">ETA</p>
            <p class="font-bold text-gray-900"><?= $eta ?> mins</p>
          </div>
        </div>
      </div>
      
      <!-- Loading Indicator -->
      <div class="mb-6">
        <div class="inline-block spinner">
          <i class="fas fa-circle-notch text-3xl text-orange-500"></i>
        </div>
        <p class="text-gray-600 mt-3 font-semibold" id="countdown-text">Redirecting to your rentals in <span id="countdown">3</span> seconds...</p>
      </div>
      
      <!-- Manual Link -->
      <a href="<?= BASE_URL ?: '/' ?>my-rentals.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all">
        <i class="fas fa-box-open mr-2"></i>View My Rentals Now
      </a>
    </div>
    
    <script>
      let seconds = 3;
      const countdownEl = document.getElementById('countdown');
      
      const interval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        
        if (seconds <= 0) {
          clearInterval(interval);
          window.location.href = '<?= BASE_URL ?: '/' ?>my-rentals.php';
        }
      }, 1000);
    </script>
  </body>
  </html>
  <?php
  exit;
  
} catch (Exception $e) {
  $_SESSION['flash_error'] = 'Failed to complete rental. Please try again.';
  header('Location: ' . (BASE_URL ?: '/') . 'rent-product.php?id=' . $product_id); 
  exit;
}
