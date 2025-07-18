<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0 d-flex justify-content-between">
                            <h3>Orders Records</h3>
                            <button onclick="printReport()" class="btn btn-primary">Print Report</button>
                        </div>
                        <div class="table-responsive" id="reportContent">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success">Code</th>
                                        <th>Customer</th>
                                        <th class="text-success">Product</th>
                                        <th>Unit Price</th>
                                        <th class="text-success">Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM rpos_orders ORDER BY created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($order = $res->fetch_object()) {
                                        $total = ($order->prod_price * $order->prod_qty);
                                    ?>
                                        <tr>
                                            <td class="text-success"><?php echo htmlspecialchars($order->order_code); ?></td>
                                            <td><?php echo htmlspecialchars($order->customer_name); ?></td>
                                            <td class="text-success"><?php echo htmlspecialchars($order->prod_name); ?></td>
                                            <td>$ <?php echo number_format($order->prod_price, 2); ?></td>
                                            <td class="text-success"><?php echo htmlspecialchars($order->prod_qty); ?></td>
                                            <td>$ <?php echo number_format($total, 2); ?></td>
                                            <td>
                                                <?php echo ($order->order_status == '') ? "<span class='badge badge-danger'>Not Paid</span>" : "<span class='badge badge-success'>" . htmlspecialchars($order->order_status) . "</span>"; ?>
                                            </td>
                                            <td><?php echo date('d/M/Y g:i A', strtotime($order->created_at)); ?></td>
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

    <!-- Print Script -->
    <script>
        function printReport() {
            var content = document.getElementById("reportContent").innerHTML;
            var originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <html>
                <head>
                    <title>Order Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; }
                        h2 { margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid black; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .badge { padding: 5px 10px; font-size: 12px; border-radius: 4px; }
                        .badge-danger { background-color: red; color: white; }
                        .badge-success { background-color: green; color: white; }
                    </style>
                </head>
                <body>
                    <h2>Order Report</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>${content}</tbody>
                    </table>
                </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    </script>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
