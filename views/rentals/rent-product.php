<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login(); // must run before output
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$st = $pdo->prepare("SELECT p.*, u.name AS owner_name FROM products p JOIN users u ON u.id = p.owner_id WHERE p.id = :id");
$st->execute([':id' => $id]);
$product = $st->fetch();

if (!$product) {
  echo '<div class="rounded border bg-white p-6 text-red-700 border-red-200">Product not found.</div>';
  require_once __DIR__ . '/includes/footer.php';
  exit;
}
?>
<section class="max-w-2xl">
  <div class="bg-white border rounded p-5">
    <div class="flex items-start gap-4">
      <div class="w-32 h-24 bg-gray-100 flex items-center justify-center">
        <?php if (!empty($product['image_url'])): ?>
          <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product" class="w-full h-full object-cover">
        <?php else: ?>
          <span class="text-gray-400 text-sm">Image</span>
        <?php endif; ?>
      </div>
      <div class="flex-1">
        <h1 class="text-xl font-semibold"><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($product['description']); ?></p>
        <p class="mt-2 text-sm text-gray-700">
          Owner: <span class="font-medium"><?php echo htmlspecialchars($product['owner_name']); ?></span>
        </p>
        <p class="mt-2">
          <span class="font-semibold text-blue-700">$<?php echo number_format((float)$product['price_per_hour'], 2); ?></span>
          <span class="text-gray-500">/hour</span>
          <span class="mx-2 text-gray-300">•</span>
          <span class="font-semibold text-blue-700">$<?php echo number_format((float)$product['price_per_day'], 2); ?></span>
          <span class="text-gray-500">/day</span>
        </p>
      </div>
    </div>

    <?php if (($product['status'] ?? 'available') !== 'available'): ?>
      <div class="mt-6 rounded border border-gray-200 bg-gray-50 p-4 text-gray-700">This product is currently unavailable.</div>
    <?php else: ?>
      <form action="rent-confirm.php" method="post" class="mt-6 space-y-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>" />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Start Time</label>
            <input type="datetime-local" name="start_time" required class="w-full rounded border border-gray-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">End Time</label>
            <input type="datetime-local" name="end_time" required class="w-full rounded border border-gray-300 px-3 py-2" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Payment Method</label>
          <select name="payment_method" class="w-full rounded border border-gray-300 px-3 py-2">
            <option value="card">Card</option>
            <option value="upi">UPI</option>
            <option value="wallet">Wallet</option>
            <option value="cod">Cash on Delivery</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Delivery Address</label>
          <textarea name="address" rows="3" class="w-full rounded border border-gray-300 px-3 py-2" placeholder="Street, City, ZIP"></textarea>
        </div>

        <p class="text-sm text-gray-600">Delivery ETA: 30–60 minutes after payment confirmation.</p>
        <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Pay & Place Order</button>
      </form>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
