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
require_once __DIR__ . '/../../vendor/autoload.php';

// Enable mysqli error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json');

function log_debug($msg)
{
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
}

// Check if user is logged in
if (!isset($_SESSION['customer_id']) && !isset($_SESSION['admin_id']) && !isset($_SESSION['staff_number'])) {
    log_debug('Not authenticated');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_debug('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get sender info
    $sender_id = null;
    $sender_type = null;
    $sender_name = null;
    $sender_email = null;

    if (isset($_SESSION['customer_id'])) {
        $sender_id = $_SESSION['customer_id'];
        $sender_type = 'customer';
        $stmt = $mysqli->prepare("SELECT customer_name, customer_email FROM rpos_customers WHERE customer_id = ?");
        $stmt->bind_param('s', $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($customer = $result->fetch_object()) {
            $sender_name = $customer->customer_name;
            $sender_email = $customer->customer_email;
        }
    } elseif (isset($_SESSION['admin_id'])) {
        $sender_id = $_SESSION['admin_id'];
        $sender_type = 'admin';
        $stmt = $mysqli->prepare("SELECT admin_name, admin_email FROM rpos_admin WHERE admin_id = ?");
        $stmt->bind_param('s', $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($admin = $result->fetch_object()) {
            $sender_name = $admin->admin_name;
            $sender_email = $admin->admin_email;
        }
    } elseif (isset($_SESSION['staff_number'])) {
        $sender_id = $_SESSION['staff_number'];
        $sender_type = 'staff';
        $stmt = $mysqli->prepare("SELECT staff_name, staff_email FROM rpos_staff WHERE staff_number = ?");
        $stmt->bind_param('s', $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($staff = $result->fetch_object()) {
            $sender_name = $staff->staff_name;
            $sender_email = $staff->staff_email;
        }
    }

    // Validate input
    $recipient_id = $_POST['recipient_id'] ?? '';
    $recipient_type = $_POST['recipient_type'] ?? '';
    $message_content = trim($_POST['message'] ?? '');

    if (empty($recipient_id) || empty($recipient_type) || empty($message_content)) {
        log_debug('Missing required fields: ' . json_encode($_POST));
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Get recipient details for email
    $recipient_name = '';
    $recipient_email = '';

    if ($recipient_type === 'customer') {
        $stmt = $mysqli->prepare("SELECT customer_name, customer_email FROM rpos_customers WHERE customer_id = ?");
        $stmt->bind_param('s', $recipient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($customer = $result->fetch_object()) {
            $recipient_name = $customer->customer_name;
            $recipient_email = $customer->customer_email;
        }
    } elseif ($recipient_type === 'admin') {
        $stmt = $mysqli->prepare("SELECT admin_name, admin_email FROM rpos_admin WHERE admin_id = ?");
        $stmt->bind_param('s', $recipient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($admin = $result->fetch_object()) {
            $recipient_name = $admin->admin_name;
            $recipient_email = $admin->admin_email;
        }
    } elseif ($recipient_type === 'staff') {
        $stmt = $mysqli->prepare("SELECT staff_name, staff_email FROM rpos_staff WHERE staff_number = ?");
        $stmt->bind_param('s', $recipient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($staff = $result->fetch_object()) {
            $recipient_name = $staff->staff_name;
            $recipient_email = $staff->staff_email;
        }
    }

    // Insert message into database
    $type = 'chat';
    $stmt = $mysqli->prepare("INSERT INTO rpos_messages (sender_id, receiver_id, type, content, status) VALUES (?, ?, ?, ?, 'sent')");
    $stmt->bind_param('ssss', $sender_id, $recipient_id, $type, $message_content);

    if ($stmt->execute()) {
        $message_id = $mysqli->insert_id;

        // Send email notification if recipient has email
        if (!empty($recipient_email) && strpos($recipient_email, '@noemail.com') === false) {
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'infofonepo@gmail.com';
                $mail->Password = 'zaoxwuezfjpglwjb';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
                $mail->addAddress($recipient_email, $recipient_name);
                $mail->isHTML(true);
                $mail->Subject = 'New Message from ' . $sender_name . ' - Best Friend Supermarket';
                $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;'>
                        <h2 style='color: #333; margin: 0;'>BEST FRIEND SUPERMARKET</h2>
                        <p style='margin: 5px 0;'>REMERA, GISEMENTI</p>
                    </div>
                    <div style='margin-bottom: 20px;'>
                        <h3 style='color: #333;'>New Message</h3>
                        <p>You have received a new message from <strong>" . htmlspecialchars($sender_name) . "</strong>:</p>
                    </div>
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #007bff;'>
                        <p style='margin: 0; font-style: italic;'>" . htmlspecialchars($message_content) . "</p>
                    </div>
                    <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                        <p style='color: #666; font-size: 14px;'>Login to your account to reply to this message.</p>
                    </div>
                </div>";
                $mail->send();
            } catch (Exception $e) {
                log_debug('Email error: ' . $mail->ErrorInfo);
            }
        }

        echo json_encode(['success' => true, 'message_id' => $message_id]);
    } else {
        log_debug('DB error: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to save message', 'error' => $stmt->error]);
    }
} catch (Throwable $e) {
    log_debug('Exception: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Exception', 'error' => $e->getMessage()]);
    // Direct error output for debugging
    echo '<pre style="color:red; background:#fff; padding:10px; border:1px solid #f00;">';
    echo 'Exception: ' . htmlspecialchars($e->getMessage()) . "\n";
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}
?>