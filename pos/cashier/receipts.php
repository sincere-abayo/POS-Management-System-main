<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Email receipt logic
$email_status = null;
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
        ob_start();
        ?>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0;">BEST FRIEND SUPERMARKET</h2>
                <p style="margin: 5px 0;">REMERA, GISEMENTI</p>
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
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'infofonepo@gmail.com';
            $mail->Password = 'zaoxwuezfjpglwjb';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
            $mail->addAddress($customer_email, $customer_name);
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
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <?php if ($email_status) { ?>
                <div class="alert alert-info text-center"><?php echo $email_status; ?></div>
            <?php } ?>
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Paid Orders</h3>
                        </div>
                        <div class="table-responsive">
                            <?php
                            // Fetch all orders with a paid payment
                            $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email, p.status as payment_status FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id LEFT JOIN rpos_payments p ON o.payment_id = p.payment_id WHERE p.status = 'paid' ORDER BY o.created_at DESC";
                            $stmt = $mysqli->prepare($ret);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            if ($res->num_rows == 0) {
                                echo '<div class="alert alert-success text-center">No paid orders found!</div>';
                            } else {
                                while ($order = $res->fetch_object()) {
                                    $items = json_decode($order->items, true);
                                    $customer_name = $order->customer_name ? $order->customer_name : $order->customer_id;
                                    $customer_phone = $order->customer_phoneno ? $order->customer_phoneno : '-';
                                    $customer_email = ($order->customer_email && strpos($order->customer_email, '@noemail.com') === false) ? $order->customer_email : '-';
                                    $order_id = $order->order_id;
                                    $total = 0;
                                    ?>
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Receipt for Order #<?php echo $order_id; ?></h4>
                                                    <p><b>Customer:</b> <?php echo htmlspecialchars($customer_name); ?><br>
                                                        <b>Phone:</b> <?php echo htmlspecialchars($customer_phone); ?><br>
                                                        <?php if ($customer_email !== '-') { ?>
                                                            <b>Email:</b> <?php echo htmlspecialchars($customer_email); ?><br>
                                                        <?php } ?>
                                                        <b>Date:</b>
                                                        <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?><br>
                                                    </p>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <a href="print_receipt.php?order_id=<?php echo $order_id; ?>"
                                                        target="_blank" class="btn btn-primary mb-2"><i
                                                            class="fas fa-print"></i> Print Receipt</a>
                                                    <?php if ($customer_email !== '-') { ?>
                                                        <a href="receipts.php?order_id=<?php echo $order_id; ?>&email=1"
                                                            class="btn btn-info mb-2"><i class="fas fa-envelope"></i> Email
                                                            Receipt</a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <table class="table table-bordered mt-3">
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
                                        </div>
                                    </div>
                                <?php }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>

</html>