<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

require_login();
$stmt = db()->prepare("SELECT r.*, p.name, p.image_url, p.category FROM rentals r JOIN products p ON p.id = r.product_id WHERE r.borrower_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user']['id']]);
$rentals = $stmt->fetchAll();

// Status colors
$status_colors = [
  'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
  'paid' => 'bg-blue-100 text-blue-800 border-blue-200',
  'delivered' => 'bg-green-100 text-green-800 border-green-200',
  'completed' => 'bg-purple-100 text-purple-800 border-purple-200',
  'cancelled' => 'bg-red-100 text-red-800 border-red-200',
];

$status_icons = [
  'pending' => 'fa-clock',
  'paid' => 'fa-check-circle',
  'delivered' => 'fa-truck',
  'completed' => 'fa-flag-checkered',
  'cancelled' => 'fa-times-circle',
];
?>

<!-- Header Section -->
<section class="mb-12 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl p-12 text-white">
  <div class="max-w-4xl mx-auto text-center">
    <h1 class="text-5xl font-extrabold mb-4">
      <i class="fas fa-box-open mr-3"></i>My Rentals
    </h1>
    <p class="text-xl text-white/90">Track all your tool rentals in one place</p>
    <div class="mt-8 inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-6 py-3">
      <i class="fas fa-info-circle mr-2"></i>
      <span>You have <?= count($rentals) ?> rental(s)</span>
    </div>
  </div>
</section>

<?php if (empty($rentals)): ?>
  <!-- Empty State -->
  <div class="text-center py-20 bg-white rounded-2xl shadow-lg border-2 border-gray-100">
    <div class="mb-6">
      <i class="fas fa-box-open text-8xl text-gray-300"></i>
    </div>
    <h2 class="text-3xl font-bold text-gray-900 mb-4">No Rentals Yet</h2>
    <p class="text-gray-600 text-lg mb-8">Start renting tools to see them here!</p>
    <a href="<?= BASE_URL ?: '/' ?>index.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:scale-105">
      <i class="fas fa-search mr-3"></i>Browse Tools
    </a>
  </div>
<?php else: ?>
  <!-- Rentals Grid -->
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($rentals as $r): 
      $status_class = $status_colors[$r['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
      $status_icon = $status_icons[$r['status']] ?? 'fa-question';
    ?>
      <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 group">
        <!-- Image -->
        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
          <?php if (!empty($r['image_url'])): ?>
            <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
              <i class="fas fa-tools text-7xl text-gray-300"></i>
            </div>
          <?php endif; ?>
          
          <!-- Status Badge -->
          <div class="absolute top-3 right-3 <?= $status_class ?> border-2 px-4 py-2 rounded-full shadow-lg backdrop-blur-sm">
            <i class="fas <?= $status_icon ?> mr-1"></i>
            <span class="font-bold text-sm uppercase"><?= htmlspecialchars($r['status']) ?></span>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
          <!-- Product Name -->
          <h3 class="font-bold text-xl text-gray-900 mb-3 line-clamp-1">
            <?= htmlspecialchars($r['name']) ?>
          </h3>
          
          <!-- Category -->
          <?php if (!empty($r['category'])): ?>
            <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full mb-4">
              <i class="fas fa-tag mr-1"></i>
              <?= htmlspecialchars($r['category']) ?>
            </span>
          <?php endif; ?>
          
          <!-- Rental Details -->
          <div class="space-y-3 mb-4">
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600 flex items-center">
                <i class="fas fa-calendar-check text-green-500 mr-2"></i>Start
              </span>
              <span class="font-semibold text-gray-900"><?= date('M d, Y', strtotime($r['start_time'])) ?></span>
            </div>
            
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600 flex items-center">
                <i class="fas fa-calendar-times text-red-500 mr-2"></i>End
              </span>
              <span class="font-semibold text-gray-900"><?= date('M d, Y', strtotime($r['end_time'])) ?></span>
            </div>
            
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600 flex items-center">
                <i class="fas fa-credit-card text-blue-500 mr-2"></i>Payment
              </span>
              <span class="font-semibold text-gray-900 uppercase"><?= htmlspecialchars($r['payment_method']) ?></span>
            </div>
            
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600 flex items-center">
                <i class="fas fa-truck text-purple-500 mr-2"></i>Delivery ETA
              </span>
              <span class="font-semibold text-gray-900"><?= (int)$r['delivery_eta_minutes'] ?> min</span>
            </div>
          </div>
          
          <!-- Total Amount -->
          <div class="border-t-2 border-gray-100 pt-4">
            <div class="flex items-center justify-between">
              <span class="text-gray-600 font-medium">Total Amount</span>
              <span class="text-2xl font-bold text-orange-600">
                ₹<?= number_format((float)$r['total_amount'], 2) ?>
              </span>
            </div>
          </div>
          
          <!-- Booking Date -->
          <div class="mt-4 text-xs text-gray-500 text-center">
            <i class="fas fa-clock mr-1"></i>
            Booked on <?= date('M d, Y g:i A', strtotime($r['created_at'])) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Summary Section -->
  <div class="mt-12 grid md:grid-cols-3 gap-6">
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-blue-100 mb-2">Total Rentals</p>
          <p class="text-4xl font-bold"><?= count($rentals) ?></p>
        </div>
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
          <i class="fas fa-box text-3xl"></i>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-green-100 mb-2">Total Spent</p>
          <p class="text-4xl font-bold">
            ₹<?= number_format(array_sum(array_column($rentals, 'total_amount')), 2) ?>
          </p>
        </div>
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
          <i class="fas fa-dollar-sign text-3xl"></i>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-orange-100 mb-2">Active Rentals</p>
          <p class="text-4xl font-bold">
            <?= count(array_filter($rentals, fn($r) => in_array($r['status'], ['paid', 'delivered']))) ?>
          </p>
        </div>
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
          <i class="fas fa-chart-line text-3xl"></i>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
