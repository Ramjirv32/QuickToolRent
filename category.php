<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/header.php';

$pdo = db();

// Get category from URL
$category = trim($_GET['category'] ?? '');

// Get all products in this category
if ($category) {
  $stmt = $pdo->prepare("
    SELECT p.*, u.name AS owner_name 
    FROM products p 
    JOIN users u ON u.id = p.owner_id
    WHERE p.status = 'available' AND p.category = :category
    ORDER BY p.created_at DESC
  ");
  $stmt->execute([':category' => $category]);
  $products = $stmt->fetchAll();
} else {
  // If no category, redirect to home
  header('Location: ' . (BASE_URL ?: '/') . 'index.php');
  exit;
}

// Category icons mapping
$category_icons = [
  'Power Tools' => 'fas fa-screwdriver',
  'Ladders' => 'fas fa-ladder',
  'Electronics' => 'fas fa-laptop',
  'Furniture' => 'fas fa-couch',
  'Medical Equipment' => 'fas fa-heartbeat',
  'Musical Instruments' => 'fas fa-guitar',
  'Fitness & Sports' => 'fas fa-dumbbell',
  'Generators' => 'fas fa-plug',
  'Machines & Tools' => 'fas fa-cog',
  'Camera & Lenses' => 'fas fa-camera',
  'Garden Tools' => 'fas fa-seedling',
  'Construction Tools' => 'fas fa-hard-hat',
];

$category_icon = $category_icons[$category] ?? 'fas fa-tools';
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-600 rounded-2xl shadow-2xl p-12 mb-12 text-white">
  <div class="max-w-4xl mx-auto text-center">
    <div class="inline-block mb-4">
      <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
        <i class="<?= $category_icon ?> text-4xl text-orange-600"></i>
      </div>
    </div>
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4"><?= htmlspecialchars($category) ?></h1>
    <p class="text-xl text-white/90 mb-6">Browse and rent professional <?= strtolower(htmlspecialchars($category)) ?> for your needs</p>
    <div class="flex items-center justify-center gap-6">
      <div class="bg-white/20 backdrop-blur-sm rounded-lg px-6 py-3">
        <div class="text-3xl font-bold"><?= count($products) ?></div>
        <div class="text-sm text-white/80">Available Tools</div>
      </div>
      <div class="bg-white/20 backdrop-blur-sm rounded-lg px-6 py-3">
        <div class="text-3xl font-bold">30-60</div>
        <div class="text-sm text-white/80">Min Delivery</div>
      </div>
    </div>
  </div>
</div>

<!-- Breadcrumb -->
<nav class="flex items-center text-sm text-gray-600 mb-8">
  <a href="<?= BASE_URL ?: '/' ?>index.php" class="hover:text-orange-600 flex items-center">
    <i class="fas fa-home mr-2"></i>Home
  </a>
  <i class="fas fa-chevron-right mx-3 text-gray-400"></i>
  <span class="text-gray-900 font-semibold"><?= htmlspecialchars($category) ?></span>
</nav>

<!-- Products Grid -->
<section class="mb-16">
  <?php if (empty($products)): ?>
    <div class="text-center py-20 bg-white rounded-2xl shadow-lg border border-gray-100">
      <i class="fas fa-box-open text-6xl text-gray-300 mb-6"></i>
      <h3 class="text-2xl font-bold text-gray-900 mb-3">No Tools Available in This Category</h3>
      <p class="text-gray-600 mb-8">Check back later or browse other categories</p>
      <a href="<?= BASE_URL ?: '/' ?>index.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-2xl">
        <i class="fas fa-th-large mr-3"></i>Browse All Categories
      </a>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <?php foreach ($products as $p): ?>
        <article class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-orange-300">
          <!-- Image -->
          <div class="relative h-56 bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
            <?php if (!empty($p['image_url'])): ?>
              <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            <?php else: ?>
              <div class="w-full h-full flex items-center justify-center">
                <i class="<?= $category_icon ?> text-8xl text-gray-300"></i>
              </div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center">
              <i class="fas fa-check-circle mr-1"></i> Available
            </div>
            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
              <i class="fas fa-fire text-orange-500 mr-1"></i> Popular
            </div>
          </div>
          
          <!-- Content -->
          <div class="p-5">
            <!-- Category Badge -->
            <div class="flex items-center gap-2 mb-3">
              <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">
                <i class="<?= $category_icon ?> mr-1"></i>
                <?= htmlspecialchars($p['category']) ?>
              </span>
            </div>
            
            <h3 class="font-bold text-lg text-gray-900 mb-2 group-hover:text-orange-600 transition-colors line-clamp-1">
              <?= htmlspecialchars($p['name']) ?>
            </h3>
            
            <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[40px]">
              <?= htmlspecialchars($p['description']) ?>
            </p>
            
            <div class="flex items-center text-sm text-gray-500 mb-4 pb-4 border-b border-gray-100">
              <i class="fas fa-user-circle text-blue-500 mr-2"></i>
              <span class="font-medium"><?= htmlspecialchars($p['owner_name']) ?></span>
            </div>
            
            <!-- Pricing -->
            <div class="flex items-center justify-between mb-4">
              <div>
                <div class="flex items-baseline gap-1 mb-1">
                  <span class="text-2xl font-bold text-orange-600">₹<?= number_format((float)$p['price_per_hour'], 0) ?></span>
                  <span class="text-sm text-gray-500 font-medium">/hr</span>
                </div>
                <div class="text-xs text-gray-500 flex items-center">
                  <i class="fas fa-calendar-day mr-1"></i>
                  ₹<?= number_format((float)$p['price_per_day'], 0) ?>/day
                </div>
              </div>
              <div class="text-right">
                <div class="flex items-center text-yellow-500 text-sm mb-1">
                  <i class="fas fa-star mr-1"></i>
                  <span class="font-semibold">4.8</span>
                </div>
                <div class="text-xs text-gray-500"><?= rand(50, 500) ?> rents</div>
              </div>
            </div>
            
            <!-- Rent Button -->
            <a href="<?= BASE_URL ?: '/' ?>rent-product.php?id=<?= (int)$p['id'] ?>"
               class="block w-full text-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl transition-all duration-200 shadow-md hover:shadow-xl transform hover:-translate-y-1">
              <i class="fas fa-shopping-cart mr-2"></i>Rent Now
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Related Categories -->
<section class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-12 mb-12">
  <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
    <i class="fas fa-th-large text-orange-500 mr-2"></i>Explore Other Categories
  </h2>
  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    <?php
    $all_categories = [
      ['name' => 'Power Tools', 'icon' => 'fas fa-screwdriver'],
      ['name' => 'Ladders', 'icon' => 'fas fa-ladder'],
      ['name' => 'Electronics', 'icon' => 'fas fa-laptop'],
      ['name' => 'Furniture', 'icon' => 'fas fa-couch'],
      ['name' => 'Garden Tools', 'icon' => 'fas fa-seedling'],
      ['name' => 'Camera & Lenses', 'icon' => 'fas fa-camera'],
    ];
    
    foreach ($all_categories as $cat):
      if ($cat['name'] === $category) continue;
    ?>
      <a href="<?= BASE_URL ?: '/' ?>category.php?category=<?= urlencode($cat['name']) ?>" 
         class="bg-white rounded-xl p-4 text-center hover:shadow-xl transition-all duration-200 border-2 border-gray-100 hover:border-orange-300 group">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-orange-500 transition-colors">
          <i class="<?= $cat['icon'] ?> text-2xl text-orange-600 group-hover:text-white transition-colors"></i>
        </div>
        <h3 class="font-bold text-gray-900 text-sm group-hover:text-orange-600"><?= $cat['name'] ?></h3>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
