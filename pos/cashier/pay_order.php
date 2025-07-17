<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  // Prevent Posting Blank Values
  if (empty($_POST['amount']) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_id = $_GET['order_id'];
    $customer_id = $_GET['customer_id'];
    $amount = $_POST['amount'];
    $pay_method = $_POST['pay_method'];
    $transaction_ref = isset($_POST['transaction_ref']) ? $_POST['transaction_ref'] : null;

    // Insert payment
    $postQuery = "INSERT INTO rpos_payments (order_id, method, amount, status, transaction_ref) VALUES (?, ?, ?, 'paid', ?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isds', $order_id, $pay_method, $amount, $transaction_ref);
    $postStmt->execute();
    $payment_id = $mysqli->insert_id;

    // Update order status and link payment
    $upQry = "UPDATE rpos_orders SET status = 'delivered', payment_id = ? WHERE order_id = ?";
    $upStmt = $mysqli->prepare($upQry);
    $upStmt->bind_param('ii', $payment_id, $order_id);
    $upStmt->execute();

    if ($upStmt && $postStmt) {
      $success = "Paid" && header("refresh:1; url=receipts.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>
    <?php
    $order_id = $_GET['order_id'];
    $ret = "SELECT * FROM rpos_orders WHERE order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($order = $res->fetch_object()) {
      // Calculate total from items JSON
      $items = json_decode($order->items, true);
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
      ?>
      <!-- Header -->
      <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
        class="header  pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body"></div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <!-- Table -->
        <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3>Pay for Order</h3>
              </div>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Order ID</label>
                      <input type="text" readonly value="<?php echo htmlspecialchars($order_id); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Amount (RWF)</label>
                      <input type="text" name="amount" readonly value="<?php echo htmlspecialchars($total); ?>"
                        class="form-control">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Payment Method</label>
                      <select class="form-control" name="pay_method">
                        <option value="cash" selected>Cash</option>
                        <option value="momo">MTN MoMo</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="stripe">Stripe</option>
                        <option value="bank">Banking system</option>
                        <option value="paypal">Paypal</option>
                        <option value="western">Western Union</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label>Transaction Reference (optional)</label>
                      <input type="text" name="transaction_ref" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
        <?php require_once('partials/_footer.php'); ?>
      </div>
    <?php } ?>
    </div>
    <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>