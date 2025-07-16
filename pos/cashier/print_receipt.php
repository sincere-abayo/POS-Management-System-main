<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Handle email receipt functionality
if (isset($_GET['email']) && $_GET['email'] == 1 && isset($_GET['order_id'])) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $order_id = intval($_GET['order_id']);
    $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!($order = $res->fetch_object())) {
        $email_status = "Order not found.";
    } else {
        $items = json_decode($order->items, true);
        $customer_email = $order->customer_email;
        $customer_name = $order->customer_name;
        $total = 0;

        // Generate receipt HTML
        ob_start();
        ?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #333; margin: 0;">BEST FRIEND SUPERMARKET</h2>
        <p style="margin: 5px 0;">KIGALI, Kimironko</p>
        <p style="margin: 5px 0;">0785617132</p>
    </div>

    <div style="margin-bottom: 20px;">
        <h3 style="color: #333; text-align: center;">Receipt for Order #<?php echo $order_id; ?></h3>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order->customer_phoneno); ?></p>
        <p><strong>Date:</strong> <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f8f9fa;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Item</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Code</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Qty</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Unit Price</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
                    if (is_array($items) && count($items) > 0) {
                        foreach ($items as $prod) {
                            $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                            $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                            $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                            $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                            $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : 0;
                            $total += $subtotal;
                            echo '<tr>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_name) . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_code) . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . htmlspecialchars($prod_qty) . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($prod_price) . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($subtotal) . '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Total</strong>
                </td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>RWF
                        <?php echo htmlspecialchars($total); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
        <p style="color: #666; font-size: 14px;">Thank you for your purchase!</p>
        <p style="color: #666; font-size: 12px;">Please keep this receipt for your records.</p>
    </div>
</div>
<?php
        $receipt_html = ob_get_clean();

        // Send email using PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'infofonepo@gmail.com';
            $mail->Password = 'zaoxwuezfjpglwjb';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
            $mail->addAddress($customer_email, $customer_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Receipt for Order #' . $order_id . ' - Best Friend Supermarket';
            $mail->Body = $receipt_html;

            $mail->send();
            $email_status = 'Receipt emailed successfully to ' . htmlspecialchars($customer_email);
        } catch (Exception $e) {
            $email_status = 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="MartDevelopers Inc">
    <title>Market Point Of Sale </title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../admin/assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../admin/assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../admin/assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="../admin/assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="../admin/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link href="assets/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/jquery.js"></script>
    <style>
    body {
        margin-top: 20px;
    }
    </style>
</head>
<?php
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
if (!$order_id) {
    echo '<div class="alert alert-danger">No order ID provided.</div>';
    exit;
}
$ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
if (!($order = $res->fetch_object())) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    exit;
}
$items = json_decode($order->items, true);
$customer_name = $order->customer_name ? $order->customer_name : $order->customer_id;
$customer_phone = $order->customer_phoneno ? $order->customer_phoneno : '-';
$customer_email = ($order->customer_email && strpos($order->customer_email, '@noemail.com') === false) ? $order->customer_email : '-';
?>

<body>
    <div class="container">
        <?php if (isset($email_status)) { ?>
        <div class="alert alert-info text-center"><?php echo $email_status; ?></div>
        <?php } ?>
        <div class="row">
            <div id="Receipt" class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <address>
                            <strong>BEST FRIEND SUPERMARKET</strong>
                            <br>
                            KIGALI, Kimironko
                            <br>
                            0785617132
                        </address>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                        <p>
                            <em>Date: <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></em>
                        </p>
                        <p>
                            <em class="text-success">Receipt #: <?php echo $order_id; ?></em>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="text-center">
                        <h2>Receipt</h2>
                    </div>
                    <div class="mb-2">
                        <b>Order ID:</b> <?php echo $order_id; ?><br>
                        <b>Order Date:</b> <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?><br>
                        <b>Customer:</b> <?php echo htmlspecialchars($customer_name); ?><br>
                        <b>Phone:</b> <?php echo htmlspecialchars($customer_phone); ?><br>
                        <?php if ($customer_email !== '-') { ?>
                        <b>Email:</b> <?php echo htmlspecialchars($customer_email); ?><br>
                        <?php } ?>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Code</th>
                                <th>Quantity</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            if (is_array($items) && count($items) > 0) {
                                foreach ($items as $prod) {
                                    $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                                    $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                                    $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                                    $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                                    $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : 0;
                                    $total += $subtotal;
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($prod_name) . '</td>';
                                    echo '<td>' . htmlspecialchars($prod_code) . '</td>';
                                    echo '<td class="text-center">' . htmlspecialchars($prod_qty) . '</td>';
                                    echo '<td class="text-center">RWF ' . htmlspecialchars($prod_price) . '</td>';
                                    echo '<td class="text-center">RWF ' . htmlspecialchars($subtotal) . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">No products found in this order.</td></tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th class="text-center">RWF <?php echo htmlspecialchars($total); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print
                            Receipt</button>
                        <?php if ($customer_email !== '-') { ?>
                        <a href="?order_id=<?php echo $order_id; ?>&email=1" class="btn btn-info ml-2"><i
                                class="fas fa-envelope"></i> Email Receipt</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
function printContent(el) {
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();
    $('body').empty().html(printcontent);
    window.print();
    $('body').html(restorepage);
}
</script>