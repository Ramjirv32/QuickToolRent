<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$owners = $pdo->query("SELECT id, name, email FROM users WHERE role = 'owner' ORDER BY name ASC")->fetchAll();
?>
<section class="max-w-2xl">
  <h1 class="text-xl font-semibold">Add a Product</h1>
  <p class="text-gray-600 mt-1">Set your prices within our fair limits (max $<?php echo number_format(MAX_PRICE_PER_HOUR,2); ?> per hour, $<?php echo number_format(MAX_PRICE_PER_DAY,2); ?> per day).</p>

  <form action="/handle-add-product.php" method="post" class="mt-6 space-y-4 bg-white p-5 rounded border">
    <?php echo csrf_field(); ?>
    <div>
      <label class="block text-sm font-medium mb-1">Owner</label>
      <select name="owner_id" required class="w-full rounded border border-gray-300 px-3 py-2">
        <option value="">Select owner</option>
        <?php foreach ($owners as $o): ?>
          <option value="<?php echo (int)$o['id']; ?>">
            <?php echo htmlspecialchars($o['name'] . ' (' . $o['email'] . ')'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Product Name</label>
      <input type="text" name="name" required maxlength="100" class="w-full rounded border border-gray-300 px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Description</label>
      <textarea name="description" rows="4" maxlength="1000" class="w-full rounded border border-gray-300 px-3 py-2"></textarea>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Price per Hour ($)</label>
        <input type="number" name="price_per_hour" min="0" step="0.01" required class="w-full rounded border border-gray-300 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Price per Day ($)</label>
        <input type="number" name="price_per_day" min="0" step="0.01" required class="w-full rounded border border-gray-300 px-3 py-2" />
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Image URL (optional)</label>
      <input type="url" name="image_url" placeholder="https://example.com/tool.jpg" class="w-full rounded border border-gray-300 px-3 py-2" />
    </div>
    <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save Product</button>
  </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
