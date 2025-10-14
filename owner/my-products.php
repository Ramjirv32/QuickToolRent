<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();
if (!is_owner()) { header('Location: ' . (BASE_URL ?: '/') . 'index.php'); exit; }

if (isset($_GET['toggle'])) {
  $pid = (int)$_GET['toggle'];
  $stmt = db()->prepare("UPDATE products SET status = IF(status='available','unavailable','available') WHERE id = ? AND owner_id = ?");
  $stmt->execute([$pid, $_SESSION['user']['id']]);
  header('Location: ' . (BASE_URL ?: '/') . 'owner/my-products.php'); exit;
}

if (isset($_GET['delete'])) {
  $pid = (int)$_GET['delete'];
  $stmt = db()->prepare("DELETE FROM products WHERE id = ? AND owner_id = ?");
  $stmt->execute([$pid, $_SESSION['user']['id']]);
  header('Location: ' . (BASE_URL ?: '/') . 'owner/my-products.php'); exit;
}

$stmt = db()->prepare("SELECT * FROM products WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user']['id']]);
$products = $stmt->fetchAll();
?>

<!-- Header Section -->
<section class="mb-12 bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 rounded-2xl shadow-2xl p-12 text-white">
  <div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row items-center justify-between">
      <div>
        <h1 class="text-5xl font-extrabold mb-4">
          <i class="fas fa-warehouse mr-3"></i>My Products
        </h1>
        <p class="text-xl text-white/90">Manage your tool inventory and earnings</p>
        <div class="mt-6 inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-6 py-3">
          <i class="fas fa-box mr-2"></i>
          <span class="font-semibold">You have <?= count($products) ?> product(s) listed</span>
        </div>
      </div>
      <a href="<?= BASE_URL ?: '/' ?>owner/add-product.php" class="mt-6 md:mt-0 inline-flex items-center px-8 py-4 bg-white text-orange-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all shadow-2xl transform hover:scale-105">
        <i class="fas fa-plus-circle mr-3 text-2xl"></i>Add New Product
      </a>
    </div>
  </div>
</section>

<?php if (empty($products)): ?>
  <!-- Empty State -->
  <div class="text-center py-20 bg-white rounded-2xl shadow-lg border-2 border-gray-100">
    <div class="mb-6">
      <i class="fas fa-toolbox text-8xl text-gray-300"></i>
    </div>
    <h2 class="text-3xl font-bold text-gray-900 mb-4">No Products Yet</h2>
    <p class="text-gray-600 text-lg mb-8">Start earning by listing your tools!</p>
    <a href="<?= BASE_URL ?: '/' ?>owner/add-product.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:scale-105">
      <i class="fas fa-plus-circle mr-3"></i>Add Your First Product
    </a>
  </div>
<?php else: ?>
  <!-- Products Grid -->
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    <?php foreach ($products as $p): ?>
      <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 group">
        <!-- Image -->
        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
          <?php if (!empty($p['image_url'])): ?>
            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
              <i class="fas fa-tools text-7xl text-gray-300"></i>
            </div>
          <?php endif; ?>
          
          <!-- Status Badge -->
          <div class="absolute top-3 right-3 <?= $p['status'] === 'available' ? 'bg-green-500' : 'bg-gray-500' ?> text-white px-4 py-2 rounded-full shadow-lg backdrop-blur-sm border-2 border-white">
            <i class="fas <?= $p['status'] === 'available' ? 'fa-check-circle' : 'fa-ban' ?> mr-1"></i>
            <span class="font-bold text-sm"><?= ucfirst($p['status']) ?></span>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-5">
          <!-- Category Badge -->
          <?php if (!empty($p['category'])): ?>
            <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full mb-3">
              <i class="fas fa-tag mr-1"></i>
              <?= htmlspecialchars($p['category']) ?>
            </span>
          <?php endif; ?>
          
          <h3 class="font-bold text-lg text-gray-900 mb-3 line-clamp-1">
            <?= htmlspecialchars($p['name']) ?>
          </h3>
          
          <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[40px]">
            <?= htmlspecialchars($p['description']) ?>
          </p>
          
          <!-- Pricing -->
          <div class="border-t-2 border-gray-100 pt-4 mb-4">
            <div class="flex items-baseline justify-between mb-2">
              <span class="text-gray-600 text-sm"><i class="fas fa-clock mr-1"></i>Per Hour</span>
              <span class="text-xl font-bold text-orange-600">₹<?= number_format((float)$p['price_per_hour'], 2) ?></span>
            </div>
            <div class="flex items-baseline justify-between">
              <span class="text-gray-600 text-sm"><i class="fas fa-calendar-day mr-1"></i>Per Day</span>
              <span class="text-xl font-bold text-orange-600">₹<?= number_format((float)$p['price_per_day'], 2) ?></span>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="flex gap-2">
            <a href="<?= BASE_URL ?: '/' ?>owner/my-products.php?toggle=<?= (int)$p['id'] ?>" 
               class="flex-1 text-center px-4 py-2.5 <?= $p['status'] === 'available' ? 'bg-gray-200 hover:bg-gray-300 text-gray-700' : 'bg-green-500 hover:bg-green-600 text-white' ?> font-semibold rounded-lg transition-all">
              <i class="fas <?= $p['status'] === 'available' ? 'fa-eye-slash' : 'fa-eye' ?> mr-1"></i>
              <?= $p['status'] === 'available' ? 'Hide' : 'Show' ?>
            </a>
            <a href="<?= BASE_URL ?: '/' ?>owner/my-products.php?delete=<?= (int)$p['id'] ?>" 
               onclick="return confirm('Are you sure you want to delete this product?')"
               class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-all">
              <i class="fas fa-trash"></i>
            </a>
          </div>
          
          <!-- Date Added -->
          <div class="mt-4 text-xs text-gray-500 text-center">
            <i class="fas fa-clock mr-1"></i>
            Added <?= date('M d, Y', strtotime($p['created_at'])) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Summary Cards -->
  <div class="mt-12 grid md:grid-cols-4 gap-6">
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-blue-100 text-sm mb-1">Total Products</p>
          <p class="text-3xl font-bold"><?= count($products) ?></p>
        </div>
        <i class="fas fa-box text-4xl opacity-30"></i>
      </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-green-100 text-sm mb-1">Available</p>
          <p class="text-3xl font-bold"><?= count(array_filter($products, fn($p) => $p['status'] === 'available')) ?></p>
        </div>
        <i class="fas fa-check-circle text-4xl opacity-30"></i>
      </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-orange-100 text-sm mb-1">Unavailable</p>
          <p class="text-3xl font-bold"><?= count(array_filter($products, fn($p) => $p['status'] === 'unavailable')) ?></p>
        </div>
        <i class="fas fa-ban text-4xl opacity-30"></i>
      </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-purple-100 text-sm mb-1">Categories</p>
          <p class="text-3xl font-bold"><?= count(array_unique(array_column($products, 'category'))) ?></p>
        </div>
        <i class="fas fa-tags text-4xl opacity-30"></i>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
