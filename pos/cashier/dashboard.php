<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

require_once('partials/_head.php');
require_once('partials/_analytics.php');
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
                    <!-- Card stats -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Customers</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo $customers; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Products</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo $products; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fas fa-box-open"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Orders</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo $orders; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
                                            <span class="h2 font-weight-bold mb-0">RWF <?php echo $sales; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--7">
            <div class="row mt-5">
                <div class="col-xl-12 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Recent Orders</h3>
                                </div>
                                <div class="col text-right">
                                    <a href="orders_reports.php" class="btn btn-sm btn-primary">See all</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Order ID</th>
                                        <th scope="col">Customer</th>
                                        <th class="text-success" scope="col">Products</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Status</th>
                                        <th class="text-success" scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                  $ret = "SELECT o.*, c.customer_name FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id ORDER BY o.created_at DESC LIMIT 7 ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($order = $res->fetch_object()) {
                    $items = json_decode($order->items, true);
                    $total = 0;
                    $products_html = '';
                    if (is_array($items) && count($items) > 0) {
                      foreach ($items as $prod) {
                        $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                        $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                        $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                        $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                        $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : 0;
                        $total += $subtotal;
                        $products_html .= '<div><b>' . htmlspecialchars($prod_name) . '</b>';
                        if ($prod_code)
                          $products_html .= ' <span class="text-muted">(' . htmlspecialchars($prod_code) . ')</span>';
                        $products_html .= '<br>Qty: ' . htmlspecialchars($prod_qty) . ', Unit: RWF ' . htmlspecialchars($prod_price) . ', Subtotal: RWF ' . htmlspecialchars($subtotal) . '</div>';
                      }
                    } else {
                      $products_html = '-';
                    }
                    $customer_name = $order->customer_name ? $order->customer_name : $order->customer_id;
                    ?>
                                    <tr>
                                        <th class="text-success" scope="row"><?php echo $order->order_id; ?></th>
                                        <td><?php echo htmlspecialchars($customer_name); ?></td>
                                        <td><?php echo $products_html; ?></td>
                                        <td>RWF <?php echo htmlspecialchars($total); ?></td>
                                        <td><?php echo ucfirst($order->status); ?></td>
                                        <td class="text-success">
                                            <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-xl-12">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Recent Payments</h3>
                                </div>
                                <div class="col text-right">
                                    <a href="payments_reports.php" class="btn btn-sm btn-primary">See all</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Payment ID</th>
                                        <th scope="col">Order ID</th>
                                        <th scope="col">Method</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Status</th>
                                        <th class="text-success" scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                  $ret = "SELECT * FROM rpos_payments ORDER BY created_at DESC LIMIT 7 ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($payment = $res->fetch_object()) {
                    ?>
                                    <tr>
                                        <th class="text-success" scope="row"><?php echo $payment->payment_id; ?></th>
                                        <td><a href="print_receipt.php?order_id=<?php echo $payment->order_id; ?>"
                                                target="_blank"><?php echo $payment->order_id; ?></a></td>
                                        <td><?php echo ucfirst($payment->method); ?></td>
                                        <td>RWF <?php echo htmlspecialchars($payment->amount); ?></td>
                                        <td><?php echo ucfirst($payment->status); ?></td>
                                        <td class="text-success">
                                            <?php echo date('d/M/Y g:i', strtotime($payment->created_at)); ?>
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
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
  require_once('partials/_scripts.php');
  ?>
</body>

</html>