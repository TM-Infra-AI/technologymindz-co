<?php
include_once('../db_config.php');
// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// if (!$connect) {
//     if ($_SERVER['HTTP_HOST'] == "localhost") {
//         die("Database Connection Failed: " . mysqli_connect_error());
//     } else {
//         die("Database connection issue. Please contact support.");
//     }
// }

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid request."
    ]);
    exit;
}

$query = "SELECT 
        id, 
        d_f_name, 
        d_l_name, 
        user_email, 
        user_password, 
        user_contact, 
        user_name, 
        user_active, 
        select_service, 
        d_country_code, 
        user_role 
    FROM 
        digi_card 
    WHERE id = '$id'";
$result = mysqli_query($connect, $query);
$data = mysqli_fetch_assoc($result);

if ($data) {
    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
}
?>
