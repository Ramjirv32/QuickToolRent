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

// Handle POST actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
  $post_action = $_POST['action'] ?? '';
  
  if ($post_action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'borrower';
    
    if ($name && $email && $password) {
      $password_hash = password_hash($password, PASSWORD_BCRYPT);
      try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $password_hash, $role]);
        $message = "User created successfully!";
        $action = 'list';
      } catch (Exception $e) {
        $error = "Error creating user: " . $e->getMessage();
      }
    } else {
      $error = "Please fill all required fields.";
    }
  }
  
  elseif ($post_action === 'update') {
    $update_id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'borrower';
    $password = $_POST['password'] ?? '';
    
    if ($update_id && $name && $email) {
      try {
        if ($password) {
          $password_hash = password_hash($password, PASSWORD_BCRYPT);
          $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password_hash = ?, role = ? WHERE id = ?");
          $stmt->execute([$name, $email, $phone, $password_hash, $role, $update_id]);
        } else {
          $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
          $stmt->execute([$name, $email, $phone, $role, $update_id]);
        }
        $message = "User updated successfully!";
        $action = 'list';
      } catch (Exception $e) {
        $error = "Error updating user: " . $e->getMessage();
      }
    } else {
      $error = "Please fill all required fields.";
    }
  }
  
  elseif ($post_action === 'delete') {
    $delete_id = (int)($_POST['id'] ?? 0);
    if ($delete_id) {
      try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = "User deleted successfully!";
      } catch (Exception $e) {
        $error = "Error deleting user: " . $e->getMessage();
      }
    }
  }
}

// Get user for edit
$user = null;
if ($action === 'edit' && $id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$id]);
  $user = $stmt->fetch();
  if (!$user) {
    $error = "User not found.";
    $action = 'list';
  }
}

// Get all users for list
$search = trim($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($search) {
  $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
  $search_term = '%' . $search . '%';
  $params[] = $search_term;
  $params[] = $search_term;
  $params[] = $search_term;
}

if ($role_filter) {
  $sql .= " AND role = ?";
  $params[] = $role_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto">
  <!-- Header -->
  <div class="mb-8">
    <nav class="text-sm text-gray-600 mb-4">
      <a href="<?= BASE_URL ?: '/' ?>admin/dashboard.php" class="hover:text-blue-600">
        <i class="fas fa-shield-alt mr-1"></i>Admin Dashboard
      </a>
      <span class="mx-2">/</span>
      <span class="text-gray-900 font-semibold">Manage Users</span>
    </nav>
    
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-extrabold mb-2">
            <i class="fas fa-users mr-3"></i>User Management
          </h1>
          <p class="text-blue-100">Create, Read, Update, and Delete user accounts</p>
        </div>
        <?php if ($action === 'list'): ?>
          <a href="?action=create" class="px-6 py-3 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>Add New User
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
        <i class="fas fa-<?= $action === 'create' ? 'plus' : 'edit' ?> text-blue-600 mr-2"></i>
        <?= $action === 'create' ? 'Create New User' : 'Edit User' ?>
      </h2>
      
      <form method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
        <?php if ($action === 'edit'): ?>
          <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <?php endif; ?>
        
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-user mr-1"></i>Full Name *
            </label>
            <input type="text" name="name" required value="<?= $action === 'edit' ? htmlspecialchars($user['name']) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-envelope mr-1"></i>Email Address *
            </label>
            <input type="email" name="email" required value="<?= $action === 'edit' ? htmlspecialchars($user['email']) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-phone mr-1"></i>Phone Number
            </label>
            <input type="text" name="phone" value="<?= $action === 'edit' ? htmlspecialchars($user['phone']) : '' ?>" 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-user-tag mr-1"></i>Role *
            </label>
            <select name="role" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none">
              <option value="borrower" <?= ($action === 'edit' && $user['role'] === 'borrower') ? 'selected' : '' ?>>Borrower</option>
              <option value="owner" <?= ($action === 'edit' && $user['role'] === 'owner') ? 'selected' : '' ?>>Owner</option>
              <option value="admin" <?= ($action === 'edit' && $user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-lock mr-1"></i>Password <?= $action === 'edit' ? '(Leave blank to keep current)' : '*' ?>
            </label>
            <input type="password" name="password" <?= $action === 'create' ? 'required' : '' ?> 
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none"
                   placeholder="<?= $action === 'edit' ? 'Enter new password to change' : 'Enter password' ?>">
          </div>
        </div>
        
        <div class="flex gap-4 pt-4">
          <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all">
            <i class="fas fa-save mr-2"></i><?= $action === 'create' ? 'Create User' : 'Update User' ?>
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
                   placeholder="Search by name, email, or phone..." 
                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
          </div>
          <select name="role" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
            <option value="">All Roles</option>
            <option value="borrower" <?= $role_filter === 'borrower' ? 'selected' : '' ?>>Borrower</option>
            <option value="owner" <?= $role_filter === 'owner' ? 'selected' : '' ?>>Owner</option>
            <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
          <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-all">
            <i class="fas fa-search mr-2"></i>Search
          </button>
          <?php if ($search || $role_filter): ?>
            <a href="?action=list" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition-all">
              <i class="fas fa-times mr-2"></i>Clear
            </a>
          <?php endif; ?>
        </form>
      </div>
      
      <!-- Users Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Phone</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Created</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($users as $u): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $u['id'] ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                      <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="font-semibold text-gray-900"><?= htmlspecialchars($u['name']) ?></div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($u['email']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($u['phone']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        bg-<?= $u['role'] === 'admin' ? 'red' : ($u['role'] === 'owner' ? 'purple' : 'blue') ?>-100 
                        text-<?= $u['role'] === 'admin' ? 'red' : ($u['role'] === 'owner' ? 'purple' : 'blue') ?>-800">
                    <?= strtoupper(htmlspecialchars($u['role'])) ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <?= date('M d, Y', strtotime($u['created_at'])) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <a href="?action=edit&id=<?= $u['id'] ?>" 
                       class="px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $u['id'] ?>">
                      <button type="submit" class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        
        <?php if (empty($users)): ?>
          <div class="text-center py-12">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 text-lg">No users found</p>
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Footer -->
      <div class="px-6 py-4 bg-gray-50 border-t-2 border-gray-200">
        <p class="text-sm text-gray-600">
          <i class="fas fa-info-circle mr-1"></i>
          Total: <strong><?= count($users) ?></strong> user(s)
        </p>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
