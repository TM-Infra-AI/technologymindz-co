<?php
session_start();
include 'connect.php'; // Ensure this file has no unintended output

header("Content-Type: text/plain"); // Ensures the response is plain text, not HTML

if (!isset($_POST['email'])) {
    die("error"); // Send an explicit error if email is missing
}

$email = trim($_POST['email']);
$user_id = $_SESSION['card_id_inprocess'] ?? 0; // Ensure session variable is set

// Prevent extra output by disabling error display in response
ini_set('display_errors', 0);
error_reporting(0);

$stmt = $connect->prepare("SELECT id FROM digi_card WHERE user_email = ? AND id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$stmt->store_result();

echo ($stmt->num_rows > 0) ? "exists" : "available";

$stmt->close();
// $connect->close();
?>
