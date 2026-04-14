-- SQL Initializer for Ahmed Koshary Store
-- Database: ahmed_koshary_store
-- Updated: Added service_requests table

CREATE DATABASE IF NOT EXISTS ahmed_koshary_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ahmed_koshary_store;

-- 1. Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50) DEFAULT 'token'
) ENGINE=InnoDB;

-- 2. Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    product_type ENUM('hardware', 'software') DEFAULT 'hardware',
    condition_status ENUM('new', 'used', 'grade_a', 'grade_b') DEFAULT 'new',
    software_version VARCHAR(50),
    battery_health INT DEFAULT 100,
    stock_count INT DEFAULT 0,
    main_image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. Product Gallery Images
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_path VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Technical Specifications Table (EAV)
CREATE TABLE IF NOT EXISTS product_specs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    spec_key VARCHAR(100),
    spec_value VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Users Table (Unified for Admins and Users)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,   -- bcrypt hashed
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('super_admin', 'admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. Orders Table (with customer_address and customer_id)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    whatsapp_number VARCHAR(20) NOT NULL,
    customer_id INT DEFAULT NULL,           -- FK to users (nullable for guest orders)
    customer_address TEXT DEFAULT NULL,     -- Delivery address
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    order_notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6.5 Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- 7. Store Settings
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB;

-- 8. Service/Contact Requests
CREATE TABLE IF NOT EXISTS service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    whatsapp_number VARCHAR(20) NOT NULL,
    device_model VARCHAR(255) NOT NULL,
    main_category ENUM('dead', 'hanging', 'upgrade') NOT NULL,
    sub_issue VARCHAR(100) NOT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('new', 'in_progress', 'completed') DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ─── Initial Data ───────────────────────────────────────────────────────────────

INSERT IGNORE INTO categories (name, slug, icon) VALUES 
('هواتف ذكية', 'smartphones', 'smartphone'),
('برمجيات', 'software', 'terminal'),
('إكسسوارات', 'accessories', 'headphones');

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('whatsapp_number', '201234567890'),
('store_name', 'أحمد كشري'),
('currency', 'ج.م');

-- ─── Default Admin Account ──────────────────────────────────────────────────────
-- Password: Admin@2024 (bcrypt hash)
-- Change this immediately after first login!
INSERT IGNORE INTO users (username, password, full_name, phone, role) VALUES 
('admin', '$2y$12$UWGcPEJfN3tzmY1hAv9H/.Y.9zT.0QFOuRn0CRpF3bz7d8JzH/f8m', 'Ahmed Koshary', '01000000000', 'super_admin');

-- ─── Migration: Add missing columns if upgrading from older version ─────────────
-- Safe to run on existing DBs (will silently fail if columns already exist)
SET @add_col1 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='ahmed_koshary_store' AND TABLE_NAME='orders' AND COLUMN_NAME='customer_address');
SET @add_col2 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='ahmed_koshary_store' AND TABLE_NAME='orders' AND COLUMN_NAME='customer_id');

-- Manually run these if upgrading:
-- ALTER TABLE orders ADD COLUMN customer_address TEXT DEFAULT NULL AFTER whatsapp_number;
-- ALTER TABLE orders ADD COLUMN customer_id INT DEFAULT NULL AFTER customer_address;
-- ALTER TABLE orders ADD FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL;
