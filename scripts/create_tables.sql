/*
  Tool Rental Platform - MySQL Schema
  Run this first to create the database tables.
*/

-- Create database (optional - adjust name as needed)
-- CREATE DATABASE tool_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE tool_rental;

-- Users (owners and borrowers)
-- Drop existing tables if re-running locally (optional; comment out in production)
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS rentals;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','owner','borrower') NOT NULL DEFAULT 'borrower',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products (tools)
CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_id INT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT,
  image_url VARCHAR(500),
  category VARCHAR(80) DEFAULT 'General',
  price_per_hour DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  price_per_day DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('available', 'unavailable') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Rentals (bookings/orders)
CREATE TABLE IF NOT EXISTS rentals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  borrower_id INT UNSIGNED NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  payment_method ENUM('card','upi','wallet','cod') NOT NULL,
  status ENUM('pending','paid','delivered','completed','cancelled') NOT NULL DEFAULT 'pending',
  delivery_eta_minutes INT NOT NULL DEFAULT 45,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (borrower_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Simple index helpers
CREATE INDEX idx_products_owner ON products(owner_id);
CREATE INDEX idx_rentals_borrower ON rentals(borrower_id);
CREATE INDEX idx_rentals_product ON rentals(product_id);
