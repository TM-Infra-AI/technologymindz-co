<?php

require('connect.php');
require('header.php');

// ✅ 1. Secure the Dashboard with Session Check

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

?>

<div class="dashboard">

    <!-----------Dash side 1 (Left Panel) ------------------------->
    <div class="dash_side1">
        <ul class="dash_link">  <!-- ✅ 2. Wrapped <li> inside <ul> -->
            <li><a href="create_account.php" class="active">+ Create New Card</a></li>
            <li><a href="manage_user.php"><i class="fa fa-group"></i> Manage Users</a></li>
            <li><a href="manage_franchisee.php"><i class="fa fa-group"></i> Manage Franchisee</a></li>
            <li><a href="manage_user_card.php"><i class="fa fa-credit-card"></i> Manage User Cards</a></li>
            <li><a href="manage_franchisee_card.php"><i class="fa fa-credit-card"></i> Manage Franchisee Cards</a></li>
            <li><a href="add_money.php"><i class="fa fa-battery-4"></i> Recharge Wallet</a></li>
            <li><a href="my_account.php"><i class="fa fa-gear"></i> My Account</a></li>
            <li><a href="search.php"><i class="fa fa-search"></i> Search</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </div>

    <!-----------Dash side 2 (Main Content) ------------------------->
    <div class="dash_side2">
        <?php
        // ✅ 3. Optimized query: Fetch all required counts in a single query
        $query = mysqli_query($connect, "
            SELECT 
                (SELECT COUNT(*) FROM digi_card) AS total_cards,
                (SELECT COUNT(*) FROM digi_card WHERE f_user_email != '') AS franchisee_cards,
                (SELECT COUNT(*) FROM digi_card WHERE f_user_email = '') AS user_cards,
                (SELECT COUNT(*) FROM franchisee_login) AS total_franchisees,
                (SELECT COUNT(*) FROM customer_login) AS total_users,
                (SELECT COUNT(*) FROM digi_card WHERE d_payment_status='Success') AS active_cards,
                (SELECT COUNT(*) FROM digi_card WHERE d_payment_status='Failed') AS inactive_cards,
                (SELECT COUNT(*) FROM digi_card WHERE d_payment_status='Created') AS trial_cards,
                (SELECT SUM(d_payment_amount) FROM digi_card WHERE d_payment_status='Success') AS total_payment
        ") or die(mysqli_error($connect));

        $row = mysqli_fetch_assoc($query);
        ?>

        <div class="das_box">
            <p>Total Cards</p>
            <p><i class="fa fa-credit-card"></i> <?php echo $row['total_cards']; ?></p>
        </div>

        <div class="das_box" onclick="location.href='manage_franchisee_card.php'">
            <p>Franchisee Cards</p>
            <p><i class="fa fa-credit-card"></i> <?php echo $row['franchisee_cards']; ?></p>
        </div>

        <div class="das_box" onclick="location.href='manage_user_card.php'">
            <p>User Cards</p>
            <p><i class="fa fa-credit-card"></i> <?php echo $row['user_cards']; ?></p>
        </div>

        <div class="das_box" onclick="location.href='manage_franchisee.php'">
            <p>All Franchisees</p>
            <p><i class="fa fa-group"></i> <?php echo $row['total_franchisees']; ?></p>
        </div>

        <div class="das_box" onclick="location.href='manage_user.php'">
            <p>All Users</p>
            <p><i class="fa fa-group"></i> <?php echo $row['total_users']; ?></p>
        </div>

        <!---------------Account Summary------------------------->
        <div class="user_details">
            <h3>Your Account Summary</h3>
            <div class="flex_box"><p>Total Cards</p><p><?php echo $row['total_cards']; ?></p></div>
            <div class="flex_box"><p>Active Cards</p><p><?php echo $row['active_cards']; ?></p></div>
            <div class="flex_box"><p>Inactive Cards</p><p><?php echo $row['inactive_cards']; ?></p></div>
            <div class="flex_box"><p>Trial Cards</p><p><?php echo $row['trial_cards']; ?></p></div>
            <div class="flex_box"><p>Payment Total</p><p><?php echo number_format($row['total_payment'], 2); ?> Rs</p></div>
        </div>
    </div>

</div>

<footer>
    <p>Copyright 2025 || <?php echo $_SERVER['HTTP_HOST']; ?></p>
</footer>
