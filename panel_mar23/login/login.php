<?php
require('connect.php');

if(isset($_SESSION['sender_token'])){
	$sender_token=$_SESSION['sender_token'];
}

?>
<div class="clip_path1"></div>
<div class="login">
	
	<form action="" method="post" autocomplete="off" id="login">
	<h1>Customer Login</h1>
	<img src="images/user2.png">
	<p>Please login with your email id and password to create/View your digital visiting card</p>
		<input type="text" name="user_id" placeholder="Enter Email id or Mobile Number" autocomplete="off" required>
		<input type="password" name="user_password" placeholder="Password" autocomplete="off" required>
		<input type="submit" name="login_user" value="Login">
		<br>
		<br>
		<!-- 16 March 2025 - Remove create user option from customer as discussed with Ritvik by Muskan gwala-->
		<!--<a id="register_en">New User? Create An Account</a>-->
		<a id="forgot_p">Forgot Password?</a>
		<br>
		<br>
	</form>

	<!--<form action="" method="post" autocomplete="off" id="register">-->
	<!--<h1>Create An Account</h1>-->
	<!--<p>Create an account with your email id and password to create your digital visiting card</p>-->
	<!--	<input type="text" name="user_name" placeholder="Enter Name" autocomplete="off" required>-->
	<!--	<input type="email" name="user_email" placeholder="Enter Email" autocomplete="off" required>-->
	<!--	<input type="text" name="user_contact" maxlength="10" min="5555555555" placeholder="Enter Mobile Number" autocomplete="off" required>-->
	<!--	<input type="password" name="user_password" placeholder="New Password" autocomplete="off" required>-->
	<!--	<input type="submit" name="register" value="Create Account">-->
	<!--	<br>-->
	<!--	<br>-->
	<!--	<a id="login_en">Existing User? Click to Login</a><a id="forgot_p">Forgot Password?</a>-->
	<!--	<br>-->
	<!--	<br>-->
	<!--</form>-->


	<form action="" method="post" autocomplete="off" id="forgot_pass">
	<h1>Forgot Password?</h1>
	<p>Mention Email id, you will receive an email with password.</p>
		
		<input type="email" name="user_email" placeholder="Enter Email" autocomplete="off" required>
		
		
		<input type="submit" name="forgot_password" value="Send Password">
		<br>
		<br>
		<a id="login_en" href="">Go Back to Login</a><a id="forgot_p">Forgot Password?</a>
		<br>
		<br>
	</form>

	
<script>

// 	$('#register_en').on('click',function(){
// 		$('#login').hide();
// 		$('#register').show();
// 		$('#forgot_pass').hide();
// 	})
	$('#login_en').on('click',function(){
		$('#register').hide();
		$('#forgot_pass').hide();
		$('#login').show();
	})
	$('#forgot_p').on('click',function(){
		$('#register').hide();
		$('#login').hide();
		$('#forgot_pass').show();
	})
	

</script>

<?php

	if(isset($_POST['login_user'])){
		$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE user_email="'.$_POST['user_id'].'" OR user_contact="'.$_POST['user_id'].'" AND user_password="'.$_POST['user_password'].'" ORDER BY id DESC');
		if(mysqli_num_rows($query)>0){
			//login function 
			$row=mysqli_fetch_array($query);
			
			if($row['user_password']==$_POST['user_password'] ){
				// logged in and form display none
				echo '<style> form {display:none;} </style>';
					$_SESSION['user_email']=$row['user_email'];
					$_SESSION['user_name']=$row['user_name'];
					$_SESSION['user_contact']=$row['user_contact'];
					$_SESSION['user_id']=$row['user_id'];
					echo '<div class="alert Success">Login Success, Redirecting...</div>';
					echo '<meta http-equiv="refresh" content="3;URL=index.php">';
					echo '<div class="alert info">Also Please confirm your Email id, please click on the link to verify it. Check your SPAM folder also if email is not available in inbox.</div>';
					
				
			}else {
				echo '<div class="alert info">Password Wrong, Try Again.</div>';
			}
			
		}else {
			echo '<div class="alert info" id="register_en">Account does not exist, please check your email and try again. </div>';
		}
	}
	
// register -----------------------------------------------------------------------------

if(isset($_POST['register'])){
		$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE user_email="'.$_POST['user_email'].'" ');
		if(mysqli_num_rows($query)==0){
			

					 $token=rand(100000000,99999999999);
				$insert=mysqli_query($connect,'INSERT INTO digi_card (user_email,user_name,user_password,user_contact,user_token,user_active,sender_token) VALUES ("'.$_POST['user_email'].'","'.$_POST['user_name'].'","'.$_POST['user_password'].'","'.$_POST['user_contact'].'","'.$token.'","NO","'.$sender_token.'")');
				
				
				if($insert){
					
					$_SESSION['user_email']=$_POST['user_email'];
					$_SESSION['user_name']=$_POST['user_name'];
					$_SESSION['user_contact']=$_POST['user_contact'];
					// form display none
					echo '<style> form {display:none;} </style>';
					echo '<div class="alert Success">Redirecting...</div>';
					echo '<meta http-equiv="refresh" content="1;URL=index.php">';
					
					// email script				
// email script				
// email script				
// email script				
// email script				
// email script				

				$to = $_POST['user_email'];
$subject = $_SERVER['HTTP_HOST']." Email Varification Link";

 $message = '
Hi Dear,

Please click on this link to verify your email on '.$_SERVER['HTTP_HOST'].' (Digital Visiting Card).<br><br><br>
<a href="https://'.$_SERVER['HTTP_HOST'].'/panel/login/verify.php?email='.$_POST['user_email'].'&token='.$token.'" style="background: #00a1ff;   color: white;   padding: 10px;">Click here to verify</a><br><br><br>
Or click on this link to verify https://'.$_SERVER['HTTP_HOST'].'/panel/login/verify.php?email='.$_POST['user_email'].'&token='.$token.'


Thanks<br>
'.$_SERVER['HTTP_HOST'].' Team

';

						// Always set content-type when sending HTML email
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

						// More headers
						$headers .= 'From: <mail@apnacards.com>' . "\r\n";
						
						if(mail($to,$subject,$message,$headers)){
							echo '<div class="alert success" id="login_en">Verification Link sent to your email '.$_POST['user_email'].'. Click on that link to verify your account.</div>';
											
											
						}else {
							echo '<div class="alert danger">Error Email! try again</div>';
						}
// email script end 	
// email script end 	
// email script end 	
// email script end 	
// email script end 	
// email script end 	
// email script end 
				}
			
		}else {
			echo '<div class="alert info" id="login_en">Account Already Created! Check your email if not verified or Login.</div>';
			
		}
	}
// register end -------------------------------------------------------------

?>




<?php

if(isset($_POST['forgot_password'])){
	$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE user_email="'.$_POST['user_email'].'" ');
	$row=mysqli_fetch_array($query);
		if(mysqli_num_rows($query)>>0){
			
			// email script				

				$to = $_POST['user_email'];
$subject = $_SERVER['HTTP_HOST']." Password ";

 $message = '
Hi Dear,

Your Password is: '.$row['user_password'].'
to login on https://'.$_SERVER['HTTP_HOST'].'

Thanks
'.$_SERVER['HTTP_HOST'].'

';

						$headers= 'From: <mail@apnacards.com>';
						if(mail($to,$subject,$message,$headers)){
							echo '<div class="alert success" id="login_en">Password is sent to your email '.$_POST['user_email'].'. Check Junk or Spam folder also if not available in Inbox.</div>';
											
											
						}else {
							echo '<div class="alert danger">Error Email! try again</div>';
						}
			
		}else {echo '<div class="alert info" id="login_en">Account Does Not Exists! Please check email or Create new account.</div>';}
}

?>

</div>