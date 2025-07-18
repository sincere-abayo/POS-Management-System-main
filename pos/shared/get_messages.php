<?php
session_start();
// Dynamically include the correct config.php based on session
if (isset($_SESSION['admin_id'])) {
    include(__DIR__ . '/../admin/config/config.php');
} elseif (isset($_SESSION['staff_number'])) {
    include(__DIR__ . '/../cashier/config/config.php');
} else {
    include(__DIR__ . '/../customer/config/config.php');
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id']) && !isset($_SESSION['admin_id']) && !isset($_SESSION['staff_number'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
$other_id = $_GET['other_id'] ?? '';
$other_type = $_GET['other_type'] ?? '';

if (empty($other_id) || empty($other_type)) {
    echo json_encode(['success' => false, 'message' => 'Missing recipient information']);
    exit;
}

// Fetch messages between these two users
$type = 'chat';
$stmt = $mysqli->prepare("
    SELECT 
        message_id,
        sender_id,
        receiver_id,
        type,
        content,
        status,
        created_at
    FROM rpos_messages 
    WHERE 
        (sender_id = ? AND receiver_id = ? AND type = ?) 
        OR 
        (sender_id = ? AND receiver_id = ? AND type = ?)
    ORDER BY created_at ASC
");

$stmt->bind_param('ssssss', $user_id, $other_id, $type, $other_id, $user_id, $type);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Determine if this message was sent by current user
    $is_sent_by_current_user = ($row['sender_id'] === $user_id);

    $messages[] = [
        'message_id' => $row['message_id'],
        'sender_id' => $row['sender_id'],
        'sender_type' => $is_sent_by_current_user ? $user_type : $other_type,
        'receiver_id' => $row['receiver_id'],
        'receiver_type' => $is_sent_by_current_user ? $other_type : $user_type,
        'content' => $row['content'],
        'status' => $row['status'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'messages' => $messages]);
?>