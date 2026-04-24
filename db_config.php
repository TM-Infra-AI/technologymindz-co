<?php
// Determine the current environment
$host = $_SERVER['HTTP_HOST'];

if ($host === 'localhost') {
    // Local Development
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'technologyminzmigration';
} elseif ($host === 'staging-tech.technologymindz.com') {
    // Staging
    $db_host = 'localhost';
    $db_user = 'jbugduzc_myvcard_tm';
    $db_pass = 'cZ*t]D&=_J3R';
    $db_name = 'jbugduzc_myvcard_tm';
} else {
    // Production or fallback (edit as needed)
    $db_host = 'localhost';
    $db_user = 'jbugduzc_myvcard_tm_prod';
    $db_pass = 'R+kqz2,b$_i8-RdZ';
    $db_name = 'jbugduzc_myvcard_tm_prod';
}

// Attempt connection
$connect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$connect) {
    if ($host === 'localhost') {
        die("Database Connection Failed: " . mysqli_connect_error());
    } else {
        die("Database connection issue. Please contact support.");
    }
}
?>
