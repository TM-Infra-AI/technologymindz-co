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

// Fetch or Insert digi_card3 details
$stmt2 = $connect->prepare("SELECT * FROM digi_card3 WHERE id = ?");
$stmt2->bind_param("i", $_SESSION['card_id_inprocess']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_assoc();

if (!$row2) {
    $stmt_insert = $connect->prepare("INSERT INTO digi_card3 (id) VALUES (?)");
    $stmt_insert->bind_param("i", $_SESSION['card_id_inprocess']);
    $stmt_insert->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="main3">
    <div class="navigator_up">
        <a href="select_theme.php"><div class="nav_cont"><i class="fa fa-map"></i> Select Theme</div></a>
        <a href="create_card2.php"><div class="nav_cont"><i class="fa fa-bank"></i> Company Details</div></a>
        <a href="create_card3.php"><div class="nav_cont"><i class="fa fa-facebook"></i> Social Links</div></a>
        <a href="create_card4.php"><div class="nav_cont"><i class="fa fa-ticket"></i> Products & Services</div></a>
        <a href="create_card5.php"><div class="nav_cont active"><i class="fa fa-image"></i> Image Gallery</div></a>
        <a href="preview_page.php"><div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div></a>
    </div>

    <div class="btn_holder">
        <a href="create_card4.php"><div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div></a>
        <a href="preview_page.php"><div class="skip_btn">Skip <i class="fa fa-chevron-circle-right"></i></div></a>
    </div>

    <h1>Image Gallery (Upload up to 10 Images)</h1>
    <p class="sug_alert">(At least **1 image** is required, max size 250 KB)</p>

    <form id="card_form" action="" method="POST" enctype="multipart/form-data">
        <?php 
        for ($m = 1; $m <= 10; $m++) { 
            $image_path = !empty($row2["d_gall_img$m"]) ? 'data:image/*;base64,'.base64_encode($row2["d_gall_img$m"]) : 'images/upload.png';
            ?>
            <div class="divider">
                <div class="num"><?php echo $m; ?></div>
                <?php if (!empty($row2["d_gall_img$m"])) { ?>
                    <div class="delImg" onclick="removeData(<?php echo $_SESSION['card_id_inprocess']; ?>, <?php echo $m; ?>)">
                        <i class="fa fa-trash-o"></i>
                    </div>
                <?php } ?>
                <img src="<?php echo $image_path; ?>" alt="Select image" id="showPreviewLogo<?php echo $m; ?>" onclick="document.getElementById('clickMeImage<?php echo $m; ?>').click();">
                <div class="input_box">
                    <input type="file" name="d_gall_img<?php echo $m; ?>" id="clickMeImage<?php echo $m; ?>" accept="image/*" onchange="previewImage(this, <?php echo $m; ?>);">
                </div>
            </div>
        <?php } ?>
        <input type="submit" name="process5" value="Complete & Preview">
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
    if (isset($_POST['process5'])) {
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
            if (!empty($_FILES["d_gall_img$x"]['tmp_name'])) {
                $at_least_one_image = true;
                $compressedImage = compressImage($_FILES["d_gall_img$x"]['tmp_name']);
                if ($compressedImage) {
                    $stmt = $connect->prepare("UPDATE digi_card3 SET d_gall_img$x=? WHERE id=?");
                    $stmt->bind_param("si", $compressedImage, $_SESSION['card_id_inprocess']);
                    $stmt->execute();
                }
            }
        }

        if (!$at_least_one_image) {
            echo '<div class="alert danger">Please upload at least **1 image** before proceeding.</div>';
        } else {
            header("Location: preview_page.php");
            exit;
        }
    }
    ?>
</div>

<footer><p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p></footer>

</body>
</html>
