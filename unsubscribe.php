<?php
require_once 'functions.php';

$message = '';
$error = '';
$unsubscribed = false;

// Handle unsubscribe
if (isset($_GET['email']) && !empty($_GET['email'])) {
	$email = trim($_GET['email']);
	
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		if (unsubscribeEmail($email)) {
			$message = 'You have been successfully unsubscribed from task reminders.';
			$unsubscribed = true;
		} else {
			$error = 'Unsubscribe failed. You may not be subscribed or there was an error processing your request.';
		}
	} else {
		$error = 'Invalid email address provided.';
	}
} else {
	$error = 'No email address provided for unsubscription.';
}

// Handle resubscribe form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !empty($_POST['email'])) {
	$resubscribe_email = trim($_POST['email']);
	if (filter_var($resubscribe_email, FILTER_VALIDATE_EMAIL)) {
		if (subscribeEmail($resubscribe_email)) {
			$message = 'Verification email sent! Please check your inbox to complete resubscription.';
			$error = ''; // Clear any previous errors
		} else {
			$error = 'Failed to resubscribe. You may already be subscribed.';
		}
	} else {
		$error = 'Please enter a valid email address for resubscription.';
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Unsubscribe - Task Planner</title>
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
		.resubscribe-form {
			margin-top: 30px;
			padding: 20px;
			background-color: #f8f9fa;
			border-radius: 5px;
		}
		.resubscribe-form input[type="email"] {
			width: 100%;
			max-width: 300px;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			font-size: 16px;
			margin-bottom: 15px;
			box-sizing: border-box;
		}
		.resubscribe-form button {
			background-color: #28a745;
			color: white;
			padding: 12px 24px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 16px;
		}
		.resubscribe-form button:hover {
			background-color: #1e7e34;
		}
	</style>
</head>
<body>
	<div class="container">
		<!-- Do not modify the ID of the heading -->
		<h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>
		
		<?php if ($message): ?>
			<?php if ($unsubscribed): ?>
				<div class="checkmark">✓</div>
			<?php endif; ?>
			<div class="message success"><?php echo htmlspecialchars($message); ?></div>
		<?php endif; ?>
		
		<?php if ($error): ?>
			<div class="error-icon">✗</div>
			<div class="message error"><?php echo htmlspecialchars($error); ?></div>
		<?php endif; ?>
		
		<?php if ($unsubscribed): ?>
			<p>You will no longer receive task reminder emails from us.</p>
			<p>If you change your mind, you can always resubscribe using the form below.</p>
			
			<div class="resubscribe-form">
				<h3>Want to resubscribe?</h3>
				<p>If you changed your mind, you can resubscribe here:</p>
				<form method="POST" action="">
					<input type="email" name="email" placeholder="Enter your email address" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
					<br>
					<button type="submit">Resubscribe</button>
				</form>
			</div>
		<?php elseif (!$message): ?>
			<p>If you have any issues with unsubscribing, please contact our support team.</p>
		<?php endif; ?>
		
		<a href="index.php" class="btn">Return to Task Planner</a>
	</div>
</body>
</html>