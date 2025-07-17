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
                                        <th scope="col">Payment Status</th>
                                        <th scop="col">Order Status</th>
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
                                        <td>
                                            <?php
                                                        // Fetch payment status for this order
                                                        $pay_stmt = $mysqli->prepare("SELECT status FROM rpos_payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1");
                                                        $pay_stmt->bind_param('i', $order->order_id);
                                                        $pay_stmt->execute();
                                                        $pay_stmt->bind_result($pay_status);
                                                        $has_payment = $pay_stmt->fetch();
                                                        $pay_stmt->close();
                                                        if ($has_payment && $pay_status == 'paid') {
                                                            echo "<span class='badge badge-success'>Paid</span>";
                                                        } else {
                                                            echo "<span class='badge badge-danger'>Unpaid</span> ";
                                                            echo "<a href='pay_order.php?order_id=" . urlencode($order->order_id) . "' class='btn btn-sm btn-primary ml-2'>Pay</a>";
                                                        }
                                                        ?>
                                        </td>
                                        <td><?php
                                                    echo ($order->status == 'pending') ? "<span class='badge badge-danger'>Pending</span>" : "<span class='badge badge-success'>" . htmlspecialchars($order->status) . "</span>";
                                                    if ($order->order_type == 'online') {
                                                        echo ' <button type="button" class="btn btn-sm btn-info ml-2 track-btn" data-order-id="' . $order->order_id . '" data-status="' . htmlspecialchars($order->status) . '">Track</button>';
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
    <!-- Add modal for tracking -->
    <div class="modal fade" id="trackModal" tabindex="-1" role="dialog" aria-labelledby="trackModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackModalLabel">Order Tracking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="trackingTimeline"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Show tracking modal with timeline
    const statusSteps = ['pending', 'packed', 'delivered', 'cancelled'];
    document.querySelectorAll('.track-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            let timeline = '<ul class="list-group">';
            for (let step of statusSteps) {
                let active = (step === status) ? 'list-group-item-success' : '';
                timeline +=
                    `<li class="list-group-item ${active}">${step.charAt(0).toUpperCase() + step.slice(1)}</li>`;
                if (step === status) break;
            }
            timeline += '</ul>';
            document.getElementById('trackingTimeline').innerHTML = timeline;
            $('#trackModal').modal('show');
        });
    });
    </script>
</body>

</html>