<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $stmt = $mysqli->prepare("UPDATE rpos_orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: orders_reports.php");
    exit();
}
require_once('partials/_head.php');
?>

<body>
    <?php require_once('partials/_sidebar.php'); ?>
    <div class="main-content">
        <?php require_once('partials/_topnav.php'); ?>
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        <div class="container-fluid mt--8">
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="get" class="form-inline justify-content-end">
                        <label class="mr-2">From:</label>
                        <input type="date" name="from" class="form-control mr-2"
                            value="<?php echo isset($_GET['from']) ? htmlspecialchars($_GET['from']) : ''; ?>">
                        <label class="mr-2">To:</label>
                        <input type="date" name="to" class="form-control mr-2"
                            value="<?php echo isset($_GET['to']) ? htmlspecialchars($_GET['to']) : ''; ?>">
                        <button type="submit" class="btn btn-info">Filter</button>
                        <button type="button" class="btn btn-primary ml-2" onclick="printReport()"><i
                                class="fas fa-print"></i> Print Report</button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <span class="h3">Order Records</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Products</th>
                                        <th>Total</th>
                                        <th>Payment Status</th>
                                        <th>Status</th>
                                        <th>Order Type</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $where = '';
                                    $params = [];
                                    if (!empty($_GET['from']) && !empty($_GET['to'])) {
                                        $where = 'WHERE DATE(o.created_at) BETWEEN ? AND ?';
                                        $params[] = $_GET['from'];
                                        $params[] = $_GET['to'];
                                    } elseif (!empty($_GET['from'])) {
                                        $where = 'WHERE DATE(o.created_at) >= ?';
                                        $params[] = $_GET['from'];
                                    } elseif (!empty($_GET['to'])) {
                                        $where = 'WHERE DATE(o.created_at) <= ?';
                                        $params[] = $_GET['to'];
                                    }
                                    $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id $where ORDER BY o.created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    if ($params) {
                                        $types = str_repeat('s', count($params));
                                        $stmt->bind_param($types, ...$params);
                                    }
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $i = 1;
                                    while ($order = $res->fetch_object()) {
                                        $items = json_decode($order->items, true);
                                        $total = 0;
                                        $product_names = [];
                                        foreach ($items as $item) {
                                            $qty = isset($item['prod_qty']) ? $item['prod_qty'] : 1;
                                            $price = isset($item['prod_price']) ? $item['prod_price'] : 0;
                                            $total += $price * $qty;
                                            $product_names[] = $item['prod_name'];
                                        }
                                        // Fetch payment status
                                        $pay_stmt = $mysqli->prepare("SELECT status FROM rpos_payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1");
                                        $pay_stmt->bind_param('i', $order->order_id);
                                        $pay_stmt->execute();
                                        $pay_stmt->bind_result($pay_status);
                                        $has_payment = $pay_stmt->fetch();
                                        $pay_stmt->close();
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($order->customer_name ?? ''); ?><br>
                                                <?php echo htmlspecialchars($order->customer_phoneno ?? ''); ?><br>
                                                <?php if (!empty($order->customer_email) && strpos($order->customer_email, '@noemail.com') === false)
                                                    echo htmlspecialchars($order->customer_email); ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($product_names[0]);
                                                if (count($product_names) > 1) {
                                                    echo ' +' . (count($product_names) - 1) . ' more';
                                                }
                                                ?>
                                            </td>
                                            <td>RWF <?php echo number_format($total, 2); ?></td>
                                            <td>
                                                <?php
                                                if ($has_payment && $pay_status == 'paid') {
                                                    echo "<span class='badge badge-success'>Paid</span>";
                                                } else {
                                                    echo "<span class='badge badge-danger'>Unpaid</span>";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($order->order_type == 'online') {
                                                    ?>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="order_id"
                                                            value="<?php echo $order->order_id; ?>">
                                                        <select name="new_status"
                                                            class="form-control form-control-sm d-inline w-auto"
                                                            onchange="this.form.submit()">
                                                            <option value="pending" <?php if ($order->status == 'pending')
                                                                echo 'selected'; ?>>Pending</option>
                                                            <option value="packed" <?php if ($order->status == 'packed')
                                                                echo 'selected'; ?>>Packed</option>
                                                            <option value="delivered" <?php if ($order->status == 'delivered')
                                                                echo 'selected'; ?>>Delivered</option>
                                                            <option value="cancelled" <?php if ($order->status == 'cancelled')
                                                                echo 'selected'; ?>>Cancelled</option>
                                                        </select>
                                                    </form>
                                                    <span
                                                        class='badge badge-success ml-2'><?php echo htmlspecialchars($order->status); ?></span>
                                                    <?php
                                                } else {
                                                    echo ucfirst($order->status);
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $order->order_type)); ?></td>
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>
    <style>
        @media print {

            .btn,
            form,
            .main-content .card-header {
                display: none !important;
            }
        }
    </style>
    <script>
        function printReport() {
            window.print();
        }
    </script>
    <?php require_once('partials/_scripts.php'); ?>
</body>

</html>