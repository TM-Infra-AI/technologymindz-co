	<?php

require('config.php');


require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{
  	mysqli_query($connect,'UPDATE digi_card SET d_payment_status="Success",d_card_status="Active",d_payment_amount="'.$_SESSION['amount'].'",d_order_id="'.$_SESSION['reference_number'].'",d_payment_date="'.$date.'" WHERE id="'.$_SESSION['id'].'"');
	echo '<meta http-equiv="refresh" content="0;URL=../index.php">';
}
else
{
	
	echo 'Payment Failed';
  mysqli_query($connect,'UPDATE users SET d_payment_status="'.$error.'",d_card_status="Pending",d_payment_amount="'.$_SESSION['amount'].'",d_order_id="'.$_SESSION['reference_number'].'" WHERE id="'.$_SESSION['id'].'"');
	echo '<meta http-equiv="refresh" content="1;URL=pay.php">';
}




?>


<style>

.payment_confirmation {    border: 1px solid white;
    width: fit-content;
    padding: 22px;
    font-size: 20px;
    background: #c9f596;
    color: #107513;
    font-family: sans-serif;
    margin: 30px auto;
}


input[type=submit]{
	
	    position: relative;
    border: 0px;
    padding: 10px;
    background: #8BC34A;
    color: white;
    left: 50%;
    transform: translate(-50%, 100px);


}
</style>
