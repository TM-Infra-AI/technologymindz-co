<?php
ob_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require('connect.php');



if (isset($_POST['process5'])) {


    function compressImage($source)
    {
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
        imagejpeg($image, $source, 60);
        return (file_get_contents($source));
    }

    // $at_least_one_image = false;

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

    // // ✅ If no new images were uploaded, check if an existing image is already stored
    // if (!$at_least_one_image) {
    //     // ✅ Check if at least one image already exists in the database
    //     $stmt_check = $connect->prepare("SELECT 
    //         d_pro_img1, d_pro_img2, d_pro_img3, d_pro_img4, d_pro_img5, 
    //         d_pro_img6, d_pro_img7, d_pro_img8, d_pro_img9, d_pro_img10 
    //         FROM digi_card2 WHERE id=?");

    //     $stmt_check->bind_param("i", $_SESSION['card_id_inprocess']);
    //     $stmt_check->execute();
    //     $existing_images = $stmt_check->get_result()->fetch_assoc();
    //     foreach ($existing_images as $image) {
    //         if (!empty($image)) {
    //             $at_least_one_image = true;
    //             break;
    //         }
    //     }
    // }

    // if (!$at_least_one_image) {
    //     echo '<div class="alert danger">Please upload at least 1 image before proceeding.</div>';
    // } else {
    // ✅ Update product names
    $update_stmt = $connect->prepare("UPDATE digi_card3 SET 
        d_gall_name1=?, d_gall_name2=?, d_gall_name3=?, d_gall_name4=?, d_gall_name5=?, 
        d_gall_name6=?, d_gall_name7=?, d_gall_name8=?, d_gall_name9=?, d_gall_name10=?,
        d_gall_url1=?, d_gall_url2=?, d_gall_url3=?, d_gall_url4=?, d_gall_url5=?, 
        d_gall_url6=?, d_gall_url7=?, d_gall_url8=?, d_gall_url9=?, d_gall_url10=?
        WHERE id=?");


    $update_stmt->bind_param(
        "ssssssssssssssssssssi",
        $_POST['d_gall_name1'],
        $_POST['d_gall_name2'],
        $_POST['d_gall_name3'],
        $_POST['d_gall_name4'],
        $_POST['d_gall_name5'],
        $_POST['d_gall_name6'],
        $_POST['d_gall_name7'],
        $_POST['d_gall_name8'],
        $_POST['d_gall_name9'],
        $_POST['d_gall_name10'],
        $_POST['d_gall_url1'],
        $_POST['d_gall_url2'],
        $_POST['d_gall_url3'],
        $_POST['d_gall_url4'],
        $_POST['d_gall_url5'],
        $_POST['d_gall_url6'],
        $_POST['d_gall_url7'],
        $_POST['d_gall_url8'],
        $_POST['d_gall_url9'],
        $_POST['d_gall_url10'],
        $_SESSION['card_id_inprocess']
    );

    if ($update_stmt->execute()) {
        header("Location: create_card6.php");
        exit;
    } else {
        echo '<div class="alert danger">Error! Try Again.</div>';
    }
}
ob_end_flush();
?>
<?php
// session_start();
// require('connect.php');
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
    <!-- Toastify.js CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify.js Script -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>

<body>

    <div class="main3">
        <div class="navigator_up">
            <a href="select_theme.php">
                <div class="nav_cont"><i class="fa fa-map"></i> Select Theme</div>
            </a>
            <a href="create_card2.php">
                <div class="nav_cont"><i class="fa fa-building"></i> Personal Details</div>
            </a>
            <a href="create_card3.php">
                <div class="nav_cont"><i class="fa fa-globe"></i> Social Links</div>
            </a>
            <a href="create_card4.php">
                <div class="nav_cont"><i class="fa fa-ticket"></i> Product & Service</div>
            </a>
            <a href="create_card5.php">
                <div class="nav_cont active"><i class="fa fa-image"></i> Image Gallery</div>
            </a>
            <a href="create_card6.php" onclick="return validateAndSkip(event,'create_card6.php')">
                <div class="nav_cont"><i class="fa fa-file-pdf"></i> PDF Upload</div>
            </a>
             <a href="create_card7.php" onclick="return validateAndSkip(event,'create_card7.php')">
                <div class="nav_cont "><i class="fa fa-file-video"></i> Video Upload</div>
            </a> 
            <a href="preview_page.php" onclick="return validateAndSkip(event,'preview_page.php')">
                <div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div>
            </a>
        </div>

        <div class="btn_holder">
            <a href="create_card4.php">
                <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
            </a>
            <a href="create_card6.php">
                <div class="skip_btn" onclick="return validateAndSkip(event,'create_card6.php')">Skip <i
                        class="fa fa-chevron-circle-right"></i></div>
            </a>
        </div>

        <h1>Image Gallery (Upload up to 10 Images)</h1>
        <p class="sug_alert">Image can be uploaded up to 2GB in size.</p>

        <form id="card_form" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(false);">
            <?php
            for ($m = 1; $m <= 10; $m++) {
                $product_name = htmlspecialchars($row2["d_gall_name$m"] ?? "");
                $image_path = !empty($row2["d_gall_img$m"]) ? 'data:image/*;base64,' . base64_encode($row2["d_gall_img$m"]) : 'images/upload.png';
            ?>
                <div class="divider">

                    <?php if ($product_name) { ?>
                        <div class="delImg"
                            onclick="removeData(<?php echo $_SESSION['card_id_inprocess']; ?>, <?php echo $m; ?>)">
                            <i class="fa fa-trash-o"></i>
                        </div>
                    <?php } ?>
                    <div class="input_box">
                        <p style="top:-17px;">Image <?php echo ($m === 1 ? '* ' : ''); ?></p>
                        <input type="text" name="d_gall_name<?php echo $m; ?>" maxlength="200"
                            placeholder="Image Title" value="<?php echo $product_name; ?>"  <?php echo ($m === 1 ? 'required' : ''); ?>>
                    </div>
                    <!-- Optional URL input field -->
                    <!-- Optional URL input field -->


                    <img src="<?php echo $image_path; ?>" alt="Select image" id="showPreviewLogo<?php echo $m; ?>"
                        onclick="document.getElementById('clickMeImage<?php echo $m; ?>').click();">
                    <div class="input_box">
                        <input type="file" name="d_gall_img<?php echo $m; ?>" id="clickMeImage<?php echo $m; ?>"
                            accept="image/png, image/jpeg, image/jpg, image/gif"
                            onchange="previewImage(this, <?php echo $m; ?>);">
                    </div>
                </div>
            <?php } ?>
            <input type="submit" name="process5" value="Next">
        </form>

        <script>
            let deletedImages = []; //  Store globally
            // var atLeastOneImage = <?php
                                        //                         // PHP Code to Check Database
                                        //                         $stmt_check = $connect->prepare("SELECT d_gall_name1, d_gall_name2, d_gall_name3, d_gall_name4, d_gall_name5, d_gall_name6, d_gall_name7, d_gall_name8, d_gall_name9, d_gall_name10 FROM digi_card3 WHERE id=?");
                                        //                         $stmt_check->bind_param("i", $_SESSION['card_id_inprocess']);
                                        //                         $stmt_check->execute();
                                        //                         $existing_images = $stmt_check->get_result()->fetch_assoc();

                                        //                         // Check if any image exists
                                        //                         $at_least_one_image = false;
                                        //                         foreach ($existing_images as $image) {
                                        //                             if (!empty($image)) {
                                        //                                 $at_least_one_image = true;
                                        //                                 break;
                                        //                             }
                                        //                         }

                                        //                         // Output the result as JavaScript variable
                                        //                         echo $at_least_one_image ? 'true' : 'false';
                                        //                         
                                        ?>;

            function validateForm(skipDelete = false) {
                let isValid = false; // Track if at least one valid product exists
                // let errorMessage = "Please fill in at least one product name and upload an image.";

                // let hasOneImage = false; // Track if at least one image is uploaded
                for (let i = 1; i <= 10; i++) {
                    let productName = document.querySelector(`input[name="d_gall_name${i}"]`).value.trim();
                    let productImageInput = document.querySelector(`input[name="d_gall_img${i}"]`);
                    let productImagePreview = document.getElementById(`showPreviewLogo${i}`);


                    let isNewImageUploaded = productImageInput && productImageInput.files.length > 0;
                    let isExistingImage = productImagePreview && productImagePreview.src && !productImagePreview.src.includes("images/upload.png");
                    let imageProvided = isNewImageUploaded || isExistingImage;

                    if(!skipDelete){
                    // Validate pairs
                    if (productName && !imageProvided) {
                        alert(`Please upload/select an image for "${productName}".`);
                        return false;
                    }

                    if (!productName && imageProvided) {
                        alert(`Please enter a product/service name for the selected image in row #${i}.`);
                        return false;
                    }
                }
                if (skipDelete) {
                        isValid = true;
                        return true
                    }
                    //     // Check if a new file is uploaded
                    //     let isNewImageUploaded = productImageInput.files.length > 0;

                    //     // Check if an image already exists in the database (preloaded image)
                    //     let isExistingImage = productImagePreview && productImagePreview.src && !productImagePreview.src
                    //         .includes("images/upload.png");

                    //     // Validation Logic:
                    //     if (productName && (isNewImageUploaded || isExistingImage)) {
                    //         hasOneImage = true; // At least one image uploaded
                    //         isValid = true; // At least one valid entry found
                    //     } else if (productName && !isNewImageUploaded && !isExistingImage) {
                    //         alert(`Please upload an image for "${productName}".`);
                    //         return false;
                    //     } else if (!productName && isNewImageUploaded) {
                    //         alert("Please enter a product/service name for the selected image.");
                    //         return false;
                    //     }
                }

                // if (!hasOneImage && !skipDelete) {
                //     alert(`Please upload at least one image.`);
                //     return false;
                // }

                if (!skipDelete) {
                    deletedImages.forEach(image => {
                        let deleteButton = $(`.delImg[onclick="removeData(${image.id}, ${image.numb})"]`);

                        if (deleteButton.is(':hidden')) { // ✅ Check if button is hidden
                            $.ajax({
                                url: 'js_request.php',
                                method: 'POST',
                                data: {
                                    action: 'delete_product',
                                    id_gal: image.id,
                                    d_gall_img: image.numb
                                },
                                dataType: 'text',
                                success: function(data) {
                                    console.log(`Deleted Image ${image.numb}:`, data);
                                },
                                error: function(xhr, status, error) {
                                    console.error("AJAX error:", status, error);
                                    deleteButton.show(); // Re-show button if AJAX fails
                                }
                            });
                        }
                    });
                }
            }

            function validateAndSkip(event, nextPage) {
                event.preventDefault(); // Prevent default navigation

                // if (!atLeastOneImage) {
                //     alert("Error: You must upload at least one image before proceeding.");
                //     return;
                // }

                if (validateForm(true)) { //  Pass true to skip delete logic
                    window.location.href = nextPage;
                }
            }

            function previewImage(input, id) {
                const allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif"]; // Allowed file types
                if (input.files && input.files[0]) {
                    var file = input.files[0];
                    var maxSize = 2 * 1024 * 1024 * 1024

                    // Check file type
                    if (!allowedTypes.includes(file.type)) {
                        showToast("Invalid file type. Allowed: PNG, JPEG, JPG, GIF.", "red");
                        input.value = ""; // Clear file input
                        previewImage.src = defaultImage; // Reset preview
                        return;
                    }


                    if (file.size > maxSize) {
                        alert("The selected image exceeds the maximum size of 250 KB. Please choose a smaller file.");
                        input.value = ""; // Clear the file input
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById("showPreviewLogo" + id).src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
            // Function to show Toastify notifications
            function showToast(message, color) {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: color,
                }).showToast();
            }

            function removeData(id, numb) {
                let deleteButton = $(`.delImg[onclick="removeData(${id}, ${numb})"]`);
                deleteButton.hide(); // ✅ Hide button immediately

                // Reset image preview and input
                $('#showPreviewLogo' + numb).attr('src', 'images/upload.png');
                $('input[name="d_gall_name' + numb + '"]').val('');

                // Track deletion (Create hidden input if not exists)
                if (!$(`input[name="deleted_image${numb}"]`).length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'deleted_image' + numb,
                        value: '1'
                    }).appendTo('form');
                }

                // ✅ Store deleted image data
                deletedImages.push({
                    id,
                    numb
                });
                console.log(`Image ${numb} marked for deletion from ID ${id}`);
            }
        </script>
    </div>

    <footer>
        <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
    </footer>

</body>

</html>