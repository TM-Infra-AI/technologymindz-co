<?php
// Start output buffering and session
ob_start();
session_start();

// Destroy session and unset variables
session_unset();
session_destroy();

require_once '../../includes/config.php';

// Redirect to homepage
header("Location: https://staging-tech.technologymindz.com/index.php");
exit();
?>