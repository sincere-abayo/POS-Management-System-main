<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
                        <button type="button" class="btn btn-success ml-2" onclick="printReport()"><i
                                class="fas fa-print"></i> Print Report</button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3>Payment Reports</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Payment Method</th>
                                        <th class="text-success" scope="col">Products</th>
                                        <th scope="col">Payment Status</th>
                                        <th class="text-success" scope="col">Date Paid</th>
                                        <th scope="col">Order ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $where = '';
                                    $params = [];
                                    if (!empty($_GET['from']) && !empty($_GET['to'])) {
                                        $where = 'WHERE DATE(p.created_at) BETWEEN ? AND ?';
                                        $params[] = $_GET['from'];
                                        $params[] = $_GET['to'];
                                    } elseif (!empty($_GET['from'])) {
                                        $where = 'WHERE DATE(p.created_at) >= ?';
                                        $params[] = $_GET['from'];
                                    } elseif (!empty($_GET['to'])) {
                                        $where = 'WHERE DATE(p.created_at) <= ?';
                                        $params[] = $_GET['to'];
                                    }
                                    $ret = "SELECT p.*, o.items FROM rpos_payments p LEFT JOIN rpos_orders o ON p.order_id = o.order_id $where ORDER BY p.created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    if ($params) {
                                        $types = str_repeat('s', count($params));
                                        $stmt->bind_param($types, ...$params);
                                    }
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $i = 1;
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
                                        <td><?php echo date('d/M/Y g:i', strtotime($payment->created_at)); ?></td>
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