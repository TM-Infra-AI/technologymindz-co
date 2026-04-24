<?php
include 'db_connection.php'; // Your database connection file

$product_id = $_GET['product_id'] ?? null;
$response = ['exists' => false];

if ($product_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE id = ? AND image IS NOT NULL");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $response['exists'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
