<?php
// Simple PHPMailer test script
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Change if using another SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'your_gmail@gmail.com'; // Replace with your email
    $mail->Password = 'your_app_password'; // Replace with your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('your_gmail@gmail.com', 'PHPMailer Test');
    $mail->addAddress('recipient@example.com', 'Test Recipient'); // Change to your test recipient

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test Email';
    $mail->Body = '<h1>This is a test email from PHPMailer</h1><p>If you received this, SMTP is working.</p>';

    $mail->send();
    echo 'Test email sent successfully!';
} catch (Exception $e) {
    echo 'Test email could not be sent. Mailer Error: ', $mail->ErrorInfo;
}