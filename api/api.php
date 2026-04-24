<?php
	
// Set timezone
date_default_timezone_set("Asia/Kolkata");

// Start session
session_start();

include_once('../db_config.php');
// Database connection
// $connect = mysqli_connect("localhost", "jbugduzc_myvcard_tm", "cZ*t]D&=_J3R", "jbugduzc_myvcard_tm");

// Check connection
// if (!$connect) {
//     // Show error details only in localhost for debugging
//     if ($_SERVER['HTTP_HOST'] == "localhost") {
//         die("Database Connection Failed: " . mysqli_connect_error());
//     } else {
//         die("Database connection issue. Please contact support.");
//     }
// }

// Set UTF-8 encoding
mysqli_set_charset($connect, "utf8");

// Store current timestamp
$date = date('Y-m-d H:i:s');

if(isset($_GET['token']) && isset($_GET['ref_id'])){
			
			// check if id and password matches 
			$query=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE  id="'.$_GET['ref_id'].'"');
			if(mysqli_num_rows($query)==0){
					// if id does not exist 
					$result=array(
					'error'=> 'Error, token or id2'
					
					);
			}else {
				
				$row=mysqli_fetch_array($query);
				$password=md5($row['f_user_password']);
					if($_GET['token']==$password){
						
						//success status 
						
						$query2=mysqli_query($connect,'SELECT * FROM franchisee_login WHERE id="'.$_GET['ref_id'].'"');
						$query3=mysqli_query($connect,'SELECT * FROM digi_card WHERE f_user_email="'.$row['f_user_email'].'"');
					
						
						 while($row3=mysqli_fetch_array($query3)){
						     
						     
							     $result=array('card_id',$row3['id']);
						 }
						 
			
			
			// authentication success ends
					}else {
						$result=array(
							'status'=> '401'
							
							);
					}
				
				
			}
			
			
		}else {
			
			$result=array(
			'error'=> 'Error, token or id'
			
			);
			
}
			
			


 echo json_encode($result);

header('Content-Type: application/json');
?>

