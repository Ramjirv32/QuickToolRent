<?php require_once __DIR__.'/includes/header.php'; ?>
<?php require_once __DIR__.'/includes/helpers.php'; ?>
<section class="max-w-6xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-semibold mb-4">Browse Tools</h1>
  <form method="get" class="mb-6 grid gap-3 md:grid-cols-6">
    <input type="text" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search tools…" class="md:col-span-3 border border-slate-300 rounded-md px-3 py-2 w-full">
    <select name="sort" class="border border-slate-300 rounded-md px-3 py-2">
      <option value="">Sort by</option>
      <option value="price_asc" <?= (($_GET['sort'] ?? '')==='price_asc')?'selected':'' ?>>Price: Low to High</option>
      <option value="price_desc" <?= (($_GET['sort'] ?? '')==='price_desc')?'selected':'' ?>>Price: High to Low</option>
      <option value="newest" <?= (($_GET['sort'] ?? '')==='newest')?'selected':'' ?>>Newest</option>
    </select>
    <button class="md:col-span-2 inline-flex items-center justify-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-md">Apply</button>
  </form>

  <?php
    require_once __DIR__.'/includes/db.php';
    $q = trim($_GET['q'] ?? '');
    $sort = $_GET['sort'] ?? '';
    $sql = "SELECT id, title, image_url, price_per_hour FROM products WHERE is_available = 1";
    $params = [];
    if ($q !== '') {
      $sql .= " AND (title LIKE ? OR description LIKE ?)";
      $params[] = "%$q%";
      $params[] = "%$q%";
    }
    if ($sort === 'price_asc') $sql .= " ORDER BY price_per_hour ASC";
    elseif ($sort === 'price_desc') $sql .= " ORDER BY price_per_hour DESC";
    else $sql .= " ORDER BY created_at DESC";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
  ?>

  <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3">
    <?php foreach ($rows as $row): ?>
      <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$row['id'] ?>" class="block border border-slate-200 rounded-lg overflow-hidden hover:shadow-md transition">
        <div class="aspect-video bg-slate-100" style="background-image:url('<?= e($row['image_url'] ?: '') ?>'); background-size:cover; background-position:center;"></div>
        <div class="p-4">
          <div class="font-medium"><?= e($row['title']) ?></div>
          <div class="text-sm text-slate-600 mt-1"><?= money((float)$row['price_per_hour']) ?>/hr</div>
        </div>
      </a>
    <?php endforeach; ?>
    <?php if (empty($rows)): ?>
      <p class="text-slate-600">No tools found. Try adjusting your search.</p>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__.'/includes/footer.php'; ?>
