<?php
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

// Prepare email
$to = "info.errajuali@gmail.com"; // replace with your email
$headers = "From: " . htmlspecialchars($name) . " <" . htmlspecialchars($email) . ">\r\n";
$headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$body = "<h3>Masitor Technology - Contact Form Submission</h3>";
$body .= "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
$body .= "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
$body .= "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";
$body .= "<p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message)) . "</p>";

// Send email
if (mail($to, htmlspecialchars($subject), $body, $headers)) {
    echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
    exit;
} else {
    echo "<script>alert('Message could not be sent.'); window.history.back();</script>";
    exit;
}
?>
