# 🛠️ QuickToolRent - Tool Rental Platform

A fast, community-driven tool rental platform with 30-60 minute delivery. Built with PHP, MySQL, and Tailwind CSS.

---

## 📋 Project Overview

**QuickToolRent** connects tool owners with borrowers for quick, convenient rentals.

### Key Features:
- ✅ Multi-role system (Admin, Owner, Borrower)
- ✅ Product listing and search functionality
- ✅ Rental booking with multiple payment methods
- ✅ Admin dashboard with analytics and charts
- ✅ CSRF protection and secure authentication
- ✅ Fast delivery tracking (30-60 minutes)

---

## 🗂️ Project Structure

```
WEBTECH/
├── index.php              # Homepage (product browsing)
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # User logout
├── rent-product.php       # Product rental form
├── rent-confirm.php       # Rental confirmation handler
├── my-rentals.php         # User's rental history
├── thankyou.php           # Order confirmation page
├── about.php              # About page
├── contact.php            # Contact page
├── terms.php              # Terms of service
├── privacy.php            # Privacy policy
│
├── admin/
│   ├── index.php          # Admin dashboard with charts
│   └── login.php          # Admin login
│
├── owner/
│   ├── add-product.php    # Add new products
│   └── my-products.php    # Manage products
│
├── includes/
│   ├── config.php         # Database configuration
│   ├── db.php             # Database connection
│   ├── auth.php           # Authentication functions
│   ├── csrf.php           # CSRF protection
│   ├── helpers.php        # Helper functions
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
│
└── scripts/
    ├── create_tables.sql  # Database schema
    └── seed.sql           # Sample data
```

---

## 🚀 Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

### 1. Database Configuration

**Already configured with your credentials:**
```php
DB_HOST: localhost
DB_NAME: myapp
DB_USER: root
DB_PASS: Ramji@2311
```

### 2. Database Setup (✅ COMPLETED)

The database has been created and seeded with sample data using `setup-database.sh`.

**Tables created:**
- `users` - User accounts (admin, owner, borrower)
- `products` - Tool listings
- `rentals` - Rental/booking records

### 3. Start the Server

```bash
# Start PHP development server
php -S localhost:8000

# Or use the included script
./start-server.sh
```

### 4. Access the Application

Open your browser and navigate to:
```
http://localhost:8000
```

---

## 👥 Test Accounts

### Admin Account
- **Email:** admin@example.com
- **Password:** admin123
- **Access:** http://localhost:8000/admin/index.php

### Owner Accounts
- **Email:** alice.owner@example.com
- **Password:** password
- **Features:** Add products, manage inventory

- **Email:** bob.owner@example.com
- **Password:** password

### Borrower Accounts
- **Email:** charlie.borrower@example.com
- **Password:** password
- **Features:** Browse products, rent tools, view rentals

- **Email:** dana.borrower@example.com
- **Password:** password

---

## 🎯 User Flows

### For Borrowers:
1. Register/Login → Browse products → Select a tool
2. Choose rental duration & payment method
3. Confirm booking → Receive delivery ETA
4. View rentals in "My Rentals"

### For Owners:
1. Register as Owner → Login
2. Go to "Owner" section → Add Product
3. Set pricing (hourly/daily rates)
4. Manage product availability (toggle available/unavailable)

### For Admins:
1. Login at `/admin/login.php`
2. View dashboard with:
   - Total revenue, rentals, products, users
   - Revenue charts (last 7 days)
   - Rentals by category
   - Daily rental counts

---

## 🔒 Security Features

- **CSRF Protection:** All forms protected with CSRF tokens
- **Password Hashing:** bcrypt for secure password storage
- **Session Management:** Secure session-based authentication
- **SQL Injection Prevention:** Prepared statements throughout
- **XSS Protection:** HTML escaping for user input

---

## 💰 Pricing Configuration

Configured in `includes/config.php`:

```php
MIN_PRICE_PER_HOUR = $1.00
MAX_PRICE_PER_HOUR = $50.00
MIN_PRICE_PER_DAY = $5.00
MAX_PRICE_PER_DAY = $250.00
```

---

## 📊 Database Schema

### users
```sql
- id (PRIMARY KEY)
- name
- email (UNIQUE)
- phone
- password_hash
- role (admin/owner/borrower)
- created_at
```

### products
```sql
- id (PRIMARY KEY)
- owner_id (FOREIGN KEY → users)
- name
- description
- image_url
- category
- price_per_hour
- price_per_day
- status (available/unavailable)
- created_at
```

### rentals
```sql
- id (PRIMARY KEY)
- product_id (FOREIGN KEY → products)
- borrower_id (FOREIGN KEY → users)
- start_time
- end_time
- total_amount
- payment_method (card/upi/wallet/cod)
- status (pending/paid/delivered/completed/cancelled)
- delivery_eta_minutes
- created_at
```

---

## 🛠️ Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, Tailwind CSS (CDN)
- **Charts:** Chart.js (Admin dashboard)
- **Architecture:** MVC-inspired structure

---

## 📝 Key Functions

### Authentication (`includes/auth.php`)
- `current_user()` - Get logged-in user
- `require_login()` - Redirect if not logged in
- `is_owner()` - Check if user is owner
- `is_admin()` - Check if user is admin
- `login_user()` - Authenticate user
- `logout_user()` - End session

### CSRF Protection (`includes/csrf.php`)
- `csrf_token()` - Generate CSRF token
- `csrf_field()` - Create hidden input field
- `csrf_validate()` - Verify CSRF token

### Helpers (`includes/helpers.php`)
- `e($value)` - HTML escape
- `money($value)` - Format currency

---

## 🔧 Configuration

Edit `includes/config.php` to customize:

```php
define('APP_NAME', 'QuickToolRent');
define('APP_TAGLINE', 'Rent tools fast. Delivered in 30–60 minutes.');
define('BASE_URL', ''); // For subdirectory installations
```

---

## 📦 Sample Data

The database includes:
- 5 test users (1 admin, 2 owners, 2 borrowers)
- 3 sample products (Cordless Drill, Circular Saw, Ladder)
- 4 sample rentals (for chart testing)

---

## 🚨 Troubleshooting

### Database Connection Error
```bash
# Check MySQL is running
sudo systemctl status mysql

# Verify credentials in includes/config.php
```

### Permission Issues
```bash
# Make scripts executable
chmod +x setup-database.sh
```

### Port Already in Use
```bash
# Use different port
php -S localhost:8080
```

---

## 📈 Future Enhancements

- [ ] Image upload for products
- [ ] Rating and review system
- [ ] Email notifications
- [ ] Advanced search with filters
- [ ] Mobile responsive improvements
- [ ] Payment gateway integration
- [ ] Real-time delivery tracking

---

## 📄 License

Educational project for Web Technology course.

---

## 👨‍💻 Development

**Server is running at:** http://localhost:8000

**Stop server:** Press `Ctrl+C` in terminal

**Database management:** Use phpMyAdmin or MySQL Workbench

---

## 📞 Support

For issues or questions, check the code comments or contact the development team.

**Happy Renting! 🛠️**
# QuickToolRent
