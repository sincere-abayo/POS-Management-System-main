<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
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
</style>
<?php
// Email receipt logic
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
if ($order_id) {
    $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $order = $res->fetch_object();
    $items = json_decode($order->items, true);
    $customer_name = $order->customer_name ? $order->customer_name : $order->customer_id;
    $customer_phone = $order->customer_phoneno ? $order->customer_phoneno : '-';
    $customer_email = ($order->customer_email && strpos($order->customer_email, '@noemail.com') === false) ? $order->customer_email : '-';
    if (isset($_GET['email']) && $_GET['email'] == 1 && $customer_email !== '-') {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'infofonepo@gmail.com';
            $mail->Password = 'zaoxwuezfjpglwjb';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
            $mail->addAddress($customer_email, $customer_name);
            $mail->isHTML(true);
            $mail->Subject = 'Your Receipt from Best Friend Supermarket';
            $is_walkin = (strpos($customer_email, 'walkin_') !== false && strpos($customer_email, '@noemail.com') !== false);
            $credentials_html = '';
            if ($is_walkin) {
                $credentials_html = '<p><b>Account Created!</b><br>Your login email: ' . htmlspecialchars($customer_email) . '<br>Password: walkin123<br>Please change your password after logging in.</p>';
            }
            $mail->Body = '<div style="font-family:sans-serif;max-width:600px;margin:auto;border:1px solid #eee;padding:24px;">'
                . '<h2 style="color:#2d8f2d;">Best Friend Supermarket</h2>'
                . '<p>Thank you for your order! Here is your receipt:</p>'
                . '<hr>'
                . '<b>Order ID:</b> ' . $order_id . '<br>'
                . '<b>Date:</b> ' . date('d/M/Y g:i', strtotime($order->created_at)) . '<br>'
                . '<b>Customer:</b> ' . htmlspecialchars($customer_name) . '<br>'
                . '<b>Phone:</b> ' . htmlspecialchars($customer_phone) . '<br>'
                . ($customer_email !== '-' ? '<b>Email:</b> ' . htmlspecialchars($customer_email) . '<br>' : '')
                . $credentials_html
                . '<table style="width:100%;border-collapse:collapse;margin-top:16px;">
                    <thead><tr style="background:#f5f5f5;"><th align="left">Item</th><th align="left">Code</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead><tbody>';
            $total = 0;
            if (is_array($items) && count($items) > 0) {
                foreach ($items as $prod) {
                    $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                    $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                    $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                    $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                    $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : 0;
                    $total += $subtotal;
                    $mail->Body .= '<tr><td>' . htmlspecialchars($prod_name) . '</td><td>' . htmlspecialchars($prod_code) . '</td><td align="center">' . htmlspecialchars($prod_qty) . '</td><td align="center">RWF ' . htmlspecialchars($prod_price) . '</td><td align="center">RWF ' . htmlspecialchars($subtotal) . '</td></tr>';
                }
            }
            $mail->Body .= '</tbody><tfoot><tr><th colspan="4" align="right">Total</th><th align="center">RWF ' . htmlspecialchars($total) . '</th></tr></tfoot></table>';
            $mail->Body .= '<hr><p style="font-size:13px;color:#888;">Best Friend Supermarket, Kigali, Kimironko<br>Thank you for shopping with us!</p>';
            $mail->send();
            echo '<div class="alert alert-success mt-3">Receipt emailed to ' . htmlspecialchars($customer_email) . '.</div>';
        } catch (Exception $e) {
            echo '<div class="alert alert-danger mt-3">Failed to send email: ' . $mail->ErrorInfo . '</div>';
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
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
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
</style>
<?php
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
if ($order_id) {
    $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $order = $res->fetch_object();
    $items = json_decode($order->items, true);
    $customer_name = $order->customer_name ? $order->customer_name : $order->customer_id;
    $customer_phone = $order->customer_phoneno ? $order->customer_phoneno : '-';
    $customer_email = ($order->customer_email && strpos($order->customer_email, '@noemail.com') === false) ? $order->customer_email : '-';
    ?>

    <body>
        <div class="container">
            <div class="row">
                <div id="Receipt" class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <address>
                                <strong>BEST FRIEND SUPERMARKET</strong>
                                <br>
                                KIGALI, Kimironko
                                <br>
                                +250785617132
                            </address>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                            <p>
                                <em>Date: <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></em>
                            </p>
                            <p>
                                <em class="text-success">Receipt #: <?php echo $order->order_id; ?></em>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <h2>Receipt</h2>
                        </div>
                        </span>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Code</th>
                                    <th>Quantity</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Total</th>
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
                                        ?>
                                        <tr>
                                            <td class="col-md-4"><em> <?php echo htmlspecialchars($prod_name); ?> </em></td>
                                            <td class="col-md-2"><?php echo htmlspecialchars($prod_code); ?></td>
                                            <td class="col-md-2" style="text-align: center"> <?php echo $prod_qty; ?></td>
                                            <td class="col-md-2 text-center">RWF <?php echo number_format($prod_price, 2); ?></td>
                                            <td class="col-md-2 text-center">RWF <?php echo number_format($subtotal, 2); ?></td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td class="text-center text-danger"><strong>RWF
                                            <?php echo number_format($total, 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
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
<?php } ?>