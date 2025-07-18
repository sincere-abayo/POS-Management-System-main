<?php
session_start();
include('../../config/config.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id']) && !isset($_SESSION['admin_id']) && !isset($_SESSION['staff_number'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get current user info
$user_id = null;
$user_type = null;

if (isset($_SESSION['customer_id'])) {
    $user_id = $_SESSION['customer_id'];
    $user_type = 'customer';
} elseif (isset($_SESSION['admin_id'])) {
    $user_id = $_SESSION['admin_id'];
    $user_type = 'admin';
} elseif (isset($_SESSION['staff_number'])) {
    $user_id = $_SESSION['staff_number'];
    $user_type = 'staff';
}

// Get other user info
$other_id = $_POST['other_id'] ?? '';
$other_type = $_POST['other_type'] ?? '';

if (empty($other_id) || empty($other_type)) {
    echo json_encode(['success' => false, 'message' => 'Missing recipient information']);
    exit;
}

// Mark messages as read (messages sent to current user by the other user)
$type = 'chat';
$stmt = $mysqli->prepare("
    UPDATE rpos_messages 
    SET status = 'read' 
    WHERE sender_id = ? AND receiver_id = ? AND type = ? AND status = 'sent'
");

$stmt->bind_param('sss', $other_id, $user_id, $type);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'updated_count' => $stmt->affected_rows]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark messages as read']);
}
?>