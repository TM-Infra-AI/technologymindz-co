 # Core PHP Project


---

## Introduction
This is a **Core PHP** project designed to be run in a local development environment. The project follows a structured approach with separate folders for `panel` and `admin` sections, ensuring efficient database connectivity and management.

## Requirements
Ensure your local machine has the following installed:
- PHP (Version 7.4 or higher recommended)
- MySQL Database
- Apache Server (XAMPP, WAMP, or any local server)
- Composer (for managing dependencies)
- Git (for version control)

## Setup Project Locally
Follow these steps to set up the project in your local environment:

### 1. Clone the Repository
Clone the project inside your local server folder:

- **For WAMP**: `Wamp64/www`
- **For XAMPP**: `htdocs`

Run the following command:
```sh
git clone <project-url>
```

### 2. Update `.htaccess`
Modify or comment out `.htaccess` rules as per your local server configuration to avoid errors.

### 3. Install Dependencies
Ensure that Composer is installed, then navigate to the project directory and run:
```sh
composer install
```
This will install all required dependencies.

### 4. Configure Database
Update the database connection settings in the configuration file (e.g., `config.php` or `.env`) with your local database credentials:
```php
$host = "localhost";
$username = "root";
$password = "";
$database = "your_database_name";
```
Make sure the database is created and properly configured in MySQL.

### 5. Folder Structure
The project has a structured approach with separate folders handling different aspects:
```
/project-root
│-- admin/         # Admin panel files
│-- panel/         # User panel files
│-- assets/        # CSS, JS, images, etc.
│-- includes/      # Common functions and configurations
│-- database/      # Database connection files
│-- index.php      # Main entry point
│-- .htaccess      # URL rewriting rules
│-- composer.json  # Composer dependencies
```
Ensure that the `panel/` and `admin/` folders have the correct database connection settings.

## Running the Project
1. Start your local server (XAMPP, WAMP, etc.).
2. Import the SQL database file (`database.sql`) into your local MySQL.
3. Open a browser and navigate to:
   - User Panel: `http://localhost/project-folder/panel/login/login.php`
   - Admin Panel: `http://localhost/project-folder/admin/users.php`

## Troubleshooting
- If you face a **500 Internal Server Error**, check your `.htaccess` file.
- If you get a **Database Connection Error**, ensure your MySQL service is running and credentials are correct.
- If Composer packages are missing, run:
  ```sh
  composer install
  ```
- If any assets (CSS, JS) are not loading, check file paths and ensure Apache's `mod_rewrite` is enabled.


---


