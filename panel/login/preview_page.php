<?php

require('connect.php');

?>


<?php

if(!isset($_SESSION['user_email'])){echo '<meta http-equiv="refresh" content="0;URL=login.php">';}
?>

<?php
$query=mysqli_query($connect,'SELECT * FROM digi_card WHERE id="'.$_SESSION['card_id_inprocess'].'" AND user_email="'.$_SESSION['user_email'].'"');

if(mysqli_num_rows($query)==0){
	echo '<meta http-equiv="refresh" content="0;URL=index.php">';
}else {
	$row=mysqli_fetch_array($query);
	
}

// auto download contact



?>
<link rel="stylesheet"
    href="<?php if(!empty($row['d_css'])){echo '../'.$row['d_css'];}else {echo '../card_css1.css';} ?>">




<script>
$(document).ready(function() {
    $('.mobile_home').on('click', function() {
        $('#header').toggleClass('add_height');

    })
})
</script>

<!----------------------copy from here ------------------------->

<?php
// if($row['d_payment_status']=='Created'){echo '<div class="card_status1"><p>Trial</p></div>';
?>
<!--<div class="save_contact_popup">-->
<!--	<div class="close" id="close_save_pop">&times;</div>-->
<!--	<p> Hi <?php if(!empty($row['d_f_name'])){echo $row['d_f_name'].' '.$row['d_l_name'];}else {echo 'Customer.';} ?></p>-->
<!--	<p>Your card has been created.</p>-->
<!--	<p>Pay <del> ₹ 2999 </del>  ₹ 999 now to activate your card.</p>-->




<!--	<a href="<?php //echo 'payment_page/pay.php?id='.$row['id']; ?>" ><div class="btn_save">Activate Now</div></a>-->
<!--</div>-->


<?php
require('../../includes/config.php');
// }else if($row['d_payment_status']=='Success'){echo '<div class="card_status2"><p>Active</p></div>';}else if($row['d_payment_status']=='Failed'){echo '<div class="card_status3"><p>Inactive</p></div>';}

?>

<div class="btn_holder">
    <a href="<?php echo BASE_URL . '/panel/login/create_card7.php'; ?>">
        <div class="back_btn"><i class="fa fa-chevron-circle-left"></i> Back</div>
    </a>

</div>

<!-----popup for customer login to see page---------------->


<script>
$(document).ready(function() {
    $('#close_save_pop').on('click', function() {
        $('.save_contact_popup').slideToggle();
    })
})
</script>
<!-----popup for customer login to see page---------------->
<!-----Copy from here---------------->
<!----------------------copy from here ------------------------->


<div class="card" id="home">

    <div class="card_content"><img
            src="<?php if(!empty($row['d_logo'])){echo 'data:image/*;base64,'.base64_encode($row['d_logo']);} ?>"
            alt="Logo"></div>
    <div class="card_content2">
        <h2><?php if(!empty($row['d_f_name'])){echo $row['d_f_name'].' '.$row['d_l_name'];} ?></h2>
        <p><?php if(!empty($row['d_position'])){echo $row['d_position'];} ?></p>
        <p><?php if(!empty($row['d_comp_name'])){echo $row['d_comp_name'];} ?></p>

    </div>
    <div class="dis_flex">
        <?php if(!empty($row['d_contact'])){echo '<a href="tel:+91'.$row['d_contact'].'" target="_blank"><div class="link_btn"><i class="fa fa-phone"></i> Call</div></a>';} ?>
        <?php if(!empty($row['d_whatsapp'])){echo '<a href="https://api.whatsapp.com/send?phone=91'.str_replace('+91','',$row['d_whatsapp']).'&text=Hi, '.$row['d_comp_name'].'" target="_blank"><div class="link_btn"><i class="fa fa-whatsapp"></i> WhatsApp</div></a>';} ?>



        <?php if(!empty($row['d_location'])){echo '<a href="'.$row['d_location'].'" target="_blank"><div class="link_btn"><i class="fa fa-map-marker"></i> Direction</div></a>';} ?>
        <?php if(!empty($row['user_email'])){echo '<a href="Mailto:'.$row['user_email'].'" target="_blank"><div class="link_btn"><i class="fa fa-envelope"></i> Mail</div></a>';} ?>
        <?php if(!empty($row['d_website'])){echo '<a href="https://'.$row['d_website'].'" target="_blank"><div class="link_btn"><i class="fa fa-globe"></i> Website</div></a>';} ?>

    </div>

    <div class="contact_details">
        <?php if(!empty($row['d_contact'])){echo '<div class="contact_d"><i class="fa fa-phone"></i><p>'.$row['d_country_code'].'-'.$row['d_contact'].'</p></div>';} ?>
        <?php if(!empty($row['d_contact2'])){echo '<div class="contact_d"><i class="fa fa-phone"></i><p>'.$row['d_country_code'].'-'.$row['d_contact2'].'</p></div>';} ?>
        <?php if(!empty($row['user_email'])){echo '<div class="contact_d"><i class="fa fa-envelope"></i><p>'.$row['user_email'].'</p></div>';} ?>
        <?php if(!empty($row['d_address'])){echo '<div class="contact_d"><i class="fa fa-map-marker" ></i><p>'.$row['d_address'].'</p></div>';} ?>

    </div>

    <!--<div class="dis_flex">-->
    <!--	<div class="share_wtsp">-->
    <!--		<form action="https://api.whatsapp.com/send" id="wtsp_form" target="_blank"><input type="text"  name="phone" placeholder="WhatsApp Number with Country code	" value="+91"><input type="hidden" name="text" value="https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>"><div class="wtsp_share_btn" onclick="subForm()"><i class="fa fa-whatsapp"></i> Share</div></form>-->

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

        <!-- <div class="big_btns" id="share_box_pop">Share <i class="fa fa-share-alt"></i></div>

        <div class="share_box">
            <div class="close" id="close_sharer">&times;</div>
            <p>Share My Digital Card </p>
            <a
                href="https://api.whatsapp.com/send?text=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>">
                <div class="shar_btns"><i class="fa fa-whatsapp" id="whatsapp2" target="_blank"></i>
                    <p>WhatsApp</p>
                </div>
            </a>
            <a href="sms:?body=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>"
                target="_blank">
                <div class="shar_btns"><i class="fas fa-comment-dots"></i>
                    <p>SMS</p>
                </div>
            </a>

            <a href="https://www.facebook.com/sharer/sharer.php?u=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>"
                target="_blank">
                <div class="shar_btns"><i class="fa fa-facebook"></i>
                    <p>Facebook</p>
                </div>
            </a>
            <a href="https://twitter.com/intent/tweet?text=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>"
                target="_blank">
                <div class="shar_btns"><i class="fa fa-twitter"></i>
                    <p>Twitter</p>
                </div>
            </a>
            <a href="" target="_blank">
                <div class="shar_btns"><i class="fa fa-instagram"></i>
                    <p>Instagram</p>
                </div>
            </a>
            <a href="https://www.linkedin.com/cws/share?url=https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $row['card_id']; ?>"
                target="_blank">
                <div class="shar_btns"><i class="fa fa-linkedin"></i>
                    <p>Linkedin</p>
                </div>
            </a>
        </div> -->

        <script>
        // $(document).ready(function() {
        //     $('#close_sharer,#share_box_pop').on('click', function() {
        //         $('.share_box').slideToggle();
        //     });
        // })
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

<div class="card2" style="display:none;">

    <h3>Scan QR Code to download the contact details</h3>
    <img src="https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=https://<?php echo $_SERVER['HTTP_HOST'];?>/<?php echo $row['card_id']; ?>"
        id="qr_code_d">

</div>


<!--------------about us --------------------------->

<div class="card2" id="about_us">
    <h3>About Us</h3>
    <?php //if(!empty($row['d_comp_est_date'])){ echo '<p>'.$row['d_comp_est_date'].'</p>';} ?>
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
    <h3>Shop Online </h3>
    <h3>From Our Store</h3>


    <?php 
		for($x=0;$x<=30;$x++){
			if(!empty($row3["pro_name$x"])){
				
				echo '<div class="order_box">';
				
				echo '<img src="data:image/*;base64,'.base64_encode($row3["pro_img$x"]).'" alt="Product">';
				echo '<h2>'.$row3["pro_name$x"].'</h2>';
				echo '<p><del><i class="fa fa-rupee"></i>'.$row3["pro_mrp$x"].' </del></p>';
				echo '<h4>'.$row3["pro_price$x"].' <i class="fa fa-rupee"></i></h4>';
				echo "<a href='https://api.whatsapp.com/send?phone=91".str_replace("+91","",$row['d_whatsapp'])."&text=I am interested in Product: ".$row3["pro_name$x"].", Price: ".$row3["pro_price$x"]."' target='_blank'><div class='btn_buy'>Enquiry</div></a>";
				
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


<!----------email to  info----------------------->
<div class="card2" id="enquery">

    <form action="#" method="post">
        <h3>Contact Us</h3>

        <input type="" name="c_name" placeholder="Enter Your Name" required>
        <input type="" name="c_contact" maxlength="13" placeholder="Enter Your Mobile No" required>
        <input type="email" name="c_email" placeholder="Enter Your Email Address">
        <textarea name="c_msg" placeholder="Enter your Message or Query" required></textarea>
        <input type="submit" Value="Send" name="email_to_client">

    </form>
</div>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';  // Load PHPMailer (Composer required)

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_to_client'])) {
    // Check if email is set and not empty
    if (!isset($_POST['c_email']) || empty($_POST['c_email'])) {
        echo '<div class="alert danger">Error: Email address is required!</div>';
    } else {
        $_SESSION['form_submitted'] = true;
        $to = trim($_POST['c_email']); // Remove unwanted spaces

        $mail = new PHPMailer(true); // Enable exceptions

        try {
            // SMTP Configuration
            // Load credentials from the credentials file
            $creds = require '../../mail_credentials.php';

            // Setup PHPMailer
            $mail->isSMTP();
            $mail->Host = $creds['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $creds['username'];
            $mail->Password = $creds['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $creds['port'];

            // From email and name
            $mail->setFrom($fromEmail ?? $creds['from_email'], $fromName ?? $creds['from_name']);

            $mail->addAddress($to); // Send to the entered email
            // $mail->addCC("muskangwala900@gmail.com"); // CC to yourself

            // Email Content
            $mail->isHTML(false);
            $mail->Subject = "Customer query from " . $_SERVER['HTTP_HOST'];
            $mail->Body    = "Name: " . htmlspecialchars($_POST['c_name']) . "\n" . "Contact Number: " . htmlspecialchars($_POST['c_contact']) . "\n"
                . "Message: " . htmlspecialchars($_POST['c_msg']);

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
//        $subject = "Customer query from ".$_SERVER['HTTP_HOST'];
   
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

?> <br>


<!--<a href="index.php"><div class="create_card_btn"> Create Your Card /</div></a>-->
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

        <div class="menu_item" onclick="location.href='#youtube_video'"><i
                class="fa fa-video-camera"></i> Youtube Videos</div>
        <?php 
            } 
        ?>
        <!-- <div class="menu_item" onclick="location.href='#enquiry'"><i class="fa fa-comment"></i> Enquiry</div> -->
    </div>
</div>
<!-- <div class="menu_bottom">
		<div class="menu_container">
			<div class="menu_item" onclick="location.href='#home'"><i class="fa fa-home"></i> Home</div>
			<div class="menu_item" onclick="location.href='#about_us'"><i class="fa fa-briefcase"></i>About Us</div>
			<div class="menu_item" onclick="location.href='#product_services'"><i class="fa fa-ticket"></i>Product & Services</div>
			<div class="menu_item" onclick="location.href='#shop_online'"><i class="fa fa-archive"></i>Shop</div>
			<div class="menu_item" onclick="location.href='#gallery'"><i class="fa fa-image"></i>Gallery</div>
			<div class="menu_item" onclick="location.href='#youtube_video'"><i class="fa fa-video-camera"></i>Youtube Videos</div>
			<div class="menu_item" onclick="location.href='#payment'"><i class="fa fa-money"></i>Payment</div>
			<div class="menu_item" onclick="location.href='#enquery'"><i class="fa fa-comment"></i>Enquery</div>
		</div>
	</div> -->