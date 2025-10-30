<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

require_login();
require_role('admin');

$pdo = db();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';

// Get products and users for dropdowns
$products = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll();
$borrowers = $pdo->query("SELECT id, name FROM users WHERE role = 'borrower' ORDER BY name")->fetchAll();
$payment_methods = ['card', 'upi', 'wallet', 'cod'];
$statuses = ['active', 'completed', 'cancelled'];

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
  $post_action = $_POST['action'] ?? '';
  
  if ($post_action === 'create') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $borrower_id = (int)($_POST['borrower_id'] ?? 0);
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $total_amount = (float)($_POST['total_amount'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? 'card';
    $status = $_POST['status'] ?? 'active';
    $delivery_eta_minutes = (int)($_POST['delivery_eta_minutes'] ?? 45);
    
    if ($product_id && $borrower_id && $start_time && $end_time && $total_amount > 0) {
      try {
        $stmt = $pdo->prepare("INSERT INTO rentals (product_id, borrower_id, start_time, end_time, total_amount, payment_method, status, delivery_eta_minutes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $borrower_id, $start_time, $end_time, $total_amount, $payment_method, $status, $delivery_eta_minutes]);
        $message = "Rental created successfully!";
        $action = 'list';
      } catch (Exception $e) {
        $error = "Error creating rental: " . $e->getMessage();
      }
    } else {
      $error = "Please fill all required fields.";
    }
  }
  
  elseif ($post_action === 'update') {
    $update_id = (int)($_POST['id'] ?? 0);
    $product_id = (int)($_POST['product_id'] ?? 0);
    $borrower_id = (int)($_POST['borrower_id'] ?? 0);
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $total_amount = (float)($_POST['total_amount'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? 'card';
    $status = $_POST['status'] ?? 'active';
    $delivery_eta_minutes = (int)($_POST['delivery_eta_minutes'] ?? 45);
    
    if ($update_id && $product_id && $borrower_id && $start_time && $end_time && $total_amount > 0) {
      try {
        $stmt = $pdo->prepare("UPDATE rentals SET product_id = ?, borrower_id = ?, start_time = ?, end_time = ?, total_amount = ?, payment_method = ?, status = ?, delivery_eta_minutes = ? WHERE id = ?");
        $stmt->execute([$product_id, $borrower_id, $start_time, $end_time, $total_amount, $payment_method, $status, $delivery_eta_minutes, $update_id]);
        $message = "Rental updated successfully!";
        $action = 'list';
      } catch (Exception $e) {
        $error = "Error updating rental: " . $e->getMessage();
      }
    } else {
      $error = "Please fill all required fields.";
    }
  }
  
  elseif ($post_action === 'delete') {
    $delete_id = (int)($_POST['id'] ?? 0);
    if ($delete_id) {
      try {
        $stmt = $pdo->prepare("DELETE FROM rentals WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = "Rental deleted successfully!";
      } catch (Exception $e) {
        $error = "Error deleting rental: " . $e->getMessage();
      }
    }
  }
}

// Get rental for edit
$rental = null;
if ($action === 'edit' && $id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM rentals WHERE id = ?");
  $stmt->execute([$id]);
  $rental = $stmt->fetch();
  if (!$rental) {
    $error = "Rental not found.";
    $action = 'list';
  }
}

// Get all rentals for list
$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$sql = "SELECT r.*, 
        p.name as product_name, 
        u.name as borrower_name,
        CASE 
          WHEN r.end_time < NOW() AND r.status = 'active' 
          THEN CEIL(TIMESTAMPDIFF(MINUTE, r.end_time, NOW()) / 60.0) * (p.price_per_hour * 0.5)
          ELSE 0
        END as fine_amount,
        TIMESTAMPDIFF(MINUTE, r.end_time, NOW()) as minutes_overdue
        FROM rentals r 
        JOIN products p ON p.id = r.product_id 
        JOIN users u ON u.id = r.borrower_id 
        WHERE 1=1";
$params = [];

if ($search) {
  $sql .= " AND (p.name LIKE ? OR u.name LIKE ?)";
  $search_term = '%' . $search . '%';
  $params[] = $search_term;
  $params[] = $search_term;
}

if ($status_filter) {
  $sql .= " AND r.status = ?";
  $params[] = $status_filter;
}

if ($date_from) {
  $sql .= " AND r.start_time >= ?";
  $params[] = $date_from;
}

if ($date_to) {
  $sql .= " AND r.start_time <= ?";
  $params[] = $date_to . ' 23:59:59';
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rentals = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 md:px-8 py-8">
  <!-- Header -->
  <div class="mb-8">
    <nav class="text-sm text-gray-600 mb-4">
      <a href="<?= BASE_URL ?: '/' ?>admin/dashboard.php" class="hover:text-orange-600">
        <i class="fas fa-shield-alt mr-1"></i>Admin Dashboard
      </a>
      <span class="mx-2">/</span>
      <span class="text-gray-900 font-semibold">Manage Rentals</span>
    </nav>
    
    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-extrabold mb-2">
            <i class="fas fa-calendar-check mr-3"></i>Rental Management
          </h1>
          <p class="text-orange-100">Create, Read, Update, and Delete rental bookings</p>
        </div>
        <?php if ($action === 'list'): ?>
          <a href="?action=create" class="px-6 py-3 bg-white text-orange-600 font-bold rounded-xl hover:bg-orange-50 transition-all shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>Add New Rental
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Messages -->
  <?php if ($message): ?>
    <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4 flex items-center">
      <i class="fas fa-check-circle text-green-500 text-2xl mr-4"></i>
      <p class="text-green-800 font-semibold"><?= htmlspecialchars($message) ?></p>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4 flex items-center">
      <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-4"></i>
      <p class="text-red-800 font-semibold"><?= htmlspecialchars($error) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($action === 'create' || $action === 'edit'): ?>
    <!-- Create/Edit Form -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-<?= $action === 'create' ? 'plus' : 'edit' ?> text-orange-600 mr-2"></i>
        <?= $action === 'create' ? 'Create New Rental' : 'Edit Rental' ?>
      </h2>
      
      <form method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
        <?php if ($action === 'edit'): ?>
          <input type="hidden" name="id" value="<?= $rental['id'] ?>">
        <?php endif; ?>
        
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-box mr-1"></i>Product *
            </label>
            <select name="product_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
              <option value="">Select Product</option>
              <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($action === 'edit' && $rental['product_id'] == $p['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-user mr-1"></i>Borrower *
            </label>
            <select name="borrower_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
              <option value="">Select Borrower</option>
              <?php foreach ($borrowers as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($action === 'edit' && $rental['borrower_id'] == $b['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($b['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-clock mr-1"></i>Start Time *
            </label>
            <input type="datetime-local" name="start_time" required 
                   value="<?= $action === 'edit' ? date('Y-m-d\TH:i', strtotime($rental['start_time'])) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-clock mr-1"></i>End Time *
            </label>
            <input type="datetime-local" name="end_time" required 
                   value="<?= $action === 'edit' ? date('Y-m-d\TH:i', strtotime($rental['end_time'])) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-rupee-sign mr-1"></i>Total Amount (₹) *
            </label>
            <input type="number" name="total_amount" step="0.01" min="0" required 
                   value="<?= $action === 'edit' ? $rental['total_amount'] : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-credit-card mr-1"></i>Payment Method *
            </label>
            <select name="payment_method" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
              <?php foreach ($payment_methods as $pm): ?>
                <option value="<?= $pm ?>" <?= ($action === 'edit' && $rental['payment_method'] === $pm) ? 'selected' : '' ?>>
                  <?= strtoupper($pm) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-toggle-on mr-1"></i>Status *
            </label>
            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
              <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= ($action === 'edit' && $rental['status'] === $s) ? 'selected' : '' ?>>
                  <?= ucfirst($s) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-truck mr-1"></i>Delivery ETA (minutes)
            </label>
            <input type="number" name="delivery_eta_minutes" min="0" 
                   value="<?= $action === 'edit' ? $rental['delivery_eta_minutes'] : 45 ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg transition-all">
            <i class="fas fa-save mr-2"></i><?= $action === 'create' ? 'Create Rental' : 'Update Rental' ?>
          </button>
          <a href="?action=list" class="px-8 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-xl transition-all">
            <i class="fas fa-times mr-2"></i>Cancel
          </a>
        </div>
      </form>
    </div>
    
  <?php else: ?>
    <!-- List View -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
      <!-- Search and Filter -->
      <div class="p-6 bg-gray-50 border-b-2 border-gray-200">
        <form method="GET" class="flex flex-wrap gap-4">
          <input type="hidden" name="action" value="list">
          <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                   placeholder="Search by product or borrower..." 
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
          </div>
          <select name="status" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
            <option value="">All Status</option>
            <?php foreach ($statuses as $s): ?>
              <option value="<?= $s ?>" <?= $status_filter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" 
                 placeholder="From Date" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
          <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" 
                 placeholder="To Date" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
          <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg transition-all">
            <i class="fas fa-search mr-2"></i>Search
          </button>
          <?php if ($search || $status_filter || $date_from || $date_to): ?>
            <a href="?action=list" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition-all">
              <i class="fas fa-times mr-2"></i>Clear
            </a>
          <?php endif; ?>
        </form>
      </div>
      
      <!-- Rentals Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100 border-b-2 border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Borrower</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rental Period</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fine</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Payment</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($rentals as $r): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#<?= $r['id'] ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($r['product_name']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900"><?= htmlspecialchars($r['borrower_name']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-xs text-gray-600">From</div>
                  <div class="text-sm font-semibold"><?= date('M d, H:i', strtotime($r['start_time'])) ?></div>
                  <div class="text-xs text-gray-600 mt-1">To</div>
                  <div class="text-sm font-semibold"><?= date('M d, H:i', strtotime($r['end_time'])) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">₹<?= number_format($r['total_amount'], 2) ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if ($r['fine_amount'] > 0): ?>
                    <span class="text-sm font-bold text-red-600">₹<?= number_format($r['fine_amount'], 2) ?></span>
                    <div class="text-xs text-red-500"><?= ceil($r['minutes_overdue'] / 60) ?>h overdue</div>
                  <?php else: ?>
                    <span class="text-sm text-gray-400">-</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold <?= 
                    $r['payment_method'] === 'card' ? 'bg-blue-100 text-blue-800' : 
                    ($r['payment_method'] === 'upi' ? 'bg-purple-100 text-purple-800' : 
                    ($r['payment_method'] === 'wallet' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) ?>">
                    <?= strtoupper($r['payment_method']) ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= 
                    $r['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                    ($r['status'] === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') ?>">
                    <?= strtoupper($r['status']) ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex justify-center gap-2">
                    <a href="?action=edit&id=<?= $r['id'] ?>" 
                       class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-semibold rounded transition-all">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this rental?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $r['id'] ?>">
                      <button type="submit" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold rounded transition-all">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <?php if (empty($rentals)): ?>
        <div class="text-center py-12">
          <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
          <p class="text-gray-600 text-lg">No rentals found</p>
        </div>
      <?php endif; ?>
      
      <!-- Footer -->
      <div class="px-6 py-4 bg-gray-50 border-t-2 border-gray-200 flex justify-between items-center">
        <p class="text-sm text-gray-600">
          <i class="fas fa-info-circle mr-1"></i>
          Total: <strong><?= count($rentals) ?></strong> rental(s)
        </p>
        <p class="text-sm text-gray-600">
          <strong>Total Fines:</strong> 
          <span class="text-red-600 font-bold">₹<?= number_format(array_sum(array_column($rentals, 'fine_amount')), 2) ?></span>
        </p>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
