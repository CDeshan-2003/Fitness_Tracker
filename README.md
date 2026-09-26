# Pulse Fitness Tracker

A medium-level fitness tracking app built with PHP, MySQL, Bootstrap, and vanilla JavaScript for WAMP.

## Requirements

- WAMP Server with Apache and MySQL/MariaDB running
- PHP 7.4 or newer with PDO MySQL enabled
- A modern browser with an internet connection for Bootstrap, Chart.js, icons, the Google Font CDN, and the Unsplash hero image

## Setup with WAMP

1. Place this project folder at `C:\wamp64\www\Fitness_Tracker`.
2. Start the WAMP services and confirm the WAMP tray icon is green.
3. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
4. Import `database.sql`. It creates and selects the `fitness_tracker` database and its tables.
5. Confirm `includes/db.php` matches your local MySQL settings. It is configured for host `127.0.0.1`, database `fitness_tracker`, user `root`, and a blank password.
6. Visit `http://localhost/Fitness_Tracker/`.

The default WAMP root account with no password is suitable only for a local development environment. Set a password and update `includes/db.php` before exposing the app to a network.

## Included features

- Registration with server-side validation and `password_hash()` password storage
- Login with `password_verify()`, session ID regeneration, and logout
- Session-protected dashboard with per-user workout logging and summary totals
- Activity breakdown chart and recent workout list
- Contact form saved to the `messages` table
- CSRF tokens for state-changing forms and prepared PDO statements for database queries
- Responsive Bootstrap layout with browser-side form validation

## Project structure

```text
Fitness Tracker/
├── css/style.css
├── js/app.js
├── images/
├── includes/db.php
├── includes/functions.php
├── auth/register.php
├── auth/login.php
├── auth/logout.php
├── contact.php
├── index.php
├── dashboard.php
├── database.sql
└── README.md
```
