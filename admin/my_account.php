
 <?php

require('connect.php');
require('header.php');

?>


<div class="container2">

	<h1>Account Manager</h1>

	<a href="index.php"><h3 class="back_btn"><i class="fa fa-arrow-circle-left"></i> back </h3></a>	
<?php
	
	
			 
	$query=mysqli_query($connect,'SELECT * FROM admin_login WHERE  admin_email="'.$_SESSION['admin_email'].'"');
	
	if(mysqli_num_rows($query) >> 0){}else {
		echo '<a href="logout.php"><div class="alert danger">Your Session is Expired! Click here to re-login .</div></a>';
		
	}
	
	$row=mysqli_fetch_array($query);
		
		

		
?>
<form  method="POST" enctype="multipart/form-data"  class="my_account_form">
		
		<img class="profile_image" src="<?php if(!empty($row['admin_image'])){echo 'data:image/*;base64,'.base64_encode($row['admin_image']);}else {echo 'images/upload.png';} ?>" alt="Select image" id="showPreviewLogo" onclick="clickFocus()" ><br>
		<div class="input_area"><p>Company Logo (Required)* 100x100 to 400x400 PX</p>
		
		
		
			<script>
				function clickFocus(){
					$('#clickMeImage').click();
				}
				
				function readURL(input){
					if(input.files && input.files[0]){
						var reader = new FileReader();
						reader.onload= function (a){
							$('#showPreviewLogo').attr('src',a.target.result);
						};
						reader.readAsDataURL(input.files[0]);
					}
					
				}
			</script>
			<input type="file" name="admin_image" id="clickMeImage" onchange="readURL(this);" accept="image/*"  >
			
		</div>
		<br>
		
		<div class="input_area"><p>Your Login Email</p>
		<input type="readonly"  value="<?php echo $row['admin_email'];  ?>"  name="update_details" readonly>
		</div>
		<div class="input_area"><p>Your Contact</p>
		<input type="readonly" value="<?php echo $row['admin_contact'];  ?>" readonly>
		</div>
		<div class="input_area"><p>New Password (Set a new password)</p>
		<input type="password"  name="admin_password" id="myPassword"  value="<?php echo $row['admin_password'];  ?>" placeholder="Enter new password..." required><span class="show_pass_alert"><input type="checkbox" onclick="showPass()"> Show Password</span>
		
		
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
		
		<div class="input_area"><p>Google Pay Number</p>
		<input type="" name="admin_google_pay" value="<?php echo $row['admin_google_pay'];  ?>" placeholder="Enter your Google Pay Number" >
		</div>
		<div class="input_area"><p>Paytm Number</p>
		<input type="" name="admin_paytm" value="<?php echo $row['admin_paytm'];  ?>" placeholder="Enter Paytm Number" >
		</div>
		<div class="input_area"><p>Razorpay Secreat</p>
		<input type="" name="admin_rz_pay" value="<?php echo $row['admin_rz_pay'];  ?>" placeholder="Enter Razorpay Secreat id" >
		</div>
		<div class="input_area"><p>Razorpay Key ID</p>
		<input type="" name="admin_rz_pay2" value="<?php echo $row['admin_rz_pay2'];  ?>" placeholder="Enter Razorpay Key id" >
		</div>
		
		<div class="btn_payment" id="update_details">Update Details</div>
		
		
	</form>





<?php

if(isset($_POST['update_details']))	{
	// image upload start
	if(!empty($_FILES['admin_image']['tmp_name'])){
				
				
					
					
					$filename=$_FILES['admin_image']['name'];
							
							 $imageFileType = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
						$file_allow=array('png','jpeg','jpg','gif');
						$filesize=$_FILES['admin_image']['size'];
				// image type check if correct
						if(in_array($imageFileType,$file_allow )){
				// image size check not more then 300KB
						if($filesize < 400000){
							$profile_image=addslashes(file_get_contents($_FILES['admin_image']['tmp_name']));
							$updateLogo=mysqli_query($connect,'UPDATE admin_login SET admin_image="'.$profile_image.'" WHERE admin_email="'.$_SESSION['admin_email'].'"');
					
						}else {
							echo '<div class="alert danger">File Size More then 400KB Please resize it and then upload.</div>';
						}
						}else {
								echo '<div class="alert danger">Only PNG,JPG,JPEG or GIF files allowed</div>';
						
						}
	}
	
	// image upload ended
	
								$admin_rz_pay=str_replace(array("'",'"',';','(',')','“','”'," ","<",">"),array("\'",'\"','\;','\(','\)','\“','\”',"","",""),$_POST['admin_rz_pay']);
								$admin_rz_pay2=str_replace(array("'",'"',';','(',')','“','”'," ","<",">"),array("\'",'\"','\;','\(','\)','\“','\”',"","",""),$_POST['admin_rz_pay2']);
								
				// image upload
			$update=mysqli_query($connect,'UPDATE admin_login SET 
			
			admin_google_pay="'.$_POST['admin_google_pay'].'",
			admin_paytm="'.$_POST['admin_paytm'].'",
			admin_password="'.$_POST['admin_password'].'",
			admin_rz_pay="'.$admin_rz_pay.'",
			admin_rz_pay2="'.$admin_rz_pay2.'" 
			WHERE admin_email="'.$_SESSION['admin_email'].'"');
			
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

<p>Copyright 2020 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>