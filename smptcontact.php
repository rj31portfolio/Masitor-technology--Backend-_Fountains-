<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Basic POST check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Trim/escape input
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'Contact form';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$captchaInput = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
$captchaHidden = isset($_POST['captcha_hidden']) ? trim($_POST['captcha_hidden']) : '';

// Validate captcha (case-insensitive)
if ($captchaInput === '' || strcasecmp($captchaInput, $captchaHidden) !== 0) {
    echo "<script>alert('Captcha incorrect. Please try again.'); window.history.back();</script>";
    exit;
}

// Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please provide a valid email.'); window.history.back();</script>";
    exit;
}

// Send email using PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info.errajuali@gmail.com';     // replace
    $mail->Password = 'Bhashi@5812';       // use app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // From must be a valid sender; using the site mailbox. Set reply-to to user.
    $mail->setFrom('yourgmail@gmail.com', 'Website Contact');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('yourgmail@gmail.com', 'Site Owner');

    $mail->isHTML(true);
    $mail->Subject = htmlspecialchars($subject);
    $body = "<h3>Contact Form Submission</h3>";
    $body .= "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
    $body .= "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    $body .= "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";
    $body .= "<p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message)) . "</p>";

    $mail->Body = $body;
    $mail->send();

    echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
    exit;
} catch (Exception $e) {
    // log error $mail->ErrorInfo if you want
    echo "<script>alert('Message could not be sent. Mailer Error.'); window.history.back();</script>";
    exit;
}
