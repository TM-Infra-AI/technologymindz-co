<?php
session_start();
ob_start();
require('connect.php');
require('header.php');

if (!isset($_SESSION['user_email'])) {
    die('Session not set. Please log in.');
}

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
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Options</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function previewImage(input, targetId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(targetId).src = e.target.result;
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
        <a href="create_card2.php"><div class="nav_cont"><i class="fa fa-bank"></i> Company Details</div></a>
        <a href="create_card3.php"><div class="nav_cont"><i class="fa fa-facebook"></i> Social Links</div></a>
        <a href="create_card4.php"><div class="nav_cont active"><i class="fa fa-rupee"></i> Payment Options</div></a>
        <a href="create_card5.php"><div class="nav_cont"><i class="fa fa-ticket"></i> Products & Services</div></a>
        <a href="create_card7.php"><div class="nav_cont"><i class="fa fa-archive"></i> Order Page</div></a>
        <a href="create_card6.php"><div class="nav_cont"><i class="fa fa-image"></i> Image Gallery</div></a>
        <a href="preview_page.php"><div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div></a>
    </div>

    <div class="btn_holder">
        <a href="create_card3.php"><div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div></a>
        <a href="create_card5.php"><div class="skip_btn">Skip <i class="fa fa-chevron-circle-right"></i></div></a>
    </div>

    <h1>Payment Options</h1>

    <form id="card_form" action="" method="POST" enctype="multipart/form-data">
        <h3>Payment</h3>
        <?php 
        $fields = [
            "d_paytm" => "PayTm Number",
            "d_google_pay" => "Google Pay",
            "d_phone_pay" => "PhonePe"
        ];
        foreach ($fields as $key => $label) {
            echo '<div class="input_box"><p>'.$label.' (Optional)</p><input type="text" name="'.$key.'" maxlength="20" value="'.htmlspecialchars($row[$key] ?? '').'"></div>';
        }
        ?>

        <h3>Bank Account Details</h3>
        <?php 
        $bank_fields = [
            "d_bank_name" => "Bank Name",
            "d_ac_name" => "Account Holder Name",
            "d_account_no" => "Bank Account Number",
            "d_ifsc" => "Bank IFSC Code",
            "d_ac_type" => "GST Number"
        ];
        foreach ($bank_fields as $key => $label) {
            echo '<div class="input_box"><p>'.$label.' (Optional)</p><input type="text" name="'.$key.'" maxlength="100" value="'.htmlspecialchars($row[$key] ?? '').'"></div>';
        }
        ?>

        <h3>Payment QR Codes</h3>
        <?php 
        $qr_fields = [
            "d_qr_paytm" => "Paytm QR Code",
            "d_qr_google_pay" => "Google Pay QR Code",
            "d_qr_phone_pay" => "PhonePe QR Code"
        ];
        foreach ($qr_fields as $key => $label) {
            echo '<div class="divider">
                    <div class="num">'.$label.'</div>
                    <img src="'.(!empty($row[$key]) ? 'data:image/*;base64,'.base64_encode($row[$key]) : 'images/upload.png').'" alt="Select image" id="preview_'.$key.'" onclick="document.getElementById(\'upload_'.$key.'\').click();">
                    <div class="input_box">
                        <input type="file" name="'.$key.'" id="upload_'.$key.'" onchange="previewImage(this, \'preview_'.$key.'\');" accept="image/*">
                    </div>
                </div>';
        }
        ?>

        <br>
        <input type="submit" name="process4" value="Next 5">
    </form>

    <?php
    if (isset($_POST['process4'])) {
        function uploadImage($fileKey) {
            if (!empty($_FILES[$fileKey]['tmp_name'])) {
                return addslashes(file_get_contents($_FILES[$fileKey]['tmp_name']));
            }
            return null;
        }

        $stmt = $connect->prepare("UPDATE digi_card SET 
            d_paytm=?, d_google_pay=?, d_phone_pay=?, d_account_no=?, d_ifsc=?, 
            d_ac_name=?, d_bank_name=?, d_ac_type=?, d_qr_paytm=?, d_qr_google_pay=?, d_qr_phone_pay=? 
            WHERE id=?");
        $stmt->bind_param("sssssssssssi",
            $_POST['d_paytm'], $_POST['d_google_pay'], $_POST['d_phone_pay'], $_POST['d_account_no'], $_POST['d_ifsc'], 
            $_POST['d_ac_name'], $_POST['d_bank_name'], $_POST['d_ac_type'],
            uploadImage('d_qr_paytm'), uploadImage('d_qr_google_pay'), uploadImage('d_qr_phone_pay'), 
            $_SESSION['card_id_inprocess']
        );

        if ($stmt->execute()) {
            header("Location: create_card5.php");
            exit;
        } else {
            echo '<div class="alert danger">Error! Try Again.</div>';
        }
    }
    ?>
</div>

<footer><p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p></footer>

</body>
</html>
