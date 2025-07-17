<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  $errors = [];
  if (empty($_POST['amount'])) {
    $errors[] = "Amount is required";
  }
  if (empty($_POST['pay_method'])) {
    $errors[] = "Payment method is required";
  }
  if (!empty($errors)) {
    $err = implode('<br>', $errors);
  }
   else {
    $order_id = $_GET['order_id'];
    $amount = $_POST['amount'];
    $pay_method = $_POST['pay_method'];
    $status = 'paid';
    // Insert payment
    $postQuery = "INSERT INTO rpos_payments (order_id, method, amount, status) VALUES (?, ?, ?, ?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isds', $order_id, $pay_method, $amount, $status);
    $postStmt->execute();
    $payment_id = $mysqli->insert_id;
    // Update order status and link payment
    $upQry = "UPDATE rpos_orders SET status = 'pending', payment_id = ? WHERE order_id = ?";
    $upStmt = $mysqli->prepare($upQry);
    $upStmt->bind_param('ii', $payment_id, $order_id);
    $upStmt->execute();
    if ($upStmt && $postStmt) {
      $success = "Paid" && header("refresh:1; url=payments_reports.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
require_once('partials/_head.php');
?>

<body>
    <?php require_once('partials/_sidebar.php'); ?>
    <div class="main-content">
        <?php
    require_once('partials/_topnav.php');
    $order_id = $_GET['order_id'];
    $ret = "SELECT * FROM rpos_orders WHERE order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($order = $res->fetch_object()) {
      $items = json_decode($order->items, true);
      $total = 0;
      if (is_array($items)) {
        foreach ($items as $item) {
          $qty = isset($item['quantity']) ? $item['quantity'] : (isset($item['prod_qty']) ? $item['prod_qty'] : 1);
          $price = isset($item['prod_price']) ? $item['prod_price'] : 0;
          $total += $qty * $price;
        }
      }
      ?>
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        <div class="container-fluid mt--8">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Pay for Order #<?php echo htmlspecialchars($order_id); ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Order ID</label>
                                        <input type="text" name="order_id" readonly
                                            value="<?php echo htmlspecialchars($order_id); ?>" class="form-control">
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Amount (RWF)</label>
                                        <input type="text" name="amount" readonly
                                            value="<?php echo htmlspecialchars($total); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Payment Method</label>
                                        <select class="form-control" name="pay_method">
                                            <option selected>cash</option>
                                            <option>momo</option>
                                            <option>airtel</option>
                                            <option>stripe</option>
                                        </select>
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
            <?php require_once('partials/_footer.php'); ?>
        </div>
        <?php }
    ?>
    </div>
    <?php require_once('partials/_scripts.php'); ?>
</body>

</html>