<?php
session_start();
header('Content-Type: application/json');
include('config/config.php');
include('config/checklogin.php');

// Run the ALTER TABLE query to add 'confirmed' status (run once, will be ignored if already applied)
$alterQuery = "ALTER TABLE `rpos_orders` 
MODIFY COLUMN `status` ENUM('pending', 'packed', 'delivered', 'cancelled', 'confirmed') DEFAULT 'pending'";
@$mysqli->query($alterQuery); // Suppress error if already altered

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

// Check if the order belongs to the customer and is delivered
$stmt = $mysqli->prepare('SELECT status FROM rpos_orders WHERE order_id = ? AND customer_id = ?');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $mysqli->error]);
    exit;
}
$stmt->bind_param('is', $order_id, $customer_id);
$stmt->execute();
$stmt->bind_result($status);
if ($stmt->fetch()) {
    if ($status === 'delivered') {
        $stmt->close();
        // Update order status to confirmed
        $up = $mysqli->prepare('UPDATE rpos_orders SET status = ? WHERE order_id = ?');
        $new_status = 'confirmed';
        $up->bind_param('si', $new_status, $order_id);
        if ($up->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status: ' . $up->error]);
        }
        $up->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Order is not delivered (current status: ' . $status . ')']);
        $stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Order not found or not delivered']);
    $stmt->close();
}