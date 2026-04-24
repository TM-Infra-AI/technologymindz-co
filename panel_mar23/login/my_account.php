
 <?php

require('connect.php');
require('header.php');

?>


<div class="container2">

	<h1>Change Password</h1>

	<a href="index.php"><h3 class="back_btn"><i class="fa fa-arrow-circle-left"></i> back </h3></a>	
<?php
	
	
			 
	$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE  user_email="'.$_SESSION['user_email'].'"');
	
	if(mysqli_num_rows($query) >> 0){}else {
		echo '<a href="logout.php"><div class="alert danger">Your Session is Expired! Click here to re-login .</div></a>';
		
	}
	
	$row=mysqli_fetch_array($query);
		
		

		
?>
<form  method="POST"   class="my_account_form">
		 
		<br>
		 
		<div class="input_area"><p>New Password (Set a new password)</p>
		<input type="password"  name="user_password" id="myPassword"  value="<?php echo $row['user_password'];  ?>" placeholder="Enter new password..." required><span class="show_pass_alert"><input type="checkbox" onclick="showPass()"> Show Password</span>
		
		
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

		</div>	
		
		 
		
		<div class="btn_payment"  ><input type="submit" name="update_pass" value="Update Password"></div>
		
		
	</form>





<?php

if(isset($_POST['update_pass']))	{
	 
				// image upload
			$update=mysqli_query($connect,'UPDATE digi_card SET  
			user_password="'.$_POST['user_password'].'" where user_email="'.$_SESSION['user_email'].'"');
			
		// enter details in database ending
		
		if($update){
			echo '<a href="my_account.php"><div class="alert info">Details Updated Wait...</div></a>';
			echo '<meta http-equiv="refresh" content="1;URL=my_account.php">';
			echo '<style>  form {display:none;} </style>';
		}else {
			echo '<a href="my_account.php"><div class="alert danger">Error! Try Again.</div></a>';
		}
		
}else {
	
	
}
		

?>


</div>

<script>
// function for submitting this data
$(document).ready(function(){
	$('#update_details').on('click',function(e){
		$('.my_account_form').submit();
	});
})


	

</script>

<footer class="">

<p>Copyright 2021 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>