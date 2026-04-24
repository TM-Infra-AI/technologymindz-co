<?php
session_start();
ob_start(); // Start output buffering to prevent header issues
require('connect.php');
require('header.php');

// Ensure user session is set
if (!isset($_SESSION['user_email'])) {
    die('Session not set. Please log in.');
}

// Process card selection
if (isset($_GET['card_number'])) {
    $_SESSION['card_id_inprocess'] = $_GET['card_number'];
}

// Validate the card id belongs to the logged-in user
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
    echo '<meta http-equiv="refresh" content="2;URL=index.php">';
    echo '<div class="alert danger">Card ID does not match with your email account</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Card Theme</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="main3">
    <div class="btn_holder">
        <a href="create_card.php?card_number=<?php echo $_SESSION['card_id_inprocess']; ?>">
            <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
        </a>
        <a href="create_card2.php"><div class="skip_btn">Skip <i class="fa fa-chevron-circle-right"></i></div></a>
    </div>

    <h1>Select Theme for your card</h1>
    
    <center>
        <?php
        // Available themes
        $themes = [
            "card_css30.css" => "template29.png",
            "card_css29.css" => "template30.png",
            "card_css28.css" => "template1.png",
            "card_css27.css" => "template2.png",
            "card_css26.css" => "template3.png",
            "card_css25.css" => "template4.png",
            "card_css24.css" => "template5.png",
            "card_css23.css" => "template6.png",
            "card_css22.css" => "template7.png",
            "card_css21.css" => "template8.png",
            "card_css20.css" => "template9.png",
            "card_css19.css" => "template10.png",
            "card_css18.css" => "template11.png",
            "card_css17.css" => "template12.png",
            "card_css16.css" => "template13.png",
            "card_css8.css"  => "template14.png",
            "card_css9.css"  => "template15.png",
            "card_css10.css" => "template16.png",
            "card_css11.css" => "template17.png",
            "card_css12.css" => "template18.png",
            "card_css13.css" => "template19.png",
            "card_css14.css" => "template20.png",
            "card_css15.css" => "template21.png",
            "card_css1.css"  => "template22.png",
            "card_css2.css"  => "template23.png",
            "card_css3.css"  => "template24.png",
            "card_css4.css"  => "template25.png",
            "card_css5.css"  => "template26.png",
            "card_css6.css"  => "template27.png",
            "card_css7.css"  => "template28.png",
            "card_css31.css" => "template31.png",
            "card_css32.css" => "template32.png",
            "card_css33.css" => "template33.png",
            "card_css34.css" => "template34.png",
            "card_css35.css" => "template35.png"
        ];

        // Generate theme selection UI
        foreach ($themes as $css => $image) {
            $selected = ($row['d_css'] == $css) ? '<div class="selected">Selected</div>' : '';
            echo '<div class="theme">' . $selected . '<a href="select_theme.php?d_css=' . $css . '"><img src="../login/images/' . $image . '"></a></div>';
        }
        ?>
    </center>

    <?php
    // Handle theme selection update
    if (isset($_GET['d_css']) && array_key_exists($_GET['d_css'], $themes)) {
        $selected_css = $_GET['d_css'];

        $stmt = $connect->prepare("UPDATE digi_card SET d_css = ? WHERE id = ?");
        $stmt->bind_param("si", $selected_css, $_SESSION['card_id_inprocess']);
        $update = $stmt->execute();

        if ($update) {
            ob_end_clean(); // Clear any previous output before redirection
            header("Location: create_card2.php"); // Redirect immediately
            exit;
        } else {
            echo '<div class="alert danger">Error! Try Again.</div>';
        }
    }
    ?>
</div>

<footer>
    <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2024</p>
</footer>

</body>
</html>
