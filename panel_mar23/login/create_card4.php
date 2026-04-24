<?php
session_start();
require('connect.php');
require('header.php');

if (!isset($_SESSION['user_email']) || !isset($_SESSION['card_id_inprocess'])) {
    header("Location: index.php");
    exit;
}

// Fetch main card details
$stmt = $connect->prepare("SELECT * FROM digi_card WHERE id = ? AND user_email = ?");
$stmt->bind_param("is", $_SESSION['card_id_inprocess'], $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    header("Location: index.php");
    exit;
}

// Fetch or Insert digi_card2 details
$stmt2 = $connect->prepare("SELECT * FROM digi_card2 WHERE id = ?");
$stmt2->bind_param("i", $_SESSION['card_id_inprocess']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_assoc();

if (!$row2) {
    $stmt_insert = $connect->prepare("INSERT INTO digi_card2 (id) VALUES (?)");
    $stmt_insert->bind_param("i", $_SESSION['card_id_inprocess']);
    $stmt_insert->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products & Services</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="main3">
    <div class="navigator_up">
        <a href="select_theme.php"><div class="nav_cont"><i class="fa fa-map"></i> Select Theme</div></a>
        <a href="create_card2.php"><div class="nav_cont"><i class="fa fa-bank"></i> Company Details</div></a>
        <a href="create_card3.php"><div class="nav_cont"><i class="fa fa-facebook"></i> Social Links</div></a>
        <a href="create_card4.php"><div class="nav_cont active"><i class="fa fa-ticket"></i> Products & Services</div></a>
        <a href="create_card5.php"><div class="nav_cont"><i class="fa fa-image"></i> Image Gallery</div></a>
        <a href="preview_page.php"><div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div></a>
    </div>

    <div class="btn_holder">
        <a href="create_card3.php"><div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div></a>
        <a href="create_card5.php"><div class="skip_btn">Skip <i class="fa fa-chevron-circle-right"></i></div></a>
    </div>

    <h1>Products & Services</h1>
    <p class="sug_alert">(At least **1 image** is required, max size 250 KB)</p>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php 
        for ($m = 1; $m <= 10; $m++) { 
            $product_name = htmlspecialchars($row2["d_pro_name$m"] ?? "");
            $product_image = !empty($row2["d_pro_img$m"]) ? 'data:image/*;base64,'.base64_encode($row2["d_pro_img$m"]) : 'images/upload.png';
            ?>
            <div class="divider">
                <div class="num"><?php echo $m; ?></div>
                <?php if ($product_name) { ?>
                    <div class="delImg" onclick="removeData(<?php echo $_SESSION['card_id_inprocess']; ?>,<?php echo $m; ?>)"><i class="fa fa-trash-o"></i></div>
                <?php } ?>
                <div class="input_box">
                    <p><?php echo $m; ?>th Product & Service</p>
                    <input type="text" name="d_pro_name<?php echo $m; ?>" maxlength="200" placeholder="Product/Service Name" value="<?php echo $product_name; ?>">
                </div>
                <img src="<?php echo $product_image; ?>" alt="Select image" id="showPreviewLogo<?php echo $m; ?>" onclick="document.getElementById('clickMeImage<?php echo $m; ?>').click();">
                <div class="input_box">
                    <input type="file" name="d_pro_img<?php echo $m; ?>" id="clickMeImage<?php echo $m; ?>" accept="image/*" onchange="previewImage(this, <?php echo $m; ?>);">
                </div>
            </div>
        <?php } ?>
        <input type="submit" name="process4" value="Next 6">
    </form>

    <script>
        function previewImage(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById("showPreviewLogo" + id).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <?php
    if (isset($_POST['process4'])) { 
        ob_start();

        function compressImage($source) {
            $imageInfo = getimagesize($source);
            $mime = $imageInfo['mime'];
            switch ($mime) {
                case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
                case 'image/png': $image = imagecreatefrompng($source); break;
                case 'image/gif': $image = imagecreatefromgif($source); break;
                default: return false;
            }
            imagejpeg($image, $source, 60);
            return addslashes(file_get_contents($source));
        }

        $at_least_one_image = false;

        for ($x = 1; $x <= 10; $x++) {
            if (!empty($_FILES["d_pro_img$x"]['tmp_name'])) {
                $at_least_one_image = true;
                $compressedImage = compressImage($_FILES["d_pro_img$x"]['tmp_name']);
                if ($compressedImage) {
                    $stmt = $connect->prepare("UPDATE digi_card2 SET d_pro_img$x=? WHERE id=?");
                    $stmt->bind_param("si", $compressedImage, $_SESSION['card_id_inprocess']);
                    $stmt->execute();
                }
            }
        }

        if (!$at_least_one_image) {
            echo '<div class="alert danger">Please upload at least **1 product image**.</div>';
        } else {
            $update_stmt = $connect->prepare("UPDATE digi_card2 SET 
                d_pro_name1=?, d_pro_name2=?, d_pro_name3=?, d_pro_name4=?, d_pro_name5=?, 
                d_pro_name6=?, d_pro_name7=?, d_pro_name8=?, d_pro_name9=?, d_pro_name10=? 
                WHERE id=?");

            $update_stmt->bind_param("ssssssssssi", 
                $_POST['d_pro_name1'], $_POST['d_pro_name2'], $_POST['d_pro_name3'], $_POST['d_pro_name4'], $_POST['d_pro_name5'], 
                $_POST['d_pro_name6'], $_POST['d_pro_name7'], $_POST['d_pro_name8'], $_POST['d_pro_name9'], $_POST['d_pro_name10'], 
                $_SESSION['card_id_inprocess']
            );

            if ($update_stmt->execute()) {
                header("Location: https://staging-tech.technologymindz.com/panel/login/create_card5.php");
                exit;
            } else {
                echo '<div class="alert danger">Error! Try Again.</div>';
            }
        }
    }
    ?>
</div>

<footer><p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p></footer>

</body>
</html>
