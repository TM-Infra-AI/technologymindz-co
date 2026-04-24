<!DOCTYPE html> 
 <?php

require('connect.php');
require('header.php');

?>


<div class="main3">
	<a href="index.php"><h3 class="back_btn"><i class="fa fa-arrow-circle-left"></i> back </h3></a>
	<h1 class="close_form">Add Franchisee</h1>
	
	<form action="" method="POST" class="close_form" enctype="multipart/form-data">
		
		<h3></h3>
		<div class="input_box"><p>Login Email (Email ID)</p><input type="email" name="f_user_email" maxlength="199" placeholder="Enter Login Email for franchisee login" required></div>
		<div class="input_box"><p>Login Mobile *(Mobile Number)</p><input type="text" name="f_user_contact" maxlength="13" placeholder="Enter Mobile Number" required></div>
		<div class="input_box"><p>Login password *</p><input type="text" name="f_user_password" maxlength="199" placeholder="Enter Password" required></div>
			
		<input type="submit" class="" name="process1" value="Create ID & Password" id="block_loader">
	
	
	</form>
	




<?php
if(isset($_POST['process1'])){
	
				
		$query=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE f_user_email="'.$_POST['f_user_email'].'" AND f_user_contact="'.$_POST['f_user_contact'].'" ');
		if(mysqli_num_rows($query)>>0){
			
			
			echo '<a href="create_card.php"><div class="alert info">Account already available.</div></a>';
			$row=mysqli_fetch_array($query);
			
					
				
				

		}else{

		
				$insert=mysqli_query($connect,'INSERT INTO franchisee_login (f_user_email,f_user_password,f_user_contact,f_user_active) VALUES ("'.$_POST['f_user_email'].'","'.$_POST['f_user_password'].'","'.$_POST['f_user_contact'].'","YES")');
				if($insert){
					echo '<a href="create_card.php"><div class="alert info">Account Created!.</div></a>';
					
					
				}
				
}

}
?>

</div>
<div class="all_franchisee">
	<div class="card_row">
	
		<p>User ID</p>
		<p>Franchisee Email</p>
		<p>Franchisee Contact</p>
		<p>Franchisee Password</p>
		<p>Franchisee Name</p>
		<p>Status</p>
		<p>Total Cards</p>
		
		<p>Action</p>
		<p>Recharge</p>
		<p>Date</p>
		
		
	</div>
	<?php
	
	

if(isset($_GET['page_no'])){
				
			}else {$_GET['page_no']='1';}

			
			 
			 $limit=30;
			 
			  $start_from=($_GET['page_no']-1)*$limit;
			 
	$query=mysqli_query($connect,'SELECT * FROM franchisee_login ORDER BY id DESC LIMIT '.$start_from.','.$limit.'');
	

		if(mysqli_num_rows($query)>>0){
			
			while($row=mysqli_fetch_array($query)){
				// check from digi card all card made by franchisee
			$query2=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$row['f_user_email'].'" ORDER BY id DESC ');
			
			echo '<li class="card_row2">';
			echo '<p>'.$row['id'].'</p>';
			echo '<p>'.$row['f_user_email'].'</p>';
			echo '<p>'.$row['f_user_contact'].'</p>';
			echo '<p>'.$row['f_user_password'].'</p>';
			
			echo '<p>'.$row['f_user_name'].' </p>';
			echo '<p>'.$row['f_user_active'].'</p>';
				echo '<p>'.mysqli_num_rows($query2).'</p>';
				
				echo '<p id="active_btn" class="idact'.$row['id'].'" onclick="activateUser('.$row['id'].')"><span class=" '.$row['f_user_active'].'">';
			if(!empty($row['f_user_active'])){echo $row['f_user_active'];}else {echo "Click To Activate";}
			echo '</span></p>';
		// check wallet balance of franchisee-----------
			$query_franchisee=mysqli_query($connect,'SELECT * FROM wallet WHERE f_user_email="'.$row['f_user_email'].'" ORDER BY ID DESC');

			$row_franchisee=mysqli_fetch_array($query_franchisee);
			echo '<p class="wallet_re" ><a href="add_money.php?id='.$row['id'].'" target="_blank">'.number_format($row_franchisee['w_balance'],2).'</a></p>';
// check wallet balance of franchisee end -----------	
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



				

				$query2=mysqli_query($connect,'SELECT * FROM franchisee_login ORDER BY id DESC ');
			
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

<!-------------------Pagination-------------------->

<script>
							
							// if approved
								function activateUser(id){
										
										$('.idact'+id).css('color','blue').html('Wait...');
									
										$.ajax({
											url:'js_request.php',
											method:'POST',
											data:{fr_id:id},
											dataType:'text',
											success:function(data){
												$('.idact'+id).html(data);
											}
											
										});
										
									}
									
							</script>





<footer class="">

<p>Copyright 2020 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>