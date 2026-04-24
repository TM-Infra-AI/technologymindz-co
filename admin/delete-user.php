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

$id = $_POST['id'];
$getEmailQuery = "SELECT user_email FROM digi_card WHERE id = '$id'";
$getEmailResult = mysqli_query($connect, $getEmailQuery);

if ($getEmailResult && mysqli_num_rows($getEmailResult) > 0) {
    $row = mysqli_fetch_assoc($getEmailResult);
    $user_email = $row['user_email'];

    //  Delete user from digi_card
    $deleteUserQuery = "DELETE FROM digi_card WHERE id='$id'";
    $deleteUserResult = mysqli_query($connect, $deleteUserQuery);

    if ($deleteUserResult) {
        // Delete from digi_card using user_email
        $deleteCardQuery = "DELETE FROM digi_card WHERE user_email = '$user_email'";
        mysqli_query($connect, $deleteCardQuery); // Optional: check result if needed

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete user."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
}
?>