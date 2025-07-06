<?php
require_once __DIR__ . '/functions.php';
$success = '';
$error = '';

// ---------- Handle task actions ----------
if (isset($_POST['task-name'])) {
    if (addTask(trim($_POST['task-name']))) {
        $success = 'Task added successfully.';
    } else {
        $error = 'Task already exists.';
    }
}

if (isset($_POST['toggle_complete'])) {
    markTaskAsCompleted($_POST['toggle_complete'], (bool)$_POST['status']);
}

if (isset($_POST['delete_task'])) {
    deleteTask($_POST['delete_task']);
}

// ---------- Handle subscription forms ----------
if (isset($_POST['email']) && !isset($_POST['verification_code']) && !isset($_POST['unsubscribe'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        if (subscribeEmail($email)) {
            $success = 'Verification mail sent.';
        } else {
            $error = 'Could not send verification email.';
        }
    } else {
        $error = 'Invalid email address.';
    }
}

if (isset($_POST['verification_code'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $code = $_POST['verification_code'];
    if ($email && $code) {
        if (verifySubscription($email, $code)) {
            $success = 'Email verified successfully!';
        } else {
            $error = 'Incorrect code.';
        }
    }
}

if (isset($_POST['unsubscribe'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        if (unsubscribeEmail($email)) {
            $success = 'You have been unsubscribed.';
        } else {
            $error = 'Unable to unsubscribe.';
        }
    } else {
        $error = 'Invalid email address.';
    }
}

$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Planner</title>
    <style>
        body {font-family: sans-serif; background: #f2f4f8; padding: 20px}
        .container {max-width: 700px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.1)}
        .task-list {list-style: none; padding: 0}
        .task-item {display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #eee}
        .task-item.completed span {text-decoration: line-through; color: #999}
        .delete-task {background: #e63946; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer}
    </style>
</head>
<body>
<div class="container">
    <h1>Task Planner</h1>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <h2>Add Task</h2>
    <form method="post">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button id="add-task">Add Task</button>
    </form>

    <h2>Tasks</h2>
    <ul class="task-list">
        <?php foreach ($tasks as $t): ?>
            <li class="task-item<?php echo $t['completed'] ? ' completed' : ''; ?>">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="toggle_complete" value="<?php echo $t['id']; ?>">
                    <input type="hidden" name="status" value="<?php echo $t['completed'] ? 0 : 1; ?>">
                    <input type="checkbox" class="task-status"<?php echo $t['completed'] ? ' checked' : ''; ?> onChange="this.form.submit()">
                </form>
                <span><?php echo htmlspecialchars($t['name']); ?></span>
                <form method="post" style="margin-left:auto;">
                    <button name="delete_task" value="<?php echo $t['id']; ?>" class="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <hr>

    <h2>Subscribe for Hourly Reminders</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Your email" required>
        <button id="submit-email">Submit</button>
    </form>

    <h3>Enter Verification Code</h3>
    <form method="post">
        <input type="email" name="email" placeholder="Your email" required>
        <input type="text" name="verification_code" maxlength="6" placeholder="000000" required>
        <button id="submit-verification">Verify</button>
    </form>

    <h3>Unsubscribe</h3>
    <form method="post">
        <input type="email" name="email" placeholder="Your email" required>
        <button name="unsubscribe" value="1">Unsubscribe</button>
    </form>
</div>
</body>
</html>
