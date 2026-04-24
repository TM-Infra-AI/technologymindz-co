<?php
// ✅ No spaces, blank lines, or HTML before this
session_start(); 

// Set timezone
date_default_timezone_set("Asia/Kolkata");

// Database connection
include_once '../../db_config.php';
// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// // Check connection
// if (!$connect) {
//     die("Database connection issue. Please contact support.");
// }

// Set UTF-8 encoding
mysqli_set_charset($connect, "utf8");

// Store current timestamp
$date = date('Y-m-d H:i:s');

?>
<title>Isavgo | Digital Visiting Card</title>

<head>
<!-- ✅ 8. Meta Information -->
<meta name="keywords" content="Digital Visiting Card">
<meta name="description" content="Best digital visiting card online with great design">
<!-- ✅ 9. Required Meta Tags -->
<meta charset="utf-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />
    
    <!-- ✅ 10. Stylesheets & Fonts -->
    <link rel='stylesheet' href='../all.css' crossorigin='anonymous'>
    <link rel="stylesheet" href="../awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- ✅ 11. Favicon -->
    <link rel="icon" href="images/cropped-vcardin-1.png" type="image/png">
    
    <!-- ✅ 12. Custom Styles & Scripts -->
    <link rel="stylesheet" href="css.css">
    <link rel="stylesheet" href="mobile_css.css">
    <script src="master_js.js"></script>
</head>
