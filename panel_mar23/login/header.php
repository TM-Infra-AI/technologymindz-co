<title>Customer Login</title>
<head>
    <link rel="fav-icon" href="images/logo.png" type="image/png">
    </head>
<?php
if(!isset($_SESSION['user_email'])){header('Location:login.php');exit;}
?>


<header id="header">
	<div class="logo" onclick="location.href='index.php'">
	<img src="images/logo.png">	<!--<h3>Customer Login</h3>-->
	</div>
	<div class="mobile_home">&equiv;</div>
	<div class="head_txt">
		<h3><?php
		if(isset($_SESSION['user_name'])){
		echo 'Hi '.$_SESSION['user_name'].'!';
		}else {echo 'Hi Guest!';}
		?>
		</h3>
		<h3>
        <a href="my_account.php"><i class="fa fa-lock"></i> Change Password</a>
        &nbsp; 
		<a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
		
		</h3>
	</div>
	

</header>


<script>

$(document).ready(function(){
	$('.mobile_home').on('click',function(){
		$('#header').toggleClass('add_height');
		
	})
})

</script>
<script>
$(document).ready(function(){
  $("form").submit(function(){
    $('#alert_display_full').css('display','block');
  });
});
</script>

<style>

</style>
<div id="alert_display_full">
	<div id="loader1"></div>
	<h3>Loading...</h3>

</div>