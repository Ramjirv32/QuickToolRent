#!/bin/bash

# QuickToolRent Database Setup Script
# This script will create the database and tables for your project

echo "=========================================="
echo "QuickToolRent Database Setup"
echo "=========================================="
echo ""

# MySQL credentials
DB_USER="root"
DB_PASS="Ramji@2311"
DB_NAME="myapp"

echo "Step 1: Creating database '$DB_NAME'..."
mysql -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✓ Database created successfully!"
else
    echo "⚠ Trying with sudo..."
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    if [ $? -eq 0 ]; then
        echo "✓ Database created successfully with sudo!"
        # Set password for root user
        sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PASS'; FLUSH PRIVILEGES;"
        echo "✓ Root password configured!"
    else
        echo "✗ Failed to create database. Please create it manually."
        exit 1
    fi
fi

echo ""
echo "Step 2: Creating tables..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < scripts/create_tables.sql 2>/dev/null

if [ $? -ne 0 ]; then
    sudo mysql "$DB_NAME" < scripts/create_tables.sql
fi

if [ $? -eq 0 ]; then
    echo "✓ Tables created successfully!"
else
    echo "✗ Failed to create tables. Please check the error messages."
    exit 1
fi

echo ""
echo "Step 3: Seeding initial data..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < scripts/seed.sql 2>/dev/null

if [ $? -ne 0 ]; then
    sudo mysql "$DB_NAME" < scripts/seed.sql
fi

if [ $? -eq 0 ]; then
    echo "✓ Data seeded successfully!"
else
    echo "⚠ Warning: Failed to seed data. You can add data manually later."
fi

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Database: $DB_NAME"
echo "Tables: users, products, rentals"
echo ""
echo "Test Accounts:"
echo "  Admin:    admin@example.com / admin123"
echo "  Owner:    alice.owner@example.com / password"
echo "  Borrower: charlie.borrower@example.com / password"
echo ""
echo "Next Steps:"
echo "  1. Start PHP server: php -S localhost:8000"
echo "  2. Open browser: http://localhost:8000"
echo "  3. Login with one of the test accounts"
echo ""
