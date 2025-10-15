<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

require_login();

// Fine rate: 50% of hourly rate per hour overdue
define('FINE_RATE_MULTIPLIER', 0.5);

// Get all rentals with calculated fines
$stmt = db()->prepare("
  SELECT 
    r.*, 
    p.name, 
    p.image_url, 
    p.category,
    p.price_per_hour,
    p.price_per_day,
    CASE 
      WHEN r.end_time < NOW() AND r.status NOT IN ('completed', 'cancelled') 
      THEN CEIL(TIMESTAMPDIFF(MINUTE, r.end_time, NOW()) / 60.0) * (p.price_per_hour * ?)
      ELSE 0 
    END as fine_amount,
    CASE 
      WHEN r.end_time < NOW() AND r.status NOT IN ('completed', 'cancelled') 
      THEN 1 
      ELSE 0 
    END as is_overdue,
    TIMESTAMPDIFF(SECOND, NOW(), r.end_time) as seconds_remaining
  FROM rentals r 
  JOIN products p ON p.id = r.product_id 
  WHERE r.borrower_id = ? 
  ORDER BY 
    CASE 
      WHEN r.status = 'paid' OR r.status = 'delivered' THEN 0
      ELSE 1
    END,
    r.created_at DESC
");
$stmt->execute([FINE_RATE_MULTIPLIER, $_SESSION['user']['id']]);
$rentals = $stmt->fetchAll();

// Calculate statistics
$active_count = 0;
$completed_count = 0;
$total_spent = 0;
$total_fines = 0;

foreach ($rentals as $r) {
  if (in_array($r['status'], ['paid', 'delivered'])) {
    $active_count++;
  }
  if ($r['status'] === 'completed') {
    $completed_count++;
  }
  $total_spent += (float)$r['total_amount'];
  $total_fines += (float)($r['fine_amount'] ?? 0);
}
?>

<!-- Success Message -->
<?php if (isset($_SESSION['flash_success'])): ?>
  <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4 flex items-center animate-fade-in">
    <i class="fas fa-check-circle text-green-500 text-2xl mr-4"></i>
    <div>
      <p class="text-green-800 font-semibold"><?= htmlspecialchars($_SESSION['flash_success']) ?></p>
      <p class="text-green-600 text-sm">Redirecting to your rentals...</p>
    </div>
  </div>
  <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<!-- Header Section -->
<section class="mb-12 bg-gradient-to-r from-orange-500 via-red-500 to-pink-600 rounded-2xl shadow-2xl p-12 text-white">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-8">
      <h1 class="text-5xl font-extrabold mb-4">
        <i class="fas fa-box-open mr-3"></i>My Rentals Dashboard
      </h1>
      <p class="text-xl text-white/90">Track your active rentals and rental history</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
        <div class="text-3xl font-bold mb-2"><?= count($rentals) ?></div>
        <div class="text-sm text-white/80">Total Rentals</div>
      </div>
      <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
        <div class="text-3xl font-bold mb-2"><?= $active_count ?></div>
        <div class="text-sm text-white/80">Active Now</div>
      </div>
      <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
        <div class="text-3xl font-bold mb-2">₹<?= number_format($total_spent, 0) ?></div>
        <div class="text-sm text-white/80">Total Spent</div>
      </div>
      <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
        <div class="text-3xl font-bold mb-2 <?= $total_fines > 0 ? 'text-red-300' : '' ?>">
          ₹<?= number_format($total_fines, 0) ?>
        </div>
        <div class="text-sm text-white/80">Total Fines</div>
      </div>
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
  <!-- Active Rentals Section -->
  <?php if ($active_count > 0): ?>
    <section class="mb-12">
      <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-clock text-orange-500 mr-3"></i>
        Active Rentals
        <span class="ml-3 text-lg bg-orange-100 text-orange-800 px-3 py-1 rounded-full"><?= $active_count ?></span>
      </h2>
      
      <div class="grid gap-6 lg:grid-cols-2">
        <?php foreach ($rentals as $r): 
          if (!in_array($r['status'], ['paid', 'delivered'])) continue;
          
          $seconds_remaining = (int)$r['seconds_remaining'];
          $is_overdue = $seconds_remaining < 0;
          $fine_amount = (float)$r['fine_amount'];
        ?>
          <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 <?= $is_overdue ? 'border-red-300' : 'border-green-300' ?>">
            <div class="relative">
              <!-- Image -->
              <div class="h-56 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden relative">
                <?php if (!empty($r['image_url'])): ?>
                  <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-tools text-8xl text-gray-300"></i>
                  </div>
                <?php endif; ?>
                
                <!-- Status Badge -->
                <div class="absolute top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-full shadow-lg font-bold text-sm flex items-center">
                  <i class="fas fa-check-circle mr-2"></i>
                  <?= strtoupper(htmlspecialchars($r['status'])) ?>
                </div>
                
                <!-- Category Badge -->
                <?php if (!empty($r['category'])): ?>
                  <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-gray-800 px-4 py-2 rounded-full shadow-lg font-semibold text-sm">
                    <i class="fas fa-tag mr-1 text-orange-500"></i>
                    <?= htmlspecialchars($r['category']) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
              <!-- Product Name -->
              <h3 class="font-bold text-2xl text-gray-900 mb-4">
                <?= htmlspecialchars($r['name']) ?>
              </h3>
              
              <!-- Countdown Timer -->
              <?php if (!$is_overdue): ?>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 mb-4 border-2 border-green-200">
                  <div class="text-center">
                    <p class="text-sm text-gray-600 mb-2 font-semibold">TIME REMAINING</p>
                    <div class="countdown text-4xl font-bold text-green-600" data-seconds="<?= $seconds_remaining ?>" data-rental-id="<?= $r['id'] ?>">
                      <span class="days">00</span>d 
                      <span class="hours">00</span>h 
                      <span class="minutes">00</span>m 
                      <span class="seconds">00</span>s
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Return by <?= date('M d, Y g:i A', strtotime($r['end_time'])) ?></p>
                  </div>
                </div>
              <?php else: ?>
                <!-- Overdue Warning -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-6 mb-4 border-2 border-red-300">
                  <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-3"></i>
                    <p class="text-lg font-bold text-red-600 mb-2">⚠️ OVERDUE!</p>
                    <p class="text-sm text-gray-600 mb-3">Should have been returned on</p>
                    <p class="font-semibold text-gray-900"><?= date('M d, Y g:i A', strtotime($r['end_time'])) ?></p>
                    
                    <?php if ($fine_amount > 0): ?>
                      <div class="mt-4 pt-4 border-t-2 border-red-200">
                        <p class="text-xs text-gray-600 mb-1">ACCUMULATED FINE</p>
                        <p class="text-3xl font-bold text-red-600">₹<?= number_format($fine_amount, 2) ?></p>
                        <p class="text-xs text-red-500 mt-2">
                          <i class="fas fa-info-circle mr-1"></i>
                          Fine: 50% of hourly rate (₹<?= number_format((float)$r['price_per_hour'] * 0.5, 2) ?>/hr)
                        </p>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <!-- Rental Details -->
              <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                  <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-calendar-check text-blue-500 mr-1"></i>Start
                  </p>
                  <p class="font-semibold text-gray-900 text-sm"><?= date('M d, g:i A', strtotime($r['start_time'])) ?></p>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-3">
                  <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-calendar-times text-purple-500 mr-1"></i>End
                  </p>
                  <p class="font-semibold text-gray-900 text-sm"><?= date('M d, g:i A', strtotime($r['end_time'])) ?></p>
                </div>
                
                <div class="bg-indigo-50 rounded-lg p-3">
                  <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-credit-card text-indigo-500 mr-1"></i>Payment
                  </p>
                  <p class="font-semibold text-gray-900 text-sm uppercase"><?= htmlspecialchars($r['payment_method']) ?></p>
                </div>
                
                <div class="bg-orange-50 rounded-lg p-3">
                  <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-truck text-orange-500 mr-1"></i>Delivery
                  </p>
                  <p class="font-semibold text-gray-900 text-sm"><?= (int)$r['delivery_eta_minutes'] ?> mins</p>
                </div>
              </div>
              
              <!-- Pricing -->
              <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4 border-2 border-orange-200">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 mb-1">Rental Amount</p>
                    <p class="text-2xl font-bold text-orange-600">₹<?= number_format((float)$r['total_amount'], 2) ?></p>
                  </div>
                  <?php if ($fine_amount > 0): ?>
                    <div class="text-right">
                      <p class="text-sm text-red-600 mb-1">+ Fine</p>
                      <p class="text-2xl font-bold text-red-600">₹<?= number_format($fine_amount, 2) ?></p>
                    </div>
                  <?php endif; ?>
                </div>
                <?php if ($fine_amount > 0): ?>
                  <div class="mt-3 pt-3 border-t-2 border-orange-200">
                    <div class="flex justify-between items-center">
                      <p class="font-semibold text-gray-900">Total Due:</p>
                      <p class="text-3xl font-bold text-red-600">₹<?= number_format((float)$r['total_amount'] + $fine_amount, 2) ?></p>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
              
              <!-- Booking Info -->
              <div class="mt-4 text-xs text-gray-500 text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Booked on <?= date('M d, Y g:i A', strtotime($r['created_at'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
  
  <!-- Rental History -->
  <?php 
  $history = array_filter($rentals, function($r) {
    return in_array($r['status'], ['completed', 'cancelled']);
  });
  ?>
  
  <?php if (!empty($history)): ?>
    <section class="mb-12">
      <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-history text-purple-500 mr-3"></i>
        Rental History
        <span class="ml-3 text-lg bg-purple-100 text-purple-800 px-3 py-1 rounded-full"><?= count($history) ?></span>
      </h2>
      
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($history as $r): ?>
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-gray-100 opacity-75 hover:opacity-100">
            <!-- Image -->
            <div class="h-40 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden relative">
              <?php if (!empty($r['image_url'])): ?>
                <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" class="w-full h-full object-cover">
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                  <i class="fas fa-tools text-6xl text-gray-300"></i>
                </div>
              <?php endif; ?>
              
              <!-- Status Badge -->
              <div class="absolute top-3 right-3 <?= $r['status'] === 'completed' ? 'bg-purple-500' : 'bg-gray-500' ?> text-white px-3 py-1 rounded-full shadow-lg font-bold text-xs">
                <i class="fas <?= $r['status'] === 'completed' ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                <?= strtoupper(htmlspecialchars($r['status'])) ?>
              </div>
            </div>
            
            <!-- Content -->
            <div class="p-5">
              <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-1">
                <?= htmlspecialchars($r['name']) ?>
              </h3>
              
              <?php if (!empty($r['category'])): ?>
                <span class="inline-block bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1 rounded-full mb-3">
                  <?= htmlspecialchars($r['category']) ?>
                </span>
              <?php endif; ?>
              
              <div class="space-y-2 mb-3">
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500">Rental Period:</span>
                  <span class="font-medium text-gray-900">
                    <?= date('M d', strtotime($r['start_time'])) ?> - <?= date('M d', strtotime($r['end_time'])) ?>
                  </span>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500">Amount:</span>
                  <span class="font-bold text-gray-900">₹<?= number_format((float)$r['total_amount'], 2) ?></span>
                </div>
              </div>
              
              <div class="text-xs text-gray-400 text-center pt-3 border-t border-gray-100">
                <?= date('M d, Y', strtotime($r['created_at'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
  
  <!-- Browse More -->
  <div class="text-center py-12 bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Need More Tools?</h3>
    <p class="text-gray-600 mb-6">Browse our collection of 80+ professional tools</p>
    <a href="<?= BASE_URL ?: '/' ?>index.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all">
      <i class="fas fa-search mr-3"></i>Browse Tools
    </a>
  </div>
<?php endif; ?>

<!-- Countdown Timer Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const countdowns = document.querySelectorAll('.countdown');
  
  countdowns.forEach(countdown => {
    let totalSeconds = parseInt(countdown.dataset.seconds);
    
    function updateCountdown() {
      if (totalSeconds <= 0) {
        // Reload page when time expires
        location.reload();
        return;
      }
      
      const days = Math.floor(totalSeconds / 86400);
      const hours = Math.floor((totalSeconds % 86400) / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;
      
      countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
      countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
      countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
      countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
      
      // Color changes based on time remaining
      if (totalSeconds < 3600) { // Less than 1 hour
        countdown.classList.remove('text-green-600', 'text-yellow-600');
        countdown.classList.add('text-red-600');
      } else if (totalSeconds < 86400) { // Less than 1 day
        countdown.classList.remove('text-green-600', 'text-red-600');
        countdown.classList.add('text-yellow-600');
      }
      
      totalSeconds--;
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
  });
});
</script>

<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

.countdown {
  font-family: 'Courier New', monospace;
  letter-spacing: 0.05em;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
