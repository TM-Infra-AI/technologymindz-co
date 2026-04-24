<?php
include_once('../../db_config.php');
// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// if (!$connect) {
//     die("Database connection failed: " . mysqli_connect_error());
// }

function getUserRole($email) {
    global $connect;
    $query = "SELECT user_role FROM digi_card WHERE user_email = '$email' LIMIT 1";
    $result = mysqli_query($connect, $query);
    $user = mysqli_fetch_assoc($result);

    return $user ? $user['user_role'] : null;
}
?>
