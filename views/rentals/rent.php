<?php require_once __DIR__.'/includes/header.php'; ?>
<?php require_once __DIR__.'/includes/auth.php'; ?>
<?php require_once __DIR__.'/includes/db.php'; ?>
<?php require_once __DIR__.'/includes/helpers.php'; ?>
<?php require_once __DIR__.'/includes/csrf.php'; ?>
<?php
  require_login();
  $id = (int)($_GET['id'] ?? 0);
  $stmt = db()->prepare("SELECT * FROM products WHERE id = ? AND is_available = 1 LIMIT 1");
  $stmt->execute([$id]);
  $product = $stmt->fetch();
  if (!$product) {
    echo '<section class="max-w-6xl mx-auto px-4 py-12"><h1 class="text-2xl font-semibold">Product unavailable</h1></section>';
    require_once __DIR__.'/includes/footer.php';
    exit;
  }
?>
<section class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-semibold mb-6">Checkout</h1>
  <div class="border border-slate-200 rounded-lg overflow-hidden">
    <div class="p-4 border-b">
      <div class="font-medium"><?= e($product['title']) ?></div>
      <div class="text-sm text-slate-600"><?= money((float)$product['price_per_hour']) ?>/hr</div>
    </div>
    <form class="p-4 grid gap-4" method="post" action="<?= BASE_URL ?>/rent_confirm.php">
      <?= csrf_input(); ?>
      <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
      <label class="grid gap-1">
        <span class="text-sm font-medium">Start Time</span>
        <input required type="datetime-local" name="start_time" class="border border-slate-300 rounded-md px-3 py-2">
      </label>
      <label class="grid gap-1">
        <span class="text-sm font-medium">End Time</span>
        <input required type="datetime-local" name="end_time" class="border border-slate-300 rounded-md px-3 py-2">
      </label>
      <label class="grid gap-1">
        <span class="text-sm font-medium">Payment Method</span>
        <select name="payment_method" class="border border-slate-300 rounded-md px-3 py-2">
          <option value="card">Card</option>
          <option value="upi">UPI</option>
          <option value="wallet">Wallet</option>
          <option value="cod">Cash on Delivery</option>
        </select>
      </label>
      <button class="inline-flex items-center justify-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-md">Pay & Place Order</button>
      <p class="text-xs text-slate-500">Delivery ETA: 30–60 minutes after payment.</p>
    </form>
  </div>
</section>
<?php require_once __DIR__.'/includes/footer.php'; ?>
