<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();
require_once __DIR__ . '/includes/header.php';
$rid = (int)($_GET['rid'] ?? 0);
$uid = $_SESSION['user']['id'] ?? 0;
if ($rid > 0 && $uid > 0) {
  $stmt = db()->prepare("SELECT r.*, p.name, p.category FROM rentals r JOIN products p ON p.id = r.product_id WHERE r.id = ? AND r.borrower_id = ? LIMIT 1");
  $stmt->execute([$rid, $uid]);
  $rental = $stmt->fetch();
} else {
  $rental = null;
}
?>
<div class="min-h-[70vh] flex items-center justify-center py-12">
  <?php if ($rental): ?>
    <div class="max-w-2xl w-full">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-green-200">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12 text-center text-white">
          <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
            <i class="fas fa-check text-5xl text-green-500"></i>
          </div>
          <h1 class="text-4xl font-extrabold mb-3">Order Confirmed!</h1>
          <p class="text-xl text-green-50">Thank you for renting with us</p>
        </div>
        <div class="p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-4"><i class="fas fa-box-open text-orange-500 mr-2"></i>Order Details</h2>
          <div class="bg-gray-50 rounded-xl p-6 mb-4">
            <h3 class="text-lg font-bold text-gray-900"><?= e($rental['name']) ?></h3>
            <div class="text-3xl font-bold text-orange-600 mt-2">₹<?= number_format((float)$rental['total_amount'], 2) ?></div>
          </div>
          <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-6 border-2 border-orange-200 mb-6">
            <p class="text-gray-700 mb-2">Delivery ETA:</p>
            <div class="text-4xl font-extrabold text-orange-600"><?= (int)$rental['delivery_eta_minutes'] ?> Minutes</div>
          </div>
          <div class="flex gap-4">
            <a href="<?= BASE_URL ?: '/' ?>my-rentals.php" class="flex-1 text-center px-6 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl"><i class="fas fa-box mr-2"></i>My Rentals</a>
            <a href="<?= BASE_URL ?: '/' ?>index.php" class="flex-1 text-center px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl"><i class="fas fa-home mr-2"></i>Home</a>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="max-w-xl w-full text-center">
      <div class="bg-white rounded-2xl shadow-2xl p-12 border-2 border-red-200">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Order Not Found</h1>
        <a href="<?= BASE_URL ?: '/' ?>index.php" class="inline-block px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold rounded-xl"><i class="fas fa-search mr-2"></i>Browse Tools</a>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
