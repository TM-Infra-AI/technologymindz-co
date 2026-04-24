<?php
// return [
//     'host' => 'smtp-relay.brevo.com',
//     'username' => '8a4d66001@smtp-brevo.com',
//     'password' => 'xsmtpsib-d6d48eced6630907ad047843369fe5d9ea32866245a4db7e1a7e1a272a930371-N0t2Jxcjq6CaURmP',
//     'port' => 587,
//     'from_email' => 'info@technologymindz.com',
//     'from_name' => 'Technology Mindz'
// ];


return [
    'host' => 'wptest.technologymindz.com',
    'username' => 'info@wptest.technologymindz.com',
    'password' => 'rlP#nah@j09s',
    'port' => 465,
    'from_email' => 'info@wptest.technologymindz.com',
    'from_name' => 'Technology Mindz'
];
// SMTP Config
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php'; // or manually include the files if not using Composer

// // SMTP Config
// $mailConfig = [
//     'host' => 'technologymindz.co',
//     'username' => 'info@technologymindz.co',
//     'password' => '3$1wDPFq8e;S',
//     'port' => 465,
//     'from_email' => 'info@technologymindz.co',
//     'from_name' => 'Technology Mindz'
// ];

// // Get form inputs
// $name = $_POST['user_name'] ?? 'N/A';
// $mobile = $_POST['user_mobile'] ?? 'N/A';
// $email = $_POST['user_email'] ?? '';
// $message = $_POST['message'] ?? '';

// $to = "sourabh_sha";
// $subject = "Mail From website";
// $body = "Name: $name\r\nMobile: $mobile\r\nEmail: $email\r\nMessage: $message";

// $mail = new PHPMailer(true);

// try {
//     // Server settings
//     $mail->isSMTP();
//     $mail->Host       = $mailConfig['host'];
//     $mail->SMTPAuth   = true;
//     $mail->Username   = $mailConfig['username'];
//     $mail->Password   = $mailConfig['password'];
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // or 'ssl'
//     $mail->Port       = $mailConfig['port'];

//     // Recipients
//     $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
//     $mail->addAddress($to);
//     // $mail->addCC('contact@mydigitalcard.isavgo.com');

//     // Content
//     $mail->Subject = $subject;
//     $mail->Body    = $body;

//     $mail->send();
//     echo "Message has been sent";
// } catch (Exception $e) {
//     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }
