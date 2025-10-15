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

// Get all owners for dropdown
$owners = $pdo->query("SELECT id, name FROM users WHERE role = 'owner' ORDER BY name")->fetchAll();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
  $post_action = $_POST['action'] ?? '';
  
  if ($post_action === 'create') {
    $owner_id = (int)($_POST['owner_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price_per_hour = (float)($_POST['price_per_hour'] ?? 0);
    $price_per_day = (float)($_POST['price_per_day'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    
    if ($owner_id && $name && $price_per_hour > 0) {
      try {
        $stmt = $pdo->prepare("INSERT INTO products (owner_id, name, description, image_url, category, price_per_hour, price_per_day, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$owner_id, $name, $description, $image_url, $category, $price_per_hour, $price_per_day, $status]);
        $message = "Product created successfully!";
        $action = 'list';
      } catch (Exception $e) {
        $error = "Error creating product: " . $e->getMessage();
      }
    } else {
      $error = "Please fill all required fields.";
    }
  }
  
  elseif ($post_action === 'update') {
    $update_id = (int)($_POST['id'] ?? 0);
    $owner_id = (int)($_POST['owner_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price_per_hour = (float)($_POST['price_per_hour'] ?? 0);
    $price_per_day = (float)($_POST['price_per_day'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    
    if ($update_id && $owner_id && $name && $price_per_hour > 0) {
      try {
        $stmt = $pdo->prepare("UPDATE products SET owner_id = ?, name = ?, description = ?, image_url = ?, category = ?, price_per_hour = ?, price_per_day = ?, status = ? WHERE id = ?");
        $stmt->execute([$owner_id, $name, $description, $image_url, $category, $price_per_hour, $price_per_day, $status, $update_id]);
        $message = "Product updated successfully!";
        $action = 'list';
      } catch (Exception $e) {
        $error = "Error updating product: " . $e->getMessage();
      }
    } else {
      $error = "Please fill all required fields.";
    }
  }
  
  elseif ($post_action === 'delete') {
    $delete_id = (int)($_POST['id'] ?? 0);
    if ($delete_id) {
      try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = "Product deleted successfully!";
      } catch (Exception $e) {
        $error = "Error deleting product: " . $e->getMessage();
      }
    }
  }
}

// Get product for edit
$product = null;
if ($action === 'edit' && $id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
  $stmt->execute([$id]);
  $product = $stmt->fetch();
  if (!$product) {
    $error = "Product not found.";
    $action = 'list';
  }
}

// Get all products for list
$search = trim($_GET['search'] ?? '');
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT p.*, u.name as owner_name FROM products p JOIN users u ON u.id = p.owner_id WHERE 1=1";
$params = [];

if ($search) {
  $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
  $search_term = '%' . $search . '%';
  $params[] = $search_term;
  $params[] = $search_term;
}

if ($category_filter) {
  $sql .= " AND p.category = ?";
  $params[] = $category_filter;
}

if ($status_filter) {
  $sql .= " AND p.status = ?";
  $params[] = $status_filter;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get unique categories
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto">
  <!-- Header -->
  <div class="mb-8">
    <nav class="text-sm text-gray-600 mb-4">
      <a href="<?= BASE_URL ?: '/' ?>admin/dashboard.php" class="hover:text-orange-600">
        <i class="fas fa-shield-alt mr-1"></i>Admin Dashboard
      </a>
      <span class="mx-2">/</span>
      <span class="text-gray-900 font-semibold">Manage Products</span>
    </nav>
    
    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-extrabold mb-2">
            <i class="fas fa-box mr-3"></i>Product Management
          </h1>
          <p class="text-orange-100">Create, Read, Update, and Delete product listings</p>
        </div>
        <?php if ($action === 'list'): ?>
          <a href="?action=create" class="px-6 py-3 bg-white text-orange-600 font-bold rounded-xl hover:bg-orange-50 transition-all shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>Add New Product
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
        <?= $action === 'create' ? 'Create New Product' : 'Edit Product' ?>
      </h2>
      
      <form method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
        <?php if ($action === 'edit'): ?>
          <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <?php endif; ?>
        
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-user mr-1"></i>Owner *
            </label>
            <select name="owner_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
              <option value="">Select Owner</option>
              <?php foreach ($owners as $owner): ?>
                <option value="<?= $owner['id'] ?>" <?= ($action === 'edit' && $product['owner_id'] == $owner['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($owner['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-tag mr-1"></i>Product Name *
            </label>
            <input type="text" name="name" required value="<?= $action === 'edit' ? htmlspecialchars($product['name']) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-align-left mr-1"></i>Description
            </label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none"><?= $action === 'edit' ? htmlspecialchars($product['description']) : '' ?></textarea>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-image mr-1"></i>Image URL
            </label>
            <input type="url" name="image_url" value="<?= $action === 'edit' ? htmlspecialchars($product['image_url']) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none"
                   placeholder="https://images.unsplash.com/...">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-folder mr-1"></i>Category
            </label>
            <input type="text" name="category" value="<?= $action === 'edit' ? htmlspecialchars($product['category']) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none"
                   list="categories"
                   placeholder="e.g., Power Tools, Electronics">
            <datalist id="categories">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
              <?php endforeach; ?>
            </datalist>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-toggle-on mr-1"></i>Status *
            </label>
            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
              <option value="available" <?= ($action === 'edit' && $product['status'] === 'available') ? 'selected' : '' ?>>Available</option>
              <option value="unavailable" <?= ($action === 'edit' && $product['status'] === 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-rupee-sign mr-1"></i>Price per Hour (₹) *
            </label>
            <input type="number" name="price_per_hour" step="0.01" min="0" required 
                   value="<?= $action === 'edit' ? $product['price_per_hour'] : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-rupee-sign mr-1"></i>Price per Day (₹)
            </label>
            <input type="number" name="price_per_day" step="0.01" min="0" 
                   value="<?= $action === 'edit' ? $product['price_per_day'] : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none">
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg transition-all">
            <i class="fas fa-save mr-2"></i><?= $action === 'create' ? 'Create Product' : 'Update Product' ?>
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
                   placeholder="Search by name or description..." 
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
          </div>
          <select name="category" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>" <?= $category_filter === $cat ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select name="status" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none">
            <option value="">All Status</option>
            <option value="available" <?= $status_filter === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="unavailable" <?= $status_filter === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
          </select>
          <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg transition-all">
            <i class="fas fa-search mr-2"></i>Search
          </button>
          <?php if ($search || $category_filter || $status_filter): ?>
            <a href="?action=list" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition-all">
              <i class="fas fa-times mr-2"></i>Clear
            </a>
          <?php endif; ?>
        </form>
      </div>
      
      <!-- Products Grid -->
      <div class="p-6 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($products as $p): ?>
          <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100">
            <!-- Image -->
            <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden relative">
              <?php if (!empty($p['image_url'])): ?>
                <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" 
                     class="w-full h-full object-cover">
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                  <i class="fas fa-box text-6xl text-gray-300"></i>
                </div>
              <?php endif; ?>
              <div class="absolute top-3 right-3">
                <span class="px-3 py-1 <?= $p['status'] === 'available' ? 'bg-green-500' : 'bg-red-500' ?> text-white text-xs font-bold rounded-full">
                  <?= strtoupper($p['status']) ?>
                </span>
              </div>
            </div>
            
            <!-- Content -->
            <div class="p-4">
              <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-1"><?= htmlspecialchars($p['name']) ?></h3>
              
              <?php if (!empty($p['category'])): ?>
                <span class="inline-block bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-1 rounded-full mb-2">
                  <?= htmlspecialchars($p['category']) ?>
                </span>
              <?php endif; ?>
              
              <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars($p['description']) ?></p>
              
              <div class="flex items-center text-sm text-gray-500 mb-3">
                <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                <?= htmlspecialchars($p['owner_name']) ?>
              </div>
              
              <div class="flex items-center justify-between mb-4 pb-4 border-t pt-3">
                <div>
                  <div class="text-xs text-gray-500">Per Hour</div>
                  <div class="text-lg font-bold text-orange-600">₹<?= number_format($p['price_per_hour'], 2) ?></div>
                </div>
                <div class="text-right">
                  <div class="text-xs text-gray-500">Per Day</div>
                  <div class="text-lg font-bold text-orange-600">₹<?= number_format($p['price_per_day'], 2) ?></div>
                </div>
              </div>
              
              <div class="flex gap-2">
                <a href="?action=edit&id=<?= $p['id'] ?>" 
                   class="flex-1 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold rounded-lg text-center transition-all">
                  <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this product?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-lg transition-all">
                    <i class="fas fa-trash mr-1"></i>Delete
                  </button>
                </form>
              </div>
              
              <div class="text-xs text-gray-400 text-center mt-3">
                ID: #<?= $p['id'] ?> • <?= date('M d, Y', strtotime($p['created_at'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <?php if (empty($products)): ?>
        <div class="text-center py-12">
          <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
          <p class="text-gray-600 text-lg">No products found</p>
        </div>
      <?php endif; ?>
      
      <!-- Footer -->
      <div class="px-6 py-4 bg-gray-50 border-t-2 border-gray-200">
        <p class="text-sm text-gray-600">
          <i class="fas fa-info-circle mr-1"></i>
          Total: <strong><?= count($products) ?></strong> product(s)
        </p>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
