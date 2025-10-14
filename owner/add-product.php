<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_login();
if (!is_owner()) { header('Location: ' . (BASE_URL ?: '/') . 'index.php'); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
  $name = trim($_POST['name'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $img = trim($_POST['image_url'] ?? '');
  $pph = (float)($_POST['price_per_hour'] ?? 0);
  $ppd = (float)($_POST['price_per_day'] ?? 0);
  if ($name && $pph >= MIN_PRICE_PER_HOUR && $pph <= MAX_PRICE_PER_HOUR && $ppd >= MIN_PRICE_PER_DAY && $ppd <= MAX_PRICE_PER_DAY) {
    $stmt = db()->prepare("INSERT INTO products (owner_id,name,description,image_url,price_per_hour,price_per_day,status) VALUES (?,?,?,?,?,?, 'available')");
    $stmt->execute([$_SESSION['user']['id'], $name, $desc, $img, $pph, $ppd]);
    header('Location: ' . (BASE_URL ?: '/') . 'owner/my-products.php'); exit;
  } else {
    $err = 'Please provide valid name and prices within limits.';
  }
}
?>
<section class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-semibold mb-6">Add Product</h1>
  <?php if ($err): ?><div class="mb-4 text-sm text-red-600"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="grid gap-4 border border-gray-200 rounded-lg p-4">
    <?= csrf_input(); ?>
    <label class="grid gap-1">
      <span class="text-sm font-medium">Name</span>
      <input required type="text" name="name" class="border border-gray-300 rounded-md px-3 py-2">
    </label>
    <label class="grid gap-1">
      <span class="text-sm font-medium">Image URL</span>
      <input type="url" name="image_url" class="border border-gray-300 rounded-md px-3 py-2" placeholder="https://...">
    </label>
    <label class="grid gap-1">
      <span class="text-sm font-medium">Description</span>
      <textarea name="description" rows="4" class="border border-gray-300 rounded-md px-3 py-2"></textarea>
    </label>
    <div class="grid gap-4 md:grid-cols-2">
      <label class="grid gap-1">
        <span class="text-sm font-medium">Price per hour (<?= money(MIN_PRICE_PER_HOUR) ?>–<?= money(MAX_PRICE_PER_HOUR) ?>)</span>
        <input required step="0.5" min="<?= MIN_PRICE_PER_HOUR ?>" max="<?= MAX_PRICE_PER_HOUR ?>" type="number" name="price_per_hour" class="border border-gray-300 rounded-md px-3 py-2">
      </label>
      <label class="grid gap-1">
        <span class="text-sm font-medium">Price per day (<?= money(MIN_PRICE_PER_DAY) ?>–<?= money(MAX_PRICE_PER_DAY) ?>)</span>
        <input required step="1" min="<?= MIN_PRICE_PER_DAY ?>" max="<?= MAX_PRICE_PER_DAY ?>" type="number" name="price_per_day" class="border border-gray-300 rounded-md px-3 py-2">
      </label>
    </div>
    <button class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Save</button>
  </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
