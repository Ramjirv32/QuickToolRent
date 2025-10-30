<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?php echo APP_NAME; ?> - <?php echo APP_TAGLINE; ?></title>
    <meta name="description" content="<?php echo APP_TAGLINE; ?>">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?: '/' ?>favicon.svg">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#FF6B35',
              secondary: '#004E89',
              accent: '#F77F00',
            },
            fontFamily: {
              'poppins': ['Poppins', 'sans-serif'],
            }
          }
        }
      }
    </script>
    <style>
      body { 
        font-family: 'Poppins', sans-serif; 
      }
      .category-card {
        transition: all 0.3s ease;
      }
      .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      }
      .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }
      .btn-orange {
        background: linear-gradient(135deg, #FF6B35 0%, #F77F00 100%);
      }
      .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }
    </style>
  </head>
  <body class="bg-gray-50 text-gray-900">
    <!-- Top Bar -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white text-sm py-2">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <span class="flex items-center">
            <i class="fas fa-check-circle mr-2 text-green-400"></i>
            Welcome to <?php echo APP_NAME; ?>
          </span>
        </div>
        <div class="flex items-center space-x-4">
          <span class="flex items-center">
            <i class="fas fa-truck mr-2"></i>
            Fast Delivery 30-60 Min
          </span>
          <span class="flex items-center">
            <i class="fas fa-headset mr-2"></i>
            24/7 Support
          </span>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          <!-- Logo -->
          <a href="<?= BASE_URL ?: '/' ?>index.php" class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center shadow-lg">
              <i class="fas fa-tools text-white text-xl"></i>
            </div>
            <div>
              <span class="text-2xl font-bold text-gray-900"><?php echo APP_NAME; ?></span>
              <p class="text-xs text-gray-500">Rent Tools Fast</p>
            </div>
          </a>
          
          <!-- Desktop Navigation -->
          <ul class="hidden lg:flex items-center space-x-1">
            <li class="relative group">
              <button class="px-4 py-2 text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center">
                <i class="fas fa-search mr-2"></i> Browse Tools
                <i class="fas fa-chevron-down ml-2 text-xs"></i>
              </button>
              <!-- Dropdown Menu -->
              <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <div class="py-2">
                  <a href="<?= BASE_URL ?: '/' ?>index.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-th-large w-5 mr-3 text-orange-500"></i>
                    <span class="font-medium">All Products</span>
                  </a>
                  <div class="border-t border-gray-100 my-2"></div>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Power+Tools" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-screwdriver w-5 mr-3 text-blue-500"></i>
                    <span>Power Tools</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Ladders" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-sort-amount-up w-5 mr-3 text-green-500"></i>
                    <span>Ladders</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Hand+Tools" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-hammer w-5 mr-3 text-red-500"></i>
                    <span>Hand Tools</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Garden+Tools" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-seedling w-5 mr-3 text-green-600"></i>
                    <span>Garden Tools</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Electronics" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-plug w-5 mr-3 text-yellow-500"></i>
                    <span>Electronics</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Cleaning+Equipment" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-broom w-5 mr-3 text-purple-500"></i>
                    <span>Cleaning Equipment</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Safety+Gear" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-hard-hat w-5 mr-3 text-orange-500"></i>
                    <span>Safety Gear</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Construction" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-building w-5 mr-3 text-gray-600"></i>
                    <span>Construction</span>
                  </a>
                  <a href="<?= BASE_URL ?: '/' ?>category.php?category=Painting" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    <i class="fas fa-paint-roller w-5 mr-3 text-pink-500"></i>
                    <span>Painting</span>
                  </a>
                </div>
              </div>
            </li>
            <li>
              <a class="px-4 py-2 text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center" href="<?= BASE_URL ?: '/' ?>about.php">
                <i class="fas fa-info-circle mr-2"></i> How It Works
              </a>
            </li>
            <li>
              <a class="px-4 py-2 text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center" href="<?= BASE_URL ?: '/' ?>contact.php">
                <i class="fas fa-envelope mr-2"></i> Contact
              </a>
            </li>
            
            <?php if (!empty($_SESSION['user'])): ?>
              <li>
                <a class="px-4 py-2 text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center" href="<?= BASE_URL ?: '/' ?>my-rentals.php">
                  <i class="fas fa-box mr-2"></i> My Rentals
                </a>
              </li>
              <?php if (($_SESSION['user']['role'] ?? '') === 'owner'): ?>
                <li>
                  <a class="px-4 py-2 text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center" href="<?= BASE_URL ?: '/' ?>owner/my-products.php">
                    <i class="fas fa-warehouse mr-2"></i> My Products
                  </a>
                </li>
              <?php endif; ?>
              <?php if (($_SESSION['user']['email'] ?? '') === 'admin@example.com'): ?>
                <li>
                  <a class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center" href="<?= BASE_URL ?: '/' ?>admin/dashboard.php">
                    <i class="fas fa-shield-alt mr-2"></i> Admin Panel
                  </a>
                </li>
              <?php endif; ?>
              <li class="px-4 py-2 bg-gray-100 rounded-lg flex items-center">
                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></span>
              </li>
              <li>
                <a class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all flex items-center" href="<?= BASE_URL ?: '/' ?>logout.php">
                  <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
              </li>
            <?php else: ?>
              <li>
                <a class="px-4 py-2 text-gray-700 hover:text-orange-600 font-medium transition-colors flex items-center" href="<?= BASE_URL ?: '/' ?>login.php">
                  <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
              </li>
              <li>
                <a class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all flex items-center" href="<?= BASE_URL ?: '/' ?>register.php">
                  <i class="fas fa-user-plus mr-2"></i> Sign Up
                </a>
              </li>
            <?php endif; ?>
            
            <!-- Cart Icon - Only show when logged in -->
            <?php if (!empty($_SESSION['user'])): ?>
            <li>
              <button class="relative p-2 text-gray-700 hover:text-orange-600">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
              </button>
            </li>
            <?php endif; ?>
          </ul>
        </div>
      </nav>
    </header>
    <main>
      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-3">
          <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3">
          <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
      <?php endif; ?>
