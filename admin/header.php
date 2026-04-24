<?php
// ✅ Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ✅ Check admin login
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit();
}

// ✅ Prevent session fixation attacks
session_regenerate_id(true);
?>

<header id="header">
    <div class="logo" onclick="location.href='index.php'">
        <img src="images/logo.png"><h3>ADMIN</h3>
    </div>
    <div class="mobile_home">&equiv;</div>
    <div class="head_txt">
        <h3><a href="index.php">Home</a></h3>
        <h3><?php echo isset($_SESSION['admin_name']) ? 'Hi! ' . htmlspecialchars($_SESSION['admin_name']) : 'Hi! Guest'; ?></h3>
        <h3>
            <?php if (isset($_SESSION['admin_email'])) { ?>
                <a href="my_account.php"><i class="fa fa-gear"></i> Setting</a>
            <?php } ?>
        </h3>
        <h3><a href="logout.php"><i class="fa fa-sign-out"></i></a></h3>
    </div>
</header>

<script>
$(document).ready(function () {
    // ✅ 4. Combine both jQuery functions into one
    $('.mobile_home').on('click', function () {
        $('#header').toggleClass('add_height');
    });

    $("form").submit(function () {
        $('#alert_display_full').css('display', 'block');
    });
});
</script>

<div id="alert_display_full">
    <div id="loader1"></div>
    <h3>Loading...</h3>
</div>
