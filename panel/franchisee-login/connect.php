<?php
// Set timezone
date_default_timezone_set("Asia/Kolkata");

// Start session
session_start();
include_once('../../db_config.php');
// Database connection
// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// Check connection
// if (!$connect) {
//     // Show error details only in localhost for debugging
//     if ($_SERVER['HTTP_HOST'] == "localhost") {
//         die("Database Connection Failed: " . mysqli_connect_error());
//     } else {
//         die("Database connection issue. Please contact support.");
//     }
// }

// Set UTF-8 encoding
mysqli_set_charset($connect, "utf8");

// Store current timestamp
$date = date('Y-m-d H:i:s');
?>

<title>My Digital Card</title>

<head>

 <meta name="keywords" content=" Digital Visiting Card">
 
 <meta name="description" content="Best digital visiting card online with great design">
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <link rel='stylesheet' href='../all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
  
  <link rel="stylesheet" href="../awesome.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
 <meta      name='viewport'      content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />
<link rel="fav-icon" href="images/logo.png" type="image/png">
<link rel="stylesheet" href="css.css" >
<link rel="stylesheet" href="mobile_css.css" >
<script src="master_js.js"></script>



</head>

