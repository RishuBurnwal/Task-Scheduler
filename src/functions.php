<?php

function loadJsonFile($filename) {
    if (!file_exists($filename)) return [];
    $data = file_get_contents($filename);
    return json_decode($data, true) ?? [];
}

function saveJsonFile($filename, $data) {
    return file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX) !== false;
}

function addTask($task_name) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = loadJsonFile($file);

    foreach ($tasks as $task) {
        if (strcasecmp($task['name'], $task_name) === 0) return false;
    }

    $tasks[] = [
        'id' => uniqid(),
        'name' => $task_name,
        'completed' => false
    ];

    return saveJsonFile($file, $tasks);
}

function getAllTasks() {
    return loadJsonFile(__DIR__ . '/tasks.txt');
}

function markTaskAsCompleted($task_id, $is_completed) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = loadJsonFile($file);

    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = (bool)$is_completed;
            break;
        }
    }

    return saveJsonFile($file, $tasks);
}

function deleteTask($task_id) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = loadJsonFile($file);

    $tasks = array_filter($tasks, fn($t) => $t['id'] !== $task_id);
    return saveJsonFile($file, array_values($tasks));
}

function generateVerificationCode() {
    return sprintf('%06d', mt_rand(100000, 999999));
}

function subscribeEmail($email) {
    $file = __DIR__ . '/pending_subscriptions.txt';
    $pending = loadJsonFile($file);
    $code = generateVerificationCode();
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];
    saveJsonFile($file, $pending);

    $link = 'http://' . $_SERVER['HTTP_HOST'] . '/verify.php?email=' . urlencode($email) . '&code=' . $code;
    $body = '<p>Click the link below to verify your subscription to Task Planner:</p><p><a id="verification-link" href="' . $link . '">Verify Subscription</a></p>';

    $headers = "From: no-reply@example.com\r\nContent-type: text/html\r\n";
    return mail($email, 'Verify subscription to Task Planner', $body, $headers);
}

function verifySubscription($email, $code) {
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $subFile = __DIR__ . '/subscribers.txt';

    $pending = loadJsonFile($pendingFile);
    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) return false;

    unset($pending[$email]);
    saveJsonFile($pendingFile, $pending);

    $subs = loadJsonFile($subFile);
    if (!in_array($email, $subs)) $subs[] = $email;
    return saveJsonFile($subFile, $subs);
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/subscribers.txt';
    $subs = loadJsonFile($file);
    $subs = array_values(array_filter($subs, fn($e) => $e !== $email));
    return saveJsonFile($file, $subs);
}

function sendTaskReminders() {
    $subs = loadJsonFile(__DIR__ . '/subscribers.txt');
    $tasks = loadJsonFile(__DIR__ . '/tasks.txt');
    $pendingTasks = array_filter($tasks, fn($t) => !$t['completed']);

    foreach ($subs as $email) {
        sendTaskEmail($email, $pendingTasks);
    }
}

function sendTaskEmail($email, $pending_tasks) {
    $body = "<h2>Pending Tasks Reminder</h2><p>Here are the current pending tasks:</p><ul>";
    foreach ($pending_tasks as $task) {
        $body .= '<li>' . htmlspecialchars($task['name']) . '</li>';
    }
    $body .= '</ul>';

    $link = 'http://' . $_SERVER['HTTP_HOST'] . '/unsubscribe.php?email=' . urlencode($email);
    $body .= '<p><a id="unsubscribe-link" href="' . $link . '">Unsubscribe from notifications</a></p>';

    $headers = "From: no-reply@example.com\r\nContent-type: text/html\r\n";
    mail($email, 'Task Planner - Pending Tasks Reminder', $body, $headers);
}
