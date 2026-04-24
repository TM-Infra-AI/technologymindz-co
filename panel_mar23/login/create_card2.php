<?php
session_start();
ob_start(); // Start output buffering to prevent header issues
require('connect.php');
require('header.php');

// Ensure user session is set
if (!isset($_SESSION['user_email'])) {
    die('Session not set. Please log in.');
}

// Validate the card ID
if (!isset($_SESSION['card_id_inprocess'])) {
    header("Location: index.php");
    exit;
}

$stmt = $connect->prepare("SELECT * FROM digi_card WHERE id = ? AND user_email = ?");
$stmt->bind_param("is", $_SESSION['card_id_inprocess'], $_SESSION['user_email']);
$stmt->execute();
$query = $stmt->get_result();
$row = $query->fetch_assoc();

if (!$row) {
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function clickFocus() {
            document.getElementById("clickMeImage").click();
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById("showPreviewLogo").src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</head>
<body>

<div class="main3">
    <div class="navigator_up">
        <a href="select_theme.php"><div class="nav_cont"><i class="fa fa-map"></i> Select Theme</div></a>
        <a href="create_card2.php"><div class="nav_cont active"><i class="fa fa-bank"></i> Company Details</div></a>
        <a href="create_card3.php"><div class="nav_cont"><i class="fa fa-facebook"></i> Social Links</div></a>
        <a href="create_card4.php"><div class="nav_cont"><i class="fa fa-ticket"></i> Products & Services</div></a>
        <a href="create_card5.php"><div class="nav_cont"><i class="fa fa-image"></i> Image Gallery</div></a>
        <a href="preview_page.php"><div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div></a>
    </div>

    <div class="btn_holder">
        <a href="select_theme.php"><div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div></a>
        <a href="create_card3.php"><div class="skip_btn">Skip <i class="fa fa-chevron-circle-right"></i></div></a>
    </div>

    <h1>Company Details</h1>

    <form id="card_form" action="" method="POST" enctype="multipart/form-data">
        <img src="<?php echo !empty($row['d_logo']) ? 'data:image/*;base64,'.base64_encode($row['d_logo']) : 'images/logo.png'; ?>" alt="Select image" id="showPreviewLogo" onclick="clickFocus()">
        <div class="input_box">
            <p>Company Logo (Max 250KB) *</p>
            <input type="file" name="d_logo" id="clickMeImage" onchange="readURL(this);" accept="image/*">
        </div>

        <h3>Personal Details</h3>
        <div class="input_box"><p>* First Name</p><input type="text" name="d_f_name" required></div>
        <div class="input_box"><p>Last Name</p><input type="text" name="d_l_name"></div>
        <div class="input_box"><p>* Designation</p><input type="text" name="d_position" required></div>
        <div class="input_box"><p>* Phone No</p><input type="text" name="d_contact" required></div>
        <div class="input_box"><p>WhatsApp No</p><input type="text" name="d_whatsapp"></div>
        <div class="input_box"><p>* Address</p><textarea name="d_address" required></textarea></div>
        <div class="input_box"><p>* Email</p><input type="email" name="d_email" required></div>
        <div class="input_box"><p>Website</p><input type="text" name="d_website"></div>
        <div class="input_box"><p>* Company Established</p><input type="text" name="d_comp_est_date" required></div>
        <div class="input_box"><p>* About Us</p><textarea name="d_about_us" required></textarea></div>
        
        <input type="submit" name="process2" value="Next">
    </form>

    <?php
    if (isset($_POST['process2'])) {
        function compressImage($source, $destination, $quality) {
            $imageInfo = getimagesize($source);
            $mime = $imageInfo['mime'];

            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($source);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($source);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($source);
                    break;
                default:
                    return false;
            }
            imagejpeg($image, $destination, $quality);
            return $destination;
        }

        if (!empty($_FILES['d_logo']['tmp_name'])) {
            $filename = $_FILES['d_logo']['name'];
            $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedTypes = ['png', 'jpeg', 'jpg', 'gif'];

            if (in_array($imageFileType, $allowedTypes)) {
                $source = $_FILES["d_logo"]['tmp_name'];
                $destination = $_FILES["d_logo"]['tmp_name'];
                $quality = ($_FILES["d_logo"]['size'] < 250000) ? 80 : 30;

                $compressImage = compressImage($source, $destination, $quality);
                $logo = addslashes(file_get_contents($compressImage));

                $stmt = $connect->prepare("UPDATE digi_card SET d_logo = ? WHERE id = ?");
                $stmt->bind_param("si", $logo, $_SESSION['card_id_inprocess']);
                $stmt->execute();
            }
        }

        $stmt = $connect->prepare("UPDATE digi_card SET d_f_name = ?, d_l_name = ?, d_position = ?, d_contact = ?, d_whatsapp = ?, d_address = ?, d_email = ?, d_website = ?, d_comp_est_date = ?, d_about_us = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $_POST['d_f_name'], $_POST['d_l_name'], $_POST['d_position'], $_POST['d_contact'], $_POST['d_whatsapp'], $_POST['d_address'], $_POST['d_email'], $_POST['d_website'], $_POST['d_comp_est_date'], $_POST['d_about_us'], $_SESSION['card_id_inprocess']);
        $stmt->execute();

        header("Location: create_card3.php");
        exit;
    }
    ?>
</div>

<footer><p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p></footer>

</body>
</html>
