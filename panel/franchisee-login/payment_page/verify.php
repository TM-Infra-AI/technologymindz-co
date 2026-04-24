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
	$query=mysqli_query($connect,'SELECT * FROM wallet WHERE f_user_email="'.$_SESSION['f_user_email'].'" ORDER BY id DESC');
	
	$row=mysqli_fetch_array($query);
	
	if(mysqli_num_rows($query)>>0){
		$balance=$_SESSION['amount']+$row['w_balance'];
	}else {$balance=$_SESSION['amount'];}
	
  	$insert=mysqli_query($connect,'INSERT INTO wallet (f_user_email,w_deposit,w_order_id,w_balance,uploaded_date) VALUES ("'.$_SESSION['f_user_email'].'","'.$_SESSION['amount'].'","'.$_SESSION['reference_number'].'","'.$balance.'","'.$date.'")');
	
	
	
	if($insert){echo 'Amount Added '.$_SESSION['amount'].' Rs.';
	echo '<a href="../wallet.php"><div class="btn1">Click Here</div></a>';
	header('Location:../wallet.php');
	}else {echo 'Contact Admin if amount deducted.';}
	
	
	// reterave data from 
	
	
	
}
else
{
	
	echo 'Payment Failed';
	
	
 
 
	echo '<meta http-equiv="refresh" content="2000;URL=../wallet.php">';
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
