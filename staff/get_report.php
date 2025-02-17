<?php
session_start();
include '../database.php';

// Restrict access to staff only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if report_id is provided
if (!isset($_GET['report_id'])) {
    echo json_encode(['error' => 'Report ID is missing']);
    exit();
}

$reportId = $_GET['report_id'];

// Fetch report data from the database
$stmt = $pdo->prepare("SELECT id, report_type, findings FROM reports WHERE id = ?");
$stmt->execute([$reportId]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

// If no report found, return an error
if ($report) {
    echo json_encode($report);
} else {
    echo json_encode(['error' => 'Report not found']);
}
?>