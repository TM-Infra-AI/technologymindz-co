<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('contact_form.php');
// include_once('db_config.php');
// Set timezone
date_default_timezone_set("Asia/Kolkata");

// Start session
// session_start();

//Database Connection
// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm")
//     or die("Database connection failed: " . mysqli_connect_error());


// Store current timestamp
$date = date('Y-m-d H:i:s');

// Check if 'n' is provided and valid
if (!isset($_GET['n']) || empty($_GET['n'])) {
    die("Error: Card ID Not Found.");
}


// Use prepared statement to prevent SQL Injection
$stmt = $connect->prepare("SELECT * FROM digi_card WHERE card_id = ?");

$stmt->bind_param("s", $_GET['n']);
$stmt->execute();
$query = $stmt->get_result();

// Check if any record exists
if ($query->num_rows == 0) {
    die("Error: No card found for the given ID.");
}

// Fetch the data
$row = $query->fetch_assoc();
?>

<?php 
$tite = 'Technology Mindz || Digital Visiting Card'
 
?>
<link rel="shortcut icon" type="image" <img
    src="<?php if(!empty($row['d_logo'])){echo 'data:image/*;base64,'.base64_encode($row['d_logo']);} ?>" />

<head>
    <!-- HTML Meta Tags -->
    <title><?php echo $tite?></title>
    <meta property="og:image"
        content="<?php if(!empty($row['d_logo'])){echo 'panel/'.str_replace('../','',$row['d_logo_location']);} ?>">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST'].'/'.$row['card_id']; ?>">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="<?php if(!empty($row['d_comp_name'])){echo $row['d_comp_name'];} ?> || Our Digital Visiting Card ">
    <meta property="og:description"
        content=" <?php if(!empty($row['d_f_name'])){echo $row['d_f_name'].' '.$row['d_l_name'];} ?><?php if(!empty($row['d_position'])){echo ' ('.$row['d_position'].')';} ?><?php if(!empty($row['d_about_us'])){echo ' '.$row['d_about_us'].'';} ?>">


    <!-- Twitter Meta Tags -->
    <meta name="twitter:image"
        content="<?php if(!empty($row['d_logo'])){echo 'panel/'.str_replace('../','',$row['d_logo_location']);} ?>">

    <meta property="twitter:url" content="https://<?php echo $_SERVER['HTTP_HOST'].'/'.$row['card_id']; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="<?php if(!empty($row['d_comp_name'])){echo $row['d_comp_name'];} ?> || Our Digital Visiting Card ">
    <meta name="twitter:description"
        content=" <?php if(!empty($row['d_f_name'])){echo $row['d_f_name'].' '.$row['d_l_name'];} ?><?php if(!empty($row['d_position'])){echo ' ('.$row['d_position'].')';} ?><?php if(!empty($row['d_about_us'])){echo ' '.$row['d_about_us'];} ?>">

    <!-- Meta Tags Generated via -->

    <link rel="icon"
        href="<?php if(!empty($row['d_logo'])){echo 'data:image/x-icon;base64,'.base64_encode($row['d_logo']);} ?>"
        type="image/*" sizes="16x16" />
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <link rel='stylesheet' href='panel/all.css'
        integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>

    <link rel="stylesheet" href="panel/awesome.min.css">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />

    <link rel="stylesheet" href="css.css">
    <link rel="stylesheet" href="mobile_css.css">
    <script src="master_js.js"></script>
    <style>
    .btn2 {
        background: chartreuse;
        border-radius: 20px;
        border-color: #ff0082;
        padding-top: 13px;
        padding-bottom: 14px;
        padding-left: 4px;
    }
    </style>




    <?php
$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE  card_id="'.$_GET['n'].'" ');

if(mysqli_num_rows($query)==0){
	
	echo '<meta https-equiv="refresh" content="100;URL=../index.php">';
}else {
	$row=mysqli_fetch_array($query);
}

if(strlen($row['f_user_email']) > 3){}else{
// check if more then 1 year 

if($row['d_card_status']=="Active"){
			 $old_date=strtotime($row['d_payment_date']);
			 $today_date=strtotime($date);
			 // 31536000 is one year
			 	 $renew_date=($today_date-$old_date)/31536000;
			
			if($renew_date>$old_date){
				mysqli_query($connect,'UPDATE digi_card SET d_payment_status="Pending", d_card_status="Inactive" WHERE id="'.$row['id'].'"');
				echo '<div class="full_page_alert"><div class="alert danger">This card does not exists or Deactivated. Contact Help +917573003890</div></div>';
			}else {
				mysqli_query($connect,'UPDATE digi_card SET d_payment_status="Success", d_card_status="Active" WHERE id="'.$row['id'].'"');
			}
}
	// check if trial avtive 

	if($row['d_payment_status']=="Created" ){
		//604800 is 7 days
			  $today=strtotime($date);
			  $card_created=strtotime($row['uploaded_date']);
			 $f_date=($today-$card_created)/604800;
			
		
			
			if($f_date > 1){
				mysqli_query($connect,'UPDATE digi_card SET d_payment_status="Created", d_card_status="Inactive" WHERE id="'.$row['id'].'"');
				echo '<div class="full_page_alert">Card is Deactiveted. Contact Help +917573003890</div>';
			}else {
				mysqli_query($connect,'UPDATE digi_card SET d_payment_status="Created", d_card_status="Active" WHERE id="'.$row['id'].'"');
			}
	}
	
	
	// check if trial avtive 
	
}
			
			

if($row['d_card_status']=="Inactive"){
	
	echo '<div class="full_page_alert"><a href="https://'.$_SERVER['HTTP_HOST'].'/panel/login/payment_page/pay.php?id='.$row['id'].'"><div class="alert danger">Card is Deactiveted. </div>If this is your card. Click here to activate your card.<div class="btn2">Activate Now</div></div>';
}else {}



?>
    <link rel="stylesheet"
        href="<?php if(!empty($row['d_css'])){echo 'panel/'.$row['d_css'];}else {echo 'panel/card_css1.css';} ?>">

    <script>
    $(document).ready(function() {
        $('.mobile_home').on('click', function() {
            $('#header').toggleClass('add_height');

        })
    })
    </script>

    <style>
    .full_page_alert {
        position: fixed;
        width: -webkit-fill-available;
        height: -webkit-fill-available;
        background: white;
        top: 0;
        z-index: 9999999;
        padding: 63px;
        text-align: center;
    }
    </style>
    <!----------------------copy from here ------------------------->

    <div class="card" id="home">
        <?php
//view counter
			
			$query_views=mysqli_query($connect,'SELECT * FROM views WHERE ip="'.$_SERVER['REMOTE_ADDR'].'" AND card_id="'.$row['id'].'"');
		// count views 
			$query_views_count=mysqli_query($connect,'SELECT * FROM views WHERE card_id="'.$row['id'].'"');
			
			if(mysqli_num_rows($query_views) >> 0){}
			else {
				$insert_view=mysqli_query($connect,'INSERT INTO views (ip,uploaded_date,card_id) VALUES ("'.$_SERVER['REMOTE_ADDR'].'","'.$date.'","'.$row['id'].'")');
				
			}
			// count views 
			// echo '<div class="view_counter"><i class="fa fa-eye"></i> <br>'.mysqli_num_rows($query_views_count).'</div>';
// view counter
			?>

        <div class="card_content"><img
                src="<?php if(!empty($row['d_logo'])){echo 'data:image/*;base64,'.base64_encode($row['d_logo']);} ?>"
                alt="Logo"></div>
        <div class="card_content2">
            <h2><?php if(!empty($row['d_f_name'])){echo $row['d_f_name'].' '.$row['d_l_name'];} ?></h2>
            <p><?php if(!empty($row['d_position'])){echo $row['d_position'];} ?></p>
            <p><?php if(!empty($row['d_comp_name'])){echo $row['d_comp_name'];} ?></p>





        </div>
        <div class="dis_flex">
            <?php if(!empty($row['d_contact'])){echo '<a href="tel:'.$row['d_country_code'].'-'.$row['d_contact'].'" target="_blank"><div class="link_btn"><i class="fa fa-phone"></i> Call</div></a>';} ?>
            <?php //if(!empty($row['d_whatsapp'])){echo '<a href="https://api.whatsapp.com/send?phone='.str_replace('','',$row['d_whatsapp']).'&text=Hi, '.$row['d_comp_name'].'" target="_blank"><div class="link_btn"><i class="fa fa-whatsapp"></i> WhatsApp</div></a>';} ?>
<?php
if (!empty($row['d_whatsapp'])) {
    $whatsapp_number = preg_replace('/\D/', '', $row['d_country_code'] . $row['d_whatsapp']);
    echo '<a href="https://api.whatsapp.com/send?phone=' . $whatsapp_number . '&text=Hi, ' . $row['d_comp_name'] . '" target="_blank"><div class="link_btn"><i class="fa fa-whatsapp"></i> WhatsApp</div></a>';
}
?>


            <?php if(!empty($row['d_location'])){echo '<a href="'.$row['d_location'].'" target="_blank"><div class="link_btn"><i class="fa fa-map-marker"></i> Direction</div></a>';} ?>
            <?php if(!empty($row['user_email'])){echo '<a href="Mailto:'.$row['user_email'].'" target="_blank"><div class="link_btn"><i class="fa fa-envelope"></i> Mail</div></a>';} ?>
            <?php if(!empty($row['d_website'])){echo '<a href="https://'.$row['d_website'].'" target="_blank"><div class="link_btn"><i class="fa fa-globe"></i> Website</div></a>';} ?>
            <?php if(!empty($row['d_review'])){echo '<a href="https://'.$row['d_review'].'" target="_blank"><div class="link_btn"><i class="fa fa-globe"></i> Review</div></a>';} ?>
        </div>

        <div class="contact_details">
            <?php if(!empty($row['d_contact'])){?> <div class="contact_d"
                onclick="location.href='<?php echo 'tel:'.$row['d_country_code'].'-'.$row['d_contact'];?>'"><i class="fa fa-phone"></i>
                <p><?php echo $row['d_country_code'].'-'.$row['d_contact']; ?></p>
            </div><?php ;} ?>

            <?php if(!empty($row['d_contact2'])){?> <div class="contact_d"
                onclick="location.href='<?php echo 'tel:'.$row['d_contact2'];?>'"><i class="fa fa-phone"></i>
                <p><?php echo $row['d_country_code'].'-'.$row['d_contact2']; ?></p>
            </div><?php ;} ?>

            <?php if(!empty($row['user_email'])){?> <div class="contact_d"
                onclick="location.href='<?php echo 'Mailto:'.$row['user_email'];?>'"><i class="fa fa-envelope"></i>
                <p><?php echo $row['user_email']; ?></p>
            </div><?php ;} ?>

            <?php if(!empty($row['d_address'])){?> <div class="contact_d" onclick="location.href='<?php 
					if(!empty($row['d_location'])){
						echo $row['d_location'];
					}else {
						echo 'https://google.com/maps?q='.$row['d_address'];
					}				
				?>'"><i class="fa fa-map-marker"></i>
                <p><?php echo $row['d_address']; ?></p>
            </div><?php ;} ?>


        </div>

        <!--	<div class="dis_flex">-->
        <!--	<div class="share_wtsp">-->
        <!--		<form action="https://api.whatsapp.com/send" id="wtsp_form" target="_blank"><input type="text"  name="phone" placeholder="Number with country code" value=""><input type="hidden" name="text" value="https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>"><div class="wtsp_share_btn" onclick="subForm()"><i class="fa fa-whatsapp"></i> Share</div></form>-->

        <!--		<script>-->

        <!--		$(document).ready(function(){-->
        <!--			$('.wtsp_share_btn').on('click',function(){-->
        <!--				$('#wtsp_form').submit();-->
        <!--			})-->

        <!--		})-->
        <!--		</script>-->
        <!--	</div>-->
        <!--</div>-->

        <div class="dis_flex">

            <?php if(!empty($row['d_contact'])){echo '<a href="contact_download.php?id='.$row['id'].'"><div class="big_btns">Save to Contacts <i class="fa fa-download"></i></div></a>';} ?>

            <!--<div class="big_btns" id="share_box_pop">Share <i class="fa fa-share-alt"></i></div>-->

            <!-- <div class="share_box">
                <div class="close" id="close_sharer">&times;</div>
                <p>Share My Digital Card </p>
            </div> -->

            <script>
            $(document).ready(function() {
                $('#close_sharer,#share_box_pop').on('click', function() {
                    $('.share_box').slideToggle();
                });
            })
            </script>

        </div>

        <div class="dis_flex">
            <?php if (!empty($row['d_fb'])) { ?>
            <a href="<?= $row['d_fb']; ?>" target="_blank" class="social_med">
                <img src="images/facebook.svg" alt="Facebook" width="40">
            </a>
            <?php } ?>

            <?php if (!empty($row['d_youtube'])) { ?>
            <a href="<?= $row['d_youtube']; ?>" target="_blank" class="social_med">
                <img src="images/youtube.svg" alt="YouTube" width="40">
            </a>
            <?php } ?>

            <?php if (!empty($row['d_twitter'])) { ?>
            <a href="<?= $row['d_twitter']; ?>" target="_blank" class="social_med">
                <img src="images/twitter.svg" alt="Twitter" width="40">
            </a>
            <?php } ?>

            <?php if (!empty($row['d_instagram'])) { ?>
            <a href="<?= $row['d_instagram']; ?>" target="_blank" class="social_med">
                <img src="images/instagram.svg" alt="Instagram" width="40">
            </a>
            <?php } ?>

            <?php if (!empty($row['d_linkedin'])) { ?>
            <a href="<?= $row['d_linkedin']; ?>" target="_blank" class="social_med">
                <img src="images/linkedin.svg" alt="LinkedIn" width="40">
            </a>
            <?php } ?>

            <?php if (!empty($row['d_pinterest'])) { ?>
            <a href="<?= $row['d_pinterest']; ?>" target="_blank" class="social_med">
                <img src="images/pinterest.svg" alt="Pinterest" width="40">
            </a>
            <?php } ?>
        </div>

    </div>



    <!--------------about us --------------------------->

    <div class="card2" id="about_us">
        <h3>About Us</h3>
        <!--<?php if(!empty($row['d_comp_est_date'])){echo '<p>'.$row['d_comp_est_date'].'</p>';} ?>-->
        <?php if(!empty($row['d_about_us'])){echo '<p>'.$row['d_about_us'].'</p>';} ?>



    </div>

    <!------------shopping online-------------------------->


    <?php 
if(isset($row['id'])){
			
	$query3=mysqli_query($connect,'SELECT * FROM products WHERE id="'.$row['id'].'" ');
	$row3=mysqli_fetch_array($query3);
		}

	if(!empty($row3["pro_name1"]) || !empty($row3["pro_name2"]) || !empty($row3["pro_name3"]) || !empty($row3["pro_name4"]) || !empty($row3["pro_name5"])|| !empty($row3["pro_name6"])|| !empty($row3["pro_name7"])|| !empty($row3["pro_name8"])|| !empty($row3["pro_name9"])|| !empty($row3["pro_name10"])){ ?>
    <div class="card2" id="shop_online">
        <h3>Our Offers</h3>
        <h3></h3>


        <?php 
		for($x=0;$x<=30;$x++){
			if(!empty($row3["pro_name$x"])){
				
				echo '<div class="order_box">';
				
				echo '<img src="data:image/*;base64,'.base64_encode($row3["pro_img$x"]).'" alt="Product">';
				echo '<h2>'.$row3["pro_name$x"].'</h2>';
				echo '<p><del>'.$row3["pro_mrp$x"].' <i class="fa fa-rupee"></i></del></p>';
				echo '<h4>'.$row3["pro_price$x"].' <i class="fa fa-rupee"></i></h4>';
				echo "<a href='https://api.whatsapp.com/send?phone=".str_replace("","",$row['d_whatsapp'])."&text=I am interested in Product: ".$row3["pro_name$x"].", Price: ".$row3["pro_price$x"]."' target='_blank'><div class='btn_buy'>Enquiry</div></a>";
				
				echo '</div>';
			} 
		}
			
		?>


    </div>
    <?php } ?>




    <!--------------youtube videos--------------------------->

    <?php 	if(!empty($row["d_youtube1"]) || !empty($row["d_youtube2"]) || !empty($row["d_youtube3"]) || !empty($row["d_youtube4"]) || !empty($row["d_youtube5"])){ ?>
    <div class="card2" id="youtube_video">
        <h3>Youtube Videos</h3>


        <?php 
		for($x=0;$x<=10;$x++){
			if(!empty($row["d_youtube$x"])){
				
					
				$array1=array('youtu.be/','watch?v=','&feature=youtu.be');
				$array2=array('www.youtube.com/embed/','embed/','');
				
				$youtubelink=str_replace($array1,$array2,$row["d_youtube$x"]);
			
				echo '<iframe src="'.$youtubelink.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
			} 
		}
			
		?>


    </div>
    <?php } ?>



    <!----------product and services ----------------------->
    <?php 
if(isset($row['id'])){
			
	$query2=mysqli_query($connect,'SELECT * FROM digi_card2 WHERE id="'.$row['id'].'" ');
	$row2=mysqli_fetch_array($query2);
		}

	if(!empty($row2["d_pro_img1"]) || !empty($row2["d_pro_img2"]) || !empty($row2["d_pro_img3"]) || !empty($row2["d_pro_img4"]) || !empty($row2["d_pro_img5"]) || !empty($row2["d_pro_img6"])|| !empty($row2["d_pro_img7"])|| !empty($row2["d_pro_img8"])|| !empty($row2["d_pro_img9"])|| !empty($row2["d_pro_img10"])|| !empty($row2["d_pro_img11"])|| !empty($row2["d_pro_img12"])|| !empty($row2["d_pro_img13"])|| !empty($row2["d_pro_img14"])|| !empty($row2["d_pro_img15"])|| !empty($row2["d_pro_img16"])|| !empty($row2["d_pro_img17"])|| !empty($row2["d_pro_img18"])|| !empty($row2["d_pro_img19"])|| !empty($row2["d_pro_img20"])) { ?>

    <div class="card2" id="product_services">
        <h3>Products & Services</h3>


        <?php 
            for ($x = 0; $x <= 20; $x++) {
                if (!empty($row2["d_pro_img$x"])) {
                    $productName = htmlspecialchars($row2["d_pro_name$x"] ?? "");
                    $productImage = 'data:image/*;base64,' . base64_encode($row2["d_pro_img$x"]);
                    $productUrl = trim($row2["d_pro_url$x"] ?? ""); // Trim whitespace
            
                    // Check if the URL is a valid link
                    if (!empty($productUrl) && !preg_match("/^(https?:\/\/)/", $productUrl)) {
                        // If it's not a valid URL, turn it into a Google search query
                        $productUrl = "https://www.google.com/search?q=" . urlencode($productUrl);
                    }
            
                    echo '<div class="product_s">';
                    echo "<p>{$productName}</p>";
            
                    // Only wrap the image in a link if there is a valid URL or search query
                    if (!empty($productUrl)) {
                        echo "<a href='{$productUrl}' target='_blank'>
                                <img src='{$productImage}' alt='Logo'>
                              </a>";
                    } else {
                        echo "<img src='{$productImage}' alt='Logo'>"; // Show image without a link if no URL
                    }
            
                    // Show "See More" button only if a valid URL/search is available
                    // if (!empty($productUrl)) {
                    //     echo "<br><br><a href='{$productUrl}' target='_blank'><div class='btn_buy'>See More</div></a>";
                    // }
            
                    echo '</div>';	
                }
            }
        ?>



    </div>

    <?php } ?>


    <!---------- Image Gallery ----------------------->
    <?php 
if(isset($row['id'])){
    $query3 = mysqli_query($connect, 'SELECT * FROM digi_card3 WHERE id="'.$row['id'].'" ');
    $row3 = mysqli_fetch_array($query3);
}

if (!empty($row3["d_gall_img1"]) || !empty($row3["d_gall_img2"]) || !empty($row3["d_gall_img3"]) || 
    !empty($row3["d_gall_img4"]) || !empty($row3["d_gall_img5"]) || !empty($row3["d_gall_img6"]) || 
    !empty($row3["d_gall_img7"]) || !empty($row3["d_gall_img8"]) || !empty($row3["d_gall_img9"]) || 
    !empty($row3["d_gall_img10"])) { ?>

    <div class="card2" id="gallery">
        <h3>Image Gallery</h3>
        <?php 
        for ($x = 1; $x <= 10; $x++) { // Loop from 1 to 10 (Not 0 to 10)
            $column_name = "d_gall_img" . $x; // Correct way to create column name dynamically
            if (!empty($row3[$column_name])) {
                echo '<div class="img_gall">';
                echo '<img src="data:image/*;base64,'.base64_encode($row3[$column_name]).'" alt="Gallery Image">';
                echo '</div>';
            } 
        }
        ?>
    </div>
    <?php } ?>




    <!----------payment info----------------------->
    <?php 	if(!empty($row["d_paytm"]) || !empty($row["d_account_no"]) ||!empty($row["d_qr_paytm"]) ||!empty($row["d_qr_phone_pay"]) ||!empty($row["d_qr_google_pay"]) || !empty($row["d_google_pay"]) || !empty($row["d_phone_pay"])|| !empty($row["d_ac_type"])  ){ ?>

    <div class="card2" id="payment">
        <h3>Payment Info</h3>


        <?php 	if(!empty($row["d_paytm"])){echo '<h2>Paytm</h2><p>'.$row['d_paytm'].'</p>';}	?>
        <?php 	if(!empty($row["d_google_pay"])){echo '<h2>Google Pay</h2><p>'.$row['d_google_pay'].'</p>';}?>
        <?php 	if(!empty($row["d_phone_pay"])){echo '<h2>PhonePe</h2><p>'.$row['d_phone_pay'].'</p>';}	?>

        <?php 	if(!empty($row["d_account_no"])){echo '<h3>Bank Account Details</h3>'; } ?>

        <?php 	if(!empty($row["d_ac_name"])){echo '<h2>Name:</h2><p>'.$row['d_ac_name'].'</p>';}	?>
        <?php 	if(!empty($row["d_account_no"])){echo '<h2>Account Number:</h2><p>'.$row['d_account_no'].'</p>';}?>
        <?php 	if(!empty($row["d_ifsc"])){echo '<h2>IFSC Code:</h2><p>'.$row['d_ifsc'].'</p>';	}?>
        <?php 	if(!empty($row["d_bank_name"])){echo '<h2>BANK Name:</h2><p>'.$row['d_bank_name'].'</p>';}	?>

        <?php 	if(!empty($row["d_ac_type"])){echo '<h3>GST Number </h3><h2>GST No:</h2><p>'.$row['d_ac_type'].'</p>';	}?>

        <?php if(!empty($row["d_qr_paytm"])){echo '<img src="data:image/*;base64,'.base64_encode($row["d_qr_paytm"]).'" alt="Paytm QR">';	}	?>
        <?php if(!empty($row["d_qr_google_pay"])){echo '<img src="data:image/*;base64,'.base64_encode($row["d_qr_google_pay"]).'" alt="Google Pay QR">';	}	?>
        <?php if(!empty($row["d_qr_phone_pay"])){echo '<img src="data:image/*;base64,'.base64_encode($row["d_qr_phone_pay"]).'" alt="PhonePe QR">';	}	?>



    </div>
    <?php } ?>

    <!----------Feedback----------------------->
    <!--<div class="card2" id="feedback">-->

    <!--<h3>Feedback</h3>-->
    <!--<script>-->

    <!--$(':radio').change(function() {-->
    <!--  console.log('New star rating: ' + this.value);-->
    <!--});-->
    <!--</script>-->
    <!--<form id="feedback_form"  method="post">-->
    <!--<p class="select_star"> Select Star</p>-->
    <!--	<div class="rating">-->

    <!--	  <label>-->
    <!--		<input type="radio" name="r_star" value="1" required>-->
    <!--		<span class="icon">★</span>-->
    <!--	  </label>-->
    <!--	  <label>-->
    <!--		<input type="radio" name="r_star" value="2" required>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--	  </label>-->
    <!--	  <label>-->
    <!--		<input type="radio" name="r_star" value="3" required>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>   -->
    <!--	  </label>-->
    <!--	  <label>-->
    <!--		<input type="radio" name="r_star" value="4" required>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--	  </label>-->
    <!--	  <label>-->
    <!--		<input type="radio" name="r_star"  value="5" required>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--		<span class="icon">★</span>-->
    <!--	  </label>-->

    <!--	</div>-->

    <!--	<input type="name" name="r_name" placeholder="Your name" required>-->
    <!--	<input type="email" name="r_email" placeholder="Your email id" >-->

    <!--	<input type="number" max="999999999999" min="5555555555" name="r_contact" placeholder="Your contact ">-->
    <!--	<textarea name="r_msg" placeholder="Your feedback "></textarea>-->
    <!--	<input type="submit" name="submit_feedback" value="Submit Feedback"> -->

    <!--	<p class="note">Note: for privecy and security reasons we do not show your contact details. For more info you can contact admin or your franchisee.</p>-->
    <!--</form>-->


    <!--</div>-->
    <!----------Feedback end ----------------------->



	<div class="card2" id="enquery">

<form action="#" method="post">
	<h3>Contact Us</h3>

	<input type="text" name="c_name" placeholder="Enter Your Name" required style="margin-top:15px;">
	<input type="text" name="c_contact" maxlength="13" placeholder="Enter Your Mobile No" required>
	<input type="email" name="c_email" placeholder="Enter Your Email Address" required>
	<textarea name="c_msg" placeholder="Enter your Message or Query" required></textarea>
	<input type="submit" Value="Send" name="email_to_client">
</form>

</div>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer (Composer required)

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_to_client'])) {
// Check if email is set and not empty
if (!isset($_POST['c_email']) || empty($_POST['c_email'])) {
	echo '<div class="alert danger">Error: Email address is required!</div>';
} else {
	$_SESSION['form_submitted'] = true;
	$to = trim($row['user_email']); // Remove unwanted spaces
	
    // Sanitize form inputs
    $name    = htmlspecialchars($_POST['c_name'] ?? 'N/A');
    $contact = htmlspecialchars($_POST['c_contact'] ?? 'N/A');
    $email   = htmlspecialchars($_POST['c_email'] ?? 'N/A'); 
    $message = htmlspecialchars($_POST['c_msg'] ?? 'N/A');
    
    // Assuming you have the card owner's name
    $cardOwnerName = htmlspecialchars($name);   

    $mail = new PHPMailer(true); // Enable exceptions

	try {
		// SMTP Configuration
	  // Load credentials from the credentials file
      $creds = require $_SERVER['DOCUMENT_ROOT'] . '/mail_credentials.php';

      // Setup PHPMailer
      $mail->isSMTP();
      $mail->Host = $creds['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $creds['username'];
      $mail->Password = $creds['password'];
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = $creds['port'];

      // From email and name
        $mail->setFrom($creds['from_email'], $creds['from_name']);
      
		$mail->addAddress($to); // Send to the entered email
        

		// Email Content
		$mail->isHTML(false);
		$mail->Subject = "Customer query from " . $_SERVER['HTTP_HOST'];
        // New formatted email body
        $mail->Body = "Hi $cardOwnerName,\r\n\r\n";
        $mail->Body .= "A user has filled the contact us form on your digital business card, please find the details below:\r\n\r\n";
        $mail->Body .= "Name: $name\r\n";
        $mail->Body .= "Contact Number: $contact\r\n";
        $mail->Body .= "Email: $email\r\n";
        $mail->Body .= "Message:\r\n$message\r\n\r\n";
        $mail->Body .= "Thanks,\r\n";
        $mail->Body .= "Sales TM";

		// Send Email
		if ($mail->send()) {
			echo '<div class="alert success">Thanks! We have received your email.<br>We will get back to you within 24hrs.</div>';
		} else {
			echo '<div class="alert danger">Error sending email! Please try again.</div>';
		}
	} catch (Exception $e) {
		echo '<div class="alert danger">Mailer Error: ' . $mail->ErrorInfo . '</div>';
	}
}
}
?>



        <?php
// if(isset($_POST['email_to_client'])){
//         	$to = $row['user_email'];
// 			$subject = "Customer query from ".$_SERVER['HTTP_HOST'];
        
//         $message ='
//         Name:'.$_POST['c_name'].'
//         Contact Number: '.$_POST['c_contact'].'
//         Message:'.$_POST['c_msg'];
        
//         $headers .= 'From: <'.$_POST['c_email'].'>' . "\r\n";
//         $headers .= 'Cc: <'.$_POST['c_email'].'>' . "\r\n";
        
//         if(mail($to,$subject,$message,$headers)){
//         	echo '<div class="alert success">Thanks! We have received your email.<br> We will get back to you with in 24hrs.</div>';
//         }else {
//         	echo '<div class="alert danger">Error Email! try again</div>';
//         }
// }

?>

        <br>


        <!--<a href="index.php"><div class="create_card_btn">🔴 Create Your own Digital Visiting Card</div></a>-->
        <!--<a href="index.php"><div class="create_card_btn"> 🔴 अपना डिजिटल विजिटिंग कार्ड बनाये</div></a>-->

    </div>
    <style>
    .create_card_btn {
        background: linear-gradient(45deg, black, black);
        color: white;
        width: auto;
        padding: 20px;
        border-radius: 2px;
        line-height: 0.8;
        margin: 11px auto;
        font-size: 9px;
        text-align: center;
    }



    #svg_down {
        position: fixed;
        bottom: 0;
        z-index: -1;
        left: 0;
    }
    </style>



    <br>
    <br>
    <br>
    <br>
    <div class="menu_bottom">
        <div class="menu_container">
            <div class="menu_item" onclick="location.href='#home'"><i class="fa fa-home"></i> Home</div>
            <?php 
                if (!empty($row['d_about_us'])) { ?>
            <div class="menu_item" onclick="location.href='#about_us'"><i class="fa fa-briefcase"></i> About Us</div>
            <?php 
                } 
            
            	if(!empty($row2["d_pro_img1"]) || !empty($row2["d_pro_img2"]) || !empty($row2["d_pro_img3"]) || !empty($row2["d_pro_img4"]) || !empty($row2["d_pro_img5"]) || !empty($row2["d_pro_img6"])|| !empty($row2["d_pro_img7"])|| !empty($row2["d_pro_img8"])|| !empty($row2["d_pro_img9"])|| !empty($row2["d_pro_img10"])|| !empty($row2["d_pro_img11"])|| !empty($row2["d_pro_img12"])|| !empty($row2["d_pro_img13"])|| !empty($row2["d_pro_img14"])|| !empty($row2["d_pro_img15"])|| !empty($row2["d_pro_img16"])|| !empty($row2["d_pro_img17"])|| !empty($row2["d_pro_img18"])|| !empty($row2["d_pro_img19"])|| !empty($row2["d_pro_img20"])) { ?>

            <div class="menu_item" onclick="location.href='#product_services'"><i class="fa fa-ticket"></i> Products &
                Services</div>
            <?php 
                } 
                if (!empty($row3["d_gall_img1"]) || !empty($row3["d_gall_img2"]) || !empty($row3["d_gall_img3"]) || 
                !empty($row3["d_gall_img4"]) || !empty($row3["d_gall_img5"]) || !empty($row3["d_gall_img6"]) || 
                !empty($row3["d_gall_img7"]) || !empty($row3["d_gall_img8"]) || !empty($row3["d_gall_img9"]) || 
                !empty($row3["d_gall_img10"])) { ?>

            <div class="menu_item" onclick="location.href='#gallery'"><i class="fa fa-image"></i> Gallery</div>
            <?php 
                } 
            	if(!empty($row["d_youtube1"]) || !empty($row["d_youtube2"]) || !empty($row["d_youtube3"]) || !empty($row["d_youtube4"]) || !empty($row["d_youtube5"])){ ?>

            <div class="menu_item" onclick="location.href='#youtube_video'"><i class="fa fa-video-camera"></i> Youtube
                Videos</div>
            <?php 
                } 
            ?>
            <!-- <div class="menu_item" onclick="location.href='#enquiry'"><i class="fa fa-comment"></i> Enquiry</div> -->
        </div>
    </div>