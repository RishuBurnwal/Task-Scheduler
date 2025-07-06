<?php
$to      = 'test@example.com';
$subject = 'Test Email';
$message = '<h1>This is a test email</h1><p>Sent from PHP</p>';
$headers = "From: no-reply@example.com\r\n";
$headers .= "Content-type: text/html\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail sent!";
} else {
    echo "Mail failed!";
}
?>
