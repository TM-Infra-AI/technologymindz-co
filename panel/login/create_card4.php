<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();  // Ensure no output before header()
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
    <title>Product & Service</title>
    <!-- <liznk rel="stylesheet" href="styles.css"> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastify.js CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify.js Script -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
        .divider {
            border: 1px solid #ccc;
            padding: 50px;
            margin-bottom: 10px;
            border-radius: 8px;
            position: relative;
        }
        .delImg {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }
        img {
            max-width: 120px;
            margin-top: 10px;
            cursor: pointer;
        }
        .add-more-btn{
            background: green;
            padding: 10px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            border: 1px;
        }
    </style>

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
            <a href="#">
                <div class="nav_cont active"><i class="fa fa-ticket"></i> Product & Service</div>
            </a>
            <a href="create_card5.php" onclick="validateAndSkip(event, 'create_card5.php')">
                <div class="nav_cont"><i class="fa fa-image"></i> Image Gallery</div>
            </a>
            <a href="create_card6.php" onclick="validateAndSkip(event, 'create_card6.php')">
                <div class="nav_cont"><i class="fa fa-file-pdf"></i> PDF Upload</div>
            </a>
             <a href="create_card7.php" onclick="validateAndSkip(event, 'create_card7.php')">
                <div class="nav_cont "><i class="fa fa-file-video"></i> Video Upload</div>
            </a> 
            <a href="preview_page.php" onclick="validateAndSkip(event, 'preview_page.php')">
                <div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div>
            </a>
        </div>

        <div class="btn_holder">
            <a href="create_card3.php">
                <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
            </a>
            <a href="create_card5.php">
                <div class="skip_btn" onclick="return validateAndSkip(event,'create_card5.php')">Skip <i
                        class="fa fa-chevron-circle-right"></i></div>
            </a>
        </div>

        <h1>Product & Service</h1>
        <p class="sug_alert">Image can be uploaded up to 2GB in size.</p>
        <div id="status_remove_img"></div>

        <form id="myForm" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(false);">
            <?php
            $lastUsedIndex = 0;

            for ($m = 1; $m <= 10; $m++) { 
                $product_name = htmlspecialchars($row2["d_pro_name$m"] ?? "");
                $product_url = htmlspecialchars($row2["d_pro_url$m"] ?? "");
                $product_image_blob = $row2["d_pro_img$m"] ?? "";
                $hasImage = !empty($product_image_blob);
                $product_image = $hasImage ? 'data:image/*;base64,' . base64_encode($product_image_blob) : 'images/upload.png';

                // Skip rendering this card if all fields are empty
                if (!$product_name && !$product_url && !$hasImage) {
                    continue;
                }

                $lastUsedIndex = $m; // Track highest index used
                ?>
                <div class="divider product-card" data-index="<?= $m ?>">
                <?php if ($m > 1) { ?>
                    <div class="delImg" onclick="removeData(<?= $_SESSION['card_id_inprocess']; ?>, <?= $m ?>)">
                        <i class="fa fa-trash-o"></i>
                    </div>
                <?php } ?>

                <div class="input_box">
                    <p>Product & Service *</p>
                    <input type="text" name="d_pro_name<?= $m ?>" maxlength="200" placeholder="Enter Product/Service" value="<?= $product_name ?>">
                </div>
                <div class="input_box">
                    <p>URL *</p>
                    <input type="text" name="d_pro_url<?= $m ?>" maxlength="500" placeholder="Enter a URL" value="<?= $product_url ?>">
                </div>
                <img src="<?= $product_image ?>" alt="Select image" id="showPreviewLogo<?= $m ?>" onclick="document.getElementById('clickMeImage<?= $m ?>').click();">
                <div class="input_box">
                    <input type="file" name="d_pro_img<?= $m ?>" id="clickMeImage<?= $m ?>" accept="image/png, image/jpeg, image/jpg, image/gif" onchange="previewImage(this, <?= $m ?>);">
                </div>
                </div>
                <?php 
            } ?>

            <!-- This hidden input is used by JS to know where to start adding new cards -->
            <input type="hidden" id="lastIndex" value="<?= $lastUsedIndex ?>">

            <div style="text-align: right; margin-top: 10px;" >
                <button class="add-more-btn" type="button" onclick="addCard()">+ Add More</button>
            </div>

            <input type="submit" name="process4" value="Next" />
        </form>

        <script>
            // ✅ Create a global array to store deleted images
            let deletedImages = [];

            // Function to validate the form
            function validateForm(skipDelete = false) {
                let isValid = false;

                const productCards = document.querySelectorAll(".product-card");
                let hasOneValid = false;

                for (let card of productCards) {
                    const index = card.getAttribute("data-index");

                    const productName = document.querySelector(`input[name="d_pro_name${index}"]`)?.value.trim() || '';
                    const productUrl = document.querySelector(`input[name="d_pro_url${index}"]`)?.value.trim() || '';
                    const productImageInput = document.querySelector(`input[name="d_pro_img${index}"]`);
                    const productImagePreview = document.getElementById(`showPreviewLogo${index}`);

                    const isNewImageUploaded = productImageInput?.files.length > 0;
                    const isExistingImage = productImagePreview?.src && !productImagePreview.src.includes("images/upload.png");

                    const imageProvided = isNewImageUploaded || isExistingImage;
                    const nameProvided = !!productName;
                    const urlProvided = !!productUrl;

                    const fieldsFilled = [nameProvided, urlProvided, imageProvided].filter(Boolean).length;

                
                    if (!skipDelete) {
                        if (fieldsFilled > 0 && fieldsFilled < 3) {
                            if (!nameProvided) {
                                showToast("Please enter a product/service name.", "red");                                  
                                return false;
                            }
                            if (!imageProvided) {
                                showToast(`Please upload or select an image for "${productName || 'this service'}".`, "red");                                  
                                return false;
                            }
                            if (!urlProvided) {
                                showToast(`Please enter a product/service URL for "${productName || 'this service'}".`, "red");                                                                  
                                return false;
                            }
                        }

                        if (fieldsFilled === 3) {
                            hasOneValid = true;
                        }
                    } else {
                        isValid = true;
                    }
                }

                if (!skipDelete && !hasOneValid) {
                    showToast("Please fill in at least one complete product with name, image, and URL.", "red");                    
                    return false;
                }

                // Delete logic for removed cards
                if (!skipDelete && typeof deletedImages !== 'undefined') {
                    deletedImages.forEach(image => {
                        let deleteButton = $(`.delImg[onclick="removeData(${image.id},${image.numb})"]`);
                        if (deleteButton.is(':hidden')) {
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
                                    deleteButton.show(); // Restore if failed
                                }
                            });
                        }
                    });
                }

                return true;
            }

            // Function to validate and skip to the next page
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

                    // Check file type
                    if (!allowedTypes.includes(file.type)) {
                        showToast("Invalid file type. Allowed: PNG, JPEG, JPG, GIF.", "red");
                        input.value = ""; // Clear file input
                        previewImage.src = defaultImage; // Reset preview
                        return;
                    }


                    // Maximum file size in bytes (2GB)
                    var maxSize = 2 * 1024 * 1024 * 1024; // 2 GB

                    if (file.size > maxSize) {
                        showToast("The selected image exceeds the maximum size of 2GB. Please choose a smaller file.", "red");                        
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
                // Hide the delete button
                let deleteButton = $('.delImg[onclick="removeData(' + id + ',' + numb + ')"]');
                deleteButton.hide();

                // Dummy delete (Frontend changes)
                $('#showPreviewLogo' + numb).attr('src', 'images/upload.png'); // Reset Image
                $('input[name="d_pro_name' + numb + '"]').val(''); // Clear Name Field
                $('input[name="d_pro_url' + numb + '"]').val(''); // 🔥 Clear URL Field

                // Track the deletion (Create a hidden input if not already created)
                if (!$(`input[name="deleted_image${numb}"]`).length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'deleted_image' + numb,
                        value: '1'
                    }).appendTo('form');
                }
                // ✅ Store id & numb globally
                deletedImages.push({
                    id,
                    numb
                });
                console.log(`Image ${numb} marked for deletion ${id}`);
            }


            function addCard() {
                // Sync cardCount with the current number of cards
                const cards = document.querySelectorAll('.product-card');
                cardCount = cards.length;

                if (cardCount >= 10) {
                    showToast("Maximum 10 products allowed.", "red");                
                    return;
                }

                if (cardCount === 0) {
                    let cardCount = document.querySelectorAll('.product-card').length || 1;
                }
                
                // Get the last card after syncing
                const lastCard = document.querySelector(`.product-card[data-index="${cardCount}"]`);
                if (!lastCard) {                    
                    showToast("Unexpected error: last card not found.", "red");  
                    return;
                }

                const nameInput = lastCard.querySelector(`input[name="d_pro_name${cardCount}"]`);
                const urlInput = lastCard.querySelector(`input[name="d_pro_url${cardCount}"]`);
                const imgInput = lastCard.querySelector(`input[name="d_pro_img${cardCount}"]`);

                const name = nameInput?.value.trim() || "";
                const url = urlInput?.value.trim() || "";
                const img = imgInput?.files.length || 0;

                if (name === "" && url === "" && img === 0) {
                    showToast("Please fill in the current card before adding a new one.", "red");                      
                    return;
                }

                cardCount++;

                const container = document.querySelector("#myForm");
                const newCard = document.createElement("div");
                newCard.classList.add("divider", "product-card");
                newCard.setAttribute("data-index", cardCount);

                newCard.innerHTML = `
                    <div class="delImg" onclick="removeData(<?php echo $_SESSION['card_id_inprocess']; ?>, ${cardCount})">
                        <i class="fa fa-trash-o"></i>
                    </div>
                    <div class="input_box">
                        <p>Product & Service *</p>
                        <input type="text" name="d_pro_name${cardCount}" maxlength="200" placeholder="Enter Product/Service">
                    </div>
                    <div class="input_box">
                        <p>URL *</p>
                        <input type="text" name="d_pro_url${cardCount}" maxlength="500" placeholder="Enter a URL">
                    </div>
                    <img src="images/upload.png" alt="Select image" id="showPreviewLogo${cardCount}" onclick="document.getElementById('clickMeImage${cardCount}').click();">
                    <div class="input_box">
                        <input type="file" name="d_pro_img${cardCount}" id="clickMeImage${cardCount}" accept="image/png, image/jpeg, image/jpg, image/gif" onchange="previewImage(this, ${cardCount});">
                    </div>
                `;

                const insertBeforeElement = container.querySelector("div[style*='text-align: right']");
                container.insertBefore(newCard, insertBeforeElement);

                // Scroll to the new card
                newCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }


            // Function to show image preview
            function previewImage(input, index) {
                const preview = document.getElementById(`showPreviewLogo${index}`);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Dummy delete function - you can replace this with actual AJAX logic
            function removeData(cardId, index) {
                const card = document.querySelector(`.product-card[data-index='${index}']`);
                if (card) card.remove();
            }
        </script>

        <?php
            if (isset($_POST['process4'])) {
            ob_start();

            function compressImage($source) {
                $imageInfo = getimagesize($source);
                $mime = $imageInfo['mime'];
                switch ($mime) {
                    case 'image/jpeg': return imagecreatefromjpeg($source);
                    case 'image/png': return imagecreatefrompng($source);
                    case 'image/gif': return imagecreatefromgif($source);
                    default: return false;
                }
            }

            // Get existing images from DB
            $stmt_check = $connect->prepare("SELECT * FROM digi_card2 WHERE id=?");
            $stmt_check->bind_param("i", $_SESSION['card_id_inprocess']);
            $stmt_check->execute();
            $existing_data = $stmt_check->get_result()->fetch_assoc();

            $names = [];
            $urls = [];
            $images = [];

            for ($x = 1; $x <= 10; $x++) {
                $name = trim($_POST["d_pro_name$x"] ?? "");
                $url = trim($_POST["d_pro_url$x"] ?? "");
                $imgField = "d_pro_img$x";

                $imgBlob = null;

                if (!empty($_FILES[$imgField]['tmp_name'])) {
                    $imgBlob = file_get_contents($_FILES[$imgField]['tmp_name']);
                } elseif (!empty($existing_data[$imgField]) && ($name !== "" || $url !== "")) {
                    // Keep previously saved image
                    $imgBlob = $existing_data[$imgField];
                }

                // If all are empty, clear the slot
                if ($name === "" && $url === "" && !$imgBlob) {
                    $names[$x] = null;
                    $urls[$x] = null;
                    $images[$x] = null;
                } else {
                    $names[$x] = $name !== "" ? $name : null;
                    $urls[$x] = $url !== "" ? $url : null;
                    $images[$x] = $imgBlob;
                }
            }

            $hasAtLeastOne = false;
            for ($x = 1; $x <= 10; $x++) {
                if (
                    !empty(trim($_POST["d_pro_name$x"])) ||
                    !empty(trim($_POST["d_pro_url$x"])) ||
                    !empty($_FILES["d_pro_img$x"]['tmp_name'])
                ) {
                    $hasAtLeastOne = true;
                    break;
                }
            }

            if (!$hasAtLeastOne) {
                echo '<div class="alert danger">Please fill at least one product or service card.</div>';
                exit;
            }
            // Prepare SQL
            $setParts = [];
            $params = [];
            $types = "";

            for ($x = 1; $x <= 10; $x++) {
                $setParts[] = "d_pro_name$x = ?, d_pro_url$x = ?, d_pro_img$x = ?";
                $params[] = $names[$x];
                $params[] = $urls[$x];
                $params[] = $images[$x];
                $types .= "sss";
            }

            $sql = "UPDATE digi_card2 SET " . implode(", ", $setParts) . " WHERE id=?";
            $params[] = $_SESSION['card_id_inprocess'];
            $types .= "i";

            $stmt = $connect->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                header("Location: create_card5.php");
                exit;
            } else {
                echo '<div class="alert danger">Error! Try Again.</div>';
            }

            ob_end_flush();
        }
        ?>
    </div>

    <footer>
        <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
    </footer>

</body>

</html>