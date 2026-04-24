<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ob_start(); // Prevent output issues before redirection

require('connect.php');
require('header.php');
$user_email = $_SESSION['user_email'];
$card_id = $_SESSION['card_id_inprocess'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hasChanges = false; // Track if any updates were made

    //radio button value  save

    $selected_pdf = isset($_POST['selected_pdf']) ? intval($_POST['selected_pdf']) : NULL;

    $stmt = $connect->prepare("UPDATE digi_card4 SET selected_pdf = ? WHERE card_id = ?");
    $stmt->bind_param("is", $selected_pdf, $card_id);

    if ($stmt->execute()) {
        echo "Selected PDF updated successfully!";
    } else {
        echo "Error updating selected PDF.";
    }
    $stmt->close();
    //radio button value  save
    // Handle file uploads

    for ($m = 1; $m <= 5; $m++) {
        $pdfKey = "d_service_pdf$m";
        $nameKey = "d_service_name$m";
    
        $documentName = isset($_POST[$nameKey]) ? htmlspecialchars($_POST[$nameKey]) : '';
        $isFileUploaded = !empty($_FILES[$pdfKey]["tmp_name"]);
    
        if ($isFileUploaded || !empty($documentName)) {
            $pdfContent = $isFileUploaded ? file_get_contents($_FILES[$pdfKey]['tmp_name']) : null;
    
            // Check if row exists
            $stmt = $connect->prepare("SELECT id FROM digi_card4 WHERE card_id = ?");
            $stmt->bind_param("s", $card_id);
            $stmt->execute();
            $stmt->store_result();
            $rowExists = $stmt->num_rows > 0;
            $stmt->close();
    
            if ($rowExists) {
                if ($isFileUploaded) {
                    $query = "UPDATE digi_card4 SET `d_service_name$m` = ?, `d_service_pdf$m` = ? WHERE card_id = ?";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("sss", $documentName, $pdfContent, $card_id);
                } else {
                    $query = "UPDATE digi_card4 SET `d_service_name$m` = ? WHERE card_id = ?";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("ss", $documentName, $card_id);
                }
            } else {
                // Only insert when there is a file uploaded
                if ($isFileUploaded) {
                    $query = "INSERT INTO digi_card4 (id, user_email, card_id, `d_service_name$m`, `d_service_pdf$m`) VALUES (UUID(), ?, ?, ?, ?)";
                    $stmt = $connect->prepare($query);
                    $stmt->bind_param("ssss", $user_email, $card_id, $documentName, $pdfContent);
                } else {
                    // If name exists but no file, skip insert to avoid empty PDF row
                    continue;
                }
            }
    
            if ($stmt->execute()) {
                $hasChanges = true;
            }
    
            $stmt->close();
        }
    }
    
    // for ($m = 1; $m <= 5; $m++) {
    //     $pdfKey = "d_service_pdf$m";
    //     $nameKey = "d_service_name$m";

    //     if (!empty($_FILES[$pdfKey]["tmp_name"]) && !empty($_POST[$nameKey])) {
    //         $pdfFile = $_FILES[$pdfKey];

    //         if ($pdfFile['error'] !== UPLOAD_ERR_OK) {
    //             echo "Error uploading file: " . htmlspecialchars($pdfFile['name']);
    //             continue;
    //         }

    //         $pdfContent = file_get_contents($pdfFile['tmp_name']);
    //         $documentName = htmlspecialchars($_POST[$nameKey]);

    //         // Check if row exists
    //         $stmt = $connect->prepare("SELECT id FROM digi_card4 WHERE card_id = ?");
    //         $stmt->bind_param("s", $card_id);
    //         $stmt->execute();
    //         $stmt->store_result();
    //         $rowExists = $stmt->num_rows > 0;
    //         $stmt->close();

    //         if ($rowExists) {
    //             $query = "UPDATE digi_card4 SET `d_service_name$m` = ?, `d_service_pdf$m` = ? WHERE card_id = ?";
    //             $stmt = $connect->prepare($query);
    //             $stmt->bind_param("sss", $documentName, $pdfContent, $card_id);
    //         } else {
    //             $query = "INSERT INTO digi_card4 (id, user_email, card_id, `d_service_name$m`, `d_service_pdf$m`) VALUES (UUID(), ?, ?, ?, ?)";
    //             $stmt = $connect->prepare($query);
    //             $stmt->bind_param("ssss", $user_email, $card_id, $documentName, $pdfContent);
    //         }

    //         if ($stmt->execute()) {
    //             $hasChanges = true;
    //         }

    //         $stmt->close();
    //     }
    // }


    header("Location: create_card7.php");
    exit();
}

// **⬇️ PDF Retrieval Before Form Display**
$stmt = $connect->prepare("SELECT * FROM digi_card4 WHERE card_id = ?");
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
    <title>service </title>
    <link rel="stylesheet" href="styles.css">
    <!-- Toastify.js CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify.js Script -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        /* PDF Card */
        .divider-pdf {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px auto;
            width: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .divider-pdf legend {
            text-align: left;
            color: rgb(61, 98, 172) !important;
            font-size: small;
        }

        #pdf_inputname {
            width: 100%;
        }

        .pdf_previewDel {
            text-align: left;
        }

        .pdf_center {
            display: flex;
            align-items: center;
            /* Center horizontally */
            justify-content: center;
            /* Center vertically */
            gap: 20px;
            /* Space between elements */
        }

        .pdf_preview_class {
            background: #001F5B;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .delpdf {

            background: #d9534f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            /* background: #f6364a;
            color: white;
            padding: 7px 8px; */
        }

        .title_legend {
            color: rgb(61, 98, 172) !important;
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
                <div class="nav_cont active"><i class="fa fa-file-pdf"></i> PDF Upload</div>
            </a>
             <a href="create_card7.php">
                <div class="nav_cont "><i class="fa fa-file-video"></i> Video Upload</div>
            </a>
            <a href="preview_page.php">
                <div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div>
            </a>
        </div>

        <div class="btn_holder">
            <a href="create_card5.php">
                <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
            </a>
            <a href="create_card7.php">
                <div class="skip_btn" onclick="return validateAndSkip(event,'create_card7.php')">Skip <i class="fa fa-chevron-circle-right"></i></div>
            </a>
        </div>

        <h1>PDF UPLOAD</h1>

        <p class="sug_alert">PDF can be uploaded combined up to 15MB in size.</p>

        <form id="card_form" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(false);">
            <?php for ($m = 1; $m <= 5; $m++): ?>
                <?php
                $document_name = htmlspecialchars($row2["d_service_name$m"] ?? "");
                $pdf_exists = !empty($row2["d_service_pdf$m"]);
                $pdf_url = "";
                if ($pdf_exists) {
                    $pdf_url = "data:application/pdf;base64," . base64_encode($row2["d_service_pdf$m"]);
                }
                ?>
                <fieldset class="divider-pdf">
                    <legend><?php echo ($m === 1 ? '* ' : ''); ?>User PDF <?php echo $m; ?></legend>
                    <div class="input_box" id="pdf_inputname">
                        <p class="title_legend"><?php echo ($m === 1 ? '* ' : ''); ?> Title <?php echo $m; ?></p>
                        <input class="" type="text" name="d_service_name<?php echo $m; ?>" maxlength="200"
                            placeholder="PDF title" value="<?php echo $document_name; ?>">
                    </div>


                    <div class="pdf_previewDel">
                        
                        <input type="file" name="d_service_pdf<?php echo $m; ?>" accept="application/pdf"
                            data-pdf-exists="<?php echo $pdf_exists ? 'true' : 'false'; ?>"
                            onchange="previewSelectedPdf(this, <?php echo $m; ?>)" <?= $pdf_exists ? 'hidden' : '' ?> >
                      

                        <div class="pdf_center" id="pdfPreview<?php echo $m; ?>">
                            <!-- Add Radio Button for selection -->
                            <?php if ($pdf_exists) { ?>
                                <div class="radio_box">
                                    <label>
                                        <input type="radio" name="selected_pdf" value="<?php echo $m; ?>"
                                            <?php echo ($row2['selected_pdf'] == $m) ? 'checked' : ''; ?>> Select as Active
                                    </label>
                                </div>
                            <?php } ?>
                            <?php if ($pdf_exists): ?>
                                <p>
                                    <button class="pdf_preview_class" type="button" onclick="openPdfPreview('<?php echo $pdf_url; ?>')">
                                        View PDF
                                    </button>
                                </p>
                            <?php else: ?>
                                <p>📄 No PDF uploaded</p>
                            <?php endif; ?>
                            <?php if ($pdf_exists) { ?>
                                <input type="hidden" name="deletedPdfs" id="deletedPdfs" value="[]">
                                <div id="deleteIcon_<?php echo $m; ?>" class="delPdf" onclick="deletePdf(<?php echo $m; ?>)">
                                    Delete PDF
                                </div>
                            <?php } ?>
                        </div>

                    </div>

                </fieldset>
            <?php endfor; ?>
            <input type="submit" class="submit-btn" name="process6" value="Next">
        </form>

        <div id="pdfPreviewModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closePdfPreview()">&times;</span>
                <iframe id="pdfFrame" width="100%" height="500px"></iframe>
            </div>
        </div>

        <script>
            let deletedPdfs = []; // Stores deleted PDF indexes

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
                ("Please Upload details");
            });


            // PHP to check if any PDFs exist for the current card_id
            var at_least_one_pdf = <?php
                                    // PHP Code to Check if PDFs exist in the database
                                    $stmt_check = $connect->prepare("SELECT d_service_pdf1, d_service_pdf2, d_service_pdf3, d_service_pdf4, d_service_pdf5, d_service_pdf6, d_service_pdf7, d_service_pdf8, d_service_pdf9, d_service_pdf10 FROM digi_card4 WHERE card_id=?");
                                    $stmt_check->bind_param("s", $_SESSION['card_id_inprocess']);
                                    $stmt_check->execute();
                                    $existing_pdfs = $stmt_check->get_result()->fetch_assoc();

                                    // Check if any PDF exists
                                    $at_least_one_pdf = false;
                                    foreach ($existing_pdfs as $pdf) {
                                        if (!empty($pdf)) {
                                            $at_least_one_pdf = true;
                                            break;
                                        }
                                    }

                                    // Output the result as JavaScript variable (true if any PDF exists, false otherwise)
                                    echo $at_least_one_pdf ? 'true' : 'false';
                                    ?>;




            function deletePdf(pdfNum) {
                let deleteButton = document.querySelector(`.delPdf[onclick="deletePdf(${pdfNum})"]`);
                if (deleteButton) {
                    deleteButton.style.display = "none"; // Hide delete button
                } else {
                    console.warn(`Delete button for PDF ${pdfNum} not found.`);
                }

                if (!deletedPdfs.includes(pdfNum)) {
                    deletedPdfs.push(pdfNum);
                }

                console.log(`Deleting PDF ${pdfNum}`);

                // Check if preview element exists
                let pdfPreview = document.getElementById(`pdfPreview${pdfNum}`);
                if (pdfPreview) {
                    pdfPreview.innerHTML = "<p>📄 No PDF uploaded</p>";
                } else {
                    console.warn(`Preview element #pdfPreview${pdfNum} not found.`);
                }

                // Check if input elements exist before modifying them
                let pdfInput = document.querySelector(`input[name='d_service_pdf${pdfNum}']`);
                let nameInput = document.querySelector(`input[name='d_service_name${pdfNum}']`);

                if (pdfInput) {
                    pdfInput.value = "";
                } else {
                    console.warn(`PDF input d_service_pdf${pdfNum} not found.`);
                }

                if (nameInput) {
                    nameInput.value = "";
                } else {
                    console.warn(`Name input d_service_name${pdfNum} not found.`);
                }

                // Check if hidden input for deleted PDFs exists
                let deletedPdfsInput = document.getElementById("deletedPdfs");
                if (deletedPdfsInput) {
                    deletedPdfsInput.value = JSON.stringify(deletedPdfs);
                } else {
                    console.warn("Hidden input #deletedPdfs not found.");
                }
            }

            function openPdfPreview(pdfUrl) {
                document.getElementById('pdfFrame').src = pdfUrl;
                document.getElementById('pdfPreviewModal').style.display = "block";
            }

            function closePdfPreview() {
                document.getElementById('pdfPreviewModal').style.display = "none";
                document.getElementById('pdfFrame').src = ""; // Clear the iframe src to stop loading
            }
            // Close modal when clicking outside the content
            window.onclick = function(event) {
                let modal = document.getElementById("pdfPreviewModal");
                if (event.target === modal) {
                    closePdfPreview();
                }
            };

            // Ensure preview is NOT visible by default
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('pdfPreviewModal').style.display = "none";
            });

            function previewSelectedPdf(input, index) {
                const file = input.files[0];
                if (file && file.type === "application/pdf") {
                    const fileURL = URL.createObjectURL(file);
                    document.getElementById('pdfPreview' + index).innerHTML = `
                    <p>
                    <button type="button" class="pdf_preview_class" onclick="openPdfPreview('${fileURL}')">Selected PDF : View PDF</button>
                    </p>
                    `;
                } else {
                    Toastify({
                        text: "Only PDF files are allowed!",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "red",
                    }).showToast();

                    // ❌ Clear the file input
                    input.value = "";
                    document.getElementById('pdfPreview' + index).innerHTML = `<p>📄 No valid PDF selected</p>`;
                }
            }

            function validateForm(isSkip = false) {
                let isValid = false; // ✅ Used for validation
                let errorMessage = "Please upload at least one PDF."; // ✅ Ensure errorMessage is defined
                // console.log(deletedPdfs.length)
                let maxFileSize = 15 * 1024 * 1024;

                let hasOneImage = false;
                for (let i = 1; i <= 5; i++) {
                    let serviceName = document.querySelector(`input[name='d_service_name${i}']`).value.trim();
                    let pdfInput = document.querySelector(`input[name='d_service_pdf${i}']`);
                    let pdfExists = pdfInput.getAttribute("data-pdf-exists") === "true"; // ✅ Check if PDF exists in DB
                    let isDeleted = deletedPdfs.includes(i); // ✅ Check if deleted

                    // ✅ Check if a new PDF is uploaded
                    if (pdfInput.files.length > 0) {
                        let uploadedFile = pdfInput.files[0];

                        // ✅ File Size Validation (Max: 2GB)
                        if (uploadedFile.size > maxFileSize && !isSkip) {
                            showToastOnce(`"${uploadedFile.name}" exceeds the 15MB   limit. Please upload a smaller file.`, "red");
                            return false;
                        }

                        // ✅ File Type Validation (Only PDFs Allowed)
                        if (!uploadedFile.type.includes("pdf")) {
                            showToastOnce(`"${uploadedFile.name}" is not a valid PDF file.`, "red");
                            return false;
                        }
                    }



                    // ✅ Check if a valid PDF is present for a service
                    if (serviceName && (pdfInput.files.length > 0 || pdfExists) && !isDeleted) {
                        hasOneImage = true;
                        isValid = true;
                    } else if (serviceName && pdfInput.files.length === 0 && !pdfExists) {
                        if (!isSkip) showToastOnce(`Please upload a PDF for "${serviceName}".`, "red");
                        return false;
                    } else if (!serviceName && pdfInput.files.length > 0) {
                        if (!isSkip) showToastOnce("Please enter title for the uploaded PDF.", "red");
                        return false;
                    }
                }

                if (!hasOneImage && !isSkip) {
                    alert(`Please upload at least one PDF.`);
                    return false;
                }


                if (!isSkip) {
                    if (deletedPdfs.length > 0) {
                        deletedPdfs.forEach(pdfNum => {


                            // Perform AJAX request to delete PDF from the server
                            let xhr = new XMLHttpRequest();
                            xhr.open("POST", "delete_pdf.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                            xhr.onload = function() {
                                console.log("Server Response:", xhr.responseText); // Debug response
                                if (xhr.status === 200) {
                                    try {
                                        let response = JSON.parse(xhr.responseText);
                                        if (response.success) {
                                            console.log(`PDF ${pdfNum} deleted successfully.`);
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

                            xhr.send("pdfNum=" + pdfNum + "&card_id=" + "<?php echo $card_id; ?>");
                        });
                    }
                }

                // if ((!at_least_one_pdf && isSkip) || !isSkip && !isValid) {
                //     alert(errorMessage);
                //     return false;
                // }

                return true;
            }


            // ✅ Store the last shown error message to avoid repeated alerts
            let lastErrorMessage = "";

            function showToastOnce(message, color) {
                if (message !== lastErrorMessage) {
                    lastErrorMessage = message; // ✅ Update last error message
                    Toastify({
                        text: message,
                        duration: 1000,
                        gravity: "top",
                        position: "center",
                        backgroundColor: color,
                    }).showToast();

                    // ✅ Reset the message after 3.5s so new errors can appear if needed
                    setTimeout(() => {
                        lastErrorMessage = "";
                    }, 3500);
                }
            }

            // Function to validate the form and check for PDFs before proceeding
            function validateAndSkip(event, nextPage) {
                event.preventDefault(); // Prevent default navigation

                // Check if at least one PDF exists before proceeding
                if (!at_least_one_pdf) {
                    alert("Error: You must upload at least one PDF before proceeding.");
                    return;
                }

                // Call your form validation logic here, passing 'true' to skip delete logic
                if (validateForm(true)) { // Pass true to skip delete logic
                    window.location.href = nextPage;
                }
            }
        </script>
    </div>

    <footer>
        <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
    </footer>

</body>

</html>