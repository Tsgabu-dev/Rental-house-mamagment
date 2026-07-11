<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'rental_house_app';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

function tableExists($tableName): bool
{
    global $conn;
    $result = $conn->query("SHOW TABLES LIKE '" . mysqli_real_escape_string($conn, $tableName) . "'");
    return $result && $result->num_rows > 0;
}

function columnExists($tableName, $columnName): bool
{
    global $conn;
    $result = $conn->query("SHOW COLUMNS FROM `" . mysqli_real_escape_string($conn, $tableName) . "` LIKE '" . mysqli_real_escape_string($conn, $columnName) . "'");
    return $result && $result->num_rows > 0;
}

function ensureDatabaseSchema(): void
{
    global $conn;

    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(255) NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) DEFAULT NULL,
        role ENUM('renter','landlord','admin') NOT NULL DEFAULT 'landlord',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if (!columnExists('users', 'email')) {
        $conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL UNIQUE AFTER username");
    }
    if (!columnExists('users', 'phone_number')) {
        $conn->query("ALTER TABLE users ADD COLUMN phone_number VARCHAR(20) DEFAULT NULL AFTER email");
    }
    if (!columnExists('users', 'role')) {
        $conn->query("ALTER TABLE users ADD COLUMN role ENUM('renter','landlord','admin') NOT NULL DEFAULT 'landlord' AFTER phone_number");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $conn->query("CREATE TABLE IF NOT EXISTS houses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_id INT NOT NULL DEFAULT 0,
        category_id INT DEFAULT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        location VARCHAR(255) NOT NULL,
        price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        bedrooms INT NOT NULL DEFAULT 1,
        bathrooms INT NOT NULL DEFAULT 1,
        status ENUM('Available','Rented','Unavailable') NOT NULL DEFAULT 'Available',
        image_url VARCHAR(500) DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if (!columnExists('houses', 'owner_id')) {
        $conn->query("ALTER TABLE houses ADD COLUMN owner_id INT NOT NULL DEFAULT 0 AFTER id");
    }
    if (!columnExists('houses', 'category_id')) {
        $conn->query("ALTER TABLE houses ADD COLUMN category_id INT DEFAULT NULL AFTER owner_id");
    }
    if (!columnExists('houses', 'description')) {
        $conn->query("ALTER TABLE houses ADD COLUMN description TEXT DEFAULT NULL AFTER title");
    }
    if (!columnExists('houses', 'image_url')) {
        $conn->query("ALTER TABLE houses ADD COLUMN image_url VARCHAR(500) DEFAULT NULL AFTER status");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS house_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        house_id INT NOT NULL,
        image_url VARCHAR(500) NOT NULL,
        is_primary TINYINT(1) DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_house_images_house FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $conn->query("CREATE TABLE IF NOT EXISTS inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        house_id INT NOT NULL,
        user_id INT DEFAULT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        message TEXT NOT NULL,
        status ENUM('Pending','Contacted','Closed') NOT NULL DEFAULT 'Pending',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_inquiries_house FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
        CONSTRAINT fk_inquiries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if ($conn->query("SELECT COUNT(*) AS count FROM categories")->fetch_assoc()['count'] == 0) {
        $conn->query("INSERT INTO categories (name) VALUES ('Apartment'), ('Villa'), ('Studio'), ('Condo')");
    }

    $userCount = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
    if ($userCount == 0) {
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@example.com', '$passwordHash', 'admin')");
    }
}

ensureDatabaseSchema();
