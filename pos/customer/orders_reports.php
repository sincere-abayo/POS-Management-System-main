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
                        <div class="card-header border-0">
                            Orders Records
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Customer</th>
                                        <th class="text-success" scope="col">Product</th>
                                        <th scope="col">Unit Price</th>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Total Price</th>
                                        <th scop="col">Status</th>
                                        <th class="text-success" scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $customer_id = $_SESSION['customer_id'];
                                    $ret = "SELECT o.*, c.customer_name FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.customer_id ='$customer_id' ORDER BY o.created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($order = $res->fetch_object()) {
                                        $items = json_decode($order->items, true);
                                        if (is_array($items)) {
                                            foreach ($items as $item) {
                                                $qty = isset($item['prod_qty']) ? $item['prod_qty'] : 1;
                                                $total = $item['prod_price'] * $qty;
                                                ?>
                                                <tr>
                                                    <th class="text-success" scope="row"><?php echo $i++; ?></th>
                                                    <td><?php echo htmlspecialchars($order->customer_name); ?></td>
                                                    <td class="text-success"><?php echo htmlspecialchars($item['prod_name']); ?>
                                                    </td>
                                                    <td>RWF <?php echo number_format($item['prod_price'], 2); ?></td>
                                                    <td class="text-success"><?php echo $qty; ?></td>
                                                    <td>RWF <?php echo number_format($total, 2); ?></td>
                                                    <td><?php
                                                    if ($order->status == 'pending') {
                                                        echo "<span class='badge badge-danger'>Not Paid</span> ";
                                                        echo "<a href='pay_order.php?order_id=" . urlencode($order->order_id) . "' class='btn btn-sm btn-primary ml-2'>Pay</a>";
                                                    } else {
                                                        echo "<span class='badge badge-success'>" . htmlspecialchars($order->status) . "</span>";
                                                    }
                                                    ?></td>
                                                    <td class="text-success">
                                                        <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    } ?>
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