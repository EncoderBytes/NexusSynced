<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $stmt = $pdo->query("SELECT id, queue_number, submission_type, idea_title, idea_description, package, status, created_at FROM submissions WHERE is_public = 1 ORDER BY queue_number ASC");
    $submissions = $stmt->fetchAll();

    $mvpsCount = getSetting($pdo, 'mvps_shipped_count') ?: '0';

    echo json_encode([
        'submissions' => $submissions,
        'mvps_count' => $mvpsCount
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['submissions' => [], 'mvps_count' => '0']);
}
