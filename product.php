<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT p.*, u.name AS owner_name FROM products p JOIN users u ON u.id = p.owner_id WHERE p.id = ? LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch();
?>
<section class="max-w-6xl mx-auto px-4 py-10">
  <?php if (!$product): ?>
    <h1 class="text-2xl font-semibold">Product not found</h1>
  <?php else: ?>
    <div class="grid gap-8 md:grid-cols-2">
      <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
        <span class="text-gray-400">Image</span>
      </div>
      <div>
        <h1 class="text-2xl font-semibold"><?= e($product['name']) ?></h1>
        <p class="text-gray-600 mt-1">Owner: <?= e($product['owner_name']) ?></p>
        <div class="mt-3">
          <span class="font-semibold text-blue-700"><?= money((float)$product['price_per_hour']) ?></span><span class="text-gray-600">/hour</span>
          <span class="mx-2 text-gray-300">•</span>
          <span class="font-semibold text-blue-700"><?= money((float)$product['price_per_day']) ?></span><span class="text-gray-600">/day</span>
        </div>
        <p class="mt-4 text-gray-700 whitespace-pre-line"><?= e($product['description'] ?? '') ?></p>
        <?php if ($product['status'] === 'available'): ?>
          <a href="<?= BASE_URL ?: '/' ?>rent-product.php?id=<?= (int)$product['id'] ?>" class="inline-flex items-center px-4 py-2 mt-6 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md">Rent Now</a>
        <?php else: ?>
          <span class="inline-flex items-center px-4 py-2 mt-6 bg-gray-200 text-gray-600 rounded-md">Unavailable</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
