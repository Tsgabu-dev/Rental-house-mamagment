-- Rental House Management database schema
-- Run this script in MySQL/MariaDB to create the application database and tables.

CREATE DATABASE IF NOT EXISTS rental_house_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rental_house_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS houses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    bedrooms INT NOT NULL DEFAULT 1,
    bathrooms INT NOT NULL DEFAULT 1,
    status ENUM('Available','Unavailable') NOT NULL DEFAULT 'Available',
    image_url VARCHAR(500) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional sample data
INSERT INTO users (username, email, password) VALUES
    ('admin', 'admin@example.com', '$2y$10$7S5WkaH4vVlQyvvEDmHdfe1OLjJ12RG5kJjrZB/7jYnjG15F3nAYC');
-- Password for example user: admin123

INSERT INTO houses (title, location, price, bedrooms, bathrooms, status, image_url, description) VALUES
    ('Cozy City Apartment', 'Addis Ababa', 550.00, 2, 1, 'Available', 'https://via.placeholder.com/600x400?text=Cozy+Apartment', 'A neat apartment located near the city center.'),
    ('Spacious Family Home', 'Bole', 1200.00, 4, 3, 'Available', 'https://via.placeholder.com/600x400?text=Family+Home', 'A large house perfect for families, with a garden and parking.');
