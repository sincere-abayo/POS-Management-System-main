<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Cancel Order
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    $adn = "DELETE FROM  rpos_orders  WHERE  order_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Deleted" && header("refresh:1; url=payments.php");
    } else {
        $err = "Try Again Later";
    }
}
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
        ?>
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <a href="orders.php" class="btn btn-outline-success">
                                <i class="fas fa-plus"></i> <i class="fas fa-utensils"></i>
                                Make A New Order
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success">#</th>
                                        <th>Payment Method</th>
                                        <th class="text-success">Products</th>
                                        <th>Amount Paid</th>
                                        <th class="text-success">Date Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $customer_id = $_SESSION['customer_id'];
                                    $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.status = 'pending' AND o.customer_id = '$customer_id' ORDER BY o.created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $i = 1;
                                    while ($order = $res->fetch_object()) {
                                        // Prepare customer info
                                        $customer_name = $order->customer_name ? $order->customer_name : $order->customer_id;
                                        $customer_phone = $order->customer_phoneno ? $order->customer_phoneno : '-';
                                        $customer_email = ($order->customer_email && strpos($order->customer_email, '@noemail.com') === false) ? $order->customer_email : '-';
                                        // Extract product info from items JSON
                                        $items = json_decode($order->items, true);
                                        ?>
                                        <tr>
                                            <td class="text-success"><?php echo $i++; ?></td>
                                            <td>
                                                <b><?php echo htmlspecialchars($customer_name); ?></b><br>
                                                <small>Phone: <?php echo htmlspecialchars($customer_phone); ?></small><br>
                                                <?php if ($customer_email !== '-') { ?>
                                                    <small>Email: <?php echo htmlspecialchars($customer_email); ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (is_array($items) && count($items) > 0) {
                                                    foreach ($items as $prod) {
                                                        $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                                                        $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                                                        $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : '-';
                                                        $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : '-';
                                                        $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : '-';
                                                        echo '<div style="margin-bottom:6px">';
                                                        echo '<b>' . htmlspecialchars($prod_name) . '</b>';
                                                        if ($prod_code)
                                                            echo ' <span class="text-muted">(' . htmlspecialchars($prod_code) . ')</span>';
                                                        echo '<br>Qty: ' . htmlspecialchars($prod_qty) . ', Unit: RWF ' . htmlspecialchars($prod_price) . ', Subtotal: RWF ' . htmlspecialchars($subtotal);
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Show total for all products
                                                $total = 0;
                                                if (is_array($items) && count($items) > 0) {
                                                    foreach ($items as $prod) {
                                                        $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                                                        $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                                                        if (is_numeric($prod_price) && is_numeric($prod_qty)) {
                                                            $total += $prod_price * $prod_qty;
                                                        }
                                                    }
                                                }
                                                echo 'RWF ' . htmlspecialchars($total);
                                                ?>
                                            </td>
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                            <td>
                                                <div><b>Type:</b> <?php echo htmlspecialchars($order->order_type); ?></div>
                                                <div><b>Status:</b> <?php echo htmlspecialchars($order->status); ?></div>
                                                <a
                                                    href="pay_order.php?order_id=<?php echo $order->order_id; ?>&customer_id=<?php echo $order->customer_id; ?>&order_status=Paid">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-handshake"></i>
                                                        Pay Order
                                                    </button>
                                                </a>
                                                <a href="payments.php?cancel=<?php echo $order->order_id; ?>">
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-window-close"></i>
                                                        Cancel Order
                                                    </button>
                                                </a>
                                                <a href="print_receipt.php?order_id=<?php echo $order->order_id; ?>"
                                                    target="_blank">
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-print"></i>
                                                        Generate Receipt
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>