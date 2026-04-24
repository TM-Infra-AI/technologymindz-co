<?php
ob_start(); // Prevent output issues before redirection

require('connect.php');
require('header.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_email = $_SESSION['user_email'];
$card_id = $_SESSION['card_id_inprocess'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hasChanges = false; // Track if any updates were made
    // Radio button value save
    $selected_video = isset($_POST['selected_video']) ? intval($_POST['selected_video']) : NULL;

    $stmt = $connect->prepare("UPDATE uploaded_video SET selected_video = ? WHERE card_id = ?");
    $stmt->bind_param("is", $selected_video, $card_id);

    if ($stmt->execute()) {
        echo "Selected video updated successfully!";
    } else {
        echo "Error updating selected video.";
    }

    $stmt->close();
    // Radio button value save


    // Handle file uploads
    for ($m = 1; $m <= 5; $m++) {
        $videoKey = "d_service_video$m";
        $nameKey = "d_service_name$m";
        $documentName = isset($_POST[$nameKey]) ? htmlspecialchars($_POST[$nameKey]) : '';
        $isFileUploaded = !empty($_FILES[$videoKey]["tmp_name"]);

        if ($isFileUploaded || !empty($documentName)) {
            $videoContent = $isFileUploaded ? file_get_contents($_FILES[$videoKey]['tmp_name']) : null;

            // Check if row exists
            $stmt = $connect->prepare("SELECT id FROM uploaded_video WHERE card_id = ?");
            $stmt->bind_param("s", $card_id);
            $stmt->execute();
            $stmt->store_result();
            $rowExists = $stmt->num_rows > 0;
            $stmt->close();

            if ($rowExists) {
                if ($isFileUploaded) {
                    $query = "UPDATE uploaded_video SET `d_service_name$m` = ?, `d_service_video$m` = ? WHERE card_id = ?";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("sss", $documentName, $videoContent, $card_id);
                } else {
                    $query = "UPDATE uploaded_video SET `d_service_name$m` = ? WHERE card_id = ?";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("ss", $documentName, $card_id);
                }
            } else {
                // Only insert if there's a file, to avoid empty video rows
                if ($isFileUploaded) {
                    $query = "INSERT INTO uploaded_video (id, user_email, card_id, `d_service_name$m`, `d_service_video$m`) VALUES (UUID(), ?, ?, ?, ?)";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("ssss", $user_email, $card_id, $documentName, $videoContent);
                } else {
                    continue; // No video or new file — skip
                }
            }

            if ($stmt->execute()) {
                $hasChanges = true;
            }

            $stmt->close();
        }


        // if (!empty($_FILES[$videoKey]["tmp_name"]) && !empty($_POST[$nameKey])) {
        //     $videoFile = $_FILES[$videoKey];

        //     if ($videoFile['error'] !== UPLOAD_ERR_OK) {
        //         echo "Error uploading file: " . htmlspecialchars($videoFile['name']);
        //         continue;
        //     }

        //     $videoContent = file_get_contents($videoFile['tmp_name']);
        //     $documentName = htmlspecialchars($_POST[$nameKey]);

        //     // Check if row exists
        //     $stmt = $connect->prepare("SELECT id FROM uploaded_video WHERE card_id = ?");
        //     $stmt->bind_param("s", $card_id);
        //     $stmt->execute();
        //     $stmt->store_result();
        //     $rowExists = $stmt->num_rows > 0;
        //     $stmt->close();

        //     if ($rowExists) {
        //         $query = "UPDATE uploaded_video SET `d_service_name$m` = ?, `d_service_video$m` = ? WHERE card_id = ?";
        //         $stmt = $connect->prepare($query);
        //         $stmt->bind_param("sss", $documentName, $videoContent, $card_id);
        //     } else {
        //         $query = "INSERT INTO uploaded_video (id, user_email, card_id, `d_service_name$m`, `d_service_video$m`) VALUES (UUID(), ?, ?, ?, ?)";
        //         $stmt = $connect->prepare($query);
        //         $stmt->bind_param("ssss", $user_email, $card_id, $documentName, $videoContent);
        //     }

        //     if ($stmt->execute()) {
        //         $hasChanges = true;
        //     }

        //     $stmt->close();
        // }
    }

    header("Location: preview_page.php");
    exit();
}

// **⬇️ Video Retrieval Before Form Display**
$stmt = $connect->prepare("SELECT * FROM uploaded_video WHERE card_id = ?");
$stmt->bind_param("s", $card_id);
$stmt->execute();
$result = $stmt->get_result();
$row2 = $result->fetch_assoc();
$stmt->close();

ob_end_flush(); // Ensure output is properly handled
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service - Video Upload</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        /* Video Card */
        .divider-video {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px auto;
            width: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .divider-video legend {
            text-align: left;
            color: rgb(61, 98, 172) !important;
            font-size: small;
        }

        #video_inputname {
            width: 100%;
        }

        .video_previewDel {
            text-align: left;
        }

        .video_center {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .video_preview_class {
            background: #001F5B;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .delvideo {
            background: #d9534f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .title_legend {
            color: rgb(61, 98, 172) !important;
        }

        /* Video Preview Modal - Initially Hidden */
        #videoPreviewModal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 60%;
            border-radius: 10px;
            position: relative;
        }

        .close {
            position: absolute;
            /* top: 10px; */
            right: 20px;
            font-size: 24px;
            cursor: pointer;
        }

        .radio_box {
            display: flex;
            align-items: center;

        }

        .radio_box input[type="radio"] {

            margin-right: 5px;
            transform: scale(1.3);
            /* Make the radio button slightly larger */
        }

        .radio_box label {
            background: #00581f;
            color: #ffffff;
            font-size: 14px;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            /* color: #333; */
            font-weight: 500;
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
                <div class="nav_cont"><i class="fa fa-bank"></i> Personal Details</div>
            </a>
            <a href="create_card3.php">
                <div class="nav_cont"><i class="fa fa-facebook"></i> Social Links</div>
            </a>
            <a href="create_card4.php">
                <div class="nav_cont"><i class="fa fa-ticket"></i> Product & Service</div>
            </a>
            <a href="create_card5.php">
                <div class="nav_cont "><i class="fa fa-image"></i> Image Gallery</div>
            </a>
            <a href="create_card6.php">
                <div class="nav_cont "><i class="fa fa-file-pdf"></i> PDF Upload</div>
            </a>
             <a href="
             .php">
                <div class="nav_cont active"><i class="fa fa-video"></i> Video Upload</div>
            </a>
            <a href="preview_page.php">
                <div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div>
            </a>
        </div>

        <div class="btn_holder">
            <a href="create_card6.php">
                <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
            </a>
            <a href="preview_page.php">
                <div class="skip_btn" onclick="return validateAndSkip(event,'preview_page.php')">Skip <i class="fa fa-chevron-circle-right"></i></div>
            </a>
        </div>

        <h1>VIDEO UPLOAD</h1>
        <p class="sug_alert">Video can be uploaded up to 15MB in size.</p>
        <form id="card_form" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(false);">
            <?php for ($m = 1; $m <= 5; $m++): ?>
                <?php
                $video_name = htmlspecialchars($row2["d_service_name$m"] ?? "");
                $video_exists = !empty($row2["d_service_video$m"]);
                $video_url = "";
                if ($video_exists) {
                    $video_url = "data:video/mp4;base64," . base64_encode($row2["d_service_video$m"]);
                }
                ?>
                 <fieldset class="divider-video">
                    <legend>User Video <?php echo $m; ?></legend>
                    <div class="input_box" id="video_inputname">
                        <p class="title_legend"> Title <?php //echo $m; ?></p>
                        <input type="text" name="d_service_name<?php echo $m; ?>" maxlength="200"
                            placeholder="Enter video title" value="<?php echo $video_name; ?>">
                    </div>                    
                    <div class="video_previewDel">
                        <input type="file" name="d_service_video<?php echo $m; ?>" accept="video/*"
                            data-video-exists="<?php echo $video_exists ? 'true' : 'false'; ?>"
                            onchange="previewSelectedVideo(this, <?php echo $m; ?>)" <?= $video_exists ? 'hidden' : '' ?>>

                        <div class="video_center" id="videoPreview<?php echo $m; ?>"> 
                            <!-- Add Radio Button for selection -->
                             <?php if ($video_exists) { ?>
                                <div class="radio_box">
                                    <label>
                                        <input type="radio" name="selected_video" value="<?php echo $m; ?>"
                                            <?php echo ($row2['selected_video'] == $m) ? 'checked' : ''; ?>> Select as Active
                                    </label>
                                </div>
                            <?php } ?> 
                             <?php if ($video_exists): ?>
                                <p>
                                    <button class="video_preview_class" type="button" onclick="openVideoPreview('<?php echo $video_url; ?>')">
                                        View Video
                                    </button>
                                </p>
                            <?php else: ?>
                                <p>🎥 No video uploaded</p>
                            <?php endif; ?>
                            <?php if ($video_exists) { ?>
                                <input type="hidden" name="deletedVideos" id="deletedVideos" value="[]">
                                <div id="deleteIcon_<?php echo $m; ?>" class="delvideo" onclick="deleteVideo(<?php echo $m; ?>)">
                                    Delete Video <?php //echo $m; ?>
                                </div>
                            <?php } ?> 
                         </div>
                    </div>
                </fieldset>
            <?php endfor; ?>
             <input type="submit" class="submit-btn" name="process7" value="Next"> 
        </form>

        <div id="videoPreviewModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeVideoPreview()">&times;</span>
                <video id="videoFrame" width="100%" height="500px" controls></video>
            </div>
        </div>

    </div>

    <footer>
        <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
    </footer>

    <script>
        let deletedVideos = []; // Stores deleted video indexes

        function openVideoPreview(videoSrc) {
            document.getElementById("videoFrame").src = videoSrc;
            document.getElementById("videoPreviewModal").style.display = "block";
        }

        function closeVideoPreview() {
            document.getElementById("videoPreviewModal").style.display = "none";
            document.getElementById("videoFrame").src = "";
        }
        // Close modal when clicking outside the content
        window.onclick = function(event) {
            let modal = document.getElementById("videoPreviewModal");
            if (event.target === modal) {
                closeVideoPreview();
            }
        };


        function deleteVideo(index) {
            document.getElementById("deleteIcon_" + index).style.display = "none";
            let deletedVideos = JSON.parse(document.getElementById("deletedVideos").value);
            deletedVideos.push(index);
            document.getElementById("deletedVideos").value = JSON.stringify(deletedVideos);
        }




        document.addEventListener("DOMContentLoaded", function() {
            let formFields = document.querySelectorAll("input, select, textarea");

            formFields.forEach(field => {
                field.addEventListener("input", function() {
                    if (!validateForm(true)) {
                        let skipButtons = document.getElementsByClassName('skip_btn');
                        for (let i = 0; i < skipButtons.length; i++) {
                            skipButtons[i].disabled = true;
                            skipButtons[i].style.opacity = "0.5";
                            skipButtons[i].style.cursor = "not-allowed";
                        }
                    } else {
                        let skipButtons = document.getElementsByClassName('skip_btn');
                        for (let i = 0; i < skipButtons.length; i++) {
                            skipButtons[i].disabled = false;
                            skipButtons[i].style.opacity = "1";
                            skipButtons[i].style.cursor = "pointer";
                        }
                    }
                });
            });
        });

        // PHP to check if any videos exist for the current card_id
        var at_least_one_video = <?php
                                    $stmt_check = $connect->prepare("SELECT d_service_video1, d_service_video2, d_service_video3, d_service_video4, d_service_video5 FROM uploaded_video WHERE card_id=?");
                                    $stmt_check->bind_param("s", $_SESSION['card_id_inprocess']);
                                    $stmt_check->execute();
                                    $existing_videos = $stmt_check->get_result()->fetch_assoc();

                                    // Check if any video exists
                                    $at_least_one_video = false;
                                    foreach ($existing_videos as $video) {
                                        if (!empty($video)) {
                                            $at_least_one_video = true;
                                            break;
                                        }
                                    }
                                    echo $at_least_one_video ? 'true' : 'false';
                                    ?>;

        function deleteVideo(videoNum) {
            let deleteButton = document.querySelector(`.delVideo[onclick="deleteVideo(${videoNum})"]`);
            if (deleteButton) {
                deleteButton.style.display = "none"; // Hide delete button
            }

            if (!deletedVideos.includes(videoNum)) {
                deletedVideos.push(videoNum);
            }

            console.log(`Deleting Video ${videoNum}`);

            // Check if preview element exists
            let videoPreview = document.getElementById(`videoPreview${videoNum}`);
            if (videoPreview) {
                videoPreview.innerHTML = "<p>🎥 No video uploaded</p>";
            }

            // Clear input values
            let videoInput = document.querySelector(`input[name='d_service_video${videoNum}']`);
            let nameInput = document.querySelector(`input[name='d_service_name${videoNum}']`);

            if (videoInput) {
                videoInput.value = "";
            }

            if (nameInput) {
                nameInput.value = "";
            }

            // Update hidden input field
            let deletedVideosInput = document.getElementById("deletedVideos");
            if (deletedVideosInput) {
                deletedVideosInput.value = JSON.stringify(deletedVideos);
            }
        }



        function previewSelectedVideo(input, index) {
            const file = input.files[0];
            if (file && file.type.startsWith("video/")) {
                const fileURL = URL.createObjectURL(file);
                document.getElementById('videoPreview' + index).innerHTML = `
                <p>
                    <button type="button" class="video_preview_class" onclick="openVideoPreview('${fileURL}')">Selected Video: Play</button>
                </p>
            `;
            } else {
                Toastify({
                    text: "Only video files are allowed!",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "red",
                }).showToast();

                // Clear the file input
                input.value = "";
                document.getElementById('videoPreview' + index).innerHTML = `<p>🎥 No valid video selected</p>`;
            }
        }

        function validateForm(isSkip = false) {
            let isValid = false;
            let errorMessage = "Please upload at least one video.";
            let maxFileSize = 15 * 1024 * 1024; // 15MB limit

           let hasOneImage = false;
            for (let i = 1; i <= 5; i++) {
                let serviceName = document.querySelector(`input[name='d_service_name${i}']`).value.trim();
                let videoInput = document.querySelector(`input[name='d_service_video${i}']`);
                let videoExists = videoInput.getAttribute("data-video-exists") === "true";
                let isDeleted = deletedVideos.includes(i);

                if (videoInput.files.length > 0) {
                    let uploadedFile = videoInput.files[0];

                    if (uploadedFile.size > maxFileSize && !isSkip) {
                        showToastOnce(`"${uploadedFile.name}" exceeds the 15MB limit. Please upload a smaller file.`, "red");
                        return false;
                    }
                }

                if (serviceName && (videoInput.files.length > 0 || videoExists) && !isDeleted) {
                    hasOneImage = true;
                    isValid = true;
                } else if (serviceName && videoInput.files.length === 0 && !videoExists) {
                    if (!isSkip) showToastOnce(`Please upload a video for "${serviceName}".`, "red");
                    return false;
                } else if (!serviceName && videoInput.files.length > 0) {
                    if (!isSkip) showToastOnce("Please enter a service name for the selected video.", "red");
                    return false;
                }
            }

            if (!hasOneImage && !isSkip) {
                    alert(`Please upload at least one image.`);
                    return false;
                }


            if (deletedVideos.length > 0) {
                deletedVideos.forEach(videoNum => {
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete_video.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    xhr.onload = function() {
                        console.log("Server Response:", xhr.responseText);
                        if (xhr.status === 200) {
                            try {
                                let response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    console.log(`Video ${videoNum} deleted successfully.`);
                                } else {
                                    alert(`Error: ${response.message}`);
                                }
                            } catch (e) {
                                console.error("Invalid JSON response:", xhr.responseText);
                            }
                        } else {
                            alert("Error: Could not connect to the server.");
                        }
                    };

                    xhr.onerror = function() {
                        alert("AJAX request failed.");
                    };

                    xhr.send("videoNum=" + videoNum + "&card_id=" + "<?php echo $card_id; ?>");
                });
            }

    
        }

        let lastErrorMessage = "";

        function showToastOnce(message, color) {
            if (message !== lastErrorMessage) {
                lastErrorMessage = message;
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: color,
                }).showToast();

                setTimeout(() => {
                    lastErrorMessage = "";
                }, 3500);
            }
        }
    </script>


</body>

</html>