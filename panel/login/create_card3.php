<?php
session_start();
ob_start();
require('connect.php');
require('header.php');

if (!isset($_SESSION['user_email']) || !isset($_SESSION['card_id_inprocess'])) {
    header("Location: index.php");
    exit;
}

// Fetch card details
$stmt = $connect->prepare("SELECT * FROM digi_card WHERE id = ? AND user_email = ?");
$stmt->bind_param("is", $_SESSION['card_id_inprocess'], $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

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
      <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Social Links</title>
    <link rel="stylesheet" href="styles.css">
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
                <div class="nav_cont active"><i class="fa fa-globe"></i> Social Links</div>
            </a>
            <a href="create_card4.php">
                <div class="nav_cont"><i class="fa fa-ticket"></i> Product & Service</div>
            </a>
            <a href="create_card5.php">
                <div class="nav_cont"><i class="fa fa-image"></i> Image Gallery</div>
            </a>
            <a href="create_card6.php">
                <div class="nav_cont"><i class="fa fa-file-pdf"></i> PDF Upload</div>
            </a>
             <a href="create_card7.php">
                <div class="nav_cont "><i class="fa fa-file-video"></i> Video Upload</div>
            </a> 
            <a href="preview_page.php">
                <div class="nav_cont"><i class="fa fa-laptop"></i> Preview Card</div>
            </a>
        </div>

        <div class="btn_holder">
            <a href="create_card2.php">
                <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
            </a>
            <a href="create_card4.php">
                <div class="skip_btn">Skip <i class="fa fa-chevron-circle-right"></i></div>
            </a>
        </div>

        <h1>Social Links</h1>

        <form id="card_form" action="" method="POST">

            <?php
            $fields = [
                "d_fb" => "Facebook",
                "d_twitter" => "Twitter",
                "d_instagram" => "Instagram",
                "d_linkedin" => "LinkedIn",
                "d_youtube" => "YouTube",
                "d_pinterest" => "Pinterest"
            ];
            foreach ($fields as $key => $label) {
                echo '<div class="input_box"><p>' . $label . ' Link (Optional)</p>
                  <input type="text" id="' . $key . '" name="' . $key . '" maxlength="200" value="' . htmlspecialchars($row[$key] ?? '') . '"></div>';
            }
            ?>

            <!--<h3>YouTube Video Links</h3>-->
            <?php
            // for ($i = 1; $i <= 5; $i++) {
            //     echo '<div class="input_box"><p>YouTube Video Link '.$i.' (Optional)</p>
            //           <input type="text" name="d_youtube'.$i.'" maxlength="200" value="'.htmlspecialchars($row['d_youtube'.$i] ?? '').'"></div>';
            // }
            ?>

            <input type="submit" name="process3" value="Next">
        </form>

        <?php
        if (isset($_POST['process3'])) {
            ob_end_clean(); // Clears any output before redirection

            // Correct SQL query with exactly 12 placeholders
            $stmt = $connect->prepare("UPDATE digi_card SET d_fb=?, d_twitter=?, d_instagram=?, d_linkedin=?, d_youtube=?, d_pinterest=?, d_youtube1=?, d_youtube2=?, d_youtube3=?, d_youtube4=?, d_youtube5=? WHERE id=?");

            // Correct number of bind parameters (11 strings + 1 integer)
            $stmt->bind_param(
                "sssssssssssi",
                $_POST['d_fb'],
                $_POST['d_twitter'],
                $_POST['d_instagram'],
                $_POST['d_linkedin'],
                $_POST['d_youtube'],
                $_POST['d_pinterest'],
                $_POST['d_youtube1'],
                $_POST['d_youtube2'],
                $_POST['d_youtube3'],
                $_POST['d_youtube4'],
                $_POST['d_youtube5'],
                $_SESSION['card_id_inprocess']
            );

            if ($stmt->execute()) {
                header("Location: create_card4.php");
                exit;
            } else {
                echo '<div class="alert danger">Error! Try Again.</div>';
            }
        }


        ?>
    </div>

    <footer>
        <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
    </footer>

</body>

<script>
document.getElementById("card_form").addEventListener("submit", function (e) {
    const fields = [
        { id: "d_fb", label: "Facebook", domain: "facebook.com" },
        { id: "d_twitter", label: "Twitter", domain: "twitter.com" },
        { id: "d_instagram", label: "Instagram", domain: "instagram.com" },
        { id: "d_linkedin", label: "LinkedIn", domain: "linkedin.com" },
        { id: "d_youtube", label: "YouTube", domain: "youtube.com" },
        { id: "d_pinterest", label: "Pinterest", domain: "pinterest.com" },
        { id: "d_youtube1", label: "YouTube Video 1", domain: "youtube.com" },
        { id: "d_youtube2", label: "YouTube Video 2", domain: "youtube.com" },
        { id: "d_youtube3", label: "YouTube Video 3", domain: "youtube.com" },
        { id: "d_youtube4", label: "YouTube Video 4", domain: "youtube.com" },
        { id: "d_youtube5", label: "YouTube Video 5", domain: "youtube.com" }
    ];

    const urlPattern = /^(https?:\/\/)[^\s/$.?#].[^\s]*$/i;
    let errorMessage = "";

    fields.forEach(field => {
        const input = document.querySelector(`[name="${field.id}"]`);
        if (input) {
            const value = input.value.trim();

            if (value !== "") {
                if (!urlPattern.test(value)) {
                    errorMessage += `• Invalid URL format for ${field.label}\n`;
                } else if (!value.includes(field.domain)) {
                    errorMessage += `• ${field.label} must contain "${field.domain}"\n`;
                }
            }
        }
    });

    if (errorMessage !== "") {
        // alert("Please fix the following errors:\n\n" + errorMessage);
        Toastify({
                    text: errorMessage, // Show the first error only
                    duration: 3000,
                    gravity: "top", // Position: top/bottom
                    position: "center", // Align: left/center/right
                    backgroundColor: "#ff4d4d", // Red background
                    stopOnFocus: true
                }).showToast();
        e.preventDefault();
    }
});
</script>







</html>