 <?php

require('connect.php');
require('header.php');

?>


<div class="main3">

	
	<div class="btn_holder">
		<a href="select_theme.php"><div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div></a>
		
	</div>
	<h1>PDF Card</h1>
	
	<form action="" method="POST" enctype="multipart/form-data">
	

<!-------------------form ----------------------->	
		<img src="<?php if(!empty($_POST['d_logo'])){echo 'data:image/*;base64,'.base64_encode($_POST['d_logo']);}else {echo 'images/logo.png';} ?>" alt="Select image" id="showPreviewLogo" onclick="clickFocus()" >
		<div class="input_box"><p>Company Logo (Required)* 100x100 to 400x400 PX</p>
		
		
		
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
			<input type="file" name="d_logo" id="clickMeImage" onchange="readURL(this);" accept="image/*"  >
			
		</div>	
		
		<h3>Personal Details</h3>
		<div class="input_box"><p>Comp Name *</p><input type="text" name="d_comp_name" maxlength="20" placeholder="Enter First Name" required></div>
		
		<div class="input_box"><p>First Name *</p><input type="text" name="d_f_name" maxlength="20" placeholder="Enter First Name" value="<?php if(!empty($_POST['d_f_name'])){echo $_POST['d_f_name'];}?>" required></div>
		
		<div class="input_box"><p>Last Name (Optional)</p><input type="text" name="d_l_name" maxlength="20" placeholder="Enter Last Name  (Optional)" value="<?php if(!empty($_POST['d_l_name'])){echo $_POST['d_l_name'];}?>"></div>
		
		<div class="input_box"><p>Position/Designation * </p><input type="text" name="d_position" maxlength="20" placeholder="Enter Position/Designation (Ex. Manager etc.)" value="<?php if(!empty($_POST['d_position'])){echo $_POST['d_position'];}?>" required></div>
		
		<div class="input_box"><p>Phone No * </p><input type="text" name="d_contact" maxlength="13" placeholder="Enter Phone Number" value="<?php if(!empty($_POST['d_contact'])){echo $_POST['d_contact'];}?>" required></div>
		
		<div class="input_box"><p>Alternet Phone No (Optional)</p><input type="text" name="d_contact2" maxlength="13" placeholder="Enter Alternet Phone Number  (Optional)" value="<?php if(!empty($_POST['d_contact2'])){echo $_POST['d_contact2'];}?>" ></div>
		
		<div class="input_box"><p>WhatsApp No * </p><input type="text" name="d_whatsapp" maxlength="13" placeholder="Enter WhatsApp Number"  value="<?php if(!empty($_POST['d_whatsapp'])){echo $_POST['d_whatsapp'];}?>" required></div>
		
		<div class="input_box"><p>Address * </p><textarea type="text" name="d_address" maxlength="200" placeholder="Full Address"  required><?php if(!empty($_POST['d_address'])){echo $_POST['d_address'];}?></textarea></div>
		
		<div class="input_box"><p>Email Id * </p><input type="email" name="d_email" maxlength="100" placeholder="Email Address" value="<?php if(!empty($_POST['d_email'])){echo $_POST['d_email'];}?>" required></div>
		<div class="input_box"><p>Website (Optional) </p><input type="text" name="d_website" maxlength="200" placeholder="Website (Optional)" value="<?php if(!empty($_POST['d_website'])){echo $_POST['d_website'];}?>" ></div>
		<div class="input_box"><p>Location (Optional) </p><input type="text" name="d_location" maxlength="999" placeholder="Your Business Location (Optional)" value="<?php if(!empty($_POST['d_location'])){echo $_POST['d_location'];}?>" ></div>
		<div class="input_box"><p>Company Est Date *</p><input type="text" name="d_comp_est_date" maxlength="200" placeholder="When your comp. was started?" value="<?php if(!empty($_POST['d_comp_est_date'])){echo $_POST['d_comp_est_date'];}?>" required></div>
		
		<div class="input_box"><p>About Us * </p><textarea type="text" name="d_about_us" maxlength="800" placeholder="About your company/business"  required><?php if(!empty($_POST['d_about_us'])){echo $_POST['d_about_us'];}?></textarea></div>
			
		<input type="submit" class="" name="process2" value="Next 3" id="block_loader">
	
<!-------------------form ending----------------------->
	</form>
	
	<?php
	if(isset($_POST['process2'])){
		
		$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE id="'.$_SESSION['card_id_inprocess'].'"');
		if(mysqli_num_rows($query)==1){
			
		// enter details in database
				// compress file function creation 
						function compressImage($source,$destination,$quality){
							$imageInfo=getimagesize($source);
							
							$mime=$imageInfo['mime'];
							
							switch($mime){
								case 'image/jpeg':
								$image=imagecreatefromjpeg($source);
								break;
								case 'image/png':
								$image=imagecreatefrompng($source);
								break;
								case 'image/gif':
								$image=imagecreatefromgif($source);
								break;
								default:
								$image-imagecreatefromjpeg($source);
							}
							imagejpeg($image,$destination,$quality);
							
							return $destination;
							
						}
					
					// compress file function ended
				// image upload
				// image upload
				if(!empty($_FILES['d_logo']['tmp_name'])){
				
				
					
					
					$filename=$_FILES['d_logo']['name'];
							
							 $imageFileType = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
						$file_allow=array('png','jpeg','jpg','gif');
			if(in_array($imageFileType,$file_allow )){
							
							$source=$_FILES["d_logo"]['tmp_name'];
							
							
					$destination='pdf_image';
						if($_FILES["d_logo"]['size'] < 1000000){
							$quality=70;
						}
						else {echo 'Images size is more then 1MB resized automatically.';echo $quality=55; }
						
						//call the function for compressing image
						$compressimage=compressImage($source,$destination,$quality);
					
					
					
					
					
							
						}else {
								echo '<div class="alert danger">Only PNG,JPG,JPEG or GIF files allowed</div>';
						}
				}
				
				
		//card div open 
		?>
		<div class="full_pdf_display"><div class="fa fa-print"></div>
			<div class="pdf_card_view">
				
					<div class="card_content"><img src="<?php if(!empty($compressimage)){echo $compressimage;} ?>" alt="Logo"></div>
			<div class="card_content2">
				<h2><?php if(!empty($_POST['d_comp_name'])){echo $_POST['d_comp_name'];} ?></h2>
				<p><?php if(!empty($_POST['d_f_name'])){echo $_POST['d_f_name'].' '.$_POST['d_l_name'];} ?></p>
				<p><?php if(!empty($_POST['d_position'])){echo $_POST['d_position'];} ?></p>
				
			</div>
			<div class="dis_flex">
				<?php if(!empty($_POST['d_contact'])){echo '<a href="tel:+91'.$_POST['d_contact'].'" target="_blank"><div class="link_btn"><i class="fa fa-phone"></i> Call</div></a>';} ?>
				<?php if(!empty($_POST['d_whatsapp'])){echo '<a href="https://api.whatsapp.com/send?phone=91'.str_replace('+91','',$_POST['d_whatsapp']).'&text=Hi, '.$_POST['d_comp_name'].'" target="_blank"><div class="link_btn"><i class="fa fa-whatsapp"></i> WhatsApp</div></a>';} ?>
				
				
				
				<?php if(!empty($_POST['d_location'])){echo '<a href="'.$_POST['d_location'].'" target="_blank"><div class="link_btn"><i class="fa fa-map-marker"></i> Direction</div></a>';} ?>
				<?php if(!empty($_POST['d_email'])){echo '<a href="Mailto:'.$_POST['d_email'].'" target="_blank"><div class="link_btn"><i class="fa fa-envelope"></i> Mail</div></a>';} ?>
				<?php if(!empty($_POST['d_website'])){echo '<a href="https://'.$_POST['d_website'].'" target="_blank"><div class="link_btn"><i class="fa fa-globe"></i> Website</div></a>';} ?>
			
			</div>
	
			<div class="contact_details">
				<?php if(!empty($_POST['d_contact'])){echo '<div class="contact_d"><i class="fa fa-phone"></i><p>'.$_POST['d_contact'].'</p></div>';} ?>
				<?php if(!empty($_POST['d_contact2'])){echo '<div class="contact_d"><i class="fa fa-phone"></i><p>'.$_POST['d_contact2'].'</p></div>';} ?>
				<?php if(!empty($_POST['d_email'])){echo '<div class="contact_d"><i class="fa fa-envelope"></i><p>'.$_POST['d_email'].'</p></div>';} ?>
				<?php if(!empty($_POST['d_address'])){echo '<div class="contact_d"><i class="fa fa-map-marker" ></i><p>'.$_POST['d_address'].'</p></div>';} ?>
				
			</div>
			
			<div class="dis_flex">
				<div class="share_wtsp">
					<form action="https://api.whatsapp.com/send" id="wtsp_form" target="_blank"><input type="text"  name="phone" placeholder="WhatsApp Number with Country code	" value="+91"><input type="hidden" name="text" value="https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_POST['card_id']; ?>"><div class="wtsp_share_btn" onclick="subForm()"><i class="fa fa-whatsapp"></i> Share On WhatsApp</div></form>
					
					<script>
					
					$(document).ready(function(){
						$('.wtsp_share_btn').on('click',function(){
							$('#wtsp_form').submit();
						})
						
					})
					</script>
				</div>
			</div>
			
			<div class="dis_flex">
			
			<?php if(!empty($_POST['d_contact'])){echo '';} ?>
				
				<div class="big_btns" id="share_box_pop">Share <i class="fa fa-share-alt"></i></div>
			
				<div class="share_box">
				
				
				<div class="close" id="close_sharer">&times;</div>
				<p>Share My Digital Card </p>
						<a href="https://api.whatsapp.com/send?text=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_POST['card_id']; ?>"><div class="shar_btns"><i class="fa fa-whatsapp" id="whatsapp2"  target="_blank"></i><p>WhatsApp</p></div></a>
					<a href="sms:?body=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_POST['card_id']; ?>" target="_blank"><div class="shar_btns"><i class="fas fa-comment-dots" ></i><p>SMS</p></div></a>
					
					<a href="https://www.facebook.com/sharer/sharer.php?u=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_POST['card_id']; ?>" target="_blank"><div class="shar_btns"><i class="fa fa-facebook" ></i><p>Facebook</p></div></a>
					<a href="https://twitter.com/intent/tweet?text=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_POST['card_id']; ?>" target="_blank"><div class="shar_btns"><i class="fa fa-twitter"></i><p>Twitter</p></div></a>
					<a href="" target="_blank"><div class="shar_btns"><i class="fa fa-instagram"></i><p>Instagram</p></div></a>
					<a href="https://www.linkedin.com/cws/share?url=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $_POST['card_id']; ?>" target="_blank"><div class="shar_btns"><i class="fa fa-linkedin"></i><p>Linkedin</p></div></a>
				</div>
			
				<script>
					$(document).ready(function(){
						$('#close_sharer,#share_box_pop').on('click',function(){
							$('.share_box').slideToggle();
						});
					})
				
				
				</script>
			
			</div>
			<div class="dis_flex">
			
				<?php if(!empty($_POST['d_fb'])){echo '<a href="'.$_POST['d_fb'].'" target="_blank"><div class="social_med" ><i class="fa fa-facebook"></i></div></a>';} ?>
				<?php if(!empty($_POST['d_youtube'])){echo '<a href="'.$_POST['d_youtube'].'" target="_blank"><div class="social_med"><i class="fa fa-youtube"></i></div></a>';} ?>
				<?php if(!empty($_POST['d_twitter'])){echo '<a href="'.$_POST['d_twitter'].'" target="_blank"><div class="social_med"><i class="fa fa-twitter"></i></div></a>';} ?>
				<?php if(!empty($_POST['d_instagram'])){echo '<a href="'.$_POST['d_instagram'].'" target="_blank"><div class="social_med"><i class="fa fa-instagram"></i></div></a>';} ?>
				<?php if(!empty($_POST['d_linkedin'])){echo '<a href="'.$_POST['d_linkedin'].'" target="_blank"><div class="social_med"><i class="fa fa-linkedin"></i></div></a>';} ?>
				<?php if(!empty($_POST['d_pinterest'])){echo '<a href="'.$_POST['d_pinterest'].'" target="_blank"><div class="social_med"><i class="fa fa-pinterest"></i></div></a>';} ?>
			</div>
			
			
			
				
				
		
			</div>
			</div>
			
		<?php
		
		// card pdf create div close
		
		}else {
			
			echo '<a href="create_card.php"><div class="alert danger">Detail Not Available. Try Again Click here.</div></a>';
		}
		
	}
	?>

</div>


<footer class="">

<p>Copyright 2020 || <?php echo $_SERVER['HTTP_HOST']; ?></p>

</footer>