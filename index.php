<?php
require_once 'functions.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['task-name']) && !empty($_POST['task-name'])) {
		// Add new task
		$task_name = trim($_POST['task-name']);
		if (addTask($task_name)) {
			$message = 'Task added successfully!';
		} else {
			$error = 'Failed to add task. Task may already exist.';
		}
	} elseif (isset($_POST['email']) && !empty($_POST['email'])) {
		// Subscribe email
		$email = trim($_POST['email']);
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			if (subscribeEmail($email)) {
				$message = 'Verification email sent! Please check your inbox.';
			} else {
				$error = 'Failed to subscribe. You may already be subscribed.';
			}
		} else {
			$error = 'Please enter a valid email address.';
		}
	} elseif (isset($_POST['action'])) {
		// Handle task actions
		if ($_POST['action'] === 'toggle' && isset($_POST['task_id'])) {
			$task_id = $_POST['task_id'];
			$is_completed = isset($_POST['completed']) && $_POST['completed'] === '1';
			if (markTaskAsCompleted($task_id, $is_completed)) {
				$message = 'Task status updated!';
			} else {
				$error = 'Failed to update task status.';
			}
		} elseif ($_POST['action'] === 'delete' && isset($_POST['task_id'])) {
			$task_id = $_POST['task_id'];
			if (deleteTask($task_id)) {
				$message = 'Task deleted successfully!';
			} else {
				$error = 'Failed to delete task.';
			}
		}
	}
}

// Get all tasks
$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Task Planner</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			max-width: 800px;
			margin: 0 auto;
			padding: 20px;
			background-color: #f5f5f5;
		}
		.container {
			background: white;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		h1 {
			color: #333;
			text-align: center;
			margin-bottom: 30px;
		}
		h2 {
			color: #444;
			border-bottom: 2px solid #007bff;
			padding-bottom: 10px;
		}
		.form-group {
			margin-bottom: 20px;
		}
		input[type="text"], input[type="email"] {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			font-size: 16px;
			box-sizing: border-box;
		}
		button {
			background-color: #007bff;
			color: white;
			padding: 12px 20px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 16px;
		}
		button:hover {
			background-color: #0056b3;
		}
		.delete-task {
			background-color: #dc3545;
			padding: 5px 10px;
			font-size: 14px;
		}
		.delete-task:hover {
			background-color: #c82333;
		}
		.tasks-list {
			list-style: none;
			padding: 0;
		}
		.task-item {
			background: #f8f9fa;
			margin: 10px 0;
			padding: 15px;
			border-radius: 5px;
			border: 1px solid #dee2e6;
			display: flex;
			align-items: center;
			gap: 15px;
		}
		.task-item.completed {
			background: #d4edda;
			text-decoration: line-through;
			opacity: 0.7;
		}
		.task-status {
			margin: 0;
		}
		.task-name {
			flex-grow: 1;
			font-size: 16px;
		}
		.message {
			padding: 15px;
			margin: 20px 0;
			border-radius: 5px;
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
		.section {
			margin-bottom: 40px;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>Task Planner</h1>
		
		<?php if ($message): ?>
			<div class="message success"><?php echo htmlspecialchars($message); ?></div>
		<?php endif; ?>
		
		<?php if ($error): ?>
			<div class="message error"><?php echo htmlspecialchars($error); ?></div>
		<?php endif; ?>

		<div class="section">
			<h2>Add New Task</h2>
			<!-- Add Task Form -->
			<form method="POST" action="">
				<div class="form-group">
					<input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
				</div>
				<button type="submit" id="add-task">Add Task</button>
			</form>
		</div>

		<div class="section">
			<h2>Tasks List</h2>
			<!-- Tasks List -->
			<ul id="tasks-list" class="tasks-list">
				<?php if (empty($tasks)): ?>
					<li style="text-align: center; color: #666; font-style: italic;">No tasks yet. Add your first task above!</li>
				<?php else: ?>
					<?php foreach ($tasks as $task): ?>
						<li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
							<form method="POST" action="" style="display: inline;">
								<input type="hidden" name="action" value="toggle">
								<input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
								<input type="hidden" name="completed" value="<?php echo $task['completed'] ? '0' : '1'; ?>">
								<input type="checkbox" class="task-status" <?php echo $task['completed'] ? 'checked' : ''; ?> onchange="this.form.submit();">
							</form>
							<span class="task-name"><?php echo htmlspecialchars($task['name']); ?></span>
							<form method="POST" action="" style="display: inline;">
								<input type="hidden" name="action" value="delete">
								<input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
								<button type="submit" class="delete-task" onclick="return confirm('Are you sure you want to delete this task?');">Delete</button>
							</form>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>

		<div class="section">
			<h2>Subscribe to Email Reminders</h2>
			<!-- Subscription Form -->
			<form method="POST" action="">
				<div class="form-group">
					<input type="email" name="email" placeholder="Enter your email address" required>
				</div>
				<button type="submit" id="submit-email">Subscribe</button>
			</form>
			<p style="color: #666; font-size: 14px; margin-top: 10px;">
				You'll receive hourly email reminders about your pending tasks.
			</p>
		</div>
	</div>
</body>

</html>