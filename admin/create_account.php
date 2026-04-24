<!DOCTYPE html> 
 <?php

require('connect.php');
require('header.php');

?>


<div class="main3">
	<a href="index.php"><h3 class="back_btn"><i class="fa fa-arrow-circle-left"></i> back </h3></a>
	<h1 class="close_form">Login ID & Password for User</h1>
	
	<form action="" method="POST" class="close_form" enctype="multipart/form-data">
		
		<h3></h3>
		<div class="input_box"><p>Login Email (Email ID)</p><input type="email" name="user_email" maxlength="199" placeholder="Enter Login Email for User" ></div>
		<div class="input_box"><p>Login Mobile *(Mobile Number)</p><input type="text" name="user_contact" maxlength="13" placeholder="Enter Mobile Number" required></div>
		<div class="input_box"><p>Login password *</p><input type="text" name="user_password" maxlength="199" placeholder="Enter Password" required></div>
			
		<input type="submit" class="" name="process1" value="Create ID & Password" id="block_loader">
	
	
	</form>
	




<?php
if(isset($_POST['process1'])){
	
				
		$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE user_email="'.$_POST['user_email'].'" AND user_contact="'.$_POST['user_contact'].'" ');
		if(mysqli_num_rows($query)>>0){
			
			
			echo '<a href="create_card.php"><div class="alert info">Account already available.</div><div class="next_btn">Next</div></a>';
			$row=mysqli_fetch_array($query);
			$_SESSION['user_email']=$row['user_email'];
					$_SESSION['user_contact']=$row['user_contact'];
					
					
					echo '<style>  .close_form {display:none;} </style>';
				

		}else{

		
				$insert=mysqli_query($connect,'INSERT INTO digi_card (user_email,user_password,user_contact,user_active) VALUES ("'.$_POST['user_email'].'","'.$_POST['user_password'].'","'.$_POST['user_contact'].'","YES")');
				if($insert){
					echo '<a href="create_card.php"><div class="alert info">Account Created!.</div><div class="next_btn">Next</div></a>';
					echo '<style> form {display:none;} </style>';
					
					$_SESSION['user_email']=$_POST['user_email'];
					$_SESSION['user_contact']=$_POST['user_contact'];
				}
				
}

}
?>

</div>




<footer class="">

<p>Copyright 2020 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>