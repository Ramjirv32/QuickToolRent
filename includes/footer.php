</main>
    
    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-gray-300 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          <!-- Brand -->
          <div>
            <div class="flex items-center space-x-3 mb-6">
              <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-tools text-white text-xl"></i>
              </div>
              <div>
                <span class="text-2xl font-bold text-white"><?php echo APP_NAME; ?></span>
                <p class="text-xs text-gray-400">Rent Tools Fast</p>
              </div>
            </div>
            <p class="text-gray-400 text-sm mb-4"><?php echo APP_TAGLINE; ?></p>
            <p class="text-gray-500 text-xs mb-6">Community-driven tool rental platform with fast delivery in 30-60 minutes.</p>
            
            <!-- Social Links -->
            <div class="flex space-x-3">
              <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-orange-600 rounded-full flex items-center justify-center transition-all duration-200">
                <i class="fab fa-facebook-f text-white"></i>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-orange-600 rounded-full flex items-center justify-center transition-all duration-200">
                <i class="fab fa-twitter text-white"></i>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-orange-600 rounded-full flex items-center justify-center transition-all duration-200">
                <i class="fab fa-instagram text-white"></i>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-orange-600 rounded-full flex items-center justify-center transition-all duration-200">
                <i class="fab fa-linkedin-in text-white"></i>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-white font-bold text-lg mb-6 flex items-center">
              <i class="fas fa-link text-orange-500 mr-2"></i>Quick Links
            </h3>
            <ul class="space-y-3 text-sm">
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>index.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  Browse Tools
                </a>
              </li>
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>about.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  How It Works
                </a>
              </li>
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>contact.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  Contact Us
                </a>
              </li>
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>my-rentals.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  My Rentals
                </a>
              </li>
            </ul>
          </div>

          <!-- For Owners -->
          <div>
            <h3 class="text-white font-bold text-lg mb-6 flex items-center">
              <i class="fas fa-store text-orange-500 mr-2"></i>For Owners
            </h3>
            <ul class="space-y-3 text-sm">
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>owner/add-product.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  List Your Tools
                </a>
              </li>
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>owner/my-products.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  Manage Products
                </a>
              </li>
              <li>
                <a class="hover:text-orange-500 transition-colors flex items-center group" href="<?= BASE_URL ?: '/' ?>register.php">
                  <i class="fas fa-chevron-right mr-2 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  Become an Owner
                </a>
              </li>
            </ul>
          </div>

          <!-- Contact Info -->
          <div>
            <h3 class="text-white font-bold text-lg mb-6 flex items-center">
              <i class="fas fa-phone text-orange-500 mr-2"></i>Contact Info
            </h3>
            <ul class="space-y-4 text-sm">
              <li class="flex items-start">
                <i class="fas fa-map-marker-alt text-orange-500 mr-3 mt-1"></i>
                <span>123 Tool Street<br/>City, State 12345</span>
              </li>
              <li class="flex items-center">
                <i class="fas fa-envelope text-orange-500 mr-3"></i>
                <a href="mailto:support@example.com" class="hover:text-orange-500 transition-colors">support@example.com</a>
              </li>
              <li class="flex items-center">
                <i class="fas fa-phone-alt text-orange-500 mr-3"></i>
                <a href="tel:+1234567890" class="hover:text-orange-500 transition-colors">+1 (234) 567-890</a>
              </li>
              <li class="flex items-center">
                <i class="fas fa-clock text-orange-500 mr-3"></i>
                <span>24/7 Available</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 pt-8">
          <div class="flex flex-col md:flex-row items-center justify-between">
            <p class="text-sm text-gray-500 mb-4 md:mb-0">
              &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
            </p>
            <div class="flex items-center space-x-6 text-sm">
              <a href="<?= BASE_URL ?: '/' ?>terms.php" class="text-gray-500 hover:text-orange-500 transition-colors">Terms of Service</a>
              <a href="<?= BASE_URL ?: '/' ?>privacy.php" class="text-gray-500 hover:text-orange-500 transition-colors">Privacy Policy</a>
            </div>
          </div>
          <div class="text-center mt-6 pt-6 border-t border-gray-800">
            <p class="text-xs text-gray-600 flex items-center justify-center">
              <i class="fas fa-code text-orange-500 mr-2"></i>
              Built with PHP • MySQL • Tailwind CSS • Font Awesome
            </p>
          </div>
        </div>
      </div>
    </footer>

    <!-- WhatsApp Chat Button -->
    <a href="https://wa.me/1234567890" target="_blank" class="fixed bottom-6 right-6 w-16 h-16 bg-green-500 hover:bg-green-600 rounded-full shadow-2xl flex items-center justify-center text-white text-2xl z-50 transition-all duration-200 hover:scale-110">
      <i class="fab fa-whatsapp"></i>
    </a>
  </body>
</html>
