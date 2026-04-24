<?php
if(!isset($_SESSION['f_user_email'])){header('LOCATION:login.php');}
?>


<header id="header">
	<div class="logo" onclick="location.href='index.php'">
		<img src="images/logo.png"><h3>franchisee Admin</h3>
	</div>
	<div class="mobile_home">&equiv;</div>
	<div class="head_txt">
		<h3>
	<a href="index.php">Home</a>
		</h3>
		
		<h3><?php
		if(isset($_SESSION['f_user_email'])){
		echo '<a href="my_account.php"><i class="fa fa-gear"></i> Setting</a>';
		}
		?>
		<h3>
		
	<a href="logout.php"><i class="fa fa-sign-out"></i></a>
		</h3>
		
		</h3>
		<h3><?php
		if(isset($_SESSION['f_user_name'])){
		echo 'Hi! '.$_SESSION['f_user_name'];
		}else {echo 'Hi! Guest';}
		?>
		</h3>
		<h3><img src="<?php 
		$queryq=mysqli_query($connect,"SELECT * FROM franchisee_login WHERE f_user_email='$_SESSION[f_user_email]'");
		$rowq=mysqli_fetch_array($queryq);
		if(!empty($rowq['f_user_image'])){echo 'data:image/*;base64,'.base64_encode($rowq['f_user_image']);}else {echo 'images/logo.png';} ?>"></h3>
		
		
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


<div id="alert_display_full">
	<div id="loader1"></div>
	<h3>Loading...</h3>

</div>