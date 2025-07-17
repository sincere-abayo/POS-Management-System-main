<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <span>Payment Reports</span>
                            <button onclick="printReport()" class="btn btn-primary btn-sm"><i class="fas fa-print"></i>
                                Print</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Payment Method</th>
                                        <th class="text-success" scope="col">Products</th>
                                        <th scope="col">Amount Paid</th>
                                        <th class="text-success" scope="col">Date Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $customer_id = $_SESSION['customer_id'];
                                    $ret = "SELECT p.*, o.order_id FROM rpos_payments p JOIN rpos_orders o ON p.order_id = o.order_id WHERE o.customer_id = ? ORDER BY p.created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->bind_param('s', $customer_id);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $i = 1;
                                    while ($payment = $res->fetch_object()) {
                                        ?>
                                    <tr>
                                        <th class="text-success" scope="row">
                                            <?php echo $i++; ?>
                                        </th>
                                        <th scope="row">
                                            <?php echo $payment->method; ?>
                                        </th>
                                        <td class="text-success">
                                            <?php
                                                // Fetch the order's items for this payment
                                                $order_id = $payment->order_id;
                                                $order_items = '';
                                                $order_query = $mysqli->prepare("SELECT items FROM rpos_orders WHERE order_id = ?");
                                                $order_query->bind_param('i', $order_id);
                                                $order_query->execute();
                                                $order_query->bind_result($items_json);
                                                if ($order_query->fetch()) {
                                                    $items = json_decode($items_json, true);
                                                    if (is_array($items) && count($items) > 0) {
                                                        $names = array_column($items, 'prod_name');
                                                        $order_items = htmlspecialchars($names[0]);
                                                        if (count($names) > 1) {
                                                            $order_items .= ' +' . (count($names) - 1) . ' more';
                                                        }
                                                    }
                                                }
                                                $order_query->close();
                                                echo $order_items;
                                                ?>
                                        </td>
                                        <td>
                                            RWF <?php echo number_format($payment->amount, 2); ?>
                                        </td>
                                        <td class="text-success">
                                            <?php echo date('d/M/Y g:i', strtotime($payment->created_at)) ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            function printReport() {
                window.print();
            }
            </script>

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