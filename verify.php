<?php
require_once 'functions.php';

$message = '';
$error = '';
$verified = false;

// Handle verification
if (isset($_GET['email']) && isset($_GET['code'])) {
	$email = $_GET['email'];
	$code = $_GET['code'];
	
	if (verifySubscription($email, $code)) {
		$message = 'Your subscription has been verified successfully! You will now receive task reminders.';
		$verified = true;
	} else {
		$error = 'Verification failed. The verification code may be invalid or expired.';
	}
} else {
	$error = 'Invalid verification link. Please make sure you clicked the correct link from your email.';
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Subscription Verification - Task Planner</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			max-width: 600px;
			margin: 0 auto;
			padding: 20px;
			background-color: #f5f5f5;
		}
		.container {
			background: white;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			text-align: center;
		}
		h2 {
			color: #333;
			margin-bottom: 20px;
		}
		.message {
			padding: 20px;
			margin: 20px 0;
			border-radius: 5px;
			font-size: 16px;
		}
		.success {
			background-color: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}
		.error {
			background-color: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}
		.btn {
			display: inline-block;
			background-color: #007bff;
			color: white;
			padding: 12px 24px;
			text-decoration: none;
			border-radius: 5px;
			margin-top: 20px;
		}
		.btn:hover {
			background-color: #0056b3;
		}
		.checkmark {
			font-size: 48px;
			color: #28a745;
			margin-bottom: 20px;
		}
		.error-icon {
			font-size: 48px;
			color: #dc3545;
			margin-bottom: 20px;
		}
	</style>
</head>
<body>
	<div class="container">
		<!-- Do not modify the ID of the heading -->
		<h2 id="verification-heading">Subscription Verification</h2>
		
		<?php if ($verified): ?>
			<div class="checkmark">✓</div>
			<div class="message success"><?php echo htmlspecialchars($message); ?></div>
			<p>You can now close this window and continue using the Task Planner.</p>
		<?php else: ?>
			<div class="error-icon">✗</div>
			<div class="message error"><?php echo htmlspecialchars($error); ?></div>
			<p>Please check your email for the correct verification link, or try subscribing again.</p>
		<?php endif; ?>
		
		<a href="index.php" class="btn">Return to Task Planner</a>
	</div>
</body>
</html>