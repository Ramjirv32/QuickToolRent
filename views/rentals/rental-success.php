<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$st = $pdo->prepare("SELECT r.*, p.name AS product_name
                     FROM rentals r
                     JOIN products p ON p.id = r.product_id
                     WHERE r.id = :id");
$st->execute([':id' => $id]);
$r = $st->fetch();
?>
<section class="max-w-2xl">
  <?php if (!$r): ?>
    <div class="rounded border bg-white p-6 text-red-700 border-red-200">Rental not found.</div>
  <?php else: ?>
    <div class="rounded border bg-white p-6">
      <h1 class="text-xl font-semibold">Success!</h1>
      <p class="text-gray-700 mt-2">Your rental request for <span class="font-medium"><?php echo htmlspecialchars($r['product_name']); ?></span> is recorded.</p>
      <ul class="mt-4 text-sm text-gray-700 space-y-1">
        <li><span class="font-medium">Status:</span> <?php echo htmlspecialchars($r['status']); ?></li>
        <li><span class="font-medium">Payment:</span> <?php echo htmlspecialchars($r['payment_status']); ?> (<?php echo htmlspecialchars($r['payment_method']); ?>)</li>
        <li><span class="font-medium">Quantity:</span> <?php echo (int)$r['quantity']; ?> <?php echo $r['mode'] === 'day' ? 'day(s)' : 'hour(s)'; ?></li>
        <li><span class="font-medium">Total:</span> $<?php echo number_format((float)$r['total_amount'], 2); ?></li>
      </ul>
      <p class="mt-4 text-gray-600">Delivery ETA: 30–60 minutes after payment confirmation.</p>
      <a href="/index.php" class="inline-block mt-6 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Back to Browse</a>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
