<!DOCTYPE html> 
 <?php

require('connect.php');
require('header.php');
$query_franchisee=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE f_user_email="'.$_SESSION['f_user_email'].'"');
	$row_franchisee=mysqli_fetch_array($query_franchisee);

?>

<div class="dashboard">
<!-----------Dash side 1------------------------->

	<div class="dash_side1">
		
		<!----------alll links --------------------------->
		<div class="dash_link">
			<a href="create_account.php"><li class="active">+ Create New Card</li></a>
			
			<a href="user_manager.php"><li><i class="fa fa-group"></i> Manage Users</li></a>
			<a href="card_manager.php"><li><i class="fa fa-credit-card"></i> Manage Cards</li></a>
			<a href="wallet.php"><li><i class='fa fa-money'></i> Wallet</li></a>
			<a href="my_account.php"><li><i class="fa fa-gear"></i> My Account</li></a>
			<a href="logout.php"><li><i class="fa fa-sign-out"></i> Logout</li></a>
	
		</div>
	</div>
	
	
<!-----------Dash side 2------------------------->
	<div class="dash_side2">
		<div class="das_box" onclick="location.href='card_manager.php'">
			<p>Total Cards</p>
			<p><i class="fa fa-credit-card"></i> <?php $query=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'"');
					echo mysqli_num_rows($query); ?>
			</p>
		
		</div>
		<div class="das_box" onclick="location.href='user_manager.php'">
			<p>Users</p>
			<p><i class="fa fa-group"></i> <?php $query=mysqli_query($connect,'SELECT DISTINCT user_email FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'"');
					echo mysqli_num_rows($query); ?>
			</p>
		
		</div>
		<div class="das_box" onclick="location.href='wallet.php'">
			<p>Wallet</p>
			<p><i class="fa fa-rupee"></i> <?php $query_franchisee=mysqli_query($connect,'SELECT * FROM wallet WHERE f_user_email="'.$_SESSION['f_user_email'].'" ORDER BY ID DESC');

	$row_franchisee=mysqli_fetch_array($query_franchisee);
					if($row_franchisee['w_balance'] < 299 ) { echo '<span style="color:Red" title="Please Recharge to create card.">'.number_format($row_franchisee['w_balance'],2).' <i class="fa fa-question-circle" title="Amount is less, reacharge wallet to create card."></i></span>';}
					else {
						echo '<span style="color:green">'.number_format($row_franchisee['w_balance'],2).'</span>';
					}
					
					?>
			</p>
		
		</div>
		
		
		<!---------------chart details------------------------->
			<div class="user_details">

				<?php

							  
							  
				$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'" ');


				?>
					<h3>Your Account Summary</h3>
				<?php

				$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'" ');
				echo '<div class="flex_box "><p>Total Cards</p><p>'.mysqli_num_rows($query).'</p></div>';

				?>
					<?php

				$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'" and d_payment_status="Success"');
				echo '<div class="flex_box"><p>Active Cards</p><p>'.mysqli_num_rows($query).'</p></div>';

				?>
					<?php

				$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'" and d_payment_status="Failed"');
				echo '<div class="flex_box "><p>Inactive Cards</p><p>'.mysqli_num_rows($query).'</p></div>';

				?>
					<?php

				$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'" and d_payment_status="Created"');
				echo '<div class="flex_box"><p>Trial Cards</p><p>'.mysqli_num_rows($query).'</p></div>';

				?>
					<?php

				$query=mysqli_query($connect,'SELECT SUM(d_payment_amount) as payment FROM digi_card WHERE f_user_email="'.$_SESSION['f_user_email'].'" and d_payment_status="Success" ');
				$row=mysqli_fetch_array($query);
				echo '<div class="flex_box "><p>Payment Total </p><p>'.number_format($row['payment'],2).' Rs</p></div>';

				?>
					


				</div>
			
		<!---------------chart details------------------------->
	
	
	</div>



</div>


<footer class="">

<p>Copyright 2021 || Website developed & Maintained by Isavgo Technologies Pvt Ltd</p>

</footer>
