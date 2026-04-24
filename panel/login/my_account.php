<?php

ob_start(); // Start output buffering to prevent header issues

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require('connect.php');
require('header.php');

?>


<div class="container">

    
<div class="main3">

    <a href="index.php">
        <h3 class="back_btn"><i class="fa fa-arrow-circle-left"></i> back </h3>
    </a>
    <?php
	
	
	$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE  user_email="'.$_SESSION['user_email'].'"');
	
	if(mysqli_num_rows($query) >> 0){}else {
		echo '<a href="logout.php"><div class="alert danger">Your Session is Expired! Click here to re-login .</div></a>';
		
	}
	
	$row=mysqli_fetch_array($query);
		
			// die('sdfs');


		
?>
<h1>Change Password</h1>
    <form method="POST" class="my_account_form">
            <div class="input_box">
            <p>Set A New Password</p>
            <input type="password" name="user_password" id="myPassword" value="<?php echo $row['user_password'];  ?>"
                placeholder="Enter new password..." required>
                

</div>
<span class="show_pass_alert"><input type="checkbox"
                    onclick="showPass()"> Show Password</span>
            <script>
            // show password 
            function showPass() {
                var x = document.getElementById("myPassword");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
            </script>



        <div class="btn_payment changePasswordPagebtn"><input type="submit" name="update_pass" value="Update Password"></div>
    </form>
    <div id="goButton" style="display: none; text-align:center;margin-top: 15px;">
        <a href="index.php">
            <button type="button">Go to Overview</button>
        </a>
    </div>

<?php
if (isset($_POST['update_pass'])) {
    $update = mysqli_query($connect, 'UPDATE digi_card SET  
        user_password="' . $_POST['user_password'] . '" 
        WHERE user_email="' . $_SESSION['user_email'] . '"');

    if ($update) {
        echo '
        <div id="successMessage" class="alert info">
            Password has been updated successfully.
        </div>

        <script>
            // Wait for 3 seconds then show the "Go to Overview" button
            setTimeout(function() {
                document.getElementById("successMessage").style.display = "none";
                document.getElementById("goButton").style.display = "block";
            }, 3000); // 3000 ms = 3 seconds
        </script>
        ';
    } else {
        echo '<div class="alert danger">Error! Try Again.</div>';
    }
}
?>

</div>
</div>

<script>
// function for submitting this data
$(document).ready(function() {
    $('#update_details').on('click', function(e) {
        $('.my_account_form').submit();
    });
})
</script>

<footer class="">

    <p>Copyright 2025 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>

<style>
    .alert.info {
        background-color: #d9edf7;
        color: #31708f;
        padding: 10px;
        border-radius: 5px;
    }

    .alert.danger {
        background-color: #f2dede;
        color: #a94442;
        padding: 10px;
        border-radius: 5px;
    }

    button {
        padding: 10px 20px;
        background-color: #28a745;
        border: none;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

</style>