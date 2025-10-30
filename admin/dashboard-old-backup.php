<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();
require_role('admin');

$pdo = db();

// Get statistics
$totalUsers = (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
$totalProducts = (int)$pdo->query("SELECT COUNT(*) AS c FROM products")->fetch()['c'];
$totalRentals = (int)$pdo->query("SELECT COUNT(*) AS c FROM rentals")->fetch()['c'];
$activeRentals = (int)$pdo->query("SELECT COUNT(*) AS c FROM rentals WHERE status IN ('paid', 'delivered')")->fetch()['c'];
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM rentals WHERE status IN ('paid','delivered','completed')")->fetch()['s'];
$todayRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM rentals WHERE DATE(created_at) = CURDATE() AND status IN ('paid','delivered','completed')")->fetch()['s'];

// Recent activity
$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentRentals = $pdo->query("
  SELECT r.*, u.name as user_name, p.name as product_name 
  FROM rentals r 
  JOIN users u ON u.id = r.borrower_id 
  JOIN products p ON p.id = r.product_id 
  ORDER BY r.created_at DESC 
  LIMIT 5
")->fetchAll();

// Products by category
$categoryStats = $pdo->query("
  SELECT category, COUNT(*) as count, 
         SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_count
  FROM products 
  GROUP BY category 
  ORDER BY count DESC
  LIMIT 5
")->fetchAll();

// Top products by revenue
$topProducts = $pdo->query("
  SELECT p.name, COALESCE(SUM(r.total_amount), 0) as revenue
  FROM products p
  LEFT JOIN rentals r ON r.product_id = p.id AND r.status IN ('paid','delivered','completed')
  GROUP BY p.id, p.name
  ORDER BY revenue DESC
  LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .sidebar-link { transition: all 0.2s; }
    .sidebar-link:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); }
    .sidebar-link.active { background: rgba(255,255,255,0.15); border-left: 4px solid #fff; }
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); }
  </style>
</head>
<body class="bg-gray-100">
  
<!-- Sidebar Layout -->
<div class="flex min-h-screen">
  <!-- Header -->
  <section class="mb-12 bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 rounded-2xl shadow-2xl p-8 md:p-12 text-white">
    <div class="max-w-6xl mx-auto">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-5xl font-extrabold mb-4">
            <i class="fas fa-shield-alt mr-3"></i>Admin Dashboard
          </h1>
          <p class="text-xl text-white/90">Complete control and management of QuickToolRent</p>
        </div>
        <a href="<?= BASE_URL ?: '/' ?>admin/logout.php" class="px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl font-bold transition-all">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </div>
      
      <!-- Quick Stats -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-8">
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
          <div class="text-3xl font-bold mb-1"><?= $totalUsers ?></div>
          <div class="text-sm text-white/80">Total Users</div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
          <div class="text-3xl font-bold mb-1"><?= $totalProducts ?></div>
          <div class="text-sm text-white/80">Products</div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
          <div class="text-3xl font-bold mb-1"><?= $totalRentals ?></div>
          <div class="text-sm text-white/80">Total Rentals</div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
          <div class="text-3xl font-bold mb-1"><?= $activeRentals ?></div>
          <div class="text-sm text-white/80">Active Now</div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
          <div class="text-3xl font-bold mb-1">₹<?= number_format($totalRevenue, 0) ?></div>
          <div class="text-sm text-white/80">Revenue</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CRUD Management Cards -->
  <section class="mb-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">
      <i class="fas fa-database text-indigo-600 mr-3"></i>Database Management
    </h2>
    
    <div class="grid md:grid-cols-3 gap-6">
      <!-- Users Management -->
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-users.php" class="stat-card block bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden border-2 border-blue-200 group">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
              <i class="fas fa-users text-3xl"></i>
            </div>
            <div class="text-right">
              <div class="text-4xl font-bold"><?= $totalUsers ?></div>
              <div class="text-sm text-blue-100">Users</div>
            </div>
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
            Manage Users
          </h3>
          <p class="text-gray-600 text-sm mb-4">Create, view, edit, and delete user accounts</p>
          <div class="flex items-center text-blue-600 font-semibold">
            <span>Open CRUD Panel</span>
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
          </div>
        </div>
      </a>

      <!-- Products Management -->
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-products.php" class="stat-card block bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden border-2 border-orange-200 group">
        <div class="bg-gradient-to-br from-orange-500 to-red-600 p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
              <i class="fas fa-box text-3xl"></i>
            </div>
            <div class="text-right">
              <div class="text-4xl font-bold"><?= $totalProducts ?></div>
              <div class="text-sm text-orange-100">Products</div>
            </div>
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Manage Products
          </h3>
          <p class="text-gray-600 text-sm mb-4">Create, view, edit, and delete product listings</p>
          <div class="flex items-center text-orange-600 font-semibold">
            <span>Open CRUD Panel</span>
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
          </div>
        </div>
      </a>

      <!-- Rentals Management -->
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-rentals.php" class="stat-card block bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden border-2 border-green-200 group">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white">
          <div class="flex items-center justify-between mb-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
              <i class="fas fa-shopping-cart text-3xl"></i>
            </div>
            <div class="text-right">
              <div class="text-4xl font-bold"><?= $totalRentals ?></div>
              <div class="text-sm text-green-100">Rentals</div>
            </div>
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
            Manage Rentals
          </h3>
          <p class="text-gray-600 text-sm mb-4">View, edit, and manage all rental transactions</p>
          <div class="flex items-center text-green-600 font-semibold">
            <span>Open CRUD Panel</span>
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
          </div>
        </div>
      </a>
    </div>
  </section>

  <!-- Recent Activity -->
  <div class="grid lg:grid-cols-2 gap-6 mb-12">
    <!-- Recent Users -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
        <i class="fas fa-user-plus text-blue-600 mr-3"></i>Recent Users
      </h3>
      <div class="space-y-3">
        <?php foreach ($recentUsers as $user): ?>
          <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-user text-blue-600"></i>
              </div>
              <div>
                <div class="font-semibold text-gray-900"><?= htmlspecialchars($user['name']) ?></div>
                <div class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></div>
              </div>
            </div>
            <div class="text-right">
              <span class="inline-block px-2 py-1 bg-<?= $user['role'] === 'admin' ? 'red' : ($user['role'] === 'owner' ? 'purple' : 'blue') ?>-100 text-<?= $user['role'] === 'admin' ? 'red' : ($user['role'] === 'owner' ? 'purple' : 'blue') ?>-800 text-xs font-semibold rounded-full">
                <?= strtoupper(htmlspecialchars($user['role'])) ?>
              </span>
              <div class="text-xs text-gray-500 mt-1"><?= date('M d, Y', strtotime($user['created_at'])) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-users.php" class="mt-4 block text-center text-blue-600 hover:text-blue-700 font-semibold">
        View All Users <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>

    <!-- Recent Rentals -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
        <i class="fas fa-clock text-green-600 mr-3"></i>Recent Rentals
      </h3>
      <div class="space-y-3">
        <?php foreach ($recentRentals as $rental): ?>
          <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center justify-between mb-2">
              <div class="font-semibold text-gray-900"><?= htmlspecialchars($rental['product_name']) ?></div>
              <span class="text-sm font-bold text-green-600">₹<?= number_format($rental['total_amount'], 2) ?></span>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-600">
              <div>
                <i class="fas fa-user mr-1"></i><?= htmlspecialchars($rental['user_name']) ?>
              </div>
              <div>
                <span class="px-2 py-1 bg-<?= $rental['status'] === 'paid' ? 'blue' : ($rental['status'] === 'delivered' ? 'green' : 'purple') ?>-100 text-<?= $rental['status'] === 'paid' ? 'blue' : ($rental['status'] === 'delivered' ? 'green' : 'purple') ?>-800 text-xs font-semibold rounded-full">
                  <?= strtoupper(htmlspecialchars($rental['status'])) ?>
                </span>
              </div>
            </div>
            <div class="text-xs text-gray-500 mt-2"><?= date('M d, Y g:i A', strtotime($rental['created_at'])) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-rentals.php" class="mt-4 block text-center text-green-600 hover:text-green-700 font-semibold">
        View All Rentals <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>
  </div>

  <!-- Category Statistics -->
  <div class="bg-white rounded-2xl shadow-lg p-6 mb-12">
    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
      <i class="fas fa-chart-pie text-purple-600 mr-3"></i>Products by Category
    </h3>
    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
      <?php foreach ($categoryStats as $cat): ?>
        <div class="p-4 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-bold text-gray-900"><?= htmlspecialchars($cat['category']) ?></h4>
            <span class="text-2xl font-bold text-orange-600"><?= $cat['count'] ?></span>
          </div>
          <div class="text-sm text-gray-600">
            <i class="fas fa-check-circle text-green-500 mr-1"></i>
            <?= $cat['available_count'] ?> available
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-8 text-center">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quick Actions</h3>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-users.php?action=create" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all">
        <i class="fas fa-user-plus mr-2"></i>Add New User
      </a>
      <a href="<?= BASE_URL ?: '/' ?>admin/manage-products.php?action=create" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg transition-all">
        <i class="fas fa-plus-circle mr-2"></i>Add New Product
      </a>
      <a href="<?= BASE_URL ?: '/' ?>index.php" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-xl shadow-lg transition-all">
        <i class="fas fa-home mr-2"></i>View Website
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
