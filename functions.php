<?php

/**
 * Adds a new task to the task list
 * 
 * @param string $task_name The name of the task to add.
 * @return bool True on success, false on failure.
 */
function addTask( string $task_name ): bool {
	$file = __DIR__ . '/tasks.txt';
	
	// Get existing tasks
	$tasks = getAllTasks();
	
	// Check for duplicate task names
	foreach ($tasks as $task) {
		if (strtolower($task['name']) === strtolower($task_name)) {
			return false; // Duplicate task
		}
	}
	
	// Generate unique ID
	$task_id = uniqid();
	
	// Add new task
	$tasks[] = [
		'id' => $task_id,
		'name' => $task_name,
		'completed' => false
	];
	
	// Save to file
	return file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Retrieves all tasks from the tasks.txt file
 * 
 * @return array Array of tasks. -- Format [ id, name, completed ]
 */
function getAllTasks(): array {
	$file = __DIR__ . '/tasks.txt';
	
	if (!file_exists($file)) {
		return [];
	}
	
	$content = file_get_contents($file);
	if (empty($content)) {
		return [];
	}
	
	$tasks = json_decode($content, true);
	return is_array($tasks) ? $tasks : [];
}

/**
 * Marks a task as completed or uncompleted
 * 
 * @param string  $task_id The ID of the task to mark.
 * @param bool $is_completed True to mark as completed, false to mark as uncompleted.
 * @return bool True on success, false on failure
 */
function markTaskAsCompleted( string $task_id, bool $is_completed ): bool {
	$file = __DIR__ . '/tasks.txt';
	$tasks = getAllTasks();
	
	foreach ($tasks as &$task) {
		if ($task['id'] === $task_id) {
			$task['completed'] = $is_completed;
			return file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT)) !== false;
		}
	}
	
	return false;
}

/**
 * Deletes a task from the task list
 * 
 * @param string $task_id The ID of the task to delete.
 * @return bool True on success, false on failure.
 */
function deleteTask( string $task_id ): bool {
	$file = __DIR__ . '/tasks.txt';
	$tasks = getAllTasks();
	
	$updated_tasks = array_filter($tasks, function($task) use ($task_id) {
		return $task['id'] !== $task_id;
	});
	
	if (count($updated_tasks) === count($tasks)) {
		return false; // Task not found
	}
	
	// Re-index array
	$updated_tasks = array_values($updated_tasks);
	
	return file_put_contents($file, json_encode($updated_tasks, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Generates a 6-digit verification code
 * 
 * @return string The generated verification code.
 */
function generateVerificationCode(): string {
	return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Subscribe an email address to task notifications.
 *
 * Generates a verification code, stores the pending subscription,
 * and sends a verification email to the subscriber.
 *
 * @param string $email The email address to subscribe.
 * @return bool True if verification email sent successfully, false otherwise.
 */
function subscribeEmail( string $email ): bool {
	$file = __DIR__ . '/pending_subscriptions.txt';
	
	// Check if already subscribed
	$subscribers_file = __DIR__ . '/subscribers.txt';
	$subscribers = [];
	if (file_exists($subscribers_file)) {
		$content = file_get_contents($subscribers_file);
		if (!empty($content)) {
			$subscribers = json_decode($content, true) ?: [];
		}
	}
	
	if (in_array($email, $subscribers)) {
		return false; // Already subscribed
	}
	
	// Get existing pending subscriptions
	$pending = [];
	if (file_exists($file)) {
		$content = file_get_contents($file);
		if (!empty($content)) {
			$pending = json_decode($content, true) ?: [];
		}
	}
	
	// Generate verification code
	$code = generateVerificationCode();
	
	// Store pending subscription
	$pending[$email] = [
		'code' => $code,
		'timestamp' => time()
	];
	
	// Save pending subscriptions
	if (file_put_contents($file, json_encode($pending, JSON_PRETTY_PRINT)) === false) {
		return false;
	}
	
	// Send verification email
	$verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/verify.php?email=" . urlencode($email) . "&code=" . $code;
	
	$subject = 'Verify subscription to Task Planner';
	$body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Subscription</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 10px; border: 1px solid #dee2e6;">
        <h2 style="color: #007bff; text-align: center; margin-bottom: 30px;">Task Planner - Email Verification</h2>
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a id="verification-link" href="' . $verification_link . '" style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Verify Subscription</a>
        </p>
        <p style="color: #666; font-size: 14px; margin-top: 30px;">If you did not request this verification, please ignore this email.</p>
    </div>
</body>
</html>';
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
	$headers .= 'From: no-reply@example.com' . "\r\n";
	$headers .= 'Reply-To: no-reply@example.com' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	
	return mail($email, $subject, $body, $headers);
}

/**
 * Verifies an email subscription
 * 
 * @param string $email The email address to verify.
 * @param string $code The verification code.
 * @return bool True on success, false on failure.
 */
function verifySubscription( string $email, string $code ): bool {
	$pending_file = __DIR__ . '/pending_subscriptions.txt';
	$subscribers_file = __DIR__ . '/subscribers.txt';
	
	// Get pending subscriptions
	if (!file_exists($pending_file)) {
		return false;
	}
	
	$content = file_get_contents($pending_file);
	if (empty($content)) {
		return false;
	}
	
	$pending = json_decode($content, true);
	if (!is_array($pending) || !isset($pending[$email])) {
		return false;
	}
	
	// Verify code
	if ($pending[$email]['code'] !== $code) {
		return false;
	}
	
	// Get existing subscribers
	$subscribers = [];
	if (file_exists($subscribers_file)) {
		$content = file_get_contents($subscribers_file);
		if (!empty($content)) {
			$subscribers = json_decode($content, true) ?: [];
		}
	}
	
	// Add to subscribers if not already there
	if (!in_array($email, $subscribers)) {
		$subscribers[] = $email;
	}
	
	// Remove from pending
	unset($pending[$email]);
	
	// Save both files
	$success1 = file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT)) !== false;
	$success2 = file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT)) !== false;
	
	return $success1 && $success2;
}

/**
 * Unsubscribes an email from the subscribers list
 * 
 * @param string $email The email address to unsubscribe.
 * @return bool True on success, false on failure.
 */
function unsubscribeEmail( string $email ): bool {
	$subscribers_file = __DIR__ . '/subscribers.txt';
	
	if (!file_exists($subscribers_file)) {
		return false;
	}
	
	$content = file_get_contents($subscribers_file);
	if (empty($content)) {
		return false;
	}
	
	$subscribers = json_decode($content, true);
	if (!is_array($subscribers)) {
		return false;
	}
	
	$key = array_search($email, $subscribers);
	if ($key === false) {
		return false; // Email not found
	}
	
	unset($subscribers[$key]);
	$subscribers = array_values($subscribers); // Re-index array
	
	return file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Sends task reminders to all subscribers
 * Internally calls  sendTaskEmail() for each subscriber
 */
function sendTaskReminders(): void {
	$subscribers_file = __DIR__ . '/subscribers.txt';
	
	if (!file_exists($subscribers_file)) {
		return;
	}
	
	$content = file_get_contents($subscribers_file);
	if (empty($content)) {
		return;
	}
	
	$subscribers = json_decode($content, true);
	if (!is_array($subscribers)) {
		return;
	}
	
	// Get all pending tasks
	$all_tasks = getAllTasks();
	$pending_tasks = array_filter($all_tasks, function($task) {
		return !$task['completed'];
	});
	
	// If no pending tasks, don't send emails
	if (empty($pending_tasks)) {
		return;
	}
	
	// Send email to each subscriber
	foreach ($subscribers as $email) {
		sendTaskEmail($email, $pending_tasks);
	}
}

/**
 * Sends a task reminder email to a subscriber with pending tasks.
 *
 * @param string $email The email address of the subscriber.
 * @param array $pending_tasks Array of pending tasks to include in the email.
 * @return bool True if email was sent successfully, false otherwise.
 */
function sendTaskEmail( string $email, array $pending_tasks ): bool {
	$subject = 'Task Planner - Pending Tasks Reminder';
	
	// Build task list
	$task_list = '';
	foreach ($pending_tasks as $task) {
		$task_list .= '<li style="margin: 8px 0; padding: 8px; background-color: #f8f9fa; border-left: 3px solid #007bff;">' . htmlspecialchars($task['name']) . '</li>';
	}
	
	// Create unsubscribe link - FIXED VERSION for localhost development
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
	$script_path = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
	
	// Handle localhost development environment
	if (strpos($host, ':') === false && $host === 'localhost') {
		$host = 'localhost:8000';
	}
	
	// Ensure script path starts with /
	if ($script_path !== '/' && !str_starts_with($script_path, '/')) {
		$script_path = '/' . $script_path;
	}
	
	// Construct the full URL properly
	$unsubscribe_link = $protocol . "://" . $host . $script_path . "/unsubscribe.php?email=" . urlencode($email);
	
	$body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 10px; border: 1px solid #dee2e6;">
        <h2 style="color: #007bff; text-align: center; margin-bottom: 30px;">Pending Tasks Reminder</h2>
        <p>Here are the current pending tasks:</p>
        <ul style="list-style-type: none; padding: 0; margin: 20px 0;">
' . $task_list . '
        </ul>
        <hr style="border: none; height: 1px; background-color: #dee2e6; margin: 30px 0;">
        <p style="text-align: center; margin-top: 30px;">
            <a id="unsubscribe-link" href="' . $unsubscribe_link . '" style="color: #6c757d; font-size: 14px; text-decoration: none;">Unsubscribe from notifications</a>
        </p>
    </div>
</body>
</html>';
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
	$headers .= 'From: no-reply@example.com' . "\r\n";
	$headers .= 'Reply-To: no-reply@example.com' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	
	return mail($email, $subject, $body, $headers);
}