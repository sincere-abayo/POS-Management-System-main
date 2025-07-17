<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
require_once('partials/_analytics.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>
    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
      class="header  pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
          <div class="row">
            <div class="col-xl-4 col-lg-6">
              <a href="orders.php">
                <div class="card card-stats mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Available Items
                        </h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo $products; ?></span>
                      </div>
                      <div class="col-auto">
                        <div class="icon icon-shape bg-purple text-white rounded-circle shadow">
                          <i class="fas fa-utensils"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-xl-4 col-lg-6">
              <a href="orders_reports.php">
                <div class="card card-stats mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total Orders</h5>
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
              </a>
            </div>
            <div class="col-xl-4 col-lg-6">
              <a href="payments_reports.php">
                <div class="card card-stats mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total Money Spent
                        </h5>
                        <span class="h2 font-weight-bold mb-0">RWF
                          <?php echo $sales ? $sales : 0; ?></span>
                      </div>
                      <div class="col-auto">
                        <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                          <i class="fas fa-wallet"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
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
                    <th>Order ID</th>
                    <th>Products</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $customer_id = $_SESSION['customer_id'];
                  $ret = "SELECT * FROM rpos_orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->bind_param('s', $customer_id);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($order = $res->fetch_object()) {
                    $items = json_decode($order->items, true);
                    $total = 0;
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($order->order_id); ?></td>
                      <td>
                        <?php
                        if (is_array($items) && count($items) > 0) {
                          foreach ($items as $prod) {
                            $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                            $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                            $prod_qty = isset($prod['quantity']) ? $prod['quantity'] : (isset($prod['prod_qty']) ? $prod['prod_qty'] : 1);
                            $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : '-';
                            $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : '-';
                            $total += is_numeric($subtotal) ? $subtotal : 0;
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
                      <td>RWF <?php echo htmlspecialchars($total); ?></td>
                      <td><?php echo ucfirst($order->status); ?></td>
                      <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
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
                  <h3 class="mb-0">My Recent Payments</h3>
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
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT p.* FROM rpos_payments p JOIN rpos_orders o ON p.order_id = o.order_id WHERE o.customer_id = ? ORDER BY p.created_at DESC LIMIT 10";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->bind_param('s', $customer_id);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($payment = $res->fetch_object()) {
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($payment->payment_id); ?></td>
                      <td>RWF <?php echo htmlspecialchars($payment->amount); ?></td>
                      <td><?php echo htmlspecialchars($payment->order_id); ?></td>
                      <td><?php echo ucfirst($payment->status); ?></td>
                      <td><?php echo date('d/M/Y g:i', strtotime($payment->created_at)); ?></td>
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
  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>