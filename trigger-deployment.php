<?php

// URL to your deploy.php script
$deployUrl = 'https://technologymindz.co/deploy.php';

// Initialize cURL session
$ch = curl_init($deployUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1200); // Set max timeout to 10 minutes
$response = curl_exec($ch);
curl_close($ch);

// Output the response
echo $response;
?>
