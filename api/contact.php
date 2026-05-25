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

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$company = trim($_POST['company'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($message === '') $errors[] = 'Message is required.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, company, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $company ?: null, $message]);

    // Send email to admin
    $adminEmail = getSetting($pdo, 'admin_email') ?: 'info@nexussynced.com';
    $emailSubject = "New Contact Message from " . $name;
    $emailBody = "Name: " . $name . "\n"
               . "Email: " . $email . "\n"
               . "Company: " . ($company ?: 'N/A') . "\n"
               . "Message:\n" . $message . "\n\n"
               . "Submitted: " . date('Y-m-d H:i:s') . "\n";
    sendEmail($adminEmail, $emailSubject, $emailBody);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error. Please try again.']);
}
