<?php
// config.php

// Automatically determine base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Base path (use empty string if script is directly under domain)
$basePath = ''; // e.g., '/project-folder' if deployed in a subdirectory

if ($host === 'localhost') {
    $basePath = '/technologymindz'; // your local subfolder name
}

define('BASE_URL', $protocol . $host . $basePath);

?>
