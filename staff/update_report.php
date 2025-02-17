<?php
session_start();
include '../database.php';

// Restrict access to staff only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if necessary data is sent
if (isset($_POST['report_id'], $_POST['report_type'], $_POST['findings'])) {
    $reportId = $_POST['report_id'];
    $reportType = $_POST['report_type'];
    $findings = $_POST['findings'];

    // Update the report in the database
    $stmt = $pdo->prepare("UPDATE reports SET report_type = ?, findings = ? WHERE id = ?");
    $stmt->execute([$reportType, $findings, $reportId]);

    // Check if the report was updated
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => 'Report updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update report']);
    }
} else {
    echo json_encode(['error' => 'Missing data']);
}
?>