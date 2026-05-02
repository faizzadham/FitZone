-- Gym Membership Management System
-- Database Creation Script

CREATE DATABASE IF NOT EXISTS gym_db;
USE gym_db;

-- Users table (authentication)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Membership packages
CREATE TABLE IF NOT EXISTS membership_packages (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in months',
    price DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- Members table
CREATE TABLE IF NOT EXISTS members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    gender ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
    join_date DATE NOT NULL,
    package_id INT DEFAULT NULL,
    status ENUM('active', 'expired') NOT NULL DEFAULT 'active',
    expiry_date DATE DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES membership_packages(package_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Cash', 'Card', 'Online') NOT NULL DEFAULT 'Cash',
    payment_status ENUM('Paid', 'Pending', 'Cancelled') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================
-- Seed Data
-- =====================

-- Default admin account (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@gym.com', '$2y$10$s4Lq2O88.yOwVj2JReqiXeBBs5Wr8kv6whXCJAjWCz8veCEJ.LZ9C', 'admin');

-- Default membership packages
INSERT INTO membership_packages (package_name, duration, price) VALUES
('Basic', 1, 100.00),
('Standard', 3, 250.00),
('Premium', 6, 450.00);
