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
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
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
                        <div class="card-header border-0 d-flex justify-content-between">
                            <span>Payment Reports</span>
                            <button class="btn btn-primary" onclick="printReport()">Print Report</button>
                        </div>
                        <div class="table-responsive" id="reportSection">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Payment Method</th>
                                        <th class="text-success" scope="col">Products</th>
                                        <th scope="col">Amount Paid</th>
                                        <th scope="col">Payment Status</th>
                                        <th class="text-success" scope="col">Date Paid</th>
                                        <th scope="col">Order ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $ret = "SELECT p.*, o.items FROM rpos_payments p LEFT JOIN rpos_orders o ON p.order_id = o.order_id ORDER BY p.created_at DESC ";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($payment = $res->fetch_object()) {
                                        ?>
                                        <tr>
                                            <th class="text-success" scope="row"><?php echo $i++; ?></th>
                                            <td><?php echo ucfirst(htmlspecialchars($payment->method)); ?></td>
                                            <td>
                                                <?php
                                                $items = isset($payment->items) ? json_decode($payment->items, true) : null;
                                                if (is_array($items) && count($items) > 0) {
                                                    $names = array_column($items, 'prod_name');
                                                    echo htmlspecialchars($names[0]);
                                                    if (count($names) > 1) {
                                                        echo ' +' . (count($names) - 1) . ' more';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>RWF <?php echo number_format($payment->amount, 2); ?></td>
                                            <td><?php echo ucfirst($payment->status); ?></td>
                                            <td class="text-success">
                                                <?php echo date('d/M/Y g:i', strtotime($payment->created_at)) ?></td>
                                            <td>
                                                <?php if ($payment->order_id) { ?>
                                                    <a href="print_receipt.php?order_id=<?php echo $payment->order_id; ?>"
                                                        target="_blank">
                                                        <?php echo htmlspecialchars($payment->order_id); ?>
                                                    </a>
                                                <?php } else {
                                                    echo '-';
                                                } ?>
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

    <script>
        function printReport() {
            var printContents = document.getElementById('reportSection').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>

    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>