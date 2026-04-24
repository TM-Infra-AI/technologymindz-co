<?php
include_once('db_config.php');
// Database connection
// $connect = mysqli_connect("localhost", "vanyaxln_root", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// Check connection
// if (!$connect) {
//     if ($_SERVER['HTTP_HOST'] == "localhost") {
//         die("Database Connection Failed: " . mysqli_connect_error());
//     } else {
//         die("Database connection issue. Please contact support.");
//     }
// }

// Sanitize the GET parameter
$id = isset($_GET['id']) ? mysqli_real_escape_string($connect, $_GET['id']) : '';

if (empty($id)) {
    die("Invalid ID provided.");
}

// Fetch data securely
$query = mysqli_query($connect, "SELECT * FROM digi_card WHERE id='$id'");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    die("No data found for the given ID.");
}

// Prevent output before headers
ob_clean();

// Set headers
header('Content-Type: text/x-vcard');
header('Content-Disposition: attachment; filename="contact.vcf"');

// Process profile photo (BLOB)
$photo_data = "";
if (!empty($row['d_logo'])) {
    $base64_image = base64_encode($row['d_logo']); // Convert BLOB to Base64
    $photo_data = "PHOTO;ENCODING=b;TYPE=JPEG:$base64_image"; // Assuming JPEG format
}

?>
BEGIN:VCARD
VERSION:3.0
PRODID:-//Apple Inc.//Mac OS X 10.14.1//EN
N:<?php echo $row['d_l_name']; ?>;<?php echo $row['d_f_name']; ?>;;;
FN:<?php echo $row['d_f_name'] . ' ' . $row['d_l_name']; ?> 
ORG:<?php echo $row['d_comp_name']; ?>;
<!-- TITLE:<?php //echo $row['d_comp_name']; ?>  -->
EMAIL;type=INTERNET;type=WORK;type=pref:<?php echo $row['user_email']; ?> 
TEL;type=WORK;type=pref:<?php echo $row['d_contact']; ?> 
TEL;type=CELL:<?php echo $row['d_contact2']; ?> 
TEL;type=HOME:<?php echo $row['d_whatsapp']; ?> 
URL;type=pref:https://<?php echo $_SERVER['HTTP_HOST'] . '/' . $row['card_id']; ?> 
<?php if (!empty($photo_data)) echo $photo_data . "\n"; ?>
END:VCARD
<?php
exit;
?>
