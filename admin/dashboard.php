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

// Products by category
$categoryStats = $pdo->query("
  SELECT category, COUNT(*) as count
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

$adminName = $_SESSION['user']['name'] ?? 'Admin';
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
  </style>
</head>
<body class="bg-gray-100">
  
<!-- Sidebar Layout -->
<div class="flex min-h-screen">
  
  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-slate-800 to-slate-900 text-white flex-shrink-0 hidden lg:block">
    <div class="p-6">
      <div class="flex items-center space-x-3 mb-8">
        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
          <i class="fas fa-tools text-white text-xl"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold"><?php echo APP_NAME; ?></h1>
          <p class="text-xs text-gray-400">Admin Panel</p>
        </div>
      </div>
      
      <div class="mb-6">
        <h3 class="text-xs uppercase text-gray-400 font-semibold mb-3">Luxor Admin</h3>
        <div class="flex items-center space-x-3 p-3 bg-white/10 rounded-lg">
          <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
            <i class="fas fa-user text-white"></i>
          </div>
          <div class="flex-1">
            <p class="font-semibold text-sm"><?= htmlspecialchars($adminName) ?></p>
            <p class="text-xs text-gray-400">Super Admin</p>
          </div>
        </div>
      </div>
      
      <nav class="space-y-1">
        <a href="<?= BASE_URL ?: '/' ?>admin/dashboard.php" class="sidebar-link active flex items-center space-x-3 px-4 py-3 rounded-lg">
          <i class="fas fa-home w-5"></i>
          <span>Dashboard</span>
        </a>
        <a href="<?= BASE_URL ?: '/' ?>admin/manage-products.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg">
          <i class="fas fa-box w-5"></i>
          <span>Products</span>
        </a>
        <a href="<?= BASE_URL ?: '/' ?>admin/manage-rentals.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg">
          <i class="fas fa-calendar-check w-5"></i>
          <span>Rentals</span>
        </a>
        <a href="<?= BASE_URL ?: '/' ?>admin/manage-users.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg">
          <i class="fas fa-users w-5"></i>
          <span>Users</span>
        </a>
        <a href="<?= BASE_URL ?: '/' ?>index.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg">
          <i class="fas fa-globe w-5"></i>
          <span>View Website</span>
        </a>
        <a href="<?= BASE_URL ?: '/' ?>logout.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-red-300 hover:text-red-200">
          <i class="fas fa-sign-out-alt w-5"></i>
          <span>Logout</span>
        </a>
      </nav>
    </div>
  </aside>
  
  <!-- Main Content -->
  <main class="flex-1 overflow-x-hidden">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-orange-600">Welcome, <?= htmlspecialchars($adminName) ?></h2>
          <p class="text-sm text-gray-600">Dashboard Overview</p>
        </div>
        <button class="px-6 py-2 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-all font-semibold text-gray-700">
          <i class="fas fa-sync-alt mr-2"></i>Refresh Dashboard
        </button>
      </div>
    </header>
    
    <!-- Dashboard Content -->
    <div class="p-8">
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Products -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-sm font-semibold text-blue-600 uppercase">Total Products</p>
              <h3 class="text-4xl font-bold text-blue-900 mt-2"><?= $totalProducts ?></h3>
            </div>
            <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-box text-white text-2xl"></i>
            </div>
          </div>
          <a href="<?= BASE_URL ?: '/' ?>admin/manage-products.php" class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
            View all products →
          </a>
        </div>
        
        <!-- Total Rentals -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-sm font-semibold text-green-600 uppercase">Total Rentals</p>
              <h3 class="text-4xl font-bold text-green-900 mt-2"><?= $totalRentals ?></h3>
            </div>
            <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-calendar-check text-white text-2xl"></i>
            </div>
          </div>
          <a href="<?= BASE_URL ?: '/' ?>admin/manage-rentals.php" class="text-sm text-green-600 hover:text-green-700 font-semibold">
            Manage rentals →
          </a>
        </div>
        
        <!-- Total Users -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-sm font-semibold text-purple-600 uppercase">Total Users</p>
              <h3 class="text-4xl font-bold text-purple-900 mt-2"><?= $totalUsers ?></h3>
            </div>
            <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-users text-white text-2xl"></i>
            </div>
          </div>
          <a href="<?= BASE_URL ?: '/' ?>admin/manage-users.php" class="text-sm text-purple-600 hover:text-purple-700 font-semibold">
            View all users →
          </a>
        </div>
        
        <!-- Revenue Today -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-sm font-semibold text-yellow-600 uppercase">Revenue (Today)</p>
              <h3 class="text-4xl font-bold text-yellow-900 mt-2">₹<?= number_format($todayRevenue, 0) ?></h3>
            </div>
            <div class="w-14 h-14 bg-yellow-500 rounded-xl flex items-center justify-center">
              <i class="fas fa-rupee-sign text-white text-2xl"></i>
            </div>
          </div>
          <select class="w-full px-3 py-2 bg-white border border-yellow-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
            <option>Today</option>
            <option>This Week</option>
            <option>This Month</option>
          </select>
        </div>
      </div>
      
      <!-- Charts Section -->
      <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <!-- Product Distribution Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Product Distribution</h3>
          <div style="height: 300px; position: relative;">
            <canvas id="productChart"></canvas>
          </div>
        </div>
        
        <!-- Top Products by Revenue -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Top Products by Revenue</h3>
            <span class="text-sm text-gray-500">Revenue by Product (Top 5)</span>
          </div>
          <div style="height: 300px; position: relative;">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <a href="<?= BASE_URL ?: '/' ?>admin/manage-users.php?action=create" class="flex flex-col items-center justify-center p-6 bg-blue-50 hover:bg-blue-100 rounded-xl transition-all border-2 border-blue-200">
            <i class="fas fa-user-plus text-3xl text-blue-600 mb-3"></i>
            <span class="font-semibold text-gray-900">Add User</span>
          </a>
          <a href="<?= BASE_URL ?: '/' ?>admin/manage-products.php?action=create" class="flex flex-col items-center justify-center p-6 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all border-2 border-orange-200">
            <i class="fas fa-plus-circle text-3xl text-orange-600 mb-3"></i>
            <span class="font-semibold text-gray-900">Add Product</span>
          </a>
          <a href="<?= BASE_URL ?: '/' ?>admin/manage-rentals.php" class="flex flex-col items-center justify-center p-6 bg-green-50 hover:bg-green-100 rounded-xl transition-all border-2 border-green-200">
            <i class="fas fa-list text-3xl text-green-600 mb-3"></i>
            <span class="font-semibold text-gray-900">View Rentals</span>
          </a>
          <a href="<?= BASE_URL ?: '/' ?>index.php" class="flex flex-col items-center justify-center p-6 bg-purple-50 hover:bg-purple-100 rounded-xl transition-all border-2 border-purple-200">
            <i class="fas fa-globe text-3xl text-purple-600 mb-3"></i>
            <span class="font-semibold text-gray-900">View Website</span>
          </a>
        </div>
      </div>
      
    </div>
  </main>
  
</div>

<script>
// Product Distribution Chart
const productCtx = document.getElementById('productChart').getContext('2d');
new Chart(productCtx, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($categoryStats, 'category')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($categoryStats, 'count')) ?>,
      backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});

// Top Products Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($topProducts, 'name')) ?>,
    datasets: [{
      label: 'Revenue (₹)',
      data: <?= json_encode(array_column($topProducts, 'revenue')) ?>,
      backgroundColor: '#F59E0B',
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

</body>
</html>
