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

// Fetch old values from DB
$stmt = $connect->prepare("SELECT user_email, d_f_name, d_l_name, card_id FROM digi_card WHERE id = ?");
$stmt->bind_param("i", $_POST['id']);
$stmt->execute();
$result = $stmt->get_result();
$old_data = $result->fetch_assoc();

$old_f_name = '';
$old_f_name  = '';
if ($old_data) {
    $user_email = $old_data['user_email'];
    $old_f_name = strtolower(trim($old_data['d_f_name']));
    $old_l_name = strtolower(trim($old_data['d_l_name']));
    $user_old_email = $old_data['user_email'];
}

//Form request
$id = $_POST['id'] ?? null;
$user_name = trim($_POST['user_name']);
$first_name = strtolower(trim($_POST['first_name']));
$last_name = strtolower(trim($_POST['last_name']));
$user_email = trim($_POST['user_email']);
$country_code = $_POST['country_code'];
$user_contact = $_POST['phone_number'];
$user_active = trim($_POST['user_active']);
$select_service = trim($_POST['select_service']);
$user_password = $_POST['user_password'] ?? null;
$old_email = $_POST['old_email'] ?? '';
$user_role = $_POST['user_role'] ?? '';



$base_card_id = preg_replace("/[^a-z0-9]/", "_", $first_name . '_' . $last_name);

if ($first_name === $old_f_name && $last_name === $old_l_name  && $id) {
	$card_id = $old_data['card_id']; // Keep same ID
} else {
	// Name changed, generate unique card ID
	$stmt = $connect->prepare("SELECT card_id FROM digi_card WHERE d_f_name = ? AND d_l_name = ?");
	$stmt->bind_param("ss", $first_name, $last_name);
	$stmt->execute();
	$result = $stmt->get_result();

	$existing_ids = [];
	while ($row = $result->fetch_assoc()) {
		$existing_ids[] = strtolower($row['card_id']); // Normalize for comparison
	}

	$suffix = 1;
	$card_id = $base_card_id;

	while (in_array($card_id, $existing_ids)) {
		$card_id = $base_card_id . str_pad($suffix, 2, "0", STR_PAD_LEFT);
		$suffix++;
	}
	$is_mail = true;
}


$success = false;


// Update logic
if ($id) {
    $query = "
        UPDATE digi_card SET 
            user_name = ?, 
            d_f_name = ?, 
            d_l_name = ?, 
            user_email = ?, 
            d_contact = ?, 
            user_contact = ?, 
            d_country_code = ?, 
            w_country_code = ?, 
            user_active = ?, 
            card_id = ?, 
            select_service = ?, 
            user_role = ?";

    $types = "ssssssssssss";
    $params = [
        $user_name,
        $first_name,
        $last_name,
        $user_email,
        $user_contact,
        $user_contact,
        $country_code, 
        $country_code, 
        $user_active,
        $card_id,
        $select_service, 
        $user_role
    ];

    // Conditionally add password
    if (!empty($user_password)) {
        $query .= ", password = ?, user_password = ?";
        $types .= "ss";
        $params[] = $user_password;
        $params[] = $user_password;
    }

    // WHERE clause
    $query .= " WHERE user_email = ?";
    $types .= "s";
    $params[] = $old_email;

    // Prepare and bind
    $stmt = $connect->prepare($query);
    $stmt->bind_param($types, ...$params);

    // Execute and respond
    $success = $stmt->execute();
} else {
    // Insert logic
    // First check if the email already exists
    $checkStmt = mysqli_prepare($connect, "SELECT id FROM digi_card WHERE user_email = ?");
    mysqli_stmt_bind_param($checkStmt, "s", $user_email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Email already exists!"
        ]);
        exit;
    }

    // Proceed to insert
    $query = "INSERT INTO digi_card (
                card_id, 
                user_name, 
                d_f_name, 
                d_l_name, 
                user_email, 
                user_contact, 
                d_contact, 
                d_country_code,
                w_country_code, 
                user_active, 
                select_service, 
                password, 
                user_password, 
                user_role
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param(
            $stmt, 
            "ssssssssssssss", 
            $card_id, 
            $user_name, 
            $first_name, 
            $last_name, 
            $user_email, 
            $user_contact, 
            $user_contact, 
            $country_code, 
            $country_code, 
            $user_active, 
            $select_service, 
            $user_password, 
            $user_password, 
            $user_role
        );

    $success = mysqli_stmt_execute($stmt);
}

if ($success) {
    echo json_encode([
        "status" => "success", 
        "message" => $id ? "User updated successfully." : "User added successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Failed to save user details."
    ]);
}

mysqli_close($connect);