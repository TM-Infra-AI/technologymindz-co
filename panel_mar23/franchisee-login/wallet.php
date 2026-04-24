 <?php

require('connect.php');
require('header.php');
$query_franchisee=mysqli_query($connect,'SELECT * FROM wallet WHERE f_user_email="'.$_SESSION['f_user_email'].'" ORDER BY ID DESC');

	$row_franchisee=mysqli_fetch_array($query_franchisee);

?>


<div class="wallet_main">

<!-----------wallet details------------------------->
	<div class="wallet_side1">
		<h2><i class="fas fa-wallet"></i> Current Balance</h2>
		<p><i class="fa fa-rupee"></i><?php  echo number_format($row_franchisee['w_balance'],2); ?></p>
	</div>
	
	
	<div class="wallet_side1">
	<h2> Add Money</h2>
		<form action="payment_page/pay.php" method="POST">
			<input type="number" min="100" max="5000" name="deposit" placeholder="Min 100 Rs Max 5000 Rs" required>
			<input type="submit">
		</form>
	
	</div>
<!-----------Wallet history-------------------------->
	<div class="wallet_history">
	
	<div class="card_row">
	
		<p>Date</p>
		<p>Balance</p>
		
		<p>Deposit</p>
		<p>Withdraw</p>
		<p>Order Id</p>
		<p>Txn Msg</p>
		
		
	</div>
	
	<?php
	$q_wallet=mysqli_query($connect,'SELECT * FROM wallet WHERE f_user_email="'.$row_franchisee['f_user_email'].'" ORDER BY ID DESC LIMIT 10');
	
	if(mysqli_num_rows($q_wallet) >> 0){
		while($q_row=mysqli_fetch_array($q_wallet)){
			
				echo '<div class="card_row2">';
				echo "<p>".date('d M y-h:s A',strtotime($q_row['uploaded_date']))."</p>";
				echo "<p> $q_row[w_balance] </p>";
				echo "<p style='color: #27b927;'> $q_row[w_deposit] </p>";
				echo "<p style='color:red'> $q_row[w_withdraw] </p>";
				echo "<p> $q_row[w_order_id] </p>";
				echo "<p> $q_row[w_txn_msg] </p>";
				
				echo '</div>';
		}
	}else {
		echo '<div class="alert info">No Txn Found!</div>';
	}
	
	
		?>
		
	
	</div>

</div>

