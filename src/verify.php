<?php
require_once __DIR__ . '/functions.php';

$success = '';
$error = '';

if (isset($_GET['email'], $_GET['code'])) {
    $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
    $code = $_GET['code'];
    if ($email && $code) {
        if (verifySubscription($email, $code)) {
            $success = "Email verified successfully!";
        } else {
            $error = "Invalid verification code.";
        }
    } else {
        $error = "Invalid parameters.";
    }
} else {
    $error = "Missing verification data.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body { font-family: sans-serif; background: #f2f4f8; padding: 40px; text-align: center }
        .box { background: #fff; display: inline-block; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 400px }
        .success { color: green }
        .error { color: red }
    </style>
</head>
<body>
<div class="box">
    <h2>Email Verification</h2>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <p><a href="index.php">Back to Task Planner</a></p>
</div>
</body>
</html>
