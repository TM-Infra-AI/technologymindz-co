<?php
require('connect.php');
error_reporting(E_ALL);
?>
<div class="clip_path1"></div>
<div class="login">
	
	<form action="" method="post" autocomplete="off" id="login">
	<h1>franchisee Login</h1>
	<p>Please login with your email id and password to create/View your digital visiting card</p>
		<input type="text" name="f_user_id" placeholder="Enter Email id or Mobile Number" autocomplete="on" required>
		<input type="password" name="f_user_password" placeholder="Password" autocomplete="off" required>
		<input type="submit" name="login_user" value="Login">
		<br>
		<br>
		<br>
		<a href="https://api.whatsapp.com/send?phone=917573003890&text=I%20am%20Interested%20For%20Digital%20Visiting%20Card%20franchise.%20Please%20Share%20Me%20Demo&source=&data=&app_absent='" target="_blank" >Get Franchise </a><a id="forgot_p">Forgot Password?</a>
		<br>
		<br>
	
	</form>
	
	
	<form action="" method="post" autocomplete="off" id="register">
	<h1>Create An Account</h1>
	<p>Create an account with your email id and password to create your digital visiting card</p>
		<input type="text" name="f_user_name" placeholder="Enter Name" autocomplete="" required>
		<input type="email" name="f_user_email" placeholder="Enter Email" autocomplete="" required>
		<input type="text" name="f_user_contact" maxlength="10" min="4555555555" placeholder="Enter Mobile Number" autocomplete="off" required>
		<input type="password" name="f_user_password" placeholder="New Password" autocomplete="off" required>
		
		<input type="submit" name="register" value="Create Account">
		<br>
		<br>
		<a id="login_en">Existing User? Click to Login</a><a id="forgot_p">Forgot Password?</a>
		<br>
		<br>
	</form>


	<form action="" method="post" autocomplete="off" id="forgot_pass">
	<h1>Forgot Password?</h1>
	<p>Mention Email id, you will receive an email with password.</p>
		
		<input type="email" name="f_user_email" placeholder="Enter Email" autocomplete="on" required>
		
		
		<input type="submit" name="forgot_password" value="Send Password">
		<br>
		<br>
		<a id="login_en" href="">Go Back to Login</a><a id="forgot_p">Forgot Password?</a>
		<br>
		<br>
	</form>

	
<script>


	$('#register_en').on('click',function(){
		$('#login').hide();
		$('#register').show();
		$('#forgot_pass').hide();
		
	})
	$('#login_en').on('click',function(){
		$('#register').hide();
		$('#forgot_pass').hide();
		$('#login').show();
	})
	$('#forgot_p').on('click',function(){
		$('#forgot_pass').show(); 
		$('#register').hide();
		$('#login').hide();
		
	})
	

</script>



<?php

	if(isset($_POST['login_user'])){
		$query=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE f_user_email="'.$_POST['f_user_id'].'" OR f_user_contact="'.$_POST['f_user_id'].'" ');
		if(mysqli_num_rows($query)>>0){
			//login function 
			$row=mysqli_fetch_array($query);
			
			if($row['f_user_password']==$_POST['f_user_password'] ){
				
				// form display none
					echo '<style> form {display:none;} </style>';
				if($row['f_user_active']=="YES"){
					$_SESSION['f_user_email']=$row['f_user_email'];
					$_SESSION['f_user_name']=$row['f_user_name'];
					$_SESSION['f_user_contact']=$row['f_user_contact'];
					echo '<div class="alert Success">Login Success, Redirecting...</div>';
					echo '<meta http-equiv="refresh" content="0;URL=index.php">';
					exit();}
				else {
					echo '<div class="alert info"><strong>Sorry!</strong> Your account is not Active/Verified. Please contact our Support for help.<br><a href="https://'.$_SERVER['HTTP_HOST'].'/index.php#contact"><b>Click here </b></a></div>';
				}
			}else {
				echo '<div class="alert info">Password Wrong, Try Again. If you forgot your password then <br><a href="https://'.$_SERVER['HTTP_HOST'].'/index.php#contact"><b>Click here </b></a></div>';
			}
			
		}else {
			echo '<div class="alert info" id="register_en">User Does Not Exists. Contact us on Info@51Digitalmedia.com for new account request. <br><a href="https://'.$_SERVER['HTTP_HOST'].'/index.php#contact"><b>Click here </b></a></div>';
		}
	}
	
// register -----------------------------------------------------------------------------

if(isset($_POST['register'])){
		$query=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE f_user_email="'.$_POST['f_user_email'].'"  ');
		if(mysqli_num_rows($query)==0){
			

					 $token=rand(100000000,99999999999);
					
					
					 
				$insert=mysqli_query($connect,'INSERT INTO franchisee_login (f_user_name,f_user_email,f_user_password,f_user_contact,f_user_active,f_user_token) VALUES ("'.$_POST['f_user_name'].'","'.$_POST['f_user_email'].'","'.$_POST['f_user_password'].'","'.$_POST['f_user_contact'].'","NO","'.$token.'")');
				
				
				
				if($insert){
					
					// form display none
					echo '<style> form {display:none;} </style>';
					echo '<div class="alert Success">Verification Email has been sent to your email '.$_POST['f_user_email'].'. Please click on that and verify your email address. (Check your Junk/Spam folder if email does not show in Inbox)</div>';
					
					
					// email script				
// email script				
// email script				
// email script				
// email script				
// email script				

				$to = $_POST['f_user_email'];
$subject = "Email Varification Link";

 $message = '
Hi Dear,

Please click on this link to verify your email on '.$_SERVER['HTTP_HOST'].' (Digital Visiting Card).<br><br><br>
<a href="https://'.$_SERVER['HTTP_HOST'].'/panel/franchisee-login/verify.php?email='.$_POST['f_user_email'].'&token='.$token.'" style="background: #00a1ff;   color: white;   padding: 10px;">Click here to verify</a><br><br><br>
Or click on this link to verify https://'.$_SERVER['HTTP_HOST'].'/panel/franchisee-login/verify.php?email='.$_POST['f_user_email'].'&token='.$token.'


Thanks<br>
'.$_SERVER['HTTP_HOST'].'

';

						// Always set content-type when sending HTML email
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

						// More headers
						$headers .= 'From: <Info@51Digitalmedia.com>' . "\r\n";
						//$headers .= 'Cc: <Info@51Digitalmedia.com>' . "\r\n";
						if(mail($to,$subject,$message,$headers)){
							echo '<div class="alert success" id="login_en">Verification Link sent to your email '.$_POST['f_user_email'].'. Click on that link to verify your account.</div>';
											
											
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
				}else {
					echo '<div class="alert danger">Failed! try again</div>';
				}
			
		}else {
			echo '<div class="alert info" id="login_en">Account Already Created! Check your email if not verified or Login.</div>';
			
		}
	}
// register end -------------------------------------------------------------

?>



<?php

if(isset($_POST['forgot_password'])){
	$query=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE f_user_email="'.$_POST['f_user_email'].'" ');
	$row=mysqli_fetch_array($query);
		if(mysqli_num_rows($query)>>0){
			
			// email script				

				$to = $_POST['f_user_email'];
$subject = $_SERVER['HTTP_HOST']." Password ";

 $message = '
Hi Dear,

Your Password is: '.$row['f_user_password'].'
to login on '.$_SERVER['HTTP_HOST'].'/panel/franchisee-login/login.php

Thanks
'.$_SERVER['HTTP_HOST'].'

';

						$headers= 'From: <Info@51Digitalmedia.com>';
						if(mail($to,$subject,$message,$headers)){
							echo '<div class="alert success" id="login_en">Password is sent to your email '.$_POST['f_user_email'].'. Check Junk or Spam folder also if not available in Inbox.</div>';
											
											
						}else {
							echo '<div class="alert danger">Error Email! try again</div>';
						}
			
		}else {echo '<div class="alert info" id="login_en">Account Does Not Exists! Please check email or Create new account.</div>';}
}

?>

</div>

<footer class="">


</div>