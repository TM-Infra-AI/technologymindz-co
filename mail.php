<?php

// SMTP Config
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // or manually include the files if not using Composer
header('Content-Type: application/json');

// SMTP Config
$mailConfig = [
    'host' => 'wptest.technologymindz.com',
    'username' => 'info@wptest.technologymindz.com',
    'password' => 'rlP#nah@j09s',
    'port' => 465,
    'from_email' => 'info@wptest.technologymindz.com',
    'from_name' => 'Technology Mindz'
];


// Get form inputs
$name = $_POST['user_name'] ?? '';
$mobile = $_POST['user_contact'] ?? '';
$email = $_POST['user_email'] ?? '';
$message = $_POST['user_msg'] ?? '';

$to = "info@technologymindz.com";
$subject = "New lead on business card website";
// Prepare the email body
$body = <<<EOD
Hi Team,

A user has filled the contact us form, please find the details below:

Name: $name
Mobile: $mobile
Email: $email
Message: 
$message

Thanks,
Sales TM
EOD;
$mail = new PHPMailer(true);



try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $mailConfig['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailConfig['username'];
    $mail->Password   = $mailConfig['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // or 'ssl'
    $mail->Port       = $mailConfig['port'];

    // Recipients
    $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
    $mail->addAddress($to);
    // $mail->addCC('contact@mydigitalcard.isavgo.com');

    // Content
    $mail->Subject = $subject;
    $mail->Body    = $body;

    if ($mail->send()) {
        echo json_encode([
            "status" => "success",
            "message" => "Thanks for submitting your details! We will reach out to you on your email id shortly."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Sorry, something went wrong. Please try again later."
        ]);
    }
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}