<?php
session_start();
require('connect.php');
require('header.php');

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");


// Ensure the user is logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    die('Session not set. Please log in.');
}

// Fetch customer details
$stmt = $connect->prepare("SELECT * FROM digi_card WHERE user_email = ?");
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$query_customer = $stmt->get_result();
$row_customer = $query_customer->fetch_assoc();

$franchisee_email = "";

if (!empty($row_customer['sender_token'])) {
    $stmt = $connect->prepare("SELECT * FROM franchisee_login WHERE id = ?");
    $stmt->bind_param("s", $row_customer['sender_token']);
    $stmt->execute();
    $query_franchisee = $stmt->get_result();
    $row_franchisee = $query_franchisee->fetch_assoc();
    
    $franchisee_email = $row_franchisee['f_user_email'] ?? "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Business Name</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure your CSS file is linked -->
</head>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let form = document.querySelector(".close_form");
        let inputField = document.querySelector("input[name='d_comp_name']");
        let skipButton = document.getElementById("skipBtn"); // Skip button reference

        function validateInput() {
            let companyName = inputField.value.trim();
            // let nameRegex = /^[a-zA-Z\s-]+$/; // Allows only letters, spaces, and hyphens
            
            if (companyName.length >=3 ) {
                skipButton.classList.remove("disabled"); // Enable Skip button
                skipButton.removeAttribute("onclick");
                return true;
            } else {
                skipButton.classList.add("disabled"); // Disable Skip button
                skipButton.setAttribute("onclick", "return false;");
                return false;
            }
        }

        // Prevent form submission if validation fails
        form.addEventListener("submit", function (event) {
            if (!validateInput()) {
                alert("Company Name must contain atleast 3.");
                event.preventDefault(); // Stop form submission
            }
        });

        inputField.addEventListener("input", validateInput);
        validateInput(); // Run validation on page load
    });
</script>

<body>

<div class="main3">
    <div class="btn_holder">
        <a href="index.php"><div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div></a>
     <a href="select_theme.php" id="skipBtn" class="skip_btn disabled">Skip <i class="fa fa-chevron-circle-right"></i></a>

    </div>


    <?php
    // Processing "Update Company Name"
    if (isset($_POST['process2']) && !empty($_POST['d_comp_name'])) {
        $d_comp_name = $_POST['d_comp_name'];

        $stmt = $connect->prepare("SELECT * FROM digi_card WHERE d_comp_name = ?");
        $stmt->bind_param("s", $d_comp_name);
        $stmt->execute();
        $query = $stmt->get_result();
        $count = $query->num_rows;

        // $card_id = preg_replace("/[^a-zA-Z0-9]/", "-", $d_comp_name);
        // if ($count > 0) {
        //     $card_id .= $count + 1;
        // }
      

        $stmt = $connect->prepare("UPDATE digi_card SET d_comp_name = ? WHERE id = ?");
        $stmt->bind_param("si", $d_comp_name, $_SESSION['card_id_inprocess']);
        $stmt->execute();

        echo '<meta http-equiv="refresh" content="1;URL=select_theme.php">';
        echo '<div class="alert success">Company Name Updated</div>';
    }
    
    if (isset($_GET['card_number'])) {
        $_SESSION['card_id_inprocess'] = $_GET['card_number'];

        $stmt = $connect->prepare("SELECT * FROM digi_card WHERE id = ? AND user_email = ?");
        $stmt->bind_param("is", $_SESSION['card_id_inprocess'], $_SESSION['user_email']);
        $stmt->execute();
        $query = $stmt->get_result();
        $row = $query->fetch_assoc();
        // echo "Before Update: " . $row['d_comp_name']; // Debugging
        

        if (!$row) {
            echo '<div class="alert danger">Card ID Removed/Not available.</div>';
        } else {
            ?>
            <h1>Update Business or Company Name</h1>
            <form action="#" method="POST" class="close_form">
                <div class="input_box">
                    <p>Company Name *</p>
                    <input type="text" name="d_comp_name" maxlength="199" value="<?php   echo htmlspecialchars($row['d_comp_name']); ?>" placeholder="Enter Company Name" required>
                </div>
                <input type="submit" name="process2" value="Submit & Next">
            </form>
            <?php
        }
    } else {
        ?>
        <h1>Business or Company Name</h1>
        <form action="#" method="POST" class="close_form">
            <div class="input_box">
                <p>Company Name *</p>
                <input type="text" name="d_comp_name" maxlength="199" value="" placeholder="Enter Company Name" required>
            </div>
            <input type="submit" name="process1" value="Submit & Next">
        </form>
        <?php
    }
    ?>

    <?php
    

    // Processing "Add New Company Name"
    if (isset($_POST['process1']) && !empty($_POST['d_comp_name'])) {
         $d_comp_name = $_POST['d_comp_name'];
        $date = date("Y-m-d H:i:s");

        $stmt = $connect->prepare("SELECT * FROM digi_card WHERE d_comp_name = ?");
        $stmt->bind_param("s", $d_comp_name);
        $stmt->execute();
        $query = $stmt->get_result();
        $count = $query->num_rows;

        // $card_id = preg_replace("/[^a-zA-Z0-9]/", "-", $d_comp_name);
        // if ($count > 0) {
        //     $card_id .= $count + 1;
        // }

        $stmt = $connect->prepare("INSERT INTO digi_card (d_comp_name, uploaded_date, d_payment_status, user_email, d_card_status, f_user_email) VALUES (?, ?, 'Created', ?, 'Active', ?)");
        $stmt->bind_param("ssss", $d_comp_name, $date, $_SESSION['user_email'], $franchisee_email);
        $stmt->execute();

        // Get the newly inserted record
        $stmt = $connect->prepare("SELECT id FROM digi_card WHERE d_comp_name = ? AND user_email = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ss", $d_comp_name, $_SESSION['user_email']);
        $stmt->execute();
        $query = $stmt->get_result();
        $row = $query->fetch_assoc();

        // Insert into additional tables
        $stmt = $connect->prepare("INSERT INTO digi_card2 (id, user_email) VALUES (?, ?)");
        $stmt->bind_param("is", $row['id'], $_SESSION['user_email']);
        $stmt->execute();

        $stmt = $connect->prepare("INSERT INTO digi_card3 (id, user_email) VALUES (?, ?)");
        $stmt->bind_param("is", $row['id'], $_SESSION['user_email']);
        $stmt->execute();

        $_SESSION['card_id_inprocess'] = $row['id'];
        echo '<meta http-equiv="refresh" content="1;URL=select_theme.php">';
        echo '<div class="alert success">Company Name Added. CARD Number is: ' . $row['id'] . '</div>';
    }
    ?>

</div>

<footer>
    <p>© <?php echo $_SERVER['HTTP_HOST']; ?> || 2025</p>
</footer>

</body>
</html>
