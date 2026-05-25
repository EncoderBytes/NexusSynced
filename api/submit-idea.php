<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

// CSRF check
$token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid session. Please refresh the page.']);
    exit;
}

// Validate required fields
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$idea_description = trim($_POST['idea_description'] ?? '');
$submission_type = ($_POST['submission_type'] ?? 'mvp') === 'worst_app' ? 'worst_app' : 'mvp';
$package = trim($_POST['package'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if ($contact === '') $errors[] = 'Contact is required.';
if ($idea_description === '') $errors[] = 'Idea description is required.';

// Extract idea title from description (first line or first 100 chars)
$idea_title = $idea_description;
$lines = explode("\n", $idea_description);
if (!empty($lines[0]) && strlen($lines[0]) <= 200) {
    $idea_title = trim($lines[0]);
} else {
    $idea_title = mb_strimwidth($idea_description, 0, 100, '...');
}
if (empty($idea_title)) $idea_title = 'Untitled Idea';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit;
}

// Only allow valid package values for mvp type
if ($submission_type === 'mvp' && !in_array($package, ['validate', 'launch', 'raise', ''])) {
    $package = '';
}

try {
    // Get next queue number
    $queueNumber = getNextQueueNumber($pdo);

    $stmt = $pdo->prepare("INSERT INTO submissions (submission_type, name, contact, idea_title, idea_description, package, queue_number, status, is_public) VALUES (?, ?, ?, ?, ?, ?, ?, 'submitted', 0)");
    $stmt->execute([$submission_type, $name, $contact, $idea_title, $idea_description, $package, $queueNumber]);

    $submissionId = $pdo->lastInsertId();

    // Send email notification to admin
    $adminEmail = getSetting($pdo, 'admin_email') ?: 'info@nexussynced.com';
    $emailSubject = "New " . ($submission_type === 'worst_app' ? 'Worst App' : 'MVP') . " Submission — Queue #" . $queueNumber;
    $emailBody = "Name: " . $name . "\n"
               . "Contact: " . $contact . "\n"
               . "Type: " . ($submission_type === 'worst_app' ? 'Worst App' : 'MVP') . "\n"
               . "Package: " . ($package ?: 'Not selected') . "\n"
               . "Idea: " . $idea_description . "\n"
               . "Submitted: " . date('Y-m-d H:i:s') . "\n";
    sendEmail($adminEmail, $emailSubject, $emailBody);

    echo json_encode([
        'success' => true,
        'queue_number' => $queueNumber,
        'id' => $submissionId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error. Please try again.']);
}
