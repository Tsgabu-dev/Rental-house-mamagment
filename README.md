# Rental House Management System

A simple PHP and MySQL web application for managing rental house listings, user authentication, and property inquiries.

## Features

- User registration and login
- Browse available houses
- View house details
- Submit inquiries for a property
- Admin/owner dashboard for managing listings
- Add, edit, and delete house listings
- Store house images in the uploads folder

## Project Structure

- index.php – Homepage with featured listings
- houses.php – Browse and search properties
- house-details.php – View a property and send an inquiry
- login.php – User login page
- register.php – New user registration
- logout.php – Logout handler
- admin/ – Dashboard and listing management pages
- includes/ – Shared PHP files for DB connection, auth, header, and footer
- uploads/ – Uploaded property images
- assets/ – CSS and JS files

## Requirements

- PHP 7.4+
- MySQL or MariaDB
- XAMPP / WAMP / LAMP

## Setup Instructions

1. Place the project folder in your web server root, for example:
   - `C:/xampp/htdocs/Rental-house-mamagment`

2. Start Apache and MySQL in XAMPP.

3. Create a database named `rental_house_app` in phpMyAdmin.

4. Import the SQL from `database.sql`.

5. Open the app in your browser:
   - `http://localhost/Rental-house-mamagment/`

## Default Login

A default admin account is created when the database is initialized:

- Email: `admin@example.com`
- Password: `admin123`

## Notes

- The app uses MySQLi with PHP.
- Uploaded images are stored in the `uploads` folder.
- If you change the database credentials, update them in `includes/db.php`.

## Future Improvements

- Image upload for add-house page
- Role-based permissions for landlords and admins
- Inquiry management dashboard
- Search and filter enhancements
