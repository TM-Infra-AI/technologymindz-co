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
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- country code -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <!-- 🟢 JavaScript for Cropping -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .iti {
            flex: 0 0 auto;
        }

        .iti--separate-dial-code .iti__selected-flag {
            background-color: white;
            border-radius: 6px;
            padding: 8px;
        }

        .iti__flag-container,
        .iti--separate-dial-code .iti__flag-container {
            padding: 10;
        }

        .iti__flag {
            height: 14px;
            background-position: -2413px 0px;
            display: none;
        }

        #phoneNumber {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
            padding: 10px;
            height: 40px;
            padding-left: 105 !important;
        }

        #whatsappNumber {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
            padding: 10px;
            height: 40px;
            padding-left: 105 !important;
        }

        .iti__country-list {
            z-index: 9999 !important;
        }

        #cropModal {
            display: none;
            width: 100%;
            /* Adjust width as per input field */
            max-width: 600px;
            /* Same width as form inputs */
            margin: 20px auto;
            /* Center it horizontally */
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        #cropImage {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            /* Prevent stretching */
            border-radius: 5px;
        }

        .crop-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .crop-buttons button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .crop-buttons button:hover {
            background: #0056b3;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function initializeIntlTelInput(input, countryCodeInput, countryFlagInput, storedContact, storedCountryCode) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "in",
                    separateDialCode: true,
                    preferredCountries: ["us", "gb", "in", "au"],
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    autoPlaceholder: "off"
                });

                if (storedContact) {
                    input.value = storedContact;
                }

                function getISOCodeByDialCode(dialCode) {
                    const countryData = window.intlTelInputGlobals.getCountryData();
                    for (let country of countryData) {
                        if (country.dialCode === dialCode.replace("+", "")) {
                            return country.iso2;
                        }
                    }
                    return null;
                }

                if (storedCountryCode) {
                    var isoCode = getISOCodeByDialCode(storedCountryCode);
                    if (isoCode) {
                        iti.setCountry(isoCode);
                    }
                }

                input.addEventListener("countrychange", function() {
                    var countryData = iti.getSelectedCountryData();
                    countryCodeInput.value = `+${countryData.dialCode}`;
                    countryFlagInput.value = `https://flagcdn.com/w40/${countryData.iso2}.png`;
                });

                return iti;
            }

            var phoneIti = initializeIntlTelInput(
                document.querySelector("#phoneNumber"),
                document.querySelector("#countryCode"),
                document.querySelector("#countryFlag"),
                "<?php echo htmlspecialchars($row['d_contact'] ?? ''); ?>",
                "<?php echo htmlspecialchars($row['d_country_code'] ?? ''); ?>"
            );

            var whatsappIti = initializeIntlTelInput(
                document.querySelector("#whatsappNumber"),
                document.querySelector("#wCountryCode"),
                document.querySelector("#wCountryFlag"),
                "<?php echo htmlspecialchars($row['d_whatsapp'] ?? ''); ?>",
                "<?php echo htmlspecialchars($row['w_country_code'] ?? ''); ?>"
            );

            document.querySelector("#card_form").addEventListener("submit", function() {
                var phoneData = phoneIti.getSelectedCountryData();
                document.querySelector("#countryCode").value = `+${phoneData.dialCode}`;
                document.querySelector("#countryFlag").value = `https://flagcdn.com/w40/${phoneData.iso2}.png`;

                var whatsappData = whatsappIti.getSelectedCountryData();
                document.querySelector("#wCountryCode").value = `+${whatsappData.dialCode}`;
                document.querySelector("#wCountryFlag").value = `https://flagcdn.com/w40/${whatsappData.iso2}.png`;
            });
        });

        function validateForm(event) {
            let valid = true;
            let errorMessages = [];
            let form = document.forms["card_form"];

            const fields = [{
                    name: "d_f_name",
                    message: "First name is required"
                },
                {
                    name: "d_position",
                    message: "Designation is required"
                },
                {
                    name: "d_contact",
                    message: "Phone number is required"
                },
                {
                    name: "d_address",
                    message: "Address is required"
                },
                {
                    name: "user_email",
                    message: "Email is required"
                },
                {
                    name: "d_about_us",
                    message: "About Us is required"
                }
            ];

            fields.forEach(field => {
                let input = document.forms["card_form"][field.name].value.trim();
                if (input === "") {
                    errorMessages.push(field.message);
                    valid = false;
                }
            });

            let nameRegex = /^[A-Za-z\s]{3,}$/;
            if (!nameRegex.test(form["d_f_name"].value.trim())) {
                errorMessages.push("First name must contain only letters, spaces, and be at least 3 characters long");
                valid = false;
            }
            if (form["d_l_name"].value.trim() && !nameRegex.test(form["d_l_name"].value.trim())) {
                errorMessages.push("Last name must contain only letters and spaces");
                valid = false;
            }
            if (!nameRegex.test(form["d_position"].value.trim())) {
                errorMessages.push("Designation must contain only letters, spaces, and be at least 3 characters long");
                valid = false;
            }

            let email = form["user_email"].value.trim();
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errorMessages.push("Invalid email format");
                valid = false;
            }

            // let phoneRegex = /^\d{10,15}$/; // Accepts 10 to 15 digits
            // if (!phoneRegex.test(form["d_contact"].value.trim())) {
            //     errorMessages.push("Phone number must be 15 digits");
            //     valid = false;
            // }

            // if (form["d_whatsapp"].value.trim() && !phoneRegex.test(form["d_whatsapp"].value.trim())) {
            //     errorMessages.push("WhatsApp number must be 15 digits");
            //     valid = false;
            // }
            let phoneValueRaw = form["d_contact"].value;
            let whatsappValueRaw = form["d_whatsapp"].value;

            // Remove all spaces
            let phoneValue = phoneValueRaw.replace(/\s+/g, '');
            let whatsappValue = whatsappValueRaw.replace(/\s+/g, '');

            // Validate phone
            if (phoneValue === "") {
                errorMessages.push("Phone number is required");
                valid = false;
            } else if (!/^[\d\s\-()]+$/.test(phoneValue)) {
                errorMessages.push("Phone number can only contain digits, spaces, dashes, and parentheses.");
                valid = false;
            } else if (phoneValue.replace(/\D/g, '').length > 12) {
                errorMessages.push("Phone number must be a maximum of 12 digits");
                valid = false;
            }

            // Validate WhatsApp (optional)
            if (whatsappValueRaw.trim() !== "") {
                if (!/^\d+$/.test(whatsappValue)) {
                    errorMessages.push("WhatsApp number must contain only digits (no letters or symbols)");
                    valid = false;
                } else if (whatsappValue.length > 12) {
                    errorMessages.push("WhatsApp number must be a maximum of 12 digits");
                    valid = false;
                }
            }

            let logoInDb = document.getElementById("logoInDb").value === "1";
            let logoFile = document.getElementById("clickMeImage").files.length;
            let croppedImage = document.getElementById("croppedImageData").value;

            if (!logoInDb && logoFile === 0 && croppedImage === "") {
                errorMessages.push("Profile photo is required");
                valid = false;
            }


            if (!valid) {
                showErrors(errorMessages);
                event.preventDefault();
                return false;
            }

            // if (!valid) event.preventDefault();
            return valid;
        }

        function showErrors(errorMessages) {
            event.preventDefault(); // Prevent form submission

            // Remove any existing toast messages
            document.querySelectorAll(".toastify").forEach(toast => toast.remove());

            if (errorMessages.length > 0) {
                // Show only the first error message
                Toastify({
                    text: errorMessages[0], // Show the first error only
                    duration: 3000,
                    gravity: "top", // Position: top/bottom
                    position: "center", // Align: left/center/right
                    backgroundColor: "#ff4d4d", // Red background
                    stopOnFocus: true
                }).showToast();
            }
        }

        function validateAndSkip(event, nextPage) {
            event.preventDefault(); // Prevent default navigation

            if (validateForm(event)) { // Call the existing validation function
                window.location.href = nextPage; // Allow skipping if validation passes
            }
        }

        const errorElement = document.getElementById("imageError");

        function clickFocus() {
            document.getElementById("clickMeImage").click();
        }

        //validate image
        // function validateImage(input) {
        //     const file = input.files[0]; // Get the selected file
        //     const maxSize = 2 * 1024 *1024 *1024; // 2GB in bytes
        //     const previewImage = document.getElementById("showPreviewLogo");
        //     const defaultImage = "images/logo.png"; // Default image
        //     const allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif"]; // Allowed file types

        //     if (file) {
        //         // Check file type
        //         if (!allowedTypes.includes(file.type)) {
        //             showToast("Invalid file type. Allowed: PNG, JPEG, JPG, GIF.", "red");
        //             input.value = ""; // Clear file input
        //             previewImage.src = defaultImage; // Reset preview
        //             return;
        //         }

        //         // Check file size
        //         if (file.size > maxSize) {
        //             showToast("File size must be less than 250KB.", "red");
        //             input.value = ""; // Clear file input
        //             previewImage.src = defaultImage; // Reset preview
        //             return;
        //         }

        //         // Show preview if valid
        //         readURL(input);
        //     } else {
        //         previewImage.src = defaultImage; // Reset if no file is selected
        //     }
        // }

        // // Function to preview image
        // function readURL(input) {
        //     const previewImage = document.getElementById("showPreviewLogo");

        //     if (input.files && input.files[0]) {
        //         const reader = new FileReader();
        //         reader.onload = function(e) {
        //             previewImage.src = e.target.result; // Set preview image
        //         };
        //         reader.readAsDataURL(input.files[0]); // Read file as data URL
        //     }
        // }


        //logo upload for crop 2 april 
        let cropper;

        function openCropper(input) {
            const file = input.files[0]; // Get the selected file
            const maxSize = 2 * 1024 * 1024 * 1024; // 250KB in bytes
            const allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif"]; // Allowed file types
            const previewImage = document.getElementById("showPreviewLogo");
            const defaultImage = "images/logo.png"; // Default image
            // Check file type
            if (!allowedTypes.includes(file.type)) {
                showToast("Invalid file type. Allowed: PNG, JPEG, JPG, GIF.", "red");
                input.value = ""; // Clear file input
                previewImage.src = defaultImage; // Reset preview
                return;
            }

            // Check file size
            if (file.size > maxSize) {
                showToast("File size must be less than 2GB.", "red");
                input.value = ""; // Clear file input
                previewImage.src = defaultImage; // Reset preview
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const cropImage = document.getElementById("cropImage");
                cropImage.src = e.target.result;
                document.getElementById("cropModal").style.display = "flex";

                cropImage.onload = function() {
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1,
                        viewMode: 2
                    });
                };
            };
            reader.readAsDataURL(input.files[0]);
        }

        function cropAndUpload() {
            if (cropper) {
                const croppedCanvas = cropper.getCroppedCanvas();
                croppedCanvas.toBlob((blob) => {
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        document.getElementById("croppedImageData").value = reader.result.split(',')[1]; // Store Base64 (only the encoded part)
                        document.getElementById("showPreviewLogo").src = reader.result; // Preview cropped image
                        closeCropper();
                    };
                    reader.readAsDataURL(blob);
                }, "image/jpeg");
            }
        }

        function uploadCroppedImage(file) {
            const formData = new FormData();
            formData.append("d_logo", file);

            fetch("upload_script.php", {
                    method: "POST",
                    body: formData
                }).then(response => response.text())
                .then(data => {
                    document.getElementById("showPreviewLogo").src = URL.createObjectURL(file);
                    closeCropper();
                });
        }

        function uploadWithoutCropping() {
            const fileInput = document.getElementById("clickMeImage");
            const file = fileInput.files[0];
            uploadCroppedImage(file);
        }

        function closeCropper() {
            document.getElementById("cropModal").style.display = "none";
            if (cropper) {
                cropper.destroy();
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
    </script>
</head>

<body>

    <div class="main3">
        <div class="navigator_up">
            <a href="select_theme.php">
                <div class="nav_cont"><i class="fa fa-map"></i> Select Theme</div>
            </a>
            <a href="create_card2.php">
                <div class="nav_cont active"><i class="fa fa-building"></i>Profile Details</div>
            </a>
            <a href="#" onclick="validateAndSkip(event, 'create_card3.php')">
                <div class="nav_cont"><i class="fa fa-globe"></i> Social Links</div>
            </a>
            <a href="#" onclick="validateAndSkip(event, 'create_card4.php')">
                <div class="nav_cont"><i class="fa fa-ticket"></i> Product & Service</div>
            </a>
            <a href="#" onclick="validateAndSkip(event, 'create_card5.php')">
                <div class="nav_cont"><i class="fa fa-image"></i> Image Gallery</div>
            </a>
            <a href="create_card6.php">
                <div class="nav_cont"><i class="fa fa-file-pdf"></i> PDF Upload</div>
            </a>
             <a href="create_card7.php">
                <div class="nav_cont "><i class="fa fa-file-video"></i> Video Upload</div>
            </a>
            <a href="#" onclick="validateAndSkip(event, 'preview_page.php')">
                <div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div>
            </a>
        </div>

        <div class="btn_holder">
            <a href="select_theme.php">
                <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
            </a>
            <a href="create_card3.php" onclick="return validateAndSkip(event, 'create_card3.php')">
                <div class="skip_btn" id="skipButton">Skip <i class="fa fa-chevron-circle-right"></i></div>
            </a>

        </div>

        <h1>Profile Details</h1>

        <form id="card_form" action="" method="POST" enctype="multipart/form-data"
            onsubmit="return validateForm(event)">

            <!-- <img src="<?php //echo !empty($row['d_logo']) ? 'data:image/*;base64,' . base64_encode($row['d_logo']) : 'images/logo.png'; 
                            ?>"
                alt="Select image" id="showPreviewLogo" onclick="clickFocus()">
            <div class="input_box">
                <p>*Profile Photo</p>
                <input type="file" name="d_logo" id="clickMeImage" onchange="validateImage(this);"
                    accept="image/png, image/jpeg, image/jpg, image/gif">

                <p id="imageError" style="color: red; display: none;">File size exceeds 2GB. Please select a smaller
                    image.</p>
            </div> -->
            <?php
            if (!empty($row['d_logo'])) {
                $logoInDb = !empty($row['d_logo']);
                $logo = 'data:image/*;base64,' . base64_encode($row['d_logo']);
            } else {
                $logo = '';
            }
            ?>
            <img src="<?php echo $logo; ?>" alt="Select image" id="showPreviewLogo" onclick="document.getElementById('clickMeImage').click();">
            <div class="input_box">
                <p>Company Name *</p>
                <input type="text" name="d_comp_name" maxlength="199" value="<?php   echo htmlspecialchars($row['d_comp_name']); ?>" placeholder="Enter Company Name" required>
            </div>
            <!-- Profile Photo -->
            <div class="input_box">
                <p>*Profile Photo</p>
                <input type="file" name="d_logo" id="clickMeImage" onchange="openCropper(this);" accept="image/png, image/jpeg, image/jpg, image/gif">
                <p id="imageError" style="color: red; display: none;">File size exceeds 2GB. Please select a smaller image.</p>
            </div>
            <input type="hidden" id="logoInDb" value="<?php echo $logoInDb ? '1' : '0'; ?>">
            <input type="hidden" name="croppedImageData" id="croppedImageData">

            <!-- Crop Modal (Initially Hidden) -->
            <div id="cropModal" style="display: none; position: relative; margin-top: 10px; flex-direction: column;">
                <h3>Crop Your Image</h3>
                <img id="cropImage">
                <div class="crop-buttons">
                    <button type="button" onclick="cropAndUpload();">Crop & Upload</button>
                    <button type="button" onclick="uploadWithoutCropping();">Upload Without Cropping</button>
                    <button type="button" onclick="closeCropper();">Cancel</button>
                </div>
            </div>


            <!-- <h3>Personal Details</h3> -->
            <div class="input_box">
                <p>* First Name</p><input type="text" name="d_f_name"
                    value="<?php echo htmlspecialchars($row['d_f_name']); ?>" required>
            </div>
            <div class="input_box">
                <p>Last Name</p><input type="text" name="d_l_name"
                    value="<?php echo htmlspecialchars($row['d_l_name']); ?>">
            </div>
            <div class="input_box">
                <p>* Designation</p><input type="text" name="d_position"
                    value="<?php echo htmlspecialchars($row['d_position']); ?>" required>
            </div>
            <div class="input_box" style="text-align:left !important;">
                <p>* Phone No</p>
                <input type="tel" id="phoneNumber" name="d_contact" required value="<?php echo htmlspecialchars($row['d_contact'] ?? ''); ?>">
                <input type="hidden" id="countryCode" name="d_country_code">
                <input type="hidden" id="countryFlag" name="d_country_flag">
            </div>
            <div class="input_box" style="text-align:left !important;">
                <p>WhatsApp No</p>
                <input type="tel" id="whatsappNumber" name="d_whatsapp"
                    value="<?php echo htmlspecialchars($row['d_whatsapp'] ?? ''); ?>">
                <input type="hidden" id="wCountryCode" name="w_country_code"
                    value="<?php echo htmlspecialchars($row['w_country_code'] ?? ''); ?>">
                <input type="hidden" id="wCountryFlag" name="w_country_flag"
                    value="<?php echo htmlspecialchars($row['w_country_flag'] ?? ''); ?>">
            </div>
            <div class="input_box">
                <p>* Address</p><textarea name="d_address"
                    required><?php echo htmlspecialchars($row['d_address']); ?></textarea>
            </div>
            <div class="input_box">
                <p>* Email</p><input type="email" name="user_email"
                    value="<?php echo htmlspecialchars($row['user_email']); ?>" required>
            </div>
            <div class="input_box">
                <p>Website</p><input type="text" name="d_website"
                    value="<?php echo htmlspecialchars($row['d_website']); ?>">
            </div>
            <!-- <div class="input_box">
                <p>* Company Established</p><input type="text" name="d_comp_est_date"
                    value="<?php //echo htmlspecialchars($row['d_comp_est_date']); 
                            ?>" required>
            </div> -->
            <div class="input_box">
                <p>* About Us</p><textarea name="d_about_us"
                    required><?php echo htmlspecialchars($row['d_about_us']); ?></textarea>
            </div>

            <label style=" padding-right: 520;">
                <input type="checkbox" name="d_status" value="1" <?php echo (!empty($row['d_status']) && $row['d_status'] == 1) ? 'checked' : ''; ?>>
                Visitor info required
            </label>

            <input type="submit" name="process2" value="Next">
        </form>

        <?php

        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;

        require __DIR__ . '/../../vendor/autoload.php';
        require_once('../../includes/config.php');

        if (isset($_POST['process2'])) {
            require 'connect.php'; // Include database connection

            function compressImage($source, $destination, $quality)
            {
                $imageInfo = getimagesize($source);
                if (!$imageInfo) {
                    return false;
                }

                $mime = $imageInfo['mime'];
                // echo "<pre>"; print_r($imageInfo); echo "</pre>";die;

                switch ($mime) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($source);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($source);
                        imagepalettetotruecolor($image); // Convert PNG to true color to allow quality setting
                        imagesavealpha($image, true);
                        break;
                    case 'image/gif':
                        $image = imagecreatefromgif($source);
                        break;
                    default:
                        return false;
                }

                // Ensure the destination directory exists
                $destinationDir = dirname($destination);
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }

                if ($image instanceof GdImage) {
                    echo "Image loaded successfully!";
                } else {
                    echo "Failed to load image.";
                }

                // echo "<pre>"; print_r($image); print_r($destination); print_r($quality); echo "</pre>";die;
                if (imagejpeg($image, $destination, $quality)) {
                    imagedestroy($image);
                    return $destination;
                } else {
                    return false;
                }
            }


            // Validate required fields
            $required_fields = ['d_comp_name', 'd_f_name', 'd_position', 'd_contact', 'd_address', 'user_email', 'd_about_us'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    die("Error: " . ucfirst(str_replace("_", " ", $field)) . " is required.");
                }
            }


            //  image upload for cropped and uncroped
            $logo = null;

            // Check if cropped image exists (Base64 format)
            if (!empty($_POST['croppedImageData'])) {
                $logo = base64_decode($_POST['croppedImageData']); // Convert Base64 to binary

            } elseif (!empty($_FILES['d_logo']['tmp_name'])) {
                // Handle normal file upload
                $filename = $_FILES['d_logo']['name'];
                $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowedTypes = ['png', 'jpeg', 'jpg', 'gif'];

                if (in_array($imageFileType, $allowedTypes)) {
                    $logo = file_get_contents($_FILES["d_logo"]['tmp_name']);
                } else {
                    die("Error: Invalid file type. Allowed: PNG, JPEG, JPG, GIF.");
                }
            }

            if ($logo !== null) {
                $stmt = $connect->prepare("UPDATE digi_card SET d_logo = ? WHERE id = ?");
                $stmt->bind_param("si", $logo, $_SESSION['card_id_inprocess']);

                if ($stmt->execute()) {
                    header("Location: create_card3.php");
                    exit;
                } else {
                    die("Error: Failed to update image in database.");
                }
            }

            // Normalize names
            $new_f_name = strtolower(trim($_POST['d_f_name']));
            $new_l_name = strtolower(trim($_POST['d_l_name']));

            // Fetch old values from DB
            $stmt = $connect->prepare("SELECT user_email, d_f_name, d_l_name, card_id FROM digi_card WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['card_id_inprocess']);
            $stmt->execute();
            $result = $stmt->get_result();
            $old_data = $result->fetch_assoc();

            $old_f_name = strtolower(trim($old_data['d_f_name']));
            $old_l_name = strtolower(trim($old_data['d_l_name']));
            $user_email = $old_data['user_email'];

            // Generate base card ID
            $base_card_id = preg_replace("/[^a-z0-9]/", "_", $new_f_name . '_' . $new_l_name);

            if ($new_f_name === $old_f_name && $new_l_name === $old_l_name) {
                $card_id = $old_data['card_id']; // Keep same ID
            } else {
                // Name changed, generate unique card ID
                $stmt = $connect->prepare("SELECT card_id FROM digi_card WHERE d_f_name = ? AND d_l_name = ?");
                $stmt->bind_param("ss", $new_f_name, $new_l_name);
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

            $d_status = isset($_POST['d_status']) ? 1 : 0; // Set default to 0 if unchecked



            // Ensure email is unique for other records
            $checkStmt = $connect->prepare("SELECT id FROM digi_card WHERE user_email = ? AND id != ?");
            $checkStmt->bind_param("si", $_POST['user_email'], $_SESSION['card_id_inprocess']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                echo "<script src='https://cdn.jsdelivr.net/npm/toastify-js'></script>
                <script>
                Toastify({
                    text: 'This email is already associated with another card!',
                    duration: 4000,
                    gravity: 'top', 
                    position: 'center', 
                    backgroundColor: '#ff6b6b',
                    close: true
                }).showToast();
                </script>";
                exit;
            }
            // ✅ Update session if email changed
            if ($_POST['user_email'] != $_SESSION['user_email']) {
                $_SESSION['user_email'] = $_POST['user_email'];
                $_SESSION['current_email'] = $_POST['user_email'];
            }
            // ✅ Update session if first name changed
            if ($_POST['d_f_name'] != $_SESSION['first_name']) {
                $_SESSION['first_name'] = $_POST['d_f_name'];
            }
            
            $d_contact = $_POST['d_contact'];
            if (!empty($_POST['d_contact'])) {
                $d_contact = preg_replace('/\D+/', '', $_POST['d_contact']); // Removes all non-digit characters
            }

            $stmt = $connect->prepare("UPDATE digi_card 
                SET d_comp_name = ?, d_f_name = ?, d_l_name = ?, d_position = ?, d_contact = ?, d_country_code = ?, d_country_flag = ?, 
                    d_whatsapp = ?, w_country_code = ?, w_country_flag = ?, d_address = ?, user_email = ?, d_website = ?, 
                    d_about_us = ?, card_id = ?,  d_status = ?
                WHERE id = ?");

            $stmt->bind_param(
                "sssssssssssssssii", // 15 strings (s), 2 integers (i)
                $_POST['d_comp_name'],
                $_POST['d_f_name'],
                $_POST['d_l_name'],
                $_POST['d_position'],
                $d_contact,
                $_POST['d_country_code'],
                $_POST['d_country_flag'],
                $_POST['d_whatsapp'],
                $_POST['w_country_code'],
                $_POST['w_country_flag'],
                $_POST['d_address'],
                $_POST['user_email'],
                $_POST['d_website'],
                $_POST['d_about_us'], // Ensure it's a valid string
                $card_id,
                $d_status, // Active or Inactive (1 or 0)
                $_SESSION['card_id_inprocess'] // Ensure this is an integer
            );

            $stmt->execute();

            if ($is_mail) {
                // SMTP Email Script
                $mail = new PHPMailer(true); // Enable exceptions

                try {
                    // SMTP Configuration
                    // Load credentials from the credentials file
                    $creds = require  __DIR__ . '/../../mail_credentials.php';

                    // Setup PHPMailer
                    $mail->isSMTP();
                    $mail->Host = $creds['host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $creds['username'];
                    $mail->Password = $creds['password'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $creds['port'];

                    // From email and name
                    $mail->setFrom($fromEmail ?? $creds['from_email'], $fromName ?? $creds['from_name']);
                    $mail->addAddress($user_email); // Recipient

                    $mail->Subject = $_SERVER['HTTP_HOST'] . " - Your Updated Business Card URL";

                    $fname = $_POST['d_f_name'];
                    $card_url = BASE_URL .'/'. $card_id;

                    $mail->Body = "Hello $fname,\n\nYour digital business card has been successfully updated.\n\nYou can view it here:\n$card_url\n\nBest regards,\n" . $_SERVER['HTTP_HOST'];

                    $mail->send();

                    echo '<div class="alert success">Your updated Business Card URL has been sent to <strong>' . $user_email . '</strong>. Please check your inbox or spam/junk folder.</div>';
                } catch (Exception $e) {
                    echo '<div class="alert danger">Error sending email: ' . $mail->ErrorInfo . '</div>';
                }
            }

            // if ($logo !== null) {
            //     $logo = file_get_contents($destination);  // Read file
            //     // $logo = base64_encode($logo);            // Convert to base64 before storing

            //     $stmt = $connect->prepare("UPDATE digi_card SET d_logo = ? WHERE id = ?");
            //     $stmt->bind_param("si", $logo, $_SESSION['card_id_inprocess']);
            //     $stmt->execute();
            // }

            header("Location: create_card3.php");
            exit;
        }
        ?>
    </div>

    <footer>
        <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
    </footer>

</body>

</html>