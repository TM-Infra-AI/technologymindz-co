<?php

require('connect.php');

function clean_input($data) {
    global $connect;
    return mysqli_real_escape_string($connect, trim($data));
}

if (isset($_POST['id_gal']) && isset($_POST['d_gall_img'])) {
    $id_gal = clean_input($_POST['id_gal']);
    $value = intval($_POST['d_gall_img']); // Ensure numeric index

    $query = mysqli_query($connect, "SELECT * FROM digi_card3 WHERE id='$id_gal'");

    if (mysqli_num_rows($query) > 0) {
        $update = mysqli_query($connect, "UPDATE digi_card3 SET d_gall_img$value='', d_gall_name$value='' WHERE id='$id_gal'");

        if ($update) {
            echo "success";
        } else {
            echo "Failed to remove gallery image.";
        }
    } else {
        echo "Gallery ID not found.";
    }
}
// 🔹 Remove Digital Card Image
if (isset($_POST['id']) && isset($_POST['d_pro_img'])) {
    $id = clean_input($_POST['id']);
    $value = clean_input($_POST['d_pro_img']);

    // Check if record exists
    $query = mysqli_query($connect, "SELECT * FROM digi_card2 WHERE id='$id'");
    
    if (mysqli_num_rows($query) > 0) {
        // Securely update
        $remove_img = mysqli_query($connect, "UPDATE digi_card2 SET d_pro_img$value='', d_pro_name$value='' WHERE id='$id'");

        if ($remove_img) {
            echo '<div class="alert success">"' . htmlspecialchars($value) . '" Image and description are removed.</div>';
        }
    } else {
        echo '<div class="alert danger">Image ID is not available</div>';
    }
}

// 🔹 Remove Gallery Image
if (isset($_POST['id_gal']) && isset($_POST['d_gall_img'])) {
    $id_gal = clean_input($_POST['id_gal']);
    $value = clean_input($_POST['d_gall_img']);

    $query = mysqli_query($connect, "SELECT * FROM digi_card3 WHERE id='$id_gal'");

    if (mysqli_num_rows($query) > 0) {
        $update = mysqli_query($connect, "UPDATE digi_card3 SET d_gall_img$value='' WHERE id='$id_gal'");
        
        if ($update) {
            echo '<div class="alert success">"' . htmlspecialchars($value) . '" Image is removed.</div>';
        }
    } else {
        echo '<div class="alert danger">Image ID is not available</div>';
    }
}

// 🔹 Remove Product Information
if (isset($_POST['pro_id']) && isset($_POST['pro_num'])) {
    $pro_id = clean_input($_POST['pro_id']);
    $value = clean_input($_POST['pro_num']);

    $query = mysqli_query($connect, "SELECT * FROM products WHERE id='$pro_id'");

    if (mysqli_num_rows($query) > 0) {
        $update = mysqli_query($connect, "UPDATE products SET pro_name$value='', pro_mrp$value='', pro_price$value='', pro_img$value='' WHERE id='$pro_id'");
        
        if ($update) {
            echo '<div class="alert success">"' . htmlspecialchars($value) . '" Product is removed.</div>';
        }
    } else {
        echo '<div class="alert danger">Product Deleted, refresh the page to see the update.</div>';
    }
}

?>
<?php
require('connect.php'); // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    $id = $_POST['id_gal'];
    $field_num = $_POST['d_gall_img']; // Get field number (1-10)

    // Validate input
    if (!is_numeric($id) || !is_numeric($field_num) || $field_num < 1 || $field_num > 10) {
        echo "Invalid request";
        exit;
    }

    // Construct column names dynamically
    $image_column = "d_pro_img" . $field_num;
    $name_column = "d_pro_name" . $field_num;

    // Update query to remove image and name
    $stmt = $connect->prepare("UPDATE digi_card2 SET $image_column = NULL, $name_column = '' WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Success"; // Response for AJAX
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
?>


