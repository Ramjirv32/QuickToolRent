<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$pdo = db();

// Get categories with count
$categories_stmt = $pdo->query("
  SELECT category, COUNT(*) as count 
  FROM products 
  WHERE status = 'available' 
  GROUP BY category
");
$categories = $categories_stmt->fetchAll();

// Simple search by name/description and category filter
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "SELECT p.*, u.name AS owner_name 
        FROM products p 
        JOIN users u ON u.id = p.owner_id
        WHERE p.status = 'available'";

$params = [];

// Category filter
if ($category !== '') {
  $sql .= " AND p.category = :category";
  $params[':category'] = $category;
}

// Search filter
if ($q !== '') {
  $sql .= " AND (p.name LIKE :q OR p.description LIKE :q)";
  $params[':q'] = '%' . $q . '%';
}

$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Category icons mapping
$category_icons = [
  'Power Tools' => 'fas fa-screwdriver',
  'Ladders' => 'fas fa-ladder',
  'Home Appliance' => 'fas fa-blender',
  'Electronics' => 'fas fa-laptop',
  'Laptops' => 'fas fa-laptop',
  'Computers' => 'fas fa-desktop',
  'Furniture' => 'fas fa-couch',
  'Medical Equipment' => 'fas fa-heartbeat',
  'Musical Instruments' => 'fas fa-guitar',
  'Kids Utilities' => 'fas fa-baby-carriage',
  'Fitness' => 'fas fa-dumbbell',
  'Sports Equipment' => 'fas fa-basketball-ball',
  'Generators' => 'fas fa-plug',
  'Vending Machine' => 'fas fa-store',
  'Machines & Tools' => 'fas fa-cog',
  'Camera & Lenses' => 'fas fa-camera',
  'General' => 'fas fa-tools',
];
?>

<!-- Hero Banner Section -->
<section class="mb-12 relative overflow-hidden rounded-2xl shadow-2xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
  <div class="absolute inset-0 opacity-10">
    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1600" alt="Tools Background" class="w-full h-full object-cover">
  </div>
  
  <div class="relative px-8 py-20 md:py-28 text-white">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
      <!-- Left Content -->
      <div>
        <div class="inline-block bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4 shadow-lg">
          <i class="fas fa-bolt mr-2"></i>Fast Delivery in 30-60 Minutes
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
          Rent Tools<br/>Anytime, Anywhere
        </h1>
        <p class="text-xl md:text-2xl mb-8 text-blue-100">
          Borrow what you need. Owners earn from unused tools. Get it delivered fast!
        </p>
        
        <!-- Search Bar -->
        <form method="get" action="<?= BASE_URL ?: '/' ?>index.php" class="mb-8">
          <div class="flex bg-white rounded-xl overflow-hidden shadow-2xl">
            <div class="flex-1 relative">
              <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              <input
                type="text"
                name="q"
                value="<?php echo htmlspecialchars($q); ?>"
                placeholder="Search for drills, saws, ladders..."
                class="w-full pl-12 pr-4 py-4 text-gray-900 focus:outline-none text-base"
              />
            </div>
            <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold px-8 transition-all duration-200">
              <i class="fas fa-search mr-2"></i>Search
            </button>
          </div>
        </form>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
          <div class="text-center">
            <div class="text-3xl font-bold"><?= count($products) ?>+</div>
            <div class="text-sm text-blue-100">Tools Available</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold">500+</div>
            <div class="text-sm text-blue-100">Happy Customers</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold">24/7</div>
            <div class="text-sm text-blue-100">Support</div>
          </div>
        </div>
      </div>

      <!-- Right Image -->
      <div class="hidden md:block">
        <img src="https://images.unsplash.com/photo-1572981779307-38b8cabb2407?w=800" alt="Tool Collection" class="rounded-2xl shadow-2xl">
      </div>
    </div>
  </div>
</section>

<!-- Top Rented Categories Section -->
<section class="mb-16">
  <div class="flex items-center justify-between mb-8">
    <div>
      <h2 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-fire text-orange-500 mr-3"></i>Top Rented Categories
      </h2>
      <p class="text-gray-600 mt-2">Browse our most popular tool categories</p>
    </div>
    <button class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-all">
      All Categories <i class="fas fa-arrow-right ml-2"></i>
    </button>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
    <?php 
    $display_categories = [
      ['name' => 'Power Tools', 'icon' => 'fas fa-screwdriver', 'image' => 'https://images.unsplash.com/photo-1572981779307-38b8cabb2407?w=400'],
      ['name' => 'Ladders', 'icon' => 'fas fa-ladder', 'image' => 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?w=400'],
      ['name' => 'Electronics', 'icon' => 'fas fa-laptop', 'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400'],
      ['name' => 'Furniture', 'icon' => 'fas fa-couch', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400'],
      ['name' => 'Medical Equipment', 'icon' => 'fas fa-heartbeat', 'image' => 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=400'],
      ['name' => 'Musical Instruments', 'icon' => 'fas fa-guitar', 'image' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400'],
      ['name' => 'Fitness & Sports', 'icon' => 'fas fa-dumbbell', 'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400'],
      ['name' => 'Generators', 'icon' => 'fas fa-plug', 'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=400'],
      ['name' => 'Machines & Tools', 'icon' => 'fas fa-cog', 'image' => 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?w=400'],
      ['name' => 'Camera & Lenses', 'icon' => 'fas fa-camera', 'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400'],
      ['name' => 'Construction Tools', 'icon' => 'fas fa-hard-hat', 'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?w=400'],
      ['name' => 'Garden Tools', 'icon' => 'fas fa-seedling', 'image' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400'],
    ];
    
    foreach ($display_categories as $cat): ?>
      <a href="<?= BASE_URL ?: '/' ?>category.php?category=<?= urlencode($cat['name']) ?>" class="category-card bg-white rounded-xl shadow-md hover:shadow-2xl overflow-hidden cursor-pointer border border-gray-100 block transition-all duration-300 transform hover:-translate-y-2">
        <div class="relative h-32 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
          <img src="<?= $cat['image'] ?>" alt="<?= $cat['name'] ?>" class="w-full h-full object-cover opacity-90">
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <div class="absolute bottom-3 left-3">
            <div class="w-12 h-12 bg-white rounded-lg shadow-lg flex items-center justify-center">
              <i class="<?= $cat['icon'] ?> text-2xl text-orange-500"></i>
            </div>
          </div>
        </div>
        <div class="p-4">
          <h3 class="font-bold text-gray-900 text-sm mb-1"><?= $cat['name'] ?></h3>
          <p class="text-xs text-gray-500">Explore tools</p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Featured Products Section -->
<section class="mb-16">
  <div class="flex items-center justify-between mb-8">
    <div>
      <h2 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-star text-yellow-500 mr-3"></i>
        <?php if ($category): ?>
          <?= htmlspecialchars($category) ?> Tools
        <?php elseif ($q): ?>
          Search Results for "<?= htmlspecialchars($q) ?>"
        <?php else: ?>
          Featured Tools for Rent
        <?php endif; ?>
      </h2>
      <p class="text-gray-600 mt-2">
        <i class="fas fa-tools text-orange-500 mr-2"></i><?= count($products) ?> tools ready to rent
      </p>
    </div>
    <?php if ($q || $category): ?>
      <a href="<?= BASE_URL ?: '/' ?>index.php" class="text-orange-600 hover:text-orange-700 font-semibold flex items-center">
        <i class="fas fa-times-circle mr-2"></i>Clear Filter
      </a>
    <?php endif; ?>
  </div>

  <?php if (empty($products)): ?>
    <div class="text-center py-20 bg-white rounded-2xl shadow-lg border border-gray-100">
      <i class="fas fa-search text-6xl text-gray-300 mb-6"></i>
      <h3 class="text-2xl font-bold text-gray-900 mb-3">No Tools Found</h3>
      <p class="text-gray-600 mb-8">Try adjusting your search or browse all available tools</p>
      <?php if ($q): ?>
        <a href="<?= BASE_URL ?: '/' ?>index.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-2xl">
          <i class="fas fa-th-large mr-3"></i>View All Tools
        </a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <?php foreach ($products as $p): 
        $category_icon = $category_icons[$p['category']] ?? 'fas fa-tools';
      ?>
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
            <?php if (!empty($p['category'])): ?>
              <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">
                  <i class="<?= $category_icon ?> mr-1"></i>
                  <?= htmlspecialchars($p['category']) ?>
                </span>
              </div>
            <?php endif; ?>
            
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
                <div class="text-xs text-gray-500">250 rents</div>
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

<!-- Features Section -->
<section class="mb-16 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-12">
  <div class="text-center mb-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-4">
      <i class="fas fa-medal text-yellow-500 mr-3"></i>Why Choose <?php echo APP_NAME; ?>?
    </h2>
    <p class="text-gray-600 text-lg">Fast, reliable, and convenient tool rental service</p>
  </div>
  
  <div class="grid md:grid-cols-4 gap-8">
    <div class="text-center group">
      <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform">
        <i class="fas fa-shipping-fast text-3xl text-white"></i>
      </div>
      <h3 class="font-bold text-lg mb-2">Fast Delivery</h3>
      <p class="text-gray-600 text-sm">Get tools delivered in 30-60 minutes</p>
    </div>
    
    <div class="text-center group">
      <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform">
        <i class="fas fa-shield-alt text-3xl text-white"></i>
      </div>
      <h3 class="font-bold text-lg mb-2">Secure Payment</h3>
      <p class="text-gray-600 text-sm">Multiple payment options available</p>
    </div>
    
    <div class="text-center group">
      <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform">
        <i class="fas fa-headset text-3xl text-white"></i>
      </div>
      <h3 class="font-bold text-lg mb-2">24/7 Support</h3>
      <p class="text-gray-600 text-sm">Round the clock customer service</p>
    </div>
    
    <div class="text-center group">
      <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 transition-transform">
        <i class="fas fa-tags text-3xl text-white"></i>
      </div>
      <h3 class="font-bold text-lg mb-2">Best Prices</h3>
      <p class="text-gray-600 text-sm">Competitive and fair pricing</p>
    </div>
  </div>
</section>

<!-- CTA Section -->
<?php if (!isset($_SESSION['user'])): ?>
<section class="mb-16 relative overflow-hidden rounded-2xl shadow-2xl">
  <div class="absolute inset-0 bg-gradient-to-r from-orange-600 via-red-600 to-pink-600"></div>
  <div class="absolute inset-0 opacity-20">
    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1600" alt="Background" class="w-full h-full object-cover">
  </div>
  
  <div class="relative px-8 py-16 text-center text-white">
    <div class="max-w-3xl mx-auto">
      <h2 class="text-4xl font-extrabold mb-4">
        <i class="fas fa-rocket mr-3"></i>Ready to Get Started?
      </h2>
      <p class="text-xl text-white/90 mb-10">
        Join thousands of users who rent and lend tools in their community
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="<?= BASE_URL ?: '/' ?>register.php" class="inline-flex items-center justify-center px-10 py-4 bg-white text-orange-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all duration-200 shadow-2xl hover:shadow-3xl transform hover:scale-105">
          <i class="fas fa-user-plus mr-3"></i>Sign Up Free
        </a>
        <a href="<?= BASE_URL ?: '/' ?>about.php" class="inline-flex items-center justify-center px-10 py-4 bg-transparent text-white font-bold text-lg rounded-xl hover:bg-white/10 transition-all duration-200 border-2 border-white shadow-lg">
          <i class="fas fa-info-circle mr-3"></i>Learn More
        </a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
