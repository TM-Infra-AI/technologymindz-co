


<?php
require('config.php');
require('razorpay-php/Razorpay.php');
error_reporting(E_ALL);
?>

<meta      name='viewport'      content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />
  <link rel="icon" href="images/favicon.png" type="image/png" />
  <link rel="stylesheet" href="css.css">
  <link rel="stylesheet" href="mobile_css.css">
  <script src="master_js.js"></script>
  <script src="js.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 
 <!--------font family--------->
<link href='https://fonts.googleapis.com/css?family=Aclonica' rel='stylesheet'>



<style>

html {
    overflow-x: hidden;
    background: repeating-linear-gradient(45deg, black, transparent 100px), repeating-linear-gradient(-45deg, black, transparent 100px);
}
.application_pro  h3:nth-child(2), h3:nth-child(1) {
    background:var(--color2);color:white
}
.application_pro h3:nth-child(2):after {
    content: '';
    position: absolute;
    border: 20px solid transparent;
    border-left: 20px solid var(--color2) !important;
    z-index: 17 !important;
    right: -37px;
}

.header {display:none}
.application_form {
    margin: -59px 0px 0px 0px;
    width: auto;
    border: 0px solid;
    padding: 20px 0px;
}


   .form_preview {
     color: #3c3b3b;
    margin: 68px auto;
    padding: 23px;
    border-radius: 7px;
    font-family: sans-serif;
    box-shadow: 1px 1px 20px 0px #000000a8;
    width: 537px;
    position: relative;
    text-transform: capitalize;
    background: white;
}

.form_preview p {
    font-size: 14px;
    margin: 5px;
    width: 200px;
    padding: 10px;
    border: 0px solid;
}

.form_preview p img {width: 101px;
    border-radius: 52px;
    border: 2px solid #db3cd1;
    padding: 7px;}

.form_preview h3, p {
    font-size: 14px;
    margin: 5px;
    width: 200px;
    padding: 10px;
    color: black;
}

.containerPrv {font-weight: 100;
    display: grid;
    grid-template-columns: auto auto;
    font-family: Didact Gothic !important;
}

.offer_50_off {
	width: auto;
}

.offer_50_off img {width: 100%;}
.offer_50_off h3 {    width: -webkit-fill-available;
    font-size: 25px;
    text-align: center;
    color: #eb1c24;}
@media screen and (max-width:700px){
	
	.form_preview {
    color: #3c3b3b;
    margin: 68px auto;
    padding: 7px;
    width: auto;
    border-radius: 7px;
    font-family: sans-serif;
    box-shadow: 0px 0px 0px 0px green;
    width: -webkit-fill-available;
    position: relative;
    text-transform: capitalize;
}
}


</style>


<div class="clippath1"></div>

<div class="application_form">

	
<div class="form_preview">
	
	<?php
		$query=mysqli_query($connect,'SELECT * FROM wallet WHERE f_user_email="'.$_SESSION['f_user_email'].'" ');
		$query2=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE f_user_email="'.$_SESSION['f_user_email'].'" ');
			$row=mysqli_fetch_array($query);
			$row2=mysqli_fetch_array($query2);
			

		
					?>
			<!------------form for paying and activating the account---------------->
			<div class="verify_details">
					<h1><img src="favicon.png">Confirm Your Details!</h1>
					
					<div>Adding Amount in Wallet for (<?php echo $row2['f_user_email']; ?>)  </div>
					<div><p><b>Order Id:</b></p><p><?php echo $_SESSION['reference_number']=date('dhsi'); ?></p></div>
					<div><p><b>Email:</b></p><p><?php echo $_SESSION['f_user_email']=$row2['f_user_email'];?></p></div>
					<div><p><b>Contact:</b> </p><p><?php echo $_SESSION['f_user_contact']=$row2['f_user_contact'];?></p></div>
					<div><p><b>Amount:</b> </p><p><?php echo '<i class="fa fa-rupee"></i> '.$_SESSION['amount']=$_POST['deposit'];?></p></div>
					
					
					</div>
			<!------------form for paying and activating the account---------------->		
			
					
					
					
		
	

<?php

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders

$payment_currency='INR';
$_SESSION['payment_currency']=$payment_currency;
//
$orderData = [
    'receipt'         => $row2['f_user_contact'].date('dhsi'),
    'amount'          => $_POST['deposit']* 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
	$_SESSION['payment_amount_c']=$displayAmount ;
}

$checkout = 'automatic';

if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
{
    $checkout = $_GET['checkout'];
}



$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "Wallet",
    "description"       => "Adding Wallet Amount",
    "image"             => "favicon.png",
    "prefill"           => [
    "name"              => $_SESSION['f_user_name'],
    "email"             => $_SESSION['f_user_email'],
    "contact"           => $_SESSION['f_user_contact'],
    ],
    "notes"             => [
    "address"           => "NA",
    "merchant_order_id" => $_SESSION['reference_number'],
    ],
    "theme"             => [
    "color"             => "#ff5476"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

require("checkout/{$checkout}.php");


?>


	</div>
	

<style>

input[type=submit]{
	position: relative;
    border: 0px;
    padding: 11px 45px;
    background: #78dc53;
    color: white;
    font-weight: 400;
    font-size: 18px;
    width: -webkit-fill-available;
    left: 50%;
    transform: translate(-50%, 10px);
    border-radius: 5px;
}

#backdrop {
    position: absolute;
    top: 0;
    background: white !important;
    left: 0;
    width: 100%;
    height: 100%;
}

.verify_details div{ display: flex;
    background: #78dc5300;
    padding: 12px;
    text-align: center;
    width: fit-content;
    font-size: 12px;
    margin: 0 auto;
    color: #3a3131;}

.verify_details img {
	width: 91px;
    height: 91px;
}

.verify_details img, h1 {display: inline-block;
    vertical-align: middle;
    margin: 0 auto;
    text-align: center;}
.verify_details h1{      text-align: center;
    color: #464a45;
    font-size: 25px;
    font-weight: 400;
}
.verify_details p{    color: gray;
    font-size: 14px;
    margin: 5px;
    width: 200px;
    text-align: justify;
    padding: 10px;
    border: 0px solid;
    text-transform: initial;}
</style>

