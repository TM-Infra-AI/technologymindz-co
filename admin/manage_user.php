<!DOCTYPE html> 
 <?php

require('connect.php');
require('header.php');

?>



<div class="all_franchisee">
<a href="index.php"><h3 class="back_btn"><i class="fa fa-arrow-circle-left"></i> back </h3></a>
	<div class="card_row">
	
		<p>User ID</p>
		<p>User Email</p>
		<p>User Contact</p>
		<p>User Password</p>
		<p>User Name</p>
		<p>Status</p>
		<p>Total Cards</p>
		
		
		<p>Date</p>
		
		
	</div>
	<?php
	
	

if(isset($_GET['page_no'])){
				
			}else {$_GET['page_no']='1';}

			
			 
			 $limit=30;
			 
			  $start_from=($_GET['page_no']-1)*$limit;
			 
	$query=mysqli_query($connect,'SELECT * FROM customer_login ORDER BY id DESC LIMIT '.$start_from.','.$limit.'');
	

		if(mysqli_num_rows($query)>>0){
			
			while($row=mysqli_fetch_array($query)){
				// check from digi card all card made by franchisee
			$query2=mysqli_query($connect,'SELECT * FROM digi_card WHERE user_email="'.$row['user_email'].'" ORDER BY id DESC ');
			
			echo '<li class="card_row2">';
			echo '<p>'.$row['id'].'</p>';
			echo '<p>'.$row['user_email'].'</p>';
			echo '<p>'.$row['user_contact'].'</p>';
			echo '<p>'.$row['user_password'].'</p>';
			
			echo '<p>'.$row['user_name'].' </p>';
			echo '<p>'.$row['user_active'].'</p>';
				echo '<p>'.mysqli_num_rows($query2).'</p>';
				
				
			echo '<p>'.$row['uploaded_date'].'</p>';
			

			
			echo '</li>';
			}
		}else {
			echo '<div class="alert info">No Data Available...</div>';
		}
	?>
	

</div>

<!-------------------Pagination-------------------->
		<div class="pagination">
			<?php 



				

				$query2=mysqli_query($connect,'SELECT * FROM customer_login ORDER BY id DESC ');
			
			 $pages=ceil(mysqli_num_rows($query2)/30);

			for($i=1;$i<=$pages;$i++){
				if($_GET['page_no']==$i){
					echo '<a href="?page_no='.$i.'"><div class="page_btn active">'. $i.'</div></a>';
				}else {
					echo '<a href="?page_no='.$i.'"><div class="page_btn">'. $i.'</div></a>';
				}
				
			}


			?>
	</div>




<footer class="">

<p>Copyright 2020 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>