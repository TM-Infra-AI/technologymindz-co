<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// session_start(); // ✅ Start sessions
// require('panel/login/connect.php'); 
include_once('db_config.php'); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$formSubmitted = isset($_SESSION['form_submitted']) ? $_SESSION['form_submitted'] : false;
$_SESSION['form_status'] = false;
$urlPath = trim($_SERVER['REQUEST_URI'], '/'); // Removes leading/trailing slashes'
$card_id = $urlPath;
// $card_id = $_SESSION['card_id_inprocess'];
// var_dump($card_id);


if (!empty($urlPath)) {
    //check for active status
    $query_status = "SELECT d_status,user_email, d_f_name FROM digi_card WHERE card_id = ?";
    $stmt_status = $connect->prepare($query_status);
    $stmt_status->bind_param("s", $card_id);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();

    if ($result_status->num_rows > 0) {
        $row_status = $result_status->fetch_assoc();
        $d_status = (int)$row_status['d_status']; // Convert to integer for comparison
        $user_email = $row_status['user_email']; // Fetch user_email
        $_SESSION['user_email'] = $user_email; // Store user_email in session
        if ($d_status === 1) {
            // If d_status is 1, redirect user
            $_SESSION['form_status'] = true;
            // $formSubmitted = false;
            // echo "<script>alert('hello  from popup');</script>";
            // $update_query = "UPDATE digi_card SET d_status = 0 WHERE card_id = ?";
            // $stmt_update = $connect->prepare($update_query);
            // $stmt_update->bind_param("s", $user_email);
            // $stmt_update->execute();
            // $stmt_update->close();
        }
    }
    $stmt_status->close();
}

// ✅ Stop mail from sending on page load
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $name = $_POST['name'];
    $phone = preg_replace('/\s+/', '', $_POST['phone']);
    $company = $_POST['company'];
    $email = $_POST['email'];


    // $user_email= $_SESSION['user_email'];
    $_SESSION['submitted_email'] = $email;

    if (!$card_id) {
        error_log("Error: card_id is missing!");
    } else {
        error_log("Using card_id: " . $card_id);
    }

    // ✅ Check if email exists and insert/update accordingly
    $sql = "INSERT INTO contact_details (name, phone, company, email, card_id) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), company = VALUES(company), card_id = VALUES(card_id)";

    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sssss", $name, $phone, $company, $email, $card_id);
    $stmt->execute();
    $stmt->close();



    // ✅ Fetch PDF if available
    $pdf_path = null;
    $pdf_name = null;
    $video_path = null;
    $video_name = null;

    if (isset($_SESSION['user_email'])) {
        $user_email = $_SESSION['user_email'];

        // Fetch selected_pdf value
        $query_select = "SELECT selected_pdf FROM digi_card4 WHERE user_email = ?";
        $stmt_select = $connect->prepare($query_select);
        $stmt_select->bind_param("s", $user_email);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();

        if ($result_select->num_rows > 0) {
            $row = $result_select->fetch_assoc();
            $selected_pdf = (int) $row['selected_pdf']; // Ensure it's an integer

            // Validate selected_pdf (prevent SQL injection)
            if ($selected_pdf >= 1 && $selected_pdf <= 5) { // Assuming max column index is 5
                $pdf_column = "d_service_pdf" . $selected_pdf;
                $name_column = "d_service_name" . $selected_pdf;
                // Fetch the selected PDF
                $query_pdf = "SELECT $pdf_column, $name_column FROM digi_card4 WHERE user_email = ?";
                $stmt_pdf = $connect->prepare($query_pdf);
                $stmt_pdf->bind_param("s", $user_email);
                $stmt_pdf->execute();
                $result_pdf = $stmt_pdf->get_result();

                if ($result_pdf->num_rows > 0) {
                    $row = $result_pdf->fetch_assoc();
                    $pdf_data = $row[$pdf_column];
                    $pdf_name = !empty($row[$name_column]) ? $row[$name_column] : 'default.pdf';

                    // Save the BLOB data to a temporary file
                    $pdf_path = '/tmp/' . uniqid('pdf_', true) . '.pdf';
                    if (!file_exists($pdf_path)) {
                        file_put_contents($pdf_path, $pdf_data);
                    }
                }
                $stmt_pdf->close();
            }
        }
        $stmt_select->close();

        // Fetch selected_video value
        $query_select_video = "SELECT selected_video FROM uploaded_video WHERE user_email = ?";
        $stmt_select_video = $connect->prepare($query_select_video);
        $stmt_select_video->bind_param("s", $user_email);
        $stmt_select_video->execute();
        $result_select_video = $stmt_select_video->get_result();

        if ($result_select_video->num_rows > 0) {
            $row = $result_select_video->fetch_assoc();
            $selected_video = isset($row['selected_video']) ? (int) trim($row['selected_video']) : 0; // Ensure it's an integer

 
                // Validate selected_video
            if ($selected_video >=1 && $selected_video <= 5) {
                $video_column = "d_service_video" . $selected_video;
                $video_name_column = "d_service_name" . $selected_video;

                $query_video = "SELECT $video_column, $video_name_column FROM uploaded_video WHERE user_email = ?";
                $stmt_video = $connect->prepare($query_video);
                $stmt_video->bind_param("s", $user_email);
                $stmt_video->execute();
                $result_video = $stmt_video->get_result();

                if ($result_video->num_rows > 0) {
                    $row = $result_video->fetch_assoc();
                    $video_data = $row[$video_column];
                    $video_name = !empty($row[$video_name_column]) ? $row[$video_name_column] : 'default_video';

                    // Save the video BLOB data to a temporary file
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime_type = $finfo->buffer($video_data);

                    $extensions = [
                        'video/mp4' => '.mp4',
                        'video/avi' => '.avi',
                        'video/quicktime' => '.mov',
                        'video/x-ms-wmv' => '.wmv',
                        'video/webm' => '.webm',
                        'video/mpeg' => '.mpeg',
                        'video/3gpp' => '.3gp',
                        // add more if needed
                    ];

                    $extension = $extensions[$mime_type] ?? '.mp4'; // fallback to .mp4 if unknown

                    $video_path = '/tmp/' . uniqid('video_', true) . $extension;

                    file_put_contents($video_path, $video_data);
                }
                $stmt_video->close();
            }
        }
        $stmt_select_video->close();
    }


    // ✅ Send Email if not already sent (checked via localStorage in JS)
    $mail = new PHPMailer(true);
    try {

        // Load credentials from the credentials file
        $creds = require $_SERVER['DOCUMENT_ROOT'] . '/mail_credentials.php';

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

        $cardOwnerName = $row_status['d_f_name'];
        $mail->addAddress($email);
        $mail->Subject = "Greetings from " . $cardOwnerName;

        $userName = htmlspecialchars($_POST['name'] ?? 'user');

        $mail->Body = "Dear $userName,\n\n";
        $mail->Body .= "Thank you for sharing your contact details.\n\n";
        $mail->Body .= "Please see our company/product information attached.\n\n";
        $mail->Body .= "Regards,\n$cardOwnerName";


        //Attach the PDF file if it exists
        if ($pdf_path && file_exists($pdf_path)) {
            $mail->addAttachment($pdf_path, $pdf_name);
        }
       
        // Attach Video if exists
        if ($video_path && file_exists($video_path)) {
            $mail->addAttachment($video_path, $video_name);
        }

        $mail->send();
        $_SESSION['form_submitted'] = true;

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
            Toastify({
                text: 'Thanks for submitting your details!, we will reach out to you on your email id shortly.',
                duration: 3000,
                gravity: 'top',
                position: 'right',
                style: { background: 'green' },
                close: true
            }).showToast();
        });
        </script>";

        $name = $phone = $company = $email = '';
        unset($_POST);
    } catch (Exception $e) {
        echo "<script>alert('Error sending email: {$mail->ErrorInfo}');</script>";
    }

    if ($pdf_path && file_exists($pdf_path)) {
        unlink($pdf_path);
    }
    if ($video_path && file_exists($video_path)) {
        unlink($video_path);
    }

    // $connect->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Details</title>
    <!-- Toastify.js CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify.js Script -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Background Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Popup Box */
        .popup {
            display: none;
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            border: 1px solid #ddd;
            transition: all 0.3s ease-in-out;
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 22px;
            font-weight: bold;
            color: red;
            border: none;
            background: none;
        }

        .popup h2 {
            margin-top: 0;
            text-align: center;
        }

        .popup form {
            display: flex;
            flex-direction: column;
        }

        .popup label {
            font-weight: bold;
            margin-top: 10px;
        }

        .popup input {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .popup button {
            margin-top: 15px;
            padding: 12px;
            border: none;
            background: #007bff;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .popup button:hover {
            background: #0056b3;
        }

        /* ✅ Responsive Design */
        @media (max-width: 500px) {
            .popup {
                width: 90%;
                padding: 15px;
            }

            .popup input {
                font-size: 14px;
                padding: 8px;
            }

            .popup button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Background Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Popup Box -->
    <div class="popup" id="popup">
        <button class="close-btn" id="closePopup">&times;</button>
        <h2>Visitor details</h2>

        <!-- Camera Preview Section -->
        <div id="camera-section" style="display: none; text-align: center;">
            <video id="video" autoplay playsinline style="width: 100%; max-height: 300px;"></video>
            <br />
            <button type="button" onclick="takeSnapshot()">Capture</button>
        </div>

        <!-- Captured Image Preview -->
        <div id="image-preview-section" style="display: none; text-align: center;">
            <img id="captured-image" src="" alt="Captured Image" style="max-width: 100%; max-height: 300px;" />
            <br />
            <button type="button" onclick="usePhoto()">Use this photo</button>
            <button type="button" onclick="retakePhoto()">Retake</button>
        </div>

        <div id="loading" style="display: none;">⏳ Reading your card, please wait...</div>

        <!-- Capture Trigger -->
        <button type="button" onclick="startCamera()">Capture Image</button>

        <!-- Hidden Canvas and Input -->
        <canvas id="canvas" style="display:none;"></canvas>
        <input type="hidden" id="capturedImageInput" name="cardPhoto">

        <form action="" method="POST">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" required>

            <label>Company:</label>
            <input type="text" name="company" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <button type="submit" name="submit">Submit</button>
        </form>
    </div>

    <script>
        window.onload = function() {
            var popup = document.getElementById("popup");
            var overlay = document.getElementById("overlay");
            var closeBtn = document.getElementById("closePopup");

            const formStatus = <?php echo isset($_SESSION['form_status']) && $_SESSION['form_status'] ? 'true' : 'false'; ?>;
            const formSubmitted = <?php echo isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] ? 'true' : 'false'; ?>;

            if (formStatus && !formSubmitted && !sessionStorage.getItem('formSubmitted')) {
                if (popup && overlay) {
                    popup.style.display = "block";
                    overlay.style.display = "block";
                }
                // sessionStorage.setItem('formSubmitted', 'true');
            }
            // Mark form as submitted in sessionStorage to prevent the popup from showing again
            document.querySelector("form").addEventListener("submit", function() {
                sessionStorage.setItem('formSubmitted', 'true');
            });

            // Close button functionality
            if (closeBtn) {
                closeBtn.addEventListener("click", function() {
                    popup.style.display = "none";
                    overlay.style.display = "none";
                });
            }

            // Close the popup if overlay is clicked
            overlay.addEventListener("click", function() {
                popup.style.display = "none";
                overlay.style.display = "none";
            });
        };
    </script>

    <!-- <script>
        window.onload = function() {
            var popup = document.getElementById("popup");
            var overlay = document.getElementById("overlay");
            var closeBtn = document.getElementById("closePopup");

            // ✅ Use PHP inside JS to check if form was submitted
            var formSubmitted = <?php echo $formSubmitted ? 'true' : 'false'; ?>;
            var formStatus = <?php echo $formStatus ? 'true' : 'false'; ?>;


            if (formStatus) { // Show popup ONLY if form is NOT submitted
                popup.style.display = "block";
                overlay.style.display = "block";
            }

            if (closeBtn) {
                closeBtn.addEventListener("click", function() {
                    popup.style.display = "none";
                    overlay.style.display = "none";
                });
            }

            overlay.addEventListener("click", function() {
                popup.style.display = "none";
                overlay.style.display = "none";
            });
        };
    </script> -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector("form").addEventListener("submit", function(e) {
                let name = document.querySelector("input[name='name']").value.trim();
                let phone = document.querySelector("input[name='phone']").value.trim();
                let company = document.querySelector("input[name='company']").value.trim();
                let email = document.querySelector("input[name='email']").value.trim();

                let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                let errorSet = new Set(); // Use a Set to store unique errors

                if (name === "") {
                    errorSet.add("Name is required.");
                }
                // let phonePattern = /^[0-9]{15}$/; // Only numbers, 10-15 digits
                // if (!phone.match(phonePattern)) {
                //     errorSet.add("Enter a valid phone number (max 15 digits).");
                // }
                let rawPhone = phone; // original input
                let cleanedPhone = phone.replace(/\s/g, ''); // remove all spaces

                let digitOnlyPattern = /^[0-9]+$/; // only digits

                // Check for invalid characters (non-digits excluding spaces)
                if (!digitOnlyPattern.test(cleanedPhone)) {
                    errorSet.add("Phone number must only contain digits (no letters or special characters).");
                } else if (cleanedPhone.length > 15) {
                    errorSet.add("Phone number must not exceed 15 digits.");
                }

                if (company === "") {
                    errorSet.add("Company name is required.");
                }

                if (!email.match(emailPattern)) {
                    errorSet.add("Enter a valid email address.");
                }

                if (errorSet.size > 0) {
                    e.preventDefault(); // Stop form submission

                    let errorMessage = Array.from(errorSet).join("\n"); // Combine errors into one message

                    // Remove existing toast before showing a new one
                    if (window.currentToast) {
                        window.currentToast.hideToast();
                    }

                    // Show single toast for all errors
                    window.currentToast = Toastify({
                        text: errorMessage,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ff3e3e", // Red for error
                    }).showToast();
                }
            });
        });


        // Parse image with OCR
        let videoStream;

        function startCamera() {
            document.getElementById('camera-section').style.display = 'block';
            document.getElementById('image-preview-section').style.display = 'none';

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: {
                        exact: "environment"
                    }
                }, // rear camera
                audio: false
            }).then(function(stream) {
                videoStream = stream;
                const video = document.getElementById('video');
                video.srcObject = stream;
            }).catch(function(err) {
                console.error("Camera error:", err);
                alert("Unable to access rear camera. Trying front camera...");

                // fallback to front camera
                navigator.mediaDevices.getUserMedia({
                    video: true
                }).then(function(stream) {
                    videoStream = stream;
                    document.getElementById('video').srcObject = stream;
                });
            });
        }

        function takeSnapshot() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageDataURL = canvas.toDataURL('image/png');
            document.getElementById('captured-image').src = imageDataURL;
            document.getElementById('capturedImageInput').value = imageDataURL;

            document.getElementById('camera-section').style.display = 'none';
            document.getElementById('image-preview-section').style.display = 'block';
        }

        function usePhoto() {
            stopCamera();

            const base64Image = document.getElementById('capturedImageInput').value;
            if (!base64Image) return;

            document.getElementById('loading').style.display = 'block';

            // Convert base64 to Blob
            const blob = dataURLtoBlob(base64Image);
            
            const formData = new FormData();
            formData.append('image', blob, 'photo.png');
            var baseUrl = window.location.origin;

            fetch(baseUrl + '/parse_card_byocr.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(raw => {
                    console.log('Raw OCR API response:', raw);

                    try {
                        const fixedResponse = fixBrokenJSON(raw);
                        console.log('Fixed JSON:', fixedResponse);

                        const data = JSON.parse(fixedResponse);
                        document.getElementById('loading').style.display = 'none';

                        if (data["Full Name"]) document.querySelector('input[name="name"]').value = data["Full Name"];
                        if (data["Email Address"]) document.querySelector('input[name="email"]').value = data["Email Address"];
                        if (data["Phone Number"]) document.querySelector('input[name="phone"]').value = data["Phone Number"];
                        if (data["Phone Number"]) {
                            let phoneRaw = data["Phone Number"];

                            // Remove country code in formats like +91, (+91), [+91], 91-, etc.
                            phoneRaw = phoneRaw.replace(/(\(?\+?\[?91\]?\)?[\s-]*)/g, '');
                            const digitsOnly = phoneRaw.replace(/\D/g, '');
                            const matchedPhone = digitsOnly.match(/\d{10}/);
                            const cleanedPhone = matchedPhone ? matchedPhone[0] : '';
                            document.querySelector('input[name="phone"]').value = cleanedPhone;
                        }

                    } catch (jsonErr) {
                        console.error('JSON parse error:', jsonErr.message);
                        alert('Failed to read the card. Server returned invalid response.');
                        document.getElementById('loading').style.display = 'none';
                    }
                })
                .catch(err => {
                    document.getElementById('loading').style.display = 'none';
                    console.error("OCR API error:", err);
                    alert("Error connecting to OCR API.");
                });
        }


        function retakePhoto() {
            document.getElementById('image-preview-section').style.display = 'none';
            startCamera();
        }

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
        }

        function dataURLtoBlob(dataURL) {
            const parts = dataURL.split(';base64,');
            const byteString = atob(parts[1]);
            const mimeString = parts[0].split(':')[1];
            const arrayBuffer = new ArrayBuffer(byteString.length);
            const intArray = new Uint8Array(arrayBuffer);

            for (let i = 0; i < byteString.length; i++) {
                intArray[i] = byteString.charCodeAt(i);
            }

            return new Blob([intArray], {
                type: mimeString
            });
        }


        function fixBrokenJSON(raw) {
            // Split lines and clean up
            const lines = raw.trim().split('\n');

            let cleanedLines = [];

            for (let line of lines) {
                line = line.trim();

                if (line === '{' || line === '}') continue; // Skip open/close braces if present

                const match = line.match(/^([^:]+):\s*(.*)$/);
                if (match) {
                    let key = match[1].trim();
                    let value = match[2].trim();

                    // If value is missing or invalid, make it empty string
                    if (!value || value === '\r' || value === '\n') value = "";

                    // Ensure both key and value are quoted
                    cleanedLines.push(`"${key}": "${value}"`);
                }
            }

            const jsonString = `{${cleanedLines.join(',')}}`;
            return jsonString;
        }
    </script>
</body>
</html>